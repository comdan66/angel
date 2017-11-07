<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */
use LINE\LINEBot;

class LogImage extends OaLineModel {

  static $table_name = 'log_images';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }

  public function saveImage () {
    if (!(isset ($this->message_id) && $this->message_id)) return null;
    
    $this->CI->load->library ('OALineBot');

    if (!$oaLineBot = OALineBot::create ())
      return null;

    $response = $oaLineBot->bot ()->getMessageContent ($this->message_id);
    if (!$response->isSucceeded ())
      return null;
echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
var_dump ( $response->getHeaders ());
exit ();

    header ('Content-Type: ' . $response->getHeader ('Content-Type'));
    return $response->getRawBody ();


    // $path = FCPATH . 'temp/input.json';
    // write_file ($path, $log . "\n", FOPEN_READ_WRITE_CREATE);
  }
}