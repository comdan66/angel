
<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Richmenu_actions extends Admin_controller {
  private $uri_1 = null;
  private $uri_2 = null;
  private $uri_b = null;
  private $parent = null;
  private $obj = null;
  private $icon = null;
  private $title = null;

  public function __construct () {
    parent::__construct (array ('richmenus'));
    
    $this->uri_1 = 'admin/richmenu';
    $this->uri_2 = 'actions';
    $this->uri_b = 'admin/richmenus';
    $this->icon = 'icon-touch_app';

    if (!(($id = $this->uri->rsegments (3, 0)) && ($this->parent = Richmenu::find_by_id ($id))))
      return redirect_message (array ('admin', 'richmenus'), array ('_fd' => '找不到該筆資料。'));

    $this->title = '「' . $this->parent->name . '」的點擊事件';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update', 'destroy')))
      if (!(($id = $this->uri->rsegments (4, 0)) && ($this->obj = RichmenuAction::find_by_id ($id))))
        return redirect_message (array ($this->uri_1, $this->parent->id, $this->uri_2), array ('_fd' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('uri_2', $this->uri_2)
         ->add_param ('uri_b', $this->uri_b)
         ->add_param ('icon', $this->icon)
         ->add_param ('title', $this->title)
         ->add_param ('parent', $this->parent)
         ->add_param ('_url', base_url ($this->uri_b));
  }

  public function index ($id, $offset = 0) {
    $parent = $this->parent;

    $searches = array ();

    $configs = array_merge (explode ('/', $this->uri_1), array ($parent->id, $this->uri_2, '%s'));
    $objs = conditions ($searches, $configs, $offset, 'RichmenuAction', array ('order' => 'id DESC'), function ($conditions) use ($parent) {
      OaModel::addConditions ($conditions, 'richmenu_id = ?', $parent->id);
      return $conditions;
    });

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
    $parent = $this->parent;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['data'] = OAInput::post ('data', false);
    $posts['richmenu_id'] = $parent->id;

    $validation = function (&$posts) {
      if (!(isset ($posts['x']) && is_string ($posts['x']) && is_numeric ($posts['x'] = trim ($posts['x'])) && ($posts['x'] >= 0 && $posts['x'] <= 2500))) return '「X 點座標」格式有誤！';
      if (!(isset ($posts['y']) && is_string ($posts['y']) && is_numeric ($posts['y'] = trim ($posts['y'])) && ($posts['y'] >= 0 && ($posts['y'] <= 1686 || $posts['y'] <= 843)))) return '「Y 點座標」格式有誤！';
      if (!(isset ($posts['width']) && is_string ($posts['width']) && is_numeric ($posts['width'] = trim ($posts['width'])) && ($posts['width'] >= 0 && $posts['width'] <= 2500))) return '「寬度範圍」格式有誤！';
      if (!(isset ($posts['height']) && is_string ($posts['height']) && is_numeric ($posts['height'] = trim ($posts['height'])) && ($posts['height'] >= 0 && ($posts['height'] <= 1686 || $posts['height'] <= 843)))) return '「高度範圍」格式有誤！';

      // if ($posts['x'] + $posts['width'] > 2500) return '請確認 X 座標與寬度範圍是否超出 2500 單位！';
      // if (($posts['y'] + $posts['height'] > 1686) && ($posts['y'] + $posts['height'] > 843)) return '請確認 Y 座標與高度範圍是否都超出 1686、843 單位！';

      if (!(isset ($posts['action_type']) && is_string ($posts['action_type']) && is_numeric ($posts['action_type'] = trim ($posts['action_type'])))) return '「事件類型」格式有誤！';
      
      switch ($posts['action_type']) {
        case RichmenuAction::ACTION_TYPE_1:if (!(isset ($posts['text']) && is_string ($posts['text']) && ($posts['text'] = catStr (trim ($posts['text']), 300)))) return '「訊息」格式有誤！';
        $posts['uri'] = $posts['data'] = '';
        $posts['action_pick_type'] = RichmenuAction::ACTION_PICK_MODE_1; break;

        case RichmenuAction::ACTION_TYPE_2: if (!(isset ($posts['uri']) && is_string ($posts['uri']) && ($posts['uri'] = catStr (trim ($posts['uri']), 1000)) && (isHttp ($posts['uri']) || isTel ($posts['uri']) || isHttps ($posts['uri'])))) return '「網址」格式有誤！';
        $posts['data'] = $posts['text'] = '';
        $posts['action_pick_type'] = RichmenuAction::ACTION_PICK_MODE_1; break;

        case RichmenuAction::ACTION_TYPE_3: if (!(isset ($posts['data']) && is_string ($posts['data']) && ($posts['data'] = catStr (trim ($posts['data']), 300)))) return '「回傳資料」格式有誤！';
        $posts['uri'] = $posts['text'] = '';
        $posts['action_pick_type'] = RichmenuAction::ACTION_PICK_MODE_1; break;

        case RichmenuAction::ACTION_TYPE_4: if (!(isset ($posts['action_pick_type']) && is_string ($posts['action_pick_type']) && is_numeric ($posts['action_pick_type'] = trim ($posts['action_pick_type'])) && in_array ($posts['action_pick_type'], array_keys (RichmenuAction::$actionPickTypeNames)))) return '「時間類型」格式有誤！';
        if (!(isset ($posts['data']) && is_string ($posts['data']) && ($posts['data'] = catStr (trim ($posts['data']), 300)))) return '「回傳資料」格式有誤！';
        $posts['text'] = $posts['uri'] = ''; break;
        default: return '「事件類型」格式有誤！'; break;
      }
      return '';
    };

    if (($msg = $validation ($posts)) || (!RichmenuAction::transaction (function () use (&$obj, $posts, $parent) { return verifyCreateOrm ($obj = RichmenuAction::create (array_intersect_key ($posts, RichmenuAction::table ()->columns))) && $parent->updateRichmenu (); }) && $msg = '新增失敗！'))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, 'add'), array ('_fd' => $msg, 'posts' => $posts));

    return redirect_message (array ($this->uri_1, $parent->id,  $this->uri_2), array ('_fi' => '新增成功！'));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);

    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
      ));
  }
  public function update () {
    $parent = $this->parent;
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();
    $posts['data'] = OAInput::post ('data', false);
    
    $validation = function (&$posts) {
      if (!(isset ($posts['x']) && is_string ($posts['x']) && is_numeric ($posts['x'] = trim ($posts['x'])) && ($posts['x'] >= 0 && $posts['x'] <= 2500))) return '「X 點座標」格式有誤！';
      if (!(isset ($posts['y']) && is_string ($posts['y']) && is_numeric ($posts['y'] = trim ($posts['y'])) && ($posts['y'] >= 0 && ($posts['y'] <= 1686 || $posts['y'] <= 843)))) return '「Y 點座標」格式有誤！';
      if (!(isset ($posts['width']) && is_string ($posts['width']) && is_numeric ($posts['width'] = trim ($posts['width'])) && ($posts['width'] >= 0 && $posts['width'] <= 2500))) return '「寬度範圍」格式有誤！';
      if (!(isset ($posts['height']) && is_string ($posts['height']) && is_numeric ($posts['height'] = trim ($posts['height'])) && ($posts['height'] >= 0 && ($posts['height'] <= 1686 || $posts['height'] <= 843)))) return '「高度範圍」格式有誤！';

      // if ($posts['x'] + $posts['width'] > 2500) return '請確認 X 座標與寬度範圍是否超出 2500 單位！';
      // if (($posts['y'] + $posts['height'] > 1686) && ($posts['y'] + $posts['height'] > 843)) return '請確認 Y 座標與高度範圍是否都超出 1686、843 單位！';

      if (!(isset ($posts['action_type']) && is_string ($posts['action_type']) && is_numeric ($posts['action_type'] = trim ($posts['action_type'])) && in_array ($posts['action_type'], array_keys (RichmenuAction::$actionTypeNames)))) return '「事件類型」格式有誤！';

      switch ($posts['action_type']) {
        case RichmenuAction::ACTION_TYPE_1:if (!(isset ($posts['text']) && is_string ($posts['text']) && ($posts['text'] = catStr (trim ($posts['text']), 300)))) return '「訊息」格式有誤！';
        $posts['uri'] = $posts['data'] = '';
        $posts['action_pick_type'] = RichmenuAction::ACTION_PICK_MODE_1; break;

        case RichmenuAction::ACTION_TYPE_2: if (!(isset ($posts['uri']) && is_string ($posts['uri']) && ($posts['uri'] = catStr (trim ($posts['uri']), 1000)) && (isHttp ($posts['uri']) || isTel ($posts['uri']) || isHttps ($posts['uri'])))) return '「網址」格式有誤！';
        $posts['data'] = $posts['text'] = '';
        $posts['action_pick_type'] = RichmenuAction::ACTION_PICK_MODE_1; break;

        case RichmenuAction::ACTION_TYPE_3: if (!(isset ($posts['data']) && is_string ($posts['data']) && ($posts['data'] = catStr (trim ($posts['data']), 300)))) return '「回傳資料」格式有誤！';
        $posts['uri'] = $posts['text'] = '';
        $posts['action_pick_type'] = RichmenuAction::ACTION_PICK_MODE_1; break;

        case RichmenuAction::ACTION_TYPE_4: if (!(isset ($posts['action_pick_type']) && is_string ($posts['action_pick_type']) && is_numeric ($posts['action_pick_type'] = trim ($posts['action_pick_type'])) && in_array ($posts['action_pick_type'], array_keys (RichmenuAction::$actionPickTypeNames)))) return '「時間類型」格式有誤！';
        if (!(isset ($posts['data']) && is_string ($posts['data']) && ($posts['data'] = catStr (trim ($posts['data']), 300)))) return '「回傳資料」格式有誤！';
        $posts['data'] = $posts['text'] = $posts['uri'] = ''; break;
        default: return '「事件類型」格式有誤！'; break;
      }

      return '';
    };

    if ($msg = $validation ($posts))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_fd' => $msg, 'posts' => $posts));

    if ($columns = array_intersect_key ($posts, $obj->table ()->columns))
      foreach ($columns as $column => $value)
        $obj->$column = $value;

    if (!RichmenuAction::transaction (function () use ($obj, $posts, $parent) { return $obj->save () && $parent->updateRichmenu (); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2, $obj->id, 'edit'), array ('_fd' => '更新失敗！', 'posts' => $posts));

    return redirect_message (array ($this->uri_1, $parent->id,  $this->uri_2), array ('_fi' => '新增成功！'));
  }

  public function destroy () {
    $parent = $this->parent;
    $obj = $this->obj;

    if (!RichmenuAction::transaction (function () use ($obj, $parent) { return $obj->destroy () && $parent->updateRichmenu (); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_fd' => '刪除失敗！'));

    return redirect_message (array ($this->uri_1, $parent->id,  $this->uri_2), array ('_fi' => '刪除成功！'));
  }
}
