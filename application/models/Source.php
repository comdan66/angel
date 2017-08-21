<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Source extends OaModel {

  static $table_name = 'sources';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  const STATUS_LEAVE    = 1;
  const STATUS_JOIN     = 2;
  const STATUS_OTHER    = 3;

  static $statusNames = array (
    self::STATUS_LEAVE => '離開',
    self::STATUS_JOIN  => '加入',
    self::STATUS_OTHER => '其他',
  );

  const TYPE_USER    = 1;
  const TYPE_GROUP   = 2;
  const TYPE_ROOM    = 3;
  const TYPE_OTHER   = 4;

  static $statusNames = array (
    self::TYPE_USER   => '使用者',
    self::TYPE_GROUP  => '群組',
    self::TYPE_ROOM   => '聊天室',
    self::TYPE_OTHER  => '其他',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
}