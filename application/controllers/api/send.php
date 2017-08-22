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
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;

class Send extends Api_controller {

  public function __construct () {
    parent::__construct ();
    
  }

  /**
   * @api {get} /send/users 取得使用者
   * @apiGroup User
   *
   * @apiSuccess {String}   id          User ID
   * @apiSuccess {String}   title       使用者名稱
   *
   * @apiSuccessExample {json} Success Response:
   *     HTTP/1.1 200 OK
   *     [
   *         {
   *             "id": "U...",
   *             "title": "吳政賢"
   *         }
   *     ]
   *
   * @apiError   {String}    message     錯誤原因
   *
   * @apiErrorExample {json} Error-Response:
   *     HTTP/1.1 405 Error
   *     {
   *         "message": "參數錯誤"
   *     }
   */
  public function users () {
    return $this->output_json (array_map (function ($source) {
      return array ('id' => $source->sid, 'title' => $source->title);
    }, Source::find ('all', array ('select' => 'title, sid', 'conditions' => array ('status = ? AND title != ?', Source::STATUS_JOIN, '')))));
  }

  /**
   * @api {get} /send/sticker/:packageId/:stickerId 傳貼圖
   * @apiGroup Send
   *
   * @apiParam {String}      packageId    package ID
   * @apiParam {String}      stickerId    sticker ID
   *
   * @apiParam {String}      user_id      接收者 User ID
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
    if (!(($source = OAInput::get ('user_id')) && ($source = trim ($source)) && ($source = Source::find ('one', array ('select' => 'sid', 'conditions' => array ('sid = ? AND status = ?', $source, Source::STATUS_JOIN))))))
      return $this->output_error_json ('使用者錯誤');

    if (!(($packageId = trim ($packageId)) && is_numeric ($packageId) && ($stickerId = trim ($stickerId)) && is_numeric ($stickerId)))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new StickerMessageBuilder ((int)$packageId, (int)$stickerId);
    $response = $bot->pushMessage ($source->sid, $builder);

    return $this->output_json (array ('status' => true));
  }

  /**
   * @api {get} /send/location 傳定位
   * @apiGroup Send
   *
   * @apiParam {String}      title       標題，最多 48 個字
   * @apiParam {String}      address     地址，最多 48 個字
   * @apiParam {String}      latitude    緯度
   * @apiParam {String}      longitude   經度
   *
   * @apiParam {String}      user_id      接收者 User ID
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
  public function location () {
    if (!(($source = OAInput::get ('user_id')) && ($source = trim ($source)) && ($source = Source::find ('one', array ('select' => 'sid', 'conditions' => array ('sid = ? AND status = ?', $source, Source::STATUS_JOIN))))))
      return $this->output_error_json ('使用者錯誤');
    
    $title = OAInput::get ('title');
    $address = OAInput::get ('address');
    $latitude = OAInput::get ('latitude');
    $longitude = OAInput::get ('longitude');
    
    if (!(($title = trim ($title)) && ($title = mb_strimwidth ($title, 0, 48 * 2, '…','UTF-8')) && ($address = trim ($address)) && ($address = mb_strimwidth ($address, 0, 48 * 2, '…','UTF-8')) && is_numeric ($latitude = trim ($latitude)) && is_numeric ($longitude = trim ($longitude))))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new LocationMessageBuilder ($title, $address, $latitude, $longitude);
    $response = $bot->pushMessage ($source->sid, $builder);

    return $this->output_json (array ('status' => true));
  }
  /**
   * @api {get} /send/image 傳圖片
   * @apiGroup Send
   *
   * @apiParam {String}      ori       原始圖片網址，需要 Https，網址長度最長 1000
   * @apiParam {String}      prev      預覽圖片網址，需要 Https，網址長度最長 1000
   *
   * @apiParam {String}      user_id      接收者 User ID
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
  public function image () {
    if (!(($source = OAInput::get ('user_id')) && ($source = trim ($source)) && ($source = Source::find ('one', array ('select' => 'sid', 'conditions' => array ('sid = ? AND status = ?', $source, Source::STATUS_JOIN))))))
      return $this->output_error_json ('使用者錯誤');
    
    $ori = OAInput::get ('ori');
    $prev = OAInput::get ('prev');

    if (!(($ori = trim ($ori)) && ($prev = trim ($prev)) && strlen ($ori) <= 1000 && strlen ($prev) <= 1000))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new ImageMessageBuilder ($ori, $prev);
    $response = $bot->pushMessage ($source->sid, $builder);
    return $this->output_json (array ('status' => true));
  }
  /**
   * @api {get} /send/video 傳影片
   * @apiGroup Send
   *
   * @apiParam {String}      ori       影片網址，格式 mp4 檔案，需要 Https，網址長度最長 1000
   * @apiParam {String}      prev      預覽圖片網址，需要 Https，網址長度最長 1000
   *
   * @apiParam {String}      user_id      接收者 User ID
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
  public function video () {
    if (!(($source = OAInput::get ('user_id')) && ($source = trim ($source)) && ($source = Source::find ('one', array ('select' => 'sid', 'conditions' => array ('sid = ? AND status = ?', $source, Source::STATUS_JOIN))))))
      return $this->output_error_json ('使用者錯誤');
    
    $ori = OAInput::get ('ori');
    $prev = OAInput::get ('prev');

    if (!(($ori = trim ($ori)) && ($prev = trim ($prev)) && strlen ($ori) <= 1000 && strlen ($prev) <= 1000))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new VideoMessageBuilder ($ori, $prev);
    $response = $bot->pushMessage ($source->sid, $builder);
    return $this->output_json (array ('status' => true));
  }
  
  /**
   * @api {get} /send/audio 傳語音
   * @apiGroup Send
   *
   * @apiParam {String}      ori       語音網址，格式 m4a 檔案，需要 Https，網址長度最長 1000
   * @apiParam {Number}      duration  語音長度，單位 milliseconds
   *
   * @apiParam {String}      user_id      接收者 User ID
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
  public function audio () {
    if (!(($source = OAInput::get ('user_id')) && ($source = trim ($source)) && ($source = Source::find ('one', array ('select' => 'sid', 'conditions' => array ('sid = ? AND status = ?', $source, Source::STATUS_JOIN))))))
      return $this->output_error_json ('使用者錯誤');
    
    $ori = OAInput::get ('ori');
    $duration = OAInput::get ('duration');

    if (!(($ori = trim ($ori)) && ($duration = trim ($duration)) && strlen ($ori) <= 1000 && is_numeric ($duration)))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new AudioMessageBuilder ($ori, (int)$duration);
    $response = $bot->pushMessage ($source->sid, $builder);
    return $this->output_json (array ('status' => true));
  }
  
  /**
   * @api {get} /send/message 傳文字
   * @apiGroup Send
   *
   * @apiParam {String}      text      文字訊息，長度最長 2000
   *
   * @apiParam {String}      user_id      接收者 User ID
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
  public function message () {
    if (!(($source = OAInput::get ('user_id')) && ($source = trim ($source)) && ($source = Source::find ('one', array ('select' => 'sid', 'conditions' => array ('sid = ? AND status = ?', $source, Source::STATUS_JOIN))))))
      return $this->output_error_json ('使用者錯誤');
    
    if (!(($text = OAInput::get ('text')) && ($text = trim ($text)))) return;
    
    $text = mb_strimwidth ($text, 0, 998 * 2, '…','UTF-8');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new TextMessageBuilder ($text);
    $response = $bot->pushMessage ($source->sid, $builder);
    return $this->output_json (array ('status' => true));
  }
}
