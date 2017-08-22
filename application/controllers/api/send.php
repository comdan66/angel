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
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;

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
   * @apiParam {String}      title       標題，最多 100 個字元，中文一個字算 2 字元
   * @apiParam {String}      address     地址，最多 100 個字元，中文一個字算 2 字元
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
    
    if (!(($title = trim ($title)) && ($title = catStr ($title, 100)) && ($address = trim ($address)) && ($address = catStr ($address, 100)) && is_numeric ($latitude = trim ($latitude)) && is_numeric ($longitude = trim ($longitude))))
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

    if (!(($ori = trim ($ori)) && isHttps ($ori) && ($prev = trim ($prev)) && isHttps ($prev) && strlen ($ori) <= 1000 && strlen ($prev) <= 1000))
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

    if (!(($ori = trim ($ori)) && ($prev = trim ($prev)) && isHttps ($ori) && isHttps ($prev) && strlen ($ori) <= 1000 && strlen ($prev) <= 1000))
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

    if (!(($ori = trim ($ori)) && isHttps ($ori) && ($duration = trim ($duration)) && strlen ($ori) <= 1000 && is_numeric ($duration)))
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
   * @apiParam {String}      text         文字訊息，最多 2000 字元，中文一個字算 2 字元
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
    
    $text = OAInput::get ('text');

    if (!(($text = trim ($text)) && ($text = catStr ($text, 2000))))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new TextMessageBuilder ($text);
    $response = $bot->pushMessage ($source->sid, $builder);
    return $this->output_json (array ('status' => true));
  }


  private function _actions ($actions = array ()) {
    return $actions && is_array ($actions) && ($actions = array_filter (array_map (function ($action) {
      if (!(isset ($action['type']) && in_array ($action['type'], array ('uri', 'message', 'postback')))) return null;
      if (!(isset ($action['label']) && ($action['label'] = trim ($action['label'])) && ($action['label'] = catStr ($action['label'], 20)))) return null;

      switch ($action['type']) {
        case 'uri':
          if (!(isset ($action['uri']) && ($action['uri'] = trim ($action['uri'])) && (strlen ($action['uri']) <= 1000) && (isHttps ($action['uri']) || isHttp ($action['uri']) || isTel ($action['uri'])))) return null;
          break;

        case 'postback':
          if (!(isset ($action['data']) && ($action['data'] = trim ($action['data'])) && ($action['data'] = catStr ($action['data'], 300)))) return null;

        case 'message':
          if (!(isset ($action['text']) && ($action['text'] = trim ($action['text'])) && ($action['text'] = catStr ($action['text'], 300)))) return null;
          break;
      }

      switch ($action['type']) {
        case 'uri':
          return new UriTemplateActionBuilder ($action['label'], $action['uri']);
          break;

        case 'message':
          return new MessageTemplateActionBuilder ($action['label'], $action['text']);
          break;

        case 'postback':
          return new PostbackTemplateActionBuilder ($action['label'], $action['data'], $action['text']);
          break;
      }

      return null;
    }, $actions))) ? $actions : array ();
  }
  private function _columns ($columns = array ()) {
    $cnt_actions = array ();

    $columns = array_slice ($columns && is_array ($columns) && ($columns = array_filter (array_map (function ($column) use (&$cnt_actions) {
      $column['img'] = isset ($column['img']) && ($column['img'] = trim ($column['img'])) && isHttps ($column['img']) ? $column['img'] : null;
      $column['title'] = isset ($column['title']) && ($column['title'] = trim ($column['title'])) && catStr ($column['title'], 40) ? $column['title'] : null;
      
      if (!(isset ($column['text']) && ($column['text'] = trim ($column['text'])) && catStr ($column['text'], $column['img'] ? 60 : 120)))
        return null;

      if (!($column['actions'] = isset ($column['actions']) ? $this->_actions ($column['actions']) : array ()))
        return null;

      array_push ($cnt_actions, count ($column['actions']));

      return new CarouselColumnTemplateBuilder ($column['title'], $column['text'], $column['img'], $column['actions']);
    }, $columns))) ? $columns : array (), 0, 5);

    return array_unique ($cnt_actions) == 1 ? $columns : array ();
  }
  public function button () {
    if (!(($source = OAInput::post ('user_id')) && ($source = trim ($source)) && ($source = Source::find ('one', array ('select' => 'sid', 'conditions' => array ('sid = ? AND status = ?', $source, Source::STATUS_JOIN))))))
      return $this->output_error_json ('使用者錯誤');
    
    $alt   = OAInput::post ('alt');;
    $title = OAInput::post ('title');;
    $text  = OAInput::post ('text');;
    $img   = ($img = OAInput::post ('img')) && ($img = trim ($img)) && isHttps ($img) ? $img : null;
  
    if (!$actions = $this->_actions (OAInput::post ('actions')))
      return $this->output_error_json ('至少要有一項 Action，或者您的 Actions 都格式錯誤');

    if (!(($alt = trim ($alt)) && ($alt = catStr ($alt, 400)) &&
          ($title = trim ($title)) && ($title = catStr ($title, 40)) &&
          ($text = trim ($text)) && ($text = catStr ($text, $img ? 60 : 160))
        ))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new TemplateMessageBuilder ($alt, new ButtonTemplateBuilder ($title, $text, $img, $actions));
    $response = $bot->pushMessage ($source->sid, $builder);
    return $this->output_json (array ('status' => true));
  }
  public function confirm () {
    if (!(($source = OAInput::post ('user_id')) && ($source = trim ($source)) && ($source = Source::find ('one', array ('select' => 'sid', 'conditions' => array ('sid = ? AND status = ?', $source, Source::STATUS_JOIN))))))
      return $this->output_error_json ('使用者錯誤');
    
    $alt   = OAInput::post ('alt');;
    $text = OAInput::post ('text');;
  
    if (count ($actions = $this->_actions (OAInput::post ('actions'))) == 2)
      return $this->output_error_json ('要有兩個 Action，或者您的 Actions 都格式錯誤');

    if (!(($alt = trim ($alt)) && ($alt = catStr ($alt, 400)) &&
          ($text = trim ($text)) && ($text = catStr ($text, 240))
        ))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new TemplateMessageBuilder ($alt, new ConfirmTemplateBuilder ($text, $actions));
    $response = $bot->pushMessage ($source->sid, $builder);
    return $this->output_json (array ('status' => true));
  }
  public function carousel () {
    if (!(($source = OAInput::post ('user_id')) && ($source = trim ($source)) && ($source = Source::find ('one', array ('select' => 'sid', 'conditions' => array ('sid = ? AND status = ?', $source, Source::STATUS_JOIN))))))
      return $this->output_error_json ('使用者錯誤');
    
    $alt   = OAInput::post ('alt');;
  
    if (!$columns = $this->_columns (OAInput::post ('columns')))
      return $this->output_error_json ('項目至少要一個，或項目全部格式錯誤');
    

    if (!(($alt = trim ($alt)) && ($alt = catStr ($alt, 400))
        ))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new TemplateMessageBuilder ($alt, new CarouselTemplateBuilder ($columns));
    $response = $bot->pushMessage ($source->sid, $builder);
    return $this->output_json (array ('status' => true));
  }
}
