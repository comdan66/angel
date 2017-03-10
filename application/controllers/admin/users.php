<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Users extends Admin_controller {
  private $uri_1 = null;
  private $obj = null;

  public function __construct () {
    parent::__construct ();
    
    if (!User::current ()->in_roles (array ('admin')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/users';

    if (in_array ($this->uri->rsegments (2, 0), array ('edit', 'update')))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = User::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('now_url', base_url ($this->uri_1));
  }
  public function index ($offset = 0) {
    $columns = array ( 
        array ('key' => 'name',   'title' => '名稱', 'sql' => 'name LIKE ?'), 
        array ('key' => 'email', 'title' => '信箱', 'sql' => 'email LIKE ?'), 
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ('%s'));
    $conditions = conditions ($columns, $configs);

    $limit = 10;
    $total = User::count (array ('conditions' => $conditions));
    $objs = User::find ('all', array ('offset' => $offset < $total ? $offset : 0, 'limit' => $limit, 'order' => 'id DESC', 'conditions' => $conditions));

    return $this->load_view (array (
        'objs' => $objs,
        'columns' => $columns,
        'pagination' => $this->_get_pagination ($limit, $total, $configs),
      ));
  }
  public function edit () {
    $posts = Session::getData ('posts', true);
    $roles = Cfg::setting ('role', 'role_names');


    return $this->load_view (array (
        'posts' => $posts,
        'obj' => $this->obj,
        'roles' => $roles
      ));
  }
  public function update () {
    $obj = $this->obj;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => '非 POST 方法，錯誤的頁面請求。'));

    $posts = OAInput::post ();

    if ($msg = $this->_validation_update ($posts, $cover, $obj))
      return redirect_message (array ($this->uri_1, $obj->id, 'edit'), array ('_flash_danger' => $msg, 'posts' => $posts));

    if ($roles = UserRole::find ('all', array ('select' => 'id', 'conditions' => array ('user_id = ?', $this->obj->id))))
      foreach ($roles as $role)
        UserRole::transaction (function () use ($role) { return $role->destroy (); });

    if ($posts['roles'])
      foreach ($posts['roles'] as $role)
        UserRole::transaction (function () use ($role, $obj) { return verifyCreateOrm (UserRole::create (array_intersect_key (array ('user_id' => $obj->id, 'name' => $role), UserRole::table ()->columns))); });

    return redirect_message (array ($this->uri_1), array ('_flash_info' => '更新成功！'));
  }
  private function _validation_update (&$posts, &$cover, $obj) {
    $posts['roles'] = isset ($posts['roles']) && is_array ($posts['roles']) && $posts['roles'] ? $posts['roles'] : array ();
    $roles = Cfg::setting ('role', 'roles');
    $posts['roles'] = array_values (array_filter ($posts['roles'], function ($role) use ($roles) { return in_array ($role, $roles); }));

    return '';
  }
}
