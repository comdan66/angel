<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;

use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;

use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;

use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;


class OALineBot {
  private $bot = null;

  public function __construct ($bot = null) {
    $this->bot = $bot;
  }

  public function bot () { return $this->bot; }

  public static function log ($log = '') {
    if (!$log) return;

    $path = FCPATH . 'temp/input.json';
    write_file ($path, $log . "\n", FOPEN_READ_WRITE_CREATE);
  }

  public static function create () {
    return new OALineBot (new LINEBot (new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token')), array ('channelSecret' => Cfg::setting ('line', 'channel', 'secret'))));
  }

  public function events () {
    if (!isset ($_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE])) return array ();
    
    try {
      $body = file_get_contents ("php://input");
      // OALineBot::log ($body);
      return $this->bot->parseEventRequest ($body, $_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE]);
    } catch (Exception $e) {
      return array ();
    }
  }
}

class OALineBotPush {
  private $bot, $source;
  private $actions = array ();

  public function __construct ($bot, $source) {
    $this->bot = $bot;
    $this->source = $source;
  }
  public static function create ($bot, $source = null) {
    return new OALineBotPush ($bot, $source);
  }
  
  public function setSource ($source) { $this->source = $source; return $this; }
  private function pushMessage ($builder) { if (!$this->source) return false; $response = $this->bot->pushMessage (is_object ($this->source) ? $this->source->sid : $this->source, $builder); return $response->isSucceeded (); }
  private function template ($alt) { return ($alt = trim ($alt)) && ($alt = catStr ($alt, 400)); }
  
  private static function templateAction ($label) { return ($label = trim ($label)) && ($label = catStr ($label, 20)); }
  private static function templatePickerAction ($label, $data) { if (!self::templateAction ($label)) return null; if (!(($data = trim ($data)) && ($data = catStr ($data, 300)))) return false; return true; }

  public function text ($text) {
    if (!(($text = trim ($text)) && ($text = catStr ($text, 2000))))
      return false;

    return $this->pushMessage (new TextMessageBuilder ($text));
  }
  public function sticker ($package_id, $sticker_id) {
    if (!(($package_id = trim ($package_id)) && ($sticker_id = trim ($sticker_id))))
      return false;

    return $this->pushMessage (new StickerMessageBuilder ($package_id, $sticker_id));
  }
  public function image ($ori, $prev) {
    if (!(($ori = trim ($ori)) && isHttps ($ori) && ($prev = trim ($prev)) && isHttps ($prev) && strlen ($ori) <= 1000 && strlen ($prev) <= 1000))
      return false;
    return $this->pushMessage (new ImageMessageBuilder ($ori, $prev));
  }
  public function video ($ori, $prev) {
    if (!(($ori = trim ($ori)) && ($prev = trim ($prev)) && isHttps ($ori) && isHttps ($prev) && strlen ($ori) <= 1000 && strlen ($prev) <= 1000))
      return false;

    return $this->pushMessage (new VideoMessageBuilder ($ori, $prev));
  }
  public function audio ($ori, $duration) {
    if (!(($ori = trim ($ori)) && isHttps ($ori) && ($duration = trim ($duration)) && strlen ($ori) <= 1000 && is_numeric ($duration)))
      return false;

    return $this->pushMessage (new AudioMessageBuilder ($ori, (int)$duration));
  }
  public function location ($title, $address, $latitude, $longitude) {
    if (!(($title = trim ($title)) && ($title = catStr ($title, 100)) && ($address = trim ($address)) && ($address = catStr ($address, 100)) && is_numeric ($latitude = trim ($latitude)) && is_numeric ($longitude = trim ($longitude))))
      return false;

    return $this->pushMessage (new LocationMessageBuilder ($title, $address, $latitude, $longitude));
  }
  
  public function imagemap ($alt, $img, $width, $height, $actions) {
    if (!(($alt = trim ($alt)) && ($alt = catStr ($alt, 400)))) return false;
    if (!(($img = trim ($img)) && isHttps ($img) && (strlen ($img) <= 1000))) return false;
    if (!(($width = trim ($width)) && is_numeric ($width))) return false;
    if (!(($height = trim ($height)) && is_numeric ($height))) return false;
    if (!($actions = array_filter ($actions))) return false;

    return $this->pushMessage (new ImagemapMessageBuilder ($img, $alt, new BaseSizeBuilder ($width, $height), array_slice ($actions, -50)));
  }
  public static function imagemapMessageAction ($x, $y, $width, $height, $text) {
    if (!(is_numeric ($x = trim ($x)))) return false;
    if (!(is_numeric ($y = trim ($y)))) return false;
    if (!(($width = trim ($width)) && is_numeric ($width))) return false;
    if (!(($height = trim ($height)) && is_numeric ($height))) return false;
    if (!(($text = trim ($text)) && ($text = catStr ($text, 400)))) return false;
    return new ImagemapMessageActionBuilder ($text, new AreaBuilder ($x, $y, $width, $height));
  }
  public static function imagemapUriAction ($x, $y, $width, $height, $uri) {
    if (!(is_numeric ($x = trim ($x)))) return false;
    if (!(is_numeric ($y = trim ($y)))) return false;
    if (!(($width = trim ($width)) && is_numeric ($width))) return false;
    if (!(($height = trim ($height)) && is_numeric ($height))) return false;
    if (!(($uri = trim ($uri)) && ($uri = catStr ($uri, 400)))) return false;
    if (!(($uri = trim ($uri)) && (strlen ($uri) <= 1000) && isHttps ($uri))) return false;
    return new ImagemapUriActionBuilder ($uri, new AreaBuilder ($x, $y, $width, $height));
  }

  public function templateButton ($alt, $text, $actions, $img = '', $title = '') {
    $title = ($title = trim ($title)) && ($title = catStr ($title, 40)) ? $title : null;
    $img   = ($img = trim ($img)) && isHttps ($img) && (strlen ($img) <= 1000) ? $img : null;

    if (!($this->template ($alt) && ($text = trim ($text)) && ($text = catStr ($text, $img ? 60 : 160)) && ($actions = array_filter ($actions))))
      return false;

    return $this->pushMessage (new TemplateMessageBuilder ($alt, new ButtonTemplateBuilder ($title, $text, $img, array_slice ($actions, -4))));
  }
  public function templateConfirm ($alt, $text, $actions) {
    if (!($this->template ($alt) && ($text = trim ($text)) && ($text = catStr ($text, 240)) && (count ($actions = array_filter ($actions)) > 1)))
      return false;

    return $this->pushMessage (new TemplateMessageBuilder ($alt, new ConfirmTemplateBuilder ($text, array_slice ($actions, -2))));
  }
  public function templateCarousel ($alt, $columns) {
    // $columns = func_get_args ();
    // if (!$this->template ($alt = array_shift ($columns)))
    if (!$this->template ($alt))
      return false;

    if (!($columns && is_array ($columns) && ($columns = array_filter (array_map (function ($column) { $column['img'] = isset ($column['img']) && ($column['img'] = trim ($column['img'])) && isHttps ($column['img']) && (strlen ($column['img']) <= 1000) ? $column['img'] : null; $column['title'] = isset ($column['title']) && ($column['title'] = trim ($column['title'])) && ($column['title'] = catStr ($column['title'], 40)) ? $column['title'] : null; if (!(isset ($column['text']) && ($column['text'] = trim ($column['text'])) && ($column['text'] = catStr ($column['text'], $column['img'] ? 60 : 120)))) return null; if (!($column['actions'] = array_filter ($column['actions']))) return null; return array ($column['title'], $column['text'], $column['img'], array_slice ($column['actions'], -3)); }, $columns)))))
      return false;
    
    if (!(count (array_unique (array_map ('count', array_map (function ($column) { return end ($column); }, $columns)))) === 1 && count (array_unique (array_map ('count', array_map ('array_filter', $columns)))) === 1)) return false;

    return $this->pushMessage (new TemplateMessageBuilder ($alt, new CarouselTemplateBuilder (array_map (function ($column) { return new CarouselColumnTemplateBuilder ($column[0], $column[1], $column[2], $column[3]); }, array_slice ($columns, -10)))));
  }
  public function templateImageCarousel ($alt, $columns) {
    // $columns = func_get_args ();

    if (!$this->template ($alt))
      return false;

    if (!($columns && is_array ($columns) && ($columns = array_filter (array_map (function ($column) { if (!(count ($column) == 2)) return null; $img = is_string ($column[0]) ? $column[0] : $column[1]; $action = is_string ($column[0]) ? $column[1] : $column[0]; if (!(is_string ($img) && ($img = trim ($img)) && isHttps ($img) && (strlen ($img) <= 1000))) return null; if (!($action && is_object ($action))) return null; return array ($img, $action); }, $columns)))))
      return false;

    return $this->pushMessage (new TemplateMessageBuilder ($alt, new ImageCarouselTemplateBuilder (array_map (function ($column) { return new ImageCarouselColumnTemplateBuilder ($column[0], $column[1]); }, array_slice ($columns, -10)))));
  }
  
  public static function templateUriAction ($label, $uri) {
    if (!self::templateAction ($label)) return null;
    if (!(($uri = trim ($uri)) && (strlen ($uri) <= 1000) && (isHttps ($uri) || isHttp ($uri) || isTel ($uri)))) return null;
    return new UriTemplateActionBuilder ($label, $uri);
  }
  public static function templateMessageAction ($label, $text) {
    if (!self::templateAction ($label)) return null;
    if (!(($text = trim ($text)) && ($text = catStr ($text, 300)))) return null;
    return new MessageTemplateActionBuilder ($label, $text);
  }
  public static function templatePostbackAction ($label, $data, $text = '') {
    if (!self::templateAction ($label)) return null;
    if (!(($data = trim ($data)) && ($data = catStr ($data, 300)))) return null;
    if (!(($text = trim ($text)) && ($text = catStr ($text, 300)))) $text = null;

    return new PostbackTemplateActionBuilder ($label, $data, $text);
  }
  public static function templateDatetimePickerAction ($label, $data, $initial = null, $max = null, $min = null) {
    if (!self::templatePickerAction ($label, $data)) return null;
    if (!(($initial = trim ($initial)) && isDatetimeT ($initial))) $initial = null;
    if (!(($max = trim ($max)) && isDatetimeT ($max) && $max <= '2100-12-31T23:59')) $max = null;
    if (!(($min = trim ($min)) && isDatetimeT ($min) && $min >= '1900-01-01T00:00')) $min = null;

    return new DatetimePickerTemplateActionBuilder ($label, $data, 'datetime', $initial, $max, $min);
  }
  public static function datePickerAction ($label, $data, $initial = null, $max = null, $min = null) {
    if (!self::templatePickerAction ($label, $data)) return null;
    if (!(($initial = trim ($initial)) && isDate ($initial))) $initial = null;
    if (!(($max = trim ($max)) && isDate ($max) && $max <= '2100-12-31')) $max = null;
    if (!(($min = trim ($min)) && isDate ($min) && $min >= '1900-01-01')) $min = null;

    return new DatetimePickerTemplateActionBuilder ($label, $data, 'date', $initial, $max, $min);
  }
  public static function timePickerAction ($label, $data, $initial = null, $max = null, $min = null) {
    if (!self::templatePickerAction ($label, $data)) return null;
    if (!(($initial = trim ($initial)) && isTimeHI ($initial))) $initial = null;
    if (!(($max = trim ($max)) && isTimeHI ($max) && $max <= '23:59')) $max = null;
    if (!(($min = trim ($min)) && isTimeHI ($min) && $min >= '00:00')) $min = null;

    return new DatetimePickerTemplateActionBuilder ($label, $data, 'time', $initial, $max, $min);
  }
}