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
  );

  static $belongs_to = array (
  );

  const SELECTED_1 = 1;
  const SELECTED_2 = 2;

  static $selectedNames = array (
    self::SELECTED_1 => '不預設',
    self::SELECTED_2 => '預設',
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

    if ($this->rid && !OALineBotRichmenu::deleteRichmenu ($this->rid))
      return false;

    if (!$rid = OALineBotRichmenu::createRichmenu ($this->format ()))
      return false;

    $this->rid = $rid;
    if (!$this->save ()) return false;

    return OALineBotRichmenu::uploadRichmenuImage ($this->rid, $this->cover->url ());
  }
  public function destroy () {
    if ($this->rid && !OALineBotRichmenu::deleteRichmenu ($this->rid))
          return false;

    if ($this->actions)
      foreach ($this->actions as $actions)
        if (!$actions->destroy ())
          return false;

    return $this->delete ();
  }
}