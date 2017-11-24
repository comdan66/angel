<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class LogSticker extends OaModel {

  static $table_name = 'log_stickers';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('speaker', 'class_name' => 'Source', 'foreign_key' => 'speaker_id'),
    array ('source', 'class_name' => 'Source', 'foreign_key' => 'source_id'),
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
}