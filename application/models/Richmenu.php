<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Richmenu extends OaModel {

  static $table_name = 'richmenus';

  static $has_one = array (
  );

  static $has_many = array (
    array ('actions', 'class_name' => 'RichmenuAction'),
    array ('sets', 'class_name' => 'SourceSet'),
  );

  static $belongs_to = array (
  );

  const SELECTED_1 = 1;
  const SELECTED_2 = 2;

  static $selectedNames = array (
    self::SELECTED_1 => '不預設',
    self::SELECTED_2 => '預設',
  );
  const STATUS_1 = 1;
  const STATUS_2 = 2;

  static $statusNames = array (
    self::STATUS_1 => '未更新',
    self::STATUS_2 => '已更新',
  );
  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmImageUploader::bind ('cover', 'RichmenuCoverImageUploader');
  }
  public function format () {
    return array (
        'size' => array (
            'width' => $this->width,
            'height' => $this->height,
          ),
        'selected' => $this->selected == Richmenu::SELECTED_2,
        'name' => $this->name,
        'chatBarText' => $this->text,
        'areas' => array_map (function ($action) { return $action->format (); }, $this->actions)
      );
  }
  public function put () {
    $this->CI->load->library ('OALineBot');

    if (count (array_filter (array_map (function ($rid) { return !OALineBotRichmenu::deleteRichmenu ($rid); }, $richMenuIds = array_diff (column_array (OALineBotRichmenu::getRichmenuList (), 'richMenuId'), column_array (Richmenu::find ('all', array ('select' => 'rid', 'conditions' => array ('rid != ?', ''))), 'rid'))))))
      return false;

    if ($this->rid && !OALineBotRichmenu::deleteRichmenu ($this->rid))
      return false;

    $this->status = Richmenu::STATUS_2;
    if (!(($this->rid = OALineBotRichmenu::createRichmenu ($this->format ())) && $this->save ()))
      return false;

    if (!(OALineBotRichmenu::uploadRichmenuImage ($this->rid, $this->cover->url ())))
      return false;

    if (($rids = array_diff (column_array (Richmenu::find ('all', array ('select' => 'rid', 'conditions' => array ('rid != ?', ''))), 'rid'), column_array (OALineBotRichmenu::getRichmenuList (), 'richMenuId'))) && count (array_filter (array_map (function ($richmenu) { return !$richmenu->reset (); }, Richmenu::find ('all', array ('select' => 'id, rid', 'include' => array ('sets'), 'conditions' => array ('rid IN (?)', $rids)))))))
      return false;

    $that = $this;
    if (array_filter (array_map(function ($set) use ($that) {
      return !($set->source ? $set->source->updateRichmenu ($that) : true);
    }, SourceSet::find ('all', array ('select' => 'source_id', 'include' => array ('source'), 'conditions' => array ('richmenu_id = ?', $this->id))))))
      return false;


    return true;
  }
  public function reset () {
    return ($this->status = Richmenu::STATUS_1) && $this->save ();
  }
  public function destroy () {
    $this->CI->load->library ('OALineBot');
    
    if (!OALineBotRichmenu::deleteRichmenu ($this->rid))
      return false;

    if ($sets = SourceSet::find ('all', array ('select' => 'source_id', 'include' => array ('source'), 'conditions' => array ('richmenu_id = ?', $this->id))))
      foreach ($sets as $set)
        if (!$set->source->removeRichmenu ())
          return false;

    if ($this->actions)
      foreach ($this->actions as $actions)
        if (!$actions->destroy ())
          return false;

    return $this->delete ();
  }
}