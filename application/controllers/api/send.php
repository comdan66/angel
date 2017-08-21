<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Send extends Api_controller {

  public function __construct () {
    parent::__construct ();
    
  }

  public function test ($message = '') {
    $user_id = 'C060c524e90c9f04dbf35d983c2e2c52e';
    $channel_secret = Cfg::setting ('line', 'channel', 'secret');
    $token = Cfg::setting ('line', 'channel', 'token');

    $httpClient = new CurlHTTPClient ($token);
    $bot = new LINEBot ($httpClient, ['channelSecret' => $channel_secret]);

    $textMessageBuilder = new TextMessageBuilder ('測試！');
    $response = $bot->pushMessage ($user_id, $textMessageBuilder);
  }
}
