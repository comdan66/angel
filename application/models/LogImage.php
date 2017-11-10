<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class LogImage extends OaModel {

  static $table_name = 'log_images';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmImageUploader::bind ('file', 'LogImageFileImageUploader');
  }
  public function putFile2S3 () {
    if (!(isset ($this->id) && isset ($this->file) && isset ($this->message_id) && !((string)$this->file) && $this->message_id)) return false;
    
    $this->CI->load->library ('OALineBot');

    if (!(($oaLineBot = OALineBot::create ()) && ($response = $oaLineBot->bot ()->getMessageContent ($this->message_id)) && $response->isSucceeded () && ($path = FCPATH . 'temp' . DIRECTORY_SEPARATOR . uniqid (rand () . '_') . (($contentType = $response->getHeader ('Content-Type')) ? contentType2ext ($contentType) : '')) && write_file ($path, $response->getRawBody ())))
      return false;

    return $this->file->put ($path);
  }
}