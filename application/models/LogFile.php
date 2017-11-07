<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

use LINE\LINEBot;

class LogFile extends OaLineModel {

  static $table_name = 'log_files';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function file_url () {
    if (!(isset ($this->message_id) && $this->message_id)) return '';
    return LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/' . urlencode ($this->message_id) . '/content';
  }
}