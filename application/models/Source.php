<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

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

  static $typeNames = array (
    self::TYPE_USER   => '使用者',
    self::TYPE_GROUP  => '群組',
    self::TYPE_ROOM   => '聊天室',
    self::TYPE_OTHER  => '其他',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function updateTitle () {
    if (!(isset ($this->id) && isset ($this->sid) && isset ($this->title))) return false;

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $response = $bot->getProfile ($this->sid);
    
    if (!$response->isSucceeded ()) return false;

    $profile = $response->getJSONDecodedBody ();
    $this->title = $profile['displayName'];
    return $this->save ();
        // echo $profile['displayName'];
        // echo $profile['pictureUrl'];
        // echo $profile['statusMessage'];
  }
}