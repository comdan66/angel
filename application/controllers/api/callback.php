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
    // $this->load->library ('OALineBot');
    // $img = LogAudio::find_by_id (1);
    // echo $img->file->url ();
    // echo $img->putFile2S3 ();

    // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    // var_dump ();
    // exit ();;
    // echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    // var_dump (contentType2ext ('image/jpeg'));
    // exit ();
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
          $push->text (($say ? $say->title . '：' : '') . $info->text);
          break;

        case 'StickerMessage':
          $push->sticker ($info->package_id, $info->sticker_id);
          break;

        case 'ImageMessage':
            $push->image ($info->file->url (), $info->file->url ('w240'));
          break;
        
        default:
          break;
      }

    }
  }
}
