<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */
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

    }
  }
}
