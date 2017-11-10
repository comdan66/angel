<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

use LINE\LINEBot\Event;

require_once FCPATH . 'vendor/autoload.php';

class Callback extends Api_controller {

  public function __construct () {
    parent::__construct ();
    
  }
  public function test () {

  }

  public function v2 () {
    $this->load->library ('OALineBot');

    if (!$oaLineBot = OALineBot::create ())
      exit ();

    foreach ($oaLineBot->events () as $event) {
      if (!$source = Source::findOrCreateSource ($event))
        continue;

      $speaker = Source::findOrCreateSpeaker ($event);

oaLineBot::log ('===>' . ($event instanceof Event\MessageEvent ? '1' : '0'));

// LINE\LINEBot\Event\MessageEvent
// LINE\LINEBot\Event\FollowEvent
// LINE\LINEBot\Event\UnfollowEvent
// LINE\LINEBot\Event\JoinEvent
// LINE\LINEBot\Event\LeaveEvent
// LINE\LINEBot\Event\PostbackEvent
// LINE\LINEBot\Event\BeaconDetectionEvent
// LINE\LINEBot\Event\MessageEvent\TextMessage
// LINE\LINEBot\Event\MessageEvent\ImageMessage
// LINE\LINEBot\Event\MessageEvent\VideoMessage
// LINE\LINEBot\Event\MessageEvent\AudioMessage
// LINE\LINEBot\Event\MessageEvent\FileMessage
// LINE\LINEBot\Event\MessageEvent\LocationMessage
// LINE\LINEBot\Event\MessageEvent\StickerMessage

      // switch ($event->getType ()) {
      //   case '':
      //     # code...
      //     break;
        
      //   default:
      //     # code...
      //     break;
      // }

      // if ($log = Log::create ($event, $source))
      //   continue;

      // if (!(($source = Source::getSource ($event, $say)) && ($log = Log::createAndInfo ($source, $event, $info))))
      //   continue;

      // $push = OALineBotPush::create ($oaLineBot->bot (), $source);

      // switch ($log->instanceof) {
        
      // }
    }
  }
}
