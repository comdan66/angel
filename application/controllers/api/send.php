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
  private $source = null;

  public function __construct () {
    parent::__construct ();

    if (!((($this->source = $this->input->get_request_header ('Id')) || ($this->source = OAInput::get ('user_id')) || ($this->source = OAInput::post ('user_id'))) && ($this->source = trim ($this->source)) && ($this->source = Source::find ('one', array ('select' => 'sid', 'conditions' => array ('sid = ? AND status = ?', $this->source, Source::STATUS_JOIN))))))
      return $this->disable ($this->output_error_json ('使用者錯誤'));
  }

  /**
   * @api {get} /send/sticker 傳貼圖
   *
   * @apiGroup Message
   *
   * @apiHeader {String}     id      接收者 User ID
   *
   * @apiParam {String}      package_id   package ID
   * @apiParam {String}      sticker_id   sticker ID
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
  public function sticker () {
    if (!(($package_id = OAInput::get ('package_id')) && ($sticker_id = OAInput::get ('sticker_id')) && ($package_id = trim ($package_id)) && ($sticker_id = trim ($sticker_id))))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new StickerMessageBuilder ($package_id, $sticker_id);
    $response = $bot->pushMessage ($this->source->sid, $builder);

    return $this->output_json (array ('status' => true));
  }

  /**
   * @api {get} /send/location 傳定位
   *
   * @apiGroup Message
   *
   * @apiHeader {String}     id     接收者 User ID
   *
   * @apiParam {String}      title       標題，最多 100 個字元，中文一個字算 2 字元
   * @apiParam {String}      address     地址，最多 100 個字元，中文一個字算 2 字元
   * @apiParam {String}      latitude    緯度
   * @apiParam {String}      longitude   經度
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
    if (!(($title = OAInput::get ('title')) && ($address = OAInput::get ('address')) && ($latitude = OAInput::get ('latitude')) && ($longitude = OAInput::get ('longitude')) && ($title = trim ($title)) && ($title = catStr ($title, 100)) && ($address = trim ($address)) && ($address = catStr ($address, 100)) && is_numeric ($latitude = trim ($latitude)) && is_numeric ($longitude = trim ($longitude))))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new LocationMessageBuilder ($title, $address, $latitude, $longitude);
    $response = $bot->pushMessage ($this->source->sid, $builder);

    return $this->output_json (array ('status' => true));
  }
  /**
   * @api {get} /send/image 傳圖片
   *
   * @apiGroup Message
   *
   * @apiHeader {String}     id      接收者 User ID
   *
   * @apiParam {String}      ori          原始圖片網址，需要 Https，網址長度最長 1000
   * @apiParam {String}      prev         預覽圖片網址，需要 Https，網址長度最長 1000
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
    if (!(($ori = OAInput::get ('ori')) && ($prev = OAInput::get ('prev')) && ($ori = trim ($ori)) && isHttps ($ori) && ($prev = trim ($prev)) && isHttps ($prev) && strlen ($ori) <= 1000 && strlen ($prev) <= 1000))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new ImageMessageBuilder ($ori, $prev);
    $response = $bot->pushMessage ($this->source->sid, $builder);
    return $this->output_json (array ('status' => true));
  }
  /**
   * @api {get} /send/video 傳影片
   *
   * @apiGroup Message
   *
   * @apiHeader {String}     id           接收者 User ID
   *
   * @apiParam {String}      ori          影片網址，格式 mp4 檔案，需要 Https，網址長度最長 1000
   * @apiParam {String}      prev         預覽圖片網址，需要 Https，網址長度最長 1000
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
    if (!(($ori = OAInput::get ('ori')) && ($prev = OAInput::get ('prev')) && ($ori = trim ($ori)) && ($prev = trim ($prev)) && isHttps ($ori) && isHttps ($prev) && strlen ($ori) <= 1000 && strlen ($prev) <= 1000))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new VideoMessageBuilder ($ori, $prev);
    $response = $bot->pushMessage ($this->source->sid, $builder);
    return $this->output_json (array ('status' => true));
  }
  
  /**
   * @api {get} /send/audio 傳語音
   *
   * @apiGroup Message
   *
   * @apiHeader {String}     id           接收者 User ID
   *
   * @apiParam {String}      ori          語音網址，格式 m4a 檔案，需要 Https，網址長度最長 1000
   * @apiParam {Number}      duration     語音長度，單位 milliseconds
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
    if (!(($ori = OAInput::get ('ori')) && ($duration = OAInput::get ('duration')) && ($ori = trim ($ori)) && isHttps ($ori) && ($duration = trim ($duration)) && strlen ($ori) <= 1000 && is_numeric ($duration)))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new AudioMessageBuilder ($ori, (int)$duration);
    $response = $bot->pushMessage ($this->source->sid, $builder);
    return $this->output_json (array ('status' => true));
  }
  
  /**
   * @api {get} /send/message 傳文字
   * @apiGroup Message
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
    if (!(($this->source = OAInput::get ('user_id')) && ($this->source = trim ($this->source)) && ($this->source = Source::find ('one', array ('select' => 'sid', 'conditions' => array ('sid = ? AND status = ?', $this->source, Source::STATUS_JOIN))))))
      return $this->output_error_json ('使用者錯誤');
    
    $text = OAInput::get ('text');

    if (!(($text = trim ($text)) && ($text = catStr ($text, 2000))))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new TextMessageBuilder ($text);
    $response = $bot->pushMessage ($this->source->sid, $builder);
    return $this->output_json (array ('status' => true));
  }
  
  /**
   * @api {post} /send/button 傳按鈕
   * @apiGroup Template
   *
   * @apiDescription 可以傳送多組按鈕
   *
   * @apiParam {String}      user_id      接收者 User ID
   * @apiParam {String}      alt          預覽訊息，最多 400 字元，中文一個字算 2 字元
   * @apiParam {String}      [title]      標題訊息，最多 40 字元，中文一個字算 2 字元
   * @apiParam {String}      text         文字訊息，沒有圖片最多 160 字元，有圖片最多 60 字元，中文一個字算 2 字元
   * @apiParam {String}      img          圖片網址，需要 Https，網址長度最長 1000
   *
   * @apiParam {Array}       actions         按鈕，最多 4 個
   * @apiParam {String}      actions.type    按鈕類型，有 uri、postback、message 三種
   * @apiParam {String}      actions.label   按鈕類型，按鈕文字，最多 20 字元，中文一個字算 2 字元
   * @apiParam {String}      actions.uri     按鈕型態為 uri 時所需要的參數，反應的網址，可以接受 http、https、tel 三種協定
   * @apiParam {String}      actions.text    按鈕型態為 postback、message 時所需要的參數，按下者會重複回應此字串，型態為 postback 時為非必要選項
   * @apiParam {String}      actions.data    按鈕型態為 postback 時所需要的參數，用途不知道，還在研究
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
  public function button () {
    if (!(($this->source = OAInput::post ('user_id')) && ($this->source = trim ($this->source)) && ($this->source = Source::find ('one', array ('select' => 'sid', 'conditions' => array ('sid = ? AND status = ?', $this->source, Source::STATUS_JOIN))))))
      return $this->output_error_json ('使用者錯誤');
    
    $alt   = OAInput::post ('alt');;
    $title = ($title = OAInput::post ('title')) && ($title = trim ($title)) && ($title = catStr ($title, 40)) ? $title : null;
    $text  = OAInput::post ('text');;
    $img   = ($img = OAInput::post ('img')) && ($img = trim ($img)) && isHttps ($img) ? $img : null;
  
    if (!$actions = array_slice ($this->_actions (OAInput::post ('actions')), 0, 4))
      return $this->output_error_json ('至少要有一項 Action，或者您的 Actions 都格式錯誤');

    if (!(($alt = trim ($alt)) && ($alt = catStr ($alt, 400)) &&
          ($text = trim ($text)) && ($text = catStr ($text, $img ? 60 : 160))
        ))
      return $this->output_error_json ('參數錯誤');

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

    $builder = new TemplateMessageBuilder ($alt, new ButtonTemplateBuilder ($title, $text, $img, $actions));
    $response = $bot->pushMessage ($this->source->sid, $builder);
    return $this->output_json (array ('status' => true));
  }

  /**
   * @api {post} /send/confirm 傳確認
   * @apiGroup Template
   *
   * @apiDescription 可以傳送選項式的訊息，注意！按鈕，一定要兩個
   *
   * @apiParam {String}      user_id      接收者 User ID
   * @apiParam {String}      alt          預覽訊息，最多 400 字元，中文一個字算 2 字元
   * @apiParam {String}      text         文字訊息，最多 240 字元，中文一個字算 2 字元
   *
   * @apiParam {Array}       actions         按鈕，一定要兩個
   * @apiParam {String}      actions.type    按鈕類型，有 uri、postback、message 三種
   * @apiParam {String}      actions.label   按鈕類型，按鈕文字，最多 20 字元，中文一個字算 2 字元
   * @apiParam {String}      actions.uri     按鈕型態為 uri 時所需要的參數，反應的網址，可以接受 http、https、tel 三種協定
   * @apiParam {String}      actions.text    按鈕型態為 postback、message 時所需要的參數，按下者會重複回應此字串，型態為 postback 時為非必要選項
   * @apiParam {String}      actions.data    按鈕型態為 postback 時所需要的參數，用途不知道，還在研究
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
  public function confirm () {
    if (!(($this->source = OAInput::post ('user_id')) && ($this->source = trim ($this->source)) && ($this->source = Source::find ('one', array ('select' => 'sid', 'conditions' => array ('sid = ? AND status = ?', $this->source, Source::STATUS_JOIN))))))
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
    $response = $bot->pushMessage ($this->source->sid, $builder);
    return $this->output_json (array ('status' => true));
  }

  /**
   * @api {post} /send/carousel 傳卡片
   * @apiGroup Template
   *
   * @apiDescription 可以傳送卡片式的訊息，注意！所有的卡片的 Action 數量要相同
   *
   * @apiParam {String}      user_id      接收者 User ID
   * @apiParam {String}      alt          預覽訊息，最多 400 字元，中文一個字算 2 字元
   *
   * @apiParam {Array}       columns          卡片，最多 5 個
   * @apiParam {String}      [columns.title]  卡片標題，最多 40 字元，中文一個字算 2 字元
   * @apiParam {String}      columns.text     卡片文字訊息，沒有圖片最多 160 字元，有圖片最多 60 字元，中文一個字算 2 字元
   * @apiParam {String}      columns.img      卡片圖片網址，需要 Https，網址長度最長 1000
   *
   * @apiParam {Array}       columns.actions         按鈕，最多三個，所有的卡片的 Action 數量要相同
   * @apiParam {String}      columns.actions.type    按鈕類型，有 uri、postback、message 三種
   * @apiParam {String}      columns.actions.label   按鈕類型，按鈕文字，最多 20 字元，中文一個字算 2 字元
   * @apiParam {String}      columns.actions.uri     按鈕型態為 uri 時所需要的參數，反應的網址，可以接受 http、https、tel 三種協定
   * @apiParam {String}      columns.actions.text    按鈕型態為 postback、message 時所需要的參數，按下者會重複回應此字串，型態為 postback 時為非必要選項
   * @apiParam {String}      columns.actions.data    按鈕型態為 postback 時所需要的參數，用途不知道，還在研究
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
  public function carousel () {
    if (!(($this->source = OAInput::post ('user_id')) && ($this->source = trim ($this->source)) && ($this->source = Source::find ('one', array ('select' => 'sid', 'conditions' => array ('sid = ? AND status = ?', $this->source, Source::STATUS_JOIN))))))
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
    $response = $bot->pushMessage ($this->source->sid, $builder);
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

    return count (array_unique ($cnt_actions)) == 1 ? $columns : array ();
  }
}
