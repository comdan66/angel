<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class LinebotLog extends OaLineModel {

  static $table_name = 'linebot_logs';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  const STATUS_INIT     = 1;
  const STATUS_CONTENT  = 2;
  const STATUS_MATCH    = 3;
  const STATUS_RESPONSE = 4;
  const STATUS_SUCCESS  = 5;

  static $statusNames = array (
    self::STATUS_INIT     => '不回應',
    self::STATUS_CONTENT  => '獲取內容',
    self::STATUS_MATCH    => '符合內容',
    self::STATUS_RESPONSE => '回應內容',
    self::STATUS_SUCCESS  => '回應成功',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function setStatus ($status) {
    if (!(isset ($this->id, $this->status) && in_array ($status, array_keys (LinebotLog::$statusNames)))) return false;
    $this->status = $status;
    return $this->save ();
  }
}