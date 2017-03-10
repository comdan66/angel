<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Keywords extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('keyword')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));

    $this->uri_1 = 'admin/keywords';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Keyword::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('now_url', base_url ($this->uri_1));
  }
  public function index ($offset = 0) {
    $columns = array ( 
        array ('key' => 'pattern', 'title' => '模式', 'sql' => 'pattern LIKE ?'), 
        array ('key' => 'content', 'title' => '回覆內容', 'sql' => 'content LIKE ?'), 
        array ('key' => 'type', 'title' => '針對類型', 'sql' => 'type = ?', 'select' => array_map (function ($key) { return array ('value' => '' . $key, 'text' => Keyword::$typeNames[$key]);}, array_keys (Keyword::$typeNames))),
        array ('key' => 'method', 'title' => '回應類型', 'sql' => 'method = ?', 'select' => array_map (function ($key) { return array ('value' => '' . $key, 'text' => Keyword::$methodNames[$key]);}, array_keys (Keyword::$methodNames))),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $limit = 25;
    $total = Keyword::count (array ('conditions' => $conditions));
    $objs = Keyword::find ('all', array ('include' => array ('contents'), 'offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'weight DESC', 'conditions' => $conditions));

    return $this->load_view (array (
        'objs' => $objs,
        'columns' => $columns,
        'pagination' => $this->_get_pagination ($limit, $total, $configs),
      ));
  }
  public function add () {
    $posts = Session::getData ('posts', true);
    $contents = isset ($posts['contents']) ? $posts['contents'] : array ();

    return $this->load_view (array (
        'posts' => $posts,
        'contents' => $contents,
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['pattern'] = OAInput::post ('pattern', true);
    $posts['contents'] = OAInput::post ('contents', true);

    if ($msg = $this->_validation_create ($posts))
      return !($posts['pattern'] = $posts['contents'] = '') && redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if (!Keyword::transaction (function () use (&$obj, $posts) { return verifyCreateOrm ($obj = Keyword::create (array_intersect_key ($posts, Keyword::table ()->columns))); }))
      return !($posts['pattern'] = $posts['contents'] = '') && redirect_message (array ($this->uri_1, 'add'), array ('_flash_danger' => '新增失敗！', 'posts' => $posts));

    if ($posts['contents'])
      foreach ($posts['contents'] as $i => $content)
        KeywordContent::transaction (function () use ($i, $content, $obj) { return verifyCreateOrm (KeywordContent::create (array_intersect_key (array ('text' => $content, 'keyword_id' => $obj->id), KeywordContent::table ()->columns))); });

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
        'contents' => isset ($posts['contents']) ? $posts['contents'] : column_array ($this->obj->contents, 'text'),
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['pattern'] = OAInput::post ('pattern');
    $posts['content'] = OAInput::post ('content');

    if ($msg = $this->_validation_update ($posts))
      return !($posts['pattern'] = $posts['contents'] = '') && redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;
    
    if (!Keyword::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return !($posts['pattern'] = $posts['contents'] = '') && redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '更新失敗！', 'posts' => $posts));

    if ($obj->contents)
      foreach ($obj->contents as $content)
        KeywordContent::transaction (function () use ($content) { return $content->destroy (); });

    if ($posts['contents'])
      foreach ($posts['contents'] as $i => $content)
        KeywordContent::transaction (function () use ($i, $content, $obj) { return verifyCreateOrm (KeywordContent::create (array_intersect_key (array ('text' => $content, 'keyword_id' => $obj->id), KeywordContent::table ()->columns))); });

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '更新成功！'));
  }

  public function destroy () {
    $obj = $this->obj;
    
    if (!Keyword::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_flash_danger' => '刪除失敗！'));

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '刪除成功！'));
  }
  private function _validation_create (&$posts) {
    if (!isset ($posts['pattern'])) return '沒有填寫 關鍵字 Pattern！';
    if (!(is_string ($posts['pattern']) && ($posts['pattern'] = trim ($posts['pattern'])))) return '關鍵字 Pattern 格式錯誤！';
    
    if (!isset ($posts['weight'])) return '沒有填寫 權重！';
    if (!(is_string ($posts['weight']) && is_numeric ($posts['weight'] = trim ($posts['weight'])))) return '權重 格式錯誤！';
    
    if (!isset ($posts['type'])) return '沒有填寫 針對類型！';
    if (!(is_string ($posts['type']) && is_numeric ($posts['type'] = trim ($posts['type'])) && in_array ($posts['type'], array_keys (Keyword::$typeNames)))) return '針對類型 格式錯誤！';

    if (!isset ($posts['method'])) return '沒有填寫 回應類型！';
    if (!(is_string ($posts['method']) && is_numeric ($posts['method'] = trim ($posts['method'])) && in_array ($posts['method'], array_keys (Keyword::$methodNames)))) return '回應類型 格式錯誤！';

    if ($posts['method'] != Keyword::METHOD_TEXT) {
      // $posts['content'] = ;

      if (in_array ($posts['method'], array (Keyword::METHOD_ALLEY_KEYWORD, Keyword::METHOD_YOUTUBE, Keyword::METHOD_FLICKR)) && !preg_match ('/\(\?P<' . LogText::KEYWORD . '>.+\)/', $posts['pattern']))
        return '沒找到要搜尋的關鍵字 Pattern 規則有誤，Sub Pattern 前加 ?P&lt;' . LogText::KEYWORD . '&gt;！';
      else
        return '';
    }
    $posts['contents'] = isset ($posts['contents']) && is_array ($posts['contents']) && $posts['contents'] ? array_values (array_filter (array_map (function ($content) { return is_string ($content) && $content && ($content = trim ($content)) ? $content : ''; }, $posts['contents']), function ($content) { return $content; })) : array ();

    
    // if (!isset ($posts['content'])) return '沒有填寫 回覆內容！';
    // if (!(is_string ($posts['content']) && ($posts['content'] = trim ($posts['content'])))) return '回覆內容 格式錯誤！';
    return '';
  }

  private function _validation_update (&$posts) {
    return $this->_validation_create ($posts);
  }
}
