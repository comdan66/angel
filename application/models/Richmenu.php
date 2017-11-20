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
    array ('sources', 'class_name' => 'Source'),
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
  const ISD4_NO = 1;
  const ISD4_YES = 2;

  static $isd4Names = array (
    self::ISD4_NO => '非預設',
    self::ISD4_YES => '預設',
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
  public function createRichmenu () {
    $this->CI->load->library ('OALineBot');
    return ($this->rid = OALineBotRichmenu::createRichmenu ($this->format ())) && OALineBotRichmenu::uploadRichmenuImage ($this->rid, $this->cover->url ()) && $this->save ();
  }
  public function updateRichmenu () {
    $that = $this;
    $that->CI->load->library ('OALineBot');
    return (!$that->rid || (OALineBotRichmenu::deleteRichmenu ($that->rid))) && ($that->rid = OALineBotRichmenu::createRichmenu ($that->format ())) && OALineBotRichmenu::uploadRichmenuImage ($that->rid, $that->cover->url ()) && $that->save () && !array_filter (array_map(function ($source) use ($that) { return !$source->linkRichmenu ($that); }, $that->sources));
  }
  public function deleteRichmenu () {
    $this->CI->load->library ('OALineBot');
    return (!$this->rid || (OALineBotRichmenu::deleteRichmenu ($this->rid))) && !array_filter (array_map(function ($source) { return !$source->unlinkRichmenu (); }, $this->sources));
  }

  public function destroy () {
    if (!(isset ($this->id) && isset ($this->rid)))
      return false;

    if ($this->actions)
      foreach ($this->actions as $actions)
        if (!$actions->destroy ())
          return false;

    return $this->deleteRichmenu () && $this->delete ();
  }
}