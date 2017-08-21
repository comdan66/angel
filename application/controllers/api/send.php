<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

require_once FCPATH . 'vendor/autoload.php';

use LINE\LINEBot;
use LINE\LINEBot\Constant\EventSourceType;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\VideoMessage;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\Event\MessageEvent\ImageMessage;
use LINE\LINEBot\Event\MessageEvent\AudioMessage;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;

class Send extends Api_controller {

  public function __construct () {
    parent::__construct ();
    
  }

  public function img () {
    $user_id = 'U4a37e32a1d11b3995d2bf299597e432f';
    $channel_secret = Cfg::setting ('line', 'channel', 'secret');
    $token = Cfg::setting ('line', 'channel', 'token');

    $httpClient = new CurlHTTPClient ($token);
    $bot = new LINEBot ($httpClient, ['channelSecret' => $channel_secret]);

    $builder = new ImageMessageBuilder ('https://pic.mazu.ioa.tw/u/album_images/name/0/0/4/76/800w_1889643416_58dd52e847cc8.jpg', 'https://pic.mazu.ioa.tw/u/album_images/name/0/0/4/76/800w_1889643416_58dd52e847cc8.jpg');
    $response = $bot->pushMessage ($user_id, $builder);
  }
  public function test () {
    if (!(($q = OAInput::get ('q')) && ($q = trim ($q)))) return;
    
    $q = mb_strimwidth ($q, 0, 998 * 2, '…','UTF-8');

    $user_id = 'C060c524e90c9f04dbf35d983c2e2c52e';
    $channel_secret = Cfg::setting ('line', 'channel', 'secret');
    $token = Cfg::setting ('line', 'channel', 'token');

    $httpClient = new CurlHTTPClient ($token);
    $bot = new LINEBot ($httpClient, ['channelSecret' => $channel_secret]);

    $textMessageBuilder = new TextMessageBuilder ($q);
    $response = $bot->pushMessage ($user_id, $textMessageBuilder);
  }
}
