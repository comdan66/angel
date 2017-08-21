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
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;

class Send extends Api_controller {

  public function __construct () {
    parent::__construct ();
    
  }

  public function sticker ($packageId = 1, $stickerId = 1) {
    if (!(($packageId = trim ($packageId)) && is_numeric ($packageId) && ($stickerId = trim ($stickerId)) && is_numeric ($stickerId)))
      return ;

    $user_id = 'C060c524e90c9f04dbf35d983c2e2c52e';
    $channel_secret = Cfg::setting ('line', 'channel', 'secret');
    $token = Cfg::setting ('line', 'channel', 'token');

    $httpClient = new CurlHTTPClient ($token);
    $bot = new LINEBot ($httpClient, ['channelSecret' => $channel_secret]);

    $builder = new StickerMessageBuilder (1, 1);
    $response = $bot->pushMessage ($user_id, $builder);
  }

  public function location () {
    $user_id = 'C060c524e90c9f04dbf35d983c2e2c52e';
    $channel_secret = Cfg::setting ('line', 'channel', 'secret');
    $token = Cfg::setting ('line', 'channel', 'token');

    $httpClient = new CurlHTTPClient ($token);
    $bot = new LINEBot ($httpClient, ['channelSecret' => $channel_secret]);

    $builder = new LocationMessageBuilder ('小夏，我在這裡，快來打我呀笨蛋～', '你右邊的後面的前面那位！', 25.075446, 121.573558);
    $response = $bot->pushMessage ($user_id, $builder);
  }
  public function img () {
    $user_id = 'U4a37e32a1d11b3995d2bf299597e432f';
    $channel_secret = Cfg::setting ('line', 'channel', 'secret');
    $token = Cfg::setting ('line', 'channel', 'token');

    $httpClient = new CurlHTTPClient ($token);
    $bot = new LINEBot ($httpClient, ['channelSecret' => $channel_secret]);

    $builder = new ImageMessageBuilder ('https://i.imgur.com/6LNrn1m.gif', 'https://i.imgur.com/6LNrn1m.gif');
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

    if ((substr ($q, 0, 8) == "https://") && in_array (pathinfo ($q, PATHINFO_EXTENSION), array ('jpg', 'gif', 'jpeg', 'png')))
      $builder = new ImageMessageBuilder ($q, $q);
    else
      $builder = new TextMessageBuilder ($q);

    $response = $bot->pushMessage ($user_id, $builder);
  }
}
