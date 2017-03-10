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
    
    if (!admin::current ()->in_roles (array ('user')))
      return redirect_message (array ('admin'), array ('_flash_danger' => '您的權限不足，或者頁面不存在。'));
    
    $this->uri_1 = 'admin/users';

    if (in_array ($this->uri->rsegments (2, 0), array ()))
      if (!(($id = $this->uri->rsegments (3, 0)) && ($this->obj = User::find ('one', array ('conditions' => array ('id = ?', $id))))))
        return redirect_message (array ($this->uri_1), array ('_flash_danger' => '找不到該筆資料。'));

    $this->add_param ('uri_1', $this->uri_1)
         ->add_param ('now_url', base_url ($this->uri_1));
  }
  public function index ($offset = 0) {
    $columns = array ( 
        array ('key' => 'name',   'title' => '名稱', 'sql' => 'name LIKE ?'), 
        array ('key' => 'email', 'title' => '信箱', 'sql' => 'email LIKE ?'), 
        array ('key' => 'ban',   'title' => '檢舉次數大於等於', 'ban >= ?'), 
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
}
