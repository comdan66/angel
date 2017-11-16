<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Richmenus extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct (array ('richmenus'));
    
    $this->uri_1 = 'admin/richmenus';
    $this->icon = 'icon-table2';
    $this->title = 'Rich Menu';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy', 'selected', 'show', 'put', 'users', 'users_pick')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = Richmenu::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_fd' => '找不到該筆資料。'));

    $this->load->library ('image/ImageUtility');
    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('_url', base_url ($this->uri_1));
  }
  public function index ($offset = 0) {
    $searches = array (
        'name'     => array ('el' => 'input', 'text' => '名稱', 'sql' => 'name LIKE ?'),
        'text'     => array ('el' => 'input', 'text' => '顯示名稱', 'sql' => 'text LIKE ?'),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $objs = conditions ($searches, $configs, $offset, 'Richmenu', array ('order' => 'id DESC'));

    return $this->load_view (array (
        'objs' => $objs,
        'total' => $offset,
        'searches' => $searches,
        'pagination' => $this->_get_pagination ($configs),
      ));
  }
  public function add () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
      ));
  }
  public function create () {
    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $cover = OAInput::file ('cover');
    $posts['rid'] = '';
    $posts['status'] = Richmenu::STATUS_1;

    $validation = function (&$posts, &$cover) {
      if (!(isset ($posts['selected']) && is_string ($posts['selected']) && is_numeric ($posts['selected'] = trim ($posts['selected'])) && in_array ($posts['selected'], array_keys (Richmenu::$selectedNames)))) $posts['selected'] = Richmenu::SELECTED_1;
      if (!(isset ($posts['name']) && is_string ($posts['name']) && ($posts['name'] = catStr (trim ($posts['name']), 300)))) return '「' . $this->title . '名稱」格式錯誤！';
      if (!(isset ($posts['text']) && is_string ($posts['text']) && ($posts['text'] = trim ($posts['text'])))) return '「顯示名稱」格式錯誤！';

      if (!(isset ($cover) && is_upload_image_format ($cover, array ('gif', 'jpeg', 'jpg', 'png')))) return '「' . $this->title . '封面」格式錯誤！';

      if (isset ($cover) && !(($img = ImageUtility::create ($cover['tmp_name'])) && ($img = $img->getDimension ()) && isset ($img['width']) && isset ($img['height']) && ($img['width'] == 2500) && ($img['height'] == 1686 || $img['height'] == 843)))
        return '「' . $this->title . '封面」格式錯誤！';

      $posts['width'] = $img['width'];
      $posts['height'] = $img['height'];
      return '';
    };

    if (($msg = $validation ($posts, $cover)) || (!Richmenu::transaction (function () use (&$obj, $posts, $cover) { return verifyCreateOrm ($obj = Richmenu::create (array_intersect_key ($posts, Richmenu::table ()->columns))) && $obj->cover->put ($cover); }) && ($msg = '資料庫處理錯誤！')))
      return redirect_message (array ($this->uri_1, 'add'), array ('_fd' => $msg, 'posts' => $posts));

    return redirect_message (array ($this->uri_1), array ('_fi' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $cover = OAInput::file ('cover');

    $validation = function (&$posts, &$cover, $obj) {
      if (isset ($posts['selected']) && !(is_string ($posts['selected']) && is_numeric ($posts['selected'] = trim ($posts['selected'])) && in_array ($posts['selected'], array_keys (Richmenu::$selectedNames)))) $posts['selected'] = Richmenu::SELECTED_1;
      
      if (isset ($posts['name']) && !(is_string ($posts['name']) && ($posts['name'] = catStr (trim ($posts['name']), 300)))) return '「' . $this->title . '名稱」格式錯誤！';
      if (isset ($posts['text']) && !(is_string ($posts['text']) && ($posts['text'] = trim ($posts['text'])))) return '「顯示名稱」格式錯誤！';

      if (!((string)$obj->cover || isset ($cover))) return '「' . $this->title . '封面」格式錯誤！';
      if (isset ($cover) && !(is_upload_image_format ($cover, array ('gif', 'jpeg', 'jpg', 'png')))) return '「' . $this->title . '封面」格式錯誤！';

      if (isset ($cover) && !(($img = ImageUtility::create ($cover['tmp_name'])) && ($img = $img->getDimension ()) && isset ($img['width']) && isset ($img['height']) && ($img['width'] == 2500) && ($img['height'] == 1686 || $img['height'] == 843)))
        return '「' . $this->title . '封面」格式錯誤！';

      if (isset ($cover)) {
        $posts['width'] = $img['width'];
        $posts['height'] = $img['height'];
      }

      $posts['status'] = Richmenu::STATUS_1;

      return '';
    };

    if ($msg = $validation ($posts, $cover, $obj))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Richmenu::transaction (function () use ($obj, $posts, $cover) { if (!$obj->save ()) return false; if ($cover && !$obj->cover->put ($cover)) return false; return true; }))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_fd' => '資料庫處理錯誤！', 'posts' => $posts));

    return redirect_message (array ($this->uri_1), array ('_fi' => '更新成功！'));
  }
  public function destroy () {
    $obj = $this->obj;

    if (!Richmenu::transaction (function () use ($obj) { return $obj->destroy (); }))
      return redirect_message (array ($this->uri_1), array ('_fd' => '資料庫處理錯誤！'));

    return redirect_message (array ($this->uri_1), array ('_fi' => '刪除成功！'));
  }

  public function put () {
    $obj = $this->obj;
    if (!Richmenu::transaction (function () use ($obj) { return $obj->put (); }))
      return redirect_message (array ($this->uri_1), array ('_fd' => '上傳失敗！'));
    return redirect_message (array ($this->uri_1), array ('_fi' => '上傳成功！'));
  }
  public function selected () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return $this->output_error_json ('非 POST 方法，錯誤的頁面請求。');

    $posts = OAInput::post ();
    $posts['status'] = Richmenu::STATUS_1;

    $validation = function (&$posts) {
      return !(isset ($posts['selected']) && is_string ($posts['selected']) && is_numeric ($posts['selected'] = trim ($posts['selected'])) && ($posts['selected'] = $posts['selected'] ? Richmenu::SELECTED_2 : Richmenu::SELECTED_1) && in_array ($posts['selected'], array_keys (Richmenu::$selectedNames))) ? '「設定上下架」發生錯誤！' : '';
    };

    if ($msg = $validation ($posts))
      return $this->output_error_json ($msg);

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!Richmenu::transaction (function () use ($obj, $posts) { return $obj->save (); }))
      return $this->output_error_json ('資料庫處理錯誤！');

    return $this->output_json ($obj->selected == Richmenu::SELECTED_2);
  }
}
