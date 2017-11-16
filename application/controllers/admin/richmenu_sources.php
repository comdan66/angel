
<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Richmenu_sources extends Admin_controller {
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
    $this->uri_2 = 'sources';
    $this->uri_b = 'admin/richmenus';
    $this->icon = 'icon-u';

    if (!(($id = $this->uri->rsegments (3, 0)) && ($this->parent = Richmenu::find_by_id ($id))))
      return redirect_message (array ('admin', 'work-tags'), array ('_fd' => '找不到該筆資料。'));

    $this->title = '選擇設定為「' . $this->parent->name . '」';

    if (in_array ($this->uri->rsegments (2, 0), array ('destroy')))
      if (!(($id = $this->uri->rsegments (4, 0)) && ($this->obj = Source::find_by_id ($id))))
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

    $searches = array (
        'title'     => array ('el' => 'input', 'text' => '名稱', 'sql' => 'title LIKE ?'),
      );

    $configs = array_merge (explode ('/', $this->uri_1), array ($parent->id, $this->uri_2, '%s'));
    
    $objs = conditions ($searches, $configs, $offset, 'Source', array ('order' => 'id DESC', 'include' => array ('set')), function ($conditions) {
      OaModel::addConditions ($conditions, 'type = ? AND title != ?', Source::TYPE_USER, '');
      return $conditions;
    });

    return $this->load_view (array (
        'obj' => $this->obj,
        'objs' => $objs,
        'total' => $offset,
        'searches' => $searches,
        'pagination' => $this->_get_pagination ($configs),
        'richmenus' => array_combine (column_array ($tmps = Richmenu::find ('all'), 'id'), $tmps),
      ));
  }
  public function create ($id) {
    $obj = $this->obj;
    $parent = $this->parent;

    if (!$this->has_post ())
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_fd' => '非 POST 方法，錯誤的頁面請求。'));

    if (!(($posts = OAInput::post ('ids')) && ($objs = Source::find ('all', array ('include' => array ('set'), 'conditions' => array ('id IN (?)', $posts))))))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_fd' => '找不到任何資料。'));

    if (array_filter (array_map (function ($o) use ($parent) { return !$o->updateRichmenu ($parent); }, $objs)))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_fd' => '設定失敗。'));

    return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_fi' => '設定成功！'));
  }
  public function destroy () {
    $parent = $this->parent;
    $obj = $this->obj;

    if (!Source::transaction (function () use ($obj) { return $obj->removeRichmenu (); }))
      return redirect_message (array ($this->uri_1, $parent->id, $this->uri_2), array ('_fd' => '移除 Richmenu 失敗！'));

    return redirect_message (array ($this->uri_1, $parent->id,  $this->uri_2), array ('_fi' => '移除 Richmenu 成功！'));
  }
}
