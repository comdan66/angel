<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */
require_once FCPATH . 'vendor/autoload.php';

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Constant\HTTPHeader;

class Callback extends Api_controller {

  public function __construct () {
    parent::__construct ();
    
  }
  public function test () {
    // $p = '機器人|(Bot)*';
    // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    // var_dump (LogText::regex ($p, '哈'));
    // exit ();;
    $file = LogImage::find_by_id (1);
    echo $file->saveImage ();
  }
  public function v2 () {
    $this->load->library ('OALineBot');

    if (!$oaLineBot = OALineBot::create ())
      exit ();

    // MsgText::push ()

    foreach ($oaLineBot->events () as $event) {
      // Get Set Source
      if (!$source = Source::getSource ($event, $say))
        continue;

      // Get Set Log And Event
      if (!$log = Log::createAndInfo ($source, $event, $info))
        continue;
      
      // if ($source->sid != 'U4a37e32a1d11b3995d2bf299597e432f')
      //   continue;
      $push = OALineBotPush::create ($oaLineBot->bot (), $source, $log);

      switch ($log->instanceof) {
        case 'TextMessage':
          $push->text (($say ? $say->title . ' ' : '') . $info->text);
          break;

        case 'StickerMessage':
        OALineBot::log ('=========='. $info->package_id .'='. $info->sticker_id);
          $push->sticker ($info->package_id, $info->sticker_id);
          break;
        
        default:
          break;
      }

      // $push = OALineBotPush::create ($oaLineBot->bot (), $source, $log);
      // $push->image ('https://i.imgur.com/n6CdmU0.jpg', 'https://i.imgur.com/n6CdmU0.jpg');

  // $push->templateImageCarousel ('123', array (
  //   'https://i.imgur.com/6LNrn1m.gif', 
  //   OALineBotPush::templateDatetimePickerAction ('Datetime', 'https://www.google.com'),
  // ), array (
  //   OALineBotPush::templateUriAction ('A', 'https://www.google.com'),
  //   'https://i.imgur.com/6LNrn1m.gif', 
  // ));



// $push->imagemap ('今晚你選誰？', 'https://angel.ioa.tw/res/image/t5', 1040, 1040, array (
//       OALineBotPush::imagemapUriAction (0, 0, 520, 520, 'https://www.google.com'),
//       OALineBotPush::imagemapUriAction (520, 0, 520, 520, 'https://www.google.com'),
//       OALineBotPush::imagemapUriAction (0, 520, 520, 520, 'https://www.google.com'),
//       OALineBotPush::imagemapUriAction (520, 520, 520, 520, 'https://www.google.com'),
//   ));

    }
  }

  public function index () {
    $path = FCPATH . 'temp/input.json';

    if (!isset ($_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE])) {
      write_file ($path, '===> Error, Header Error!' . "\n", FOPEN_READ_WRITE_CREATE);
      exit ();
    }

    $httpClient = new CurlHTTPClient (Cfg::setting ('line', 'channel', 'token'));
    $bot = new LINEBot ($httpClient, ['channelSecret' => Cfg::setting ('line', 'channel', 'secret')]);

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
      if ($event instanceof JoinEvent) $instanceof = 'JoinEvent';
      if ($event instanceof LeaveEvent) $instanceof = 'LeaveEvent';

      if (!($sid = $event->getEventSourceId ()))
        continue;

      $status = ($event->getType () == 'leave' || $event->getType () == 'unfollow') ? Source::STATUS_LEAVE : Source::STATUS_JOIN;

      if (!$source = Source::find ('one', array ('select' => 'id, sid, title, status, type', 'conditions' => array ('sid = ?', $sid)))) {
        $params = array (
          'type' => $event->isUserEvent() ? Source::TYPE_USER : ($event->isGroupEvent () ? Source::TYPE_GROUP : ($even->isRoomEvent () ? Source::TYPE_ROOM : Source::TYPE_OTHER)),
          'sid' => $sid,
          'memo' => '',
          'status' => $status,
        );
        if (!Source::transaction (function () use (&$source, $params) { return verifyCreateOrm ($source = Source::create (array_intersect_key ($params, Source::table ()->columns))); })) continue;
      }
      if ($source->status != $status && ($source->status = $status))
        $source->save ();

      if (!$source->title && $source->type == Source::TYPE_USER) {
        $source->updateTitle ();
      }

      $params = array (
          'source_id' => $source->id,
          'type' => $event->getType (),
          'instanceof' => $instanceof,
          'reply_token' => $event->getType () == 'unfollow' || !$event->getReplyToken () ? '' : $event->getReplyToken (),
          'timestamp' => $event->getTimestamp (),
          'message_type' => $event->getType () == 'message' ? $event->getMessageType () : '',
          'message_id' => $event->getType () == 'message' ? $event->getMessageId () : '',
          'status' => Log::STATUS_INIT,
        );

      if (!Log::transaction (function () use (&$log, $params) { return verifyCreateOrm ($log = Log::create (array_intersect_key ($params, Log::table ()->columns))); })) continue;

      if ($event->getType () != 'message') continue;

      switch ($log->instanceof) {
        case 'TextMessage':
          $params = array (
              'log_id' => $log->id,
              'text' => $event->getText (),
            );
          if (!LogText::transaction (function () use (&$logText, $params) { return verifyCreateOrm ($logText = LogText::create (array_intersect_key ($params, LogText::table ()->columns))); })) return false;
          $log->setStatus (Log::STATUS_CONTENT);

          // if (!in_array ($log->source_id, array (
          //   'U4a37e32a1d11b3995d2bf299597e432f',
          //   'C060c524e90c9f04dbf35d983c2e2c52e'))) return false;

          if ($logText->searchLocation ($bot) ||
              $logText->searchWeather ($bot) ||
              $logText->compare ($bot) ||
              false)
            echo 'Succeeded!';

          break;
        case 'LocationMessage':
          $params = array (
              'log_id' => $log->id,
              'title' => $event->getTitle (),
              'address' => $event->getAddress (),
              'latitude' => $event->getLatitude (),
              'longitude' => $event->getLongitude (),
            );
          if (!LogLocation::transaction (function () use (&$logLocation, $params) { return verifyCreateOrm ($logLocation = LogLocation::create (array_intersect_key ($params, LogLocation::table ()->columns))); })) return false;
          $log->setStatus (Log::STATUS_CONTENT);

          if ($logLocation->searchProducts ($bot))
            echo 'Succeeded!';

          break;
        case 'StickerMessage':
          $params = array (
              'log_id' => $log->id,
              'package_id' => $event->getPackageId (),
              'sticker_id' => $event->getStickerId (),
            );
          if (!LogSticker::transaction (function () use (&$logSticker, $params) { return verifyCreateOrm ($logSticker = LogSticker::create (array_intersect_key ($params, LogSticker::table ()->columns))); })) return false;
          $log->setStatus (Log::STATUS_CONTENT);
          break;

        case 'VideoMessage': $params = array ('log_id' => $log->id,); if (!LogVideo::transaction (function () use (&$logText, $params) { return verifyCreateOrm ($logText = LogVideo::create (array_intersect_key ($params, LogVideo::table ()->columns))); })) return false; $log->setStatus (Log::STATUS_CONTENT); break;
        case 'ImageMessage': $params = array ('log_id' => $log->id,); if (!LogImage::transaction (function () use (&$logText, $params) { return verifyCreateOrm ($logText = LogImage::create (array_intersect_key ($params, LogImage::table ()->columns))); })) return false; $log->setStatus (Log::STATUS_CONTENT); break;
        case 'AudioMessage': $params = array ('log_id' => $log->id,); if (!LogAudio::transaction (function () use (&$logText, $params) { return verifyCreateOrm ($logText = LogAudio::create (array_intersect_key ($params, LogAudio::table ()->columns))); })) return false; $log->setStatus (Log::STATUS_CONTENT); break;
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

// $push->imagemap ('今晚你選誰？', 'https://angel.ioa.tw/res/image/t5', 1040, 1040, array (
//       OALineBotPush::imagemapUriAction (0, 0, 520, 520, 'https://www.google.com'),
//   ));

// $push->templateImageCarousel ('123', array (
//     'https://i.imgur.com/6LNrn1m.gif', 
//     OALineBotPush::templateUriAction ('Datetime', 'https://www.google.com'),
//   ), array (
//     OALineBotPush::templateUriAction ('A', 'https://www.google.com'),
//     'https://i.imgur.com/6LNrn1m.gif', 
//   ));



// $push->templateCarousel ('123',
//     array (
//       'text' => '456', 
//       'img' => 'https://i.imgur.com/6LNrn1m.gif',
//       'actions' => array (
//       OALineBotPush::uriAction ('Datetime', 'https://www.google.com'),
//       OALineBotPush::uriAction ('Datetime2', 'https://www.google.com')
//     )),
//     array (
//       'text' => '789', 
//       'img' => 'https://i.imgur.com/6LNrn1m.gif',
//       'actions' => array (
//       OALineBotPush::uriAction ('Datetime', 'https://www.google.com'),
//       OALineBotPush::messageAction ('xx', 'https://www.google.com')
//     ))
//   );
//->addUriAction ('Datetime', 'https://www.google.com')
     // ->addUriAction ('Datetime', 'https://www.google.com')
     

// $push->templateButton ('123', '324', array (
//     array ('type' => 'uri', 'uri' => 'https://www.google.com/', 'label' => '123')
//   ), 'dd');