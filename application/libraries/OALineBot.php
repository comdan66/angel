<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

require_once FCPATH . 'vendor/autoload.php';

use LINE\LINEBot;
use LINE\LINEBot\Response;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Constant\Meta;
use LINE\LINEBot\HTTPClient\Curl;
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

use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\VideoMessage;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\Event\MessageEvent\ImageMessage;
use LINE\LINEBot\Event\MessageEvent\AudioMessage;
use LINE\LINEBot\Event\MessageEvent\FileMessage;

use LINE\LINEBot\Event\JoinEvent;
use LINE\LINEBot\Event\LeaveEvent;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\UnfollowEvent;
use LINE\LINEBot\Event\PostbackEvent;


class OALineBot {
  private $bot = null;

  public function __construct ($bot = null) { $this->bot = $bot; }
  public function bot () { return $this->bot; }
  
  public static function log ($log = '') { if ($log && ($log = is_array ($log) ? json_encode ($log) : (is_object ($log) ? serialize ($log) : $log)) && ($path = FCPATH . 'temp/input.json')) write_file ($path, $log . "\n", FOPEN_READ_WRITE_CREATE); }
  public static function create () { return new OALineBot (new LINEBot (new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token')), array ('channelSecret' => Cfg::setting ('line', 'channel', 'secret')))); }
  public static function events () { if (!isset ($_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE])) return array (); try { OALineBot::log ($body = file_get_contents ("php://input")); return OALineBot::create ()->bot ()->parseEventRequest ($body, $_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE]); } catch (Exception $e) { return array (); } }
  public static function createLog ($source, $speaker, $event) {
    if ($event->getType () == 'message') $message_params = array ('source_id' => $source->id, 'speaker_id' => $speaker->id, 'reply_token' => $event->getReplyToken () ? $event->getReplyToken () : '', 'message_id' => $event->getMessageId () ? $event->getMessageId () : '', 'timestamp' => $event->getTimestamp () ? $event->getTimestamp () : '');
    switch (true) {
      case $event instanceof TextMessage:     $params = array_merge ($message_params, array ('text' => $event->getText ())); return LogText::transaction (function () use (&$log, $params) { return verifyCreateOrm ($log = LogText::create (array_intersect_key ($params, LogText::table ()->columns))); }) ? $log : null;
      case $event instanceof ImageMessage:    $params = array_merge ($message_params, array ('file' => '')); return LogImage::transaction (function () use (&$log, $params) { return verifyCreateOrm ($log = LogImage::create (array_intersect_key ($params, LogImage::table ()->columns))) && $log->putFile2S3 (); }) ? $log : null;
      case $event instanceof VideoMessage:    $params = array_merge ($message_params, array ('file' => '')); return LogVideo::transaction (function () use (&$log, $params) { return verifyCreateOrm ($log = LogVideo::create (array_intersect_key ($params, LogVideo::table ()->columns))) && $log->putFile2S3 (); }) ? $log : null;
      case $event instanceof StickerMessage:  $params = array_merge ($message_params, array ('package_id' => $event->getPackageId (), 'sticker_id' => $event->getStickerId ())); return LogSticker::transaction (function () use (&$log, $params) { return verifyCreateOrm ($log = LogSticker::create (array_intersect_key ($params, LogSticker::table ()->columns))); }) ? $log : null;
      case $event instanceof LocationMessage: $params = array_merge ($message_params, array ('title' => $event->getTitle (), 'address' => $event->getAddress (), 'latitude' => $event->getLatitude (), 'longitude' => $event->getLongitude ())); return LogLocation::transaction (function () use (&$log, $params) { return verifyCreateOrm ($log = LogLocation::create (array_intersect_key ($params, LogLocation::table ()->columns))); }) ? $log : null;
      case $event instanceof AudioMessage:    $params = array_merge ($message_params, array ('file' => '')); return LogAudio::transaction (function () use (&$log, $params) { return verifyCreateOrm ($log = LogAudio::create (array_intersect_key ($params, LogAudio::table ()->columns))) && $log->putFile2S3 (); }) ? $log : null;
      case $event instanceof FileMessage:     $params = array_merge ($message_params, array ('name' => $event->getFileName (), 'size' => $event->getFileSize (), 'file' => '')); return LogFile::transaction (function () use (&$log, $params) { return verifyCreateOrm ($log = LogFile::create (array_intersect_key ($params, LogFile::table ()->columns))) && $log->putFile2S3 (); }) ? $log : null;
      case $event instanceof PostbackEvent:   $params = array ('source_id' => $source->id, 'speaker_id' => $speaker->id, 'reply_token' => $event->getReplyToken () ? $event->getReplyToken () : '', 'data' => $event->getPostbackData (), 'params' => $event->getPostbackParams () ? json_encode ($event->getPostbackParams ()) : '', 'timestamp' => $event->getTimestamp () ? $event->getTimestamp () : ''); return LogPostback::transaction (function () use (&$log, $params) { return verifyCreateOrm ($log = LogPostback::create (array_intersect_key ($params, LogPostback::table ()->columns))); }) ? $log : null;
      case $event instanceof FollowEvent:     $params = array ('source_id' => $source->id, 'reply_token' => $event->getReplyToken () ? $event->getReplyToken () : '', 'timestamp' => $event->getTimestamp () ? $event->getTimestamp () : ''); return LogFollow::transaction (function () use (&$log, $params) { return verifyCreateOrm ($log = LogFollow::create (array_intersect_key ($params, LogFollow::table ()->columns))); }) ? $log : null;
      case $event instanceof JoinEvent:       $params = array ('source_id' => $source->id, 'reply_token' => $event->getReplyToken () ? $event->getReplyToken () : '', 'timestamp' => $event->getTimestamp () ? $event->getTimestamp () : ''); return LogJoin::transaction (function () use (&$log, $params) { return verifyCreateOrm ($log = LogJoin::create (array_intersect_key ($params, LogJoin::table ()->columns))); }) ? $log : null;
      case $event instanceof UnfollowEvent:   $params = array ('source_id' => $source->id, 'timestamp' => $event->getTimestamp () ? $event->getTimestamp () : ''); return LogUnfollow::transaction (function () use (&$log, $params) { return verifyCreateOrm ($log = LogUnfollow::create (array_intersect_key ($params, LogUnfollow::table ()->columns))); }) ? $log : null;
      case $event instanceof LeaveEvent:      $params = array ('source_id' => $source->id, 'timestamp' => $event->getTimestamp () ? $event->getTimestamp () : ''); return LogLeaf::transaction (function () use (&$log, $params) { return verifyCreateOrm ($log = LogLeaf::create (array_intersect_key ($params, LogLeaf::table ()->columns))); }) ? $log : null;
    }
  }
}

class OALineBotAction {
  public static function imagemapUri ($x, $y, $width, $height, $uri) { return is_numeric ($x = trim ($x)) && is_numeric ($y = trim ($y)) && ($width = trim ($width)) && is_numeric ($width) && ($height = trim ($height)) && is_numeric ($height) && ($uri = trim ($uri)) && ($uri = catStr ($uri, 400)) && ($uri = trim ($uri)) && (strlen ($uri) <= 1000) && isHttps ($uri) ? new ImagemapUriActionBuilder ($uri, new AreaBuilder ($x, $y, $width, $height)) : null; }
  public static function imagemapMessage ($x, $y, $width, $height, $text) { return is_numeric ($x = trim ($x)) && is_numeric ($y = trim ($y)) && ($width = trim ($width)) && is_numeric ($width) && ($height = trim ($height)) && is_numeric ($height) && ($text = trim ($text)) && ($text = catStr ($text, 400)) ? new ImagemapMessageActionBuilder ($text, new AreaBuilder ($x, $y, $width, $height)) : null; }

  private static function template ($label) { return ($label = trim ($label)) && ($label = catStr ($label, 20)); }
  private static function templatePicker ($label, $data) { return self::template ($label) && ($data = trim ($data)) && ($data = catStr ($data, 300)); }
  
  public static function templateUri ($label, $uri) { return self::template ($label) && ($uri = trim ($uri)) && (strlen ($uri) <= 1000) && (isHttps ($uri) || isHttp ($uri) || isTel ($uri)) ? new UriTemplateActionBuilder ($label, $uri) : null; }
  public static function templateMessage ($label, $text) { return self::template ($label) && ($text = trim ($text)) && ($text = catStr ($text, 300)) ? new MessageTemplateActionBuilder ($label, $text) : null; }
  public static function templatePostback ($label, $data, $text = '') { return self::template ($label) && ($data = is_array ($data) ? json_encode ($data) : $data) && ($data = trim ($data)) && ($data = catStr ($data, 300)) && ((($text = trim ($text)) && ($text = catStr ($text, 300))) || !($text = null)) ? new PostbackTemplateActionBuilder ($label, $data, $text) : null; }
  public static function templateDatetimePicker ($label, $data, $initial = null, $max = null, $min = null) { return self::templatePicker ($label, $data) && ((($initial = trim ($initial)) && isDatetimeT ($initial)) || !($initial = null)) && ((($max = trim ($max)) && isDatetimeT ($max) && ($max <= '2100-12-31T23:59')) || !($max = null)) && ((($min = trim ($min)) && isDatetimeT ($min) && ($min >= '1900-01-01T00:00')) || !($min = null)) ? new DatetimePickerTemplateActionBuilder ($label, $data, 'datetime', $initial, $max, $min) : null; }
  public static function templateDatePicker ($label, $data, $initial = null, $max = null, $min = null) { return self::templatePicker ($label, $data) && ((($initial = trim ($initial)) && isDate ($initial)) || !($initial = null)) && ((($max = trim ($max)) && isDate ($max) && ($max <= '2100-12-31')) || !($max = null)) && ((($min = trim ($min)) && isDate ($min) && ($min >= '1900-01-01')) || !($min = null)) ? new DatetimePickerTemplateActionBuilder ($label, $data, 'date', $initial, $max, $min) : null; }
  public static function templateTimePicker ($label, $data, $initial = null, $max = null, $min = null) { return self::templatePicker ($label, $data) && ((($initial = trim ($initial)) && isTimeHI ($initial)) || !($initial = null)) && ((($max = trim ($max)) && isTimeHI ($max) && ($max <= '23:59')) || !($max = null)) && ((($min = trim ($min)) && isTimeHI ($min) && ($min >= '00:00')) || !($min = null)) ? new DatetimePickerTemplateActionBuilder ($label, $data, 'time', $initial, $max, $min) : null; }

}
class OALineBotMsg {
  private $builder = null;

  public function __construct () { }

  public static function create () { return new OALineBotMsg (); }
  public function push ($source) { return $source && $this->builder ? OALineBot::create ()->bot ()->pushMessage (is_object ($source) && ($source instanceof Source) ? $source->sid : $source, $this->builder)->isSucceeded () : false; }
  public function reply ($reply_token) { return $reply_token && $this->builder ? OALineBot::create ()->bot ()->replyMessage (is_object ($reply_token) && isset ($reply_token->reply_token) ? $reply_token->reply_token : $reply_token, $this->builder) : false; }
  
  public function text ($text) { $this->builder = ($text = trim ($text)) && ($text = catStr ($text, 2000)) ? new TextMessageBuilder ($text) : null; return $this; }
  public function image ($ori, $prev) { $this->builder = ($ori = trim ($ori)) && isHttps ($ori) && ($prev = trim ($prev)) && isHttps ($prev) && (strlen ($ori) <= 1000) && (strlen ($prev) <= 1000) ? new ImageMessageBuilder ($ori, $prev) : null; return $this; }
  public function video ($ori, $prev) { $this->builder = ($ori = trim ($ori)) && ($prev = trim ($prev)) && isHttps ($ori) && isHttps ($prev) && (strlen ($ori) <= 1000) && (strlen ($prev) <= 1000) ? new VideoMessageBuilder ($ori, $prev) : null; return $this; }
  public function sticker ($package_id, $sticker_id) { $this->builder = ($package_id = trim ($package_id)) && ($sticker_id = trim ($sticker_id)) ? new StickerMessageBuilder ($package_id, $sticker_id) : null; return $this; }
  public function location ($title, $address, $latitude, $longitude) { $this->builder = ($title = trim ($title)) && ($title = catStr ($title, 100)) && ($address = trim ($address)) && ($address = catStr ($address, 100)) && is_numeric ($latitude = trim ($latitude)) && is_numeric ($longitude = trim ($longitude)) ? new LocationMessageBuilder ($title, $address, $latitude, $longitude) : null; return $this; }
  public function audio ($ori, $duration) { $this->builder = ($ori = trim ($ori)) && isHttps ($ori) && ($duration = trim ($duration)) && (strlen ($ori) <= 1000) && is_numeric ($duration) ? new AudioMessageBuilder ($ori, (int)$duration) : null; return $this; }
  public function imagemap ($alt, $img, $width, $height, $actions) { $this->builder = ($alt = trim ($alt)) && ($alt = catStr ($alt, 400)) && ($img = trim ($img)) && isHttps ($img) && (strlen ($img) <= 1000) && ($width = trim ($width)) && is_numeric ($width) && ($height = trim ($height)) && is_numeric ($height) && ($actions = array_filter ($actions)) ? new ImagemapMessageBuilder ($img, $alt, new BaseSizeBuilder ($width, $height), array_slice ($actions, -50)) : null; return $this; }

  public function templateButton ($alt, $text, $actions, $img = '', $title = '') { $title = ($title = trim ($title)) && ($title = catStr ($title, 40)) ? $title : null; $img = ($img = trim ($img)) && isHttps ($img) && (strlen ($img) <= 1000) ? $img : null; $this->builder = $this->template ($alt) && ($text = trim ($text)) && ($text = catStr ($text, $img ? 60 : 160)) && ($actions = array_filter ($actions)) ? new TemplateMessageBuilder ($alt, new ButtonTemplateBuilder ($title, $text, $img, array_slice ($actions, -4))) : null; return $this; }
  public function templateConfirm ($alt, $text, $actions) { $this->builder = $this->template ($alt) && ($text = trim ($text)) && ($text = catStr ($text, 240)) && (count ($actions = array_filter ($actions)) > 1) ? new TemplateMessageBuilder ($alt, new ConfirmTemplateBuilder ($text, array_slice ($actions, -2))) : null; return $this; }
  public function templateCarousel ($alt, $columns) { $this->builder = $this->template ($alt) && $columns && is_array ($columns) && ($columns = array_filter (array_map (function ($column) { $column['img'] = isset ($column['img']) && ($column['img'] = trim ($column['img'])) && isHttps ($column['img']) && (strlen ($column['img']) <= 1000) ? $column['img'] : null; $column['title'] = isset ($column['title']) && ($column['title'] = trim ($column['title'])) && ($column['title'] = catStr ($column['title'], 40)) ? $column['title'] : null; return isset ($column['text']) && ($column['text'] = trim ($column['text'])) && ($column['text'] = catStr ($column['text'], $column['img'] ? 60 : 120)) && ($column['actions'] = array_filter ($column['actions'])) ? array ($column['title'], $column['text'], $column['img'], array_slice ($column['actions'], -3)) : null; }, $columns))) && (count (array_unique (array_map ('count', array_map (function ($column) { return end ($column); }, $columns)))) === 1) && (count (array_unique (array_map ('count', array_map ('array_filter', $columns)))) === 1) ? new TemplateMessageBuilder ($alt, new CarouselTemplateBuilder (array_map (function ($column) { return new CarouselColumnTemplateBuilder ($column[0], $column[1], $column[2], $column[3]); }, array_slice ($columns, -10)))) : null; return $this; }
  public function templateImageCarousel ($alt, $columns) { $this->builder = $this->template ($alt) && $columns && is_array ($columns) && ($columns = array_filter (array_map (function ($column) { if (!(count ($column) == 2)) return null; $img = is_string ($column[0]) ? $column[0] : $column[1]; $action = is_string ($column[0]) ? $column[1] : $column[0]; return is_string ($img) && ($img = trim ($img)) && isHttps ($img) && (strlen ($img) <= 1000) && $action && is_object ($action) ? array ($img, $action) : null; }, $columns))) ? new TemplateMessageBuilder ($alt, new ImageCarouselTemplateBuilder (array_map (function ($column) { return new ImageCarouselColumnTemplateBuilder ($column[0], $column[1]); }, array_slice ($columns, -10)))) : null; return $this; }

  private function template ($alt) { return ($alt = trim ($alt)) && ($alt = catStr ($alt, 400)); }
}

class OALineBotRichmenu {
  private static $http = null;
  public static function HTTPClient () { return OALineBotRichmenu::$http === null ? (OALineBotRichmenu::$http = new OALineBotCurlHTTPClient (Cfg::setting ('line', 'channel', 'token'))) : OALineBotRichmenu::$http; }
  public static function getRichmenuList () { return ($response = OALineBotRichmenu::HTTPClient ()->get (LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/richmenu/list')) && $response->isSucceeded () && ($response = $response->getJSONDecodedBody ()) && isset ($response['richmenus']) ? $response['richmenus'] : array (); }
  public static function createRichmenu ($json) { return ($response = OALineBotRichmenu::HTTPClient ()->post (LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/richmenu', $json)) && $response->isSucceeded () && ($response = $response->getJSONDecodedBody ()) && isset ($response['richMenuId']) ? $response['richMenuId'] : array (); }
  public static function uploadRichmenuImage ($richmenuId, $path) { return (isHttp ($path) || isHttps ($path) || (file_exists ($path) && is_readable ($path))) && ($file = file_get_contents ($path)) && ($response = OALineBotRichmenu::HTTPClient ()->post (LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/richmenu/' . $richmenuId . '/content', $file, array ('Content-Type: image/png', 'Content-Length: ' . strlen ($file)))) && $response->isSucceeded (); }
  public static function linkRichmenu2User ($richmenuId, $userId) { return ($response = OALineBotRichmenu::HTTPClient ()->post (LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/user/' . $userId . '/richmenu/' . $richmenuId)) && $response->isSucceeded (); }
  public static function unlinkRichmenuFromUser ($userId) { return ($response = OALineBotRichmenu::HTTPClient ()->delete (LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/user/' . $userId . '/richmenu')) && $response->isSucceeded (); }
  public static function deleteRichmenu ($richmenuId) { return ($response = OALineBotRichmenu::HTTPClient ()->delete (LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/richmenu/' . $richmenuId)) && $response->isSucceeded (); }
  public static function getRichmenuIdOfUser ($userId) { return ($response = OALineBotRichmenu::HTTPClient ()->get (LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/user/' . $userId . '/richmenu')) && $response->isSucceeded () && ($response = $response->getJSONDecodedBody ()) && isset ($response['richMenuId']) ? $response['richMenuId'] : ''; }
  public static function downloadRichmenuImage ($richmenuId, $path) { return ($file = ($response = OALineBotRichmenu::HTTPClient ()->get (LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/richmenu/' . $richmenuId . '/content')) && $response->isSucceeded () && ($response = $response->getRawBody ()) ? $response : null) ? write_file ($path, $file) : false; }
}

class OALineBotCurlHTTPClient {
    /** @var array */
    private $authHeaders;
    /** @var array */
    private $userAgentHeader;

    /**
     * CurlHTTPClient constructor.
     *
     * @param string $channelToken Access token of your channel.
     */
    public function __construct($channelToken)
    {
        $this->authHeaders = [
            "Authorization: Bearer $channelToken",
        ];
        $this->userAgentHeader = [
            'User-Agent: LINE-BotSDK-PHP/' . Meta::VERSION,
        ];
    }

    /**
     * Sends GET request to LINE Messaging API.
     *
     * @param string $url Request URL.
     * @return Response Response of API request.
     */
    public function get($url)
    {
        return $this->sendRequest('GET', $url, [], []);
    }

    /**
     * Sends POST request to LINE Messaging API.
     *
     * @param string $url Request URL.
     * @param array $data Request body.
     * @return Response Response of API request.
     */
    public function post($url, $data = array (), array $headers = array ('Content-Type: application/json; charset=utf-8'))
    {
        return $this->sendRequest('POST', $url, $headers, $data);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $additionalHeader
     * @param array $reqBody
     * @return Response
     * @throws CurlExecutionException
     */
    private function sendRequest($method, $url, array $additionalHeader, $reqBody)
    {
        $curl = new Curl($url);

        $headers = array_merge($this->authHeaders, $this->userAgentHeader, $additionalHeader);

        $options = [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_HEADER => true,
        ];

        if ($method === 'POST') {
            if (empty($reqBody)) {
                // Rel: https://github.com/line/line-bot-sdk-php/issues/35
                $options[CURLOPT_HTTPHEADER][] = 'Content-Length: 0';
            } else {
                $options[CURLOPT_POSTFIELDS] = is_array ($reqBody) ? json_encode($reqBody) : $reqBody;
            }
        }

        $curl->setoptArray($options);

        $result = $curl->exec();

        if ($curl->errno()) {
            throw new CurlExecutionException($curl->error());
        }

        $info = $curl->getinfo();
        $httpStatus = $info['http_code'];

        $responseHeaderSize = $info['header_size'];

        $responseHeaderStr = substr($result, 0, $responseHeaderSize);
        $responseHeaders = [];
        foreach (explode("\r\n", $responseHeaderStr) as $responseHeader) {
            $kv = explode(':', $responseHeader, 2);
            if (count($kv) === 2) {
                $responseHeaders[$kv[0]] = trim($kv[1]);
            }
        }

        $body = substr($result, $responseHeaderSize);

        return new Response($httpStatus, $body, $responseHeaders);
    }
    public function delete($url, $data = array (), array $headers = array ('Content-Type: application/json; charset=utf-8'))
    {
        return $this->sendRequest('DELETE', $url, $headers, $data);
    }
}