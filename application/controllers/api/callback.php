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

class Callback extends Api_controller {

  public function __construct () {
    parent::__construct ();
    
  }
  public function test () {
    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump (Log::count ());
    exit ();
  }
  public function index () {
    $path = FCPATH . 'temp/input.json';
    $channel_id = Cfg::setting ('line', 'channel', 'id');
    $channel_secret = Cfg::setting ('line', 'channel', 'secret');
    $token = Cfg::setting ('line', 'channel', 'token');

    if (!isset ($_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE])) {
      write_file ($path, '===> Error, Header Error!' . "\n", FOPEN_READ_WRITE_CREATE);
      exit ();
    }

    $httpClient = new CurlHTTPClient ($token);
    $bot = new LINEBot ($httpClient, ['channelSecret' => $channel_secret]);

    $signature = $_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE];
    $body = file_get_contents ("php://input");

    try {
      $events = $bot->parseEventRequest ($body, $signature);
    } catch (Exception $e) {
      write_file ($path, '===> Error, Events Error! Msg:' . $e->getMessage () . "\n", FOPEN_READ_WRITE_CREATE);
      exit ();
    }

    foreach ($events as $event) {
      $instanceof = '';

      if ($event instanceof TextMessage) $instanceof = 'TextMessage';
      if ($event instanceof LocationMessage) $instanceof = 'LocationMessage';
      
      if ($event instanceof VideoMessage) $instanceof = 'VideoMessage';
      if ($event instanceof StickerMessage) $instanceof = 'StickerMessage';
      if ($event instanceof ImageMessage) $instanceof = 'ImageMessage';
      if ($event instanceof AudioMessage) $instanceof = 'AudioMessage';
      
      $params = array (
          'type' => $event->getType (),
          'instanceof' => $instanceof,
          'reply_token' => $event->getType () == 'unfollow' ? '' : $event->getReplyToken (),
          'source_id' => $event->getEventSourceId (),
          'source_type' => $event->isUserEvent() ? EventSourceType::USER : ($event->isGroupEvent () ? EventSourceType::GROUP : EventSourceType::ROOM),
          'timestamp' => $event->getTimestamp (),
          'message_type' => $event->getType () == 'message' ? $event->getMessageType () : '',
          'message_id' => $event->getType () == 'message' ? $event->getMessageId () : '',
          'status' => LinebotLog::STATUS_INIT,
        );
      if (!LinebotLog::transaction (function () use (&$linebotLog, $params) { return verifyCreateOrm ($linebotLog = LinebotLog::create ( array_intersect_key ($params, LinebotLog::table ()->columns))); })) return false;

      if ($event->getType () != 'message') continue;

      switch ($linebotLog->instanceof) {
        case 'TextMessage':
          $params = array (
              'linebot_log_id' => $linebotLog->id,
              'text' => $event->getText (),
            );
          if (!LinebotLogText::transaction (function () use (&$linebotLogText, $params) { return verifyCreateOrm ($linebotLogText = LinebotLogText::create ( array_intersect_key ($params, LinebotLogText::table ()->columns))); })) return false;
          $linebotLog->setStatus (LinebotLog::STATUS_CONTENT);

          if ($linebotLogText->searchIWantLook ($bot) ||
              $linebotLogText->searchIWantListen ($bot) ||
              $linebotLogText->searchIWantEat ($bot) ||
              $linebotLogText->searchRecommend ($bot) ||
              $linebotLogText->searchDont ($bot) ||
              $linebotLogText->search3Q ($bot) ||
              $linebotLogText->searchSpeechles ($bot) ||
              $linebotLogText->searchNotThing ($bot) ||
              $linebotLogText->searchHaha ($bot) ||
              $linebotLogText->searchBot ($bot) ||
              $linebotLogText->searchHello ($bot) ||
              $linebotLogText->searchName ($bot) ||
              $linebotLogText->searchCallMe ($bot) ||
              $linebotLogText->searchLocation ($bot) ||
              $linebotLogText->searchWeather ($bot) ||
              $linebotLogText->searchTest ($bot) ||
              false)
            echo 'Succeeded!';

          break;
        case 'LocationMessage':
          $params = array (
              'linebot_log_id' => $linebotLog->id,
              'title' => $event->getTitle (),
              'address' => $event->getAddress (),
              'latitude' => $event->getLatitude (),
              'longitude' => $event->getLongitude (),
            );
          if (!LinebotLogLocation::transaction (function () use (&$linebotLogLocation, $params) { return verifyCreateOrm ($linebotLogLocation = LinebotLogLocation::create ( array_intersect_key ($params, LinebotLogLocation::table ()->columns))); })) return false;
          $linebotLog->setStatus (LinebotLog::STATUS_CONTENT);

          if ($linebotLogLocation->searchProducts ($bot))
            echo 'Succeeded!';

          break;
        case 'StickerMessage':
          $params = array (
              'linebot_log_id' => $linebotLog->id,
              'package_id' => $event->getPackageId (),
              'sticker_id' => $event->getStickerId (),
            );
          if (!LinebotLogSticker::transaction (function () use (&$linebotLogSticker, $params) { return verifyCreateOrm ($linebotLogSticker = LinebotLogSticker::create ( array_intersect_key ($params, LinebotLogSticker::table ()->columns))); })) return false;
          $linebotLog->setStatus (LinebotLog::STATUS_CONTENT);
          break;

        case 'VideoMessage': $params = array ('linebot_log_id' => $linebotLog->id,); if (!LinebotLogVideo::transaction (function () use (&$linebotLogText, $params) { return verifyCreateOrm ($linebotLogText = LinebotLogVideo::create ( array_intersect_key ($params, LinebotLogVideo::table ()->columns))); })) return false; $linebotLog->setStatus (LinebotLog::STATUS_CONTENT); break;
        case 'ImageMessage': $params = array ('linebot_log_id' => $linebotLog->id,); if (!LinebotLogImage::transaction (function () use (&$linebotLogText, $params) { return verifyCreateOrm ($linebotLogText = LinebotLogImage::create ( array_intersect_key ($params, LinebotLogImage::table ()->columns))); })) return false; $linebotLog->setStatus (LinebotLog::STATUS_CONTENT); break;
        case 'AudioMessage': $params = array ('linebot_log_id' => $linebotLog->id,); if (!LinebotLogAudio::transaction (function () use (&$linebotLogText, $params) { return verifyCreateOrm ($linebotLogText = LinebotLogAudio::create ( array_intersect_key ($params, LinebotLogAudio::table ()->columns))); })) return false; $linebotLog->setStatus (LinebotLog::STATUS_CONTENT); break;
        default:
          break;
      }
    }
  }

  private function Get_Address_From_Google_Maps ($lat, $lng) {

  $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lng . '&sensor=false&language=zh-TW';

  $data = @file_get_contents($url);
  $jsondata = json_decode($data,true);

  if (!$this->check_status ($jsondata)) return '';

  // $address = array(
  //     'country' => google_getCountry($jsondata),
  //     'province' => google_getProvince($jsondata),
  //     'city' => google_getCity($jsondata),
  //     'street' => google_getStreet($jsondata),
  //     'postal_code' => google_getPostalCode($jsondata),
  //     'country_code' => google_getCountryCode($jsondata),
  //     'formatted_address' => google_getAddress($jsondata),
  // );

  return $this->google_getAddress ($jsondata);
  }
  private function check_status ($jsondata) {
      if ($jsondata["status"] == "OK") return true;
      return false;
  }
  private function google_getAddress ($jsondata) {
      return $jsondata["results"][0]["formatted_address"];
  }
}
