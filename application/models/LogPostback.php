<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class LogPostback extends OaModel {

  static $table_name = 'log_postbacks';

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
  public function getData ($k = null) {
    if (!(isset ($this->data) && $this->data && is_array ($data = json_decode ($this->data, true)))) return null;
    return $k === null ? $data : (isset ($data[$k]) ? $data[$k] : null);
  }
}