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

  /**
   * @api {get} /send/sticker/:packageId/:stickerId 景點內容
   * @apiGroup Send
   *
   * @apiParam {String}      packageId    package ID
   * @apiParam {String}      stickerId    sticker ID
   *
   * @apiParam {String}      user_id       User ID
   *
   * @apiSuccess {Boolean}   status       執行狀態
   *
   * @apiSuccessExample {json} Success Response:
   *     HTTP/1.1 200 OK
   *     {
   *         "status": true
   *     }
   *
   *
   * @apiError   {String}    message     錯誤原因
   *
   * @apiErrorExample {json} Error-Response:
   *     HTTP/1.1 405 Error
   *     {
   *         "message": "參數錯誤"
   *     }
   */
  public function sticker ($packageId = 0, $stickerId = 0) {
    if (!(($source = OAInput::get ('user_id')) && ($source = trim ($source)) && ($source = Source::find ('one', array ('select' => 'sid', 'conditions' => array ('sid = ? AND status = ?', $source, Source::STATUS_JOIN)))))) {
      echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
      var_dump ($source);
      exit ();
      return $this->output_error_json ('使用者錯誤');
    }

    if (!(($packageId = trim ($packageId)) && is_numeric ($packageId) && ($stickerId = trim ($stickerId)) && is_numeric ($stickerId)))
      return $this->output_error_json ('參數錯誤');

    $channel_secret = Cfg::setting ('line', 'channel', 'secret');
    $token = Cfg::setting ('line', 'channel', 'token');

    $httpClient = new CurlHTTPClient ($token);
    $bot = new LINEBot ($httpClient, ['channelSecret' => $channel_secret]);

    $builder = new StickerMessageBuilder ((int)$packageId, (int)$stickerId);
    $response = $bot->pushMessage ($source->sid, $builder);

    return $this->output_json (array ('status' => true));
  }

  public function location () {
    $user_id = 'U4a37e32a1d11b3995d2bf299597e432f';
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

    $user_id = 'U4a37e32a1d11b3995d2bf299597e432f';
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
