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

    foreach ($oaLineBot->events () as $event) {
      if (!$source = Source::findOrCreate ($event))
        continue;

      if ($log = Log::create ($event, $source))
        continue;

      // if (!(($source = Source::getSource ($event, $say)) && ($log = Log::createAndInfo ($source, $event, $info))))
      //   continue;

      // $push = OALineBotPush::create ($oaLineBot->bot (), $source);

      // switch ($log->instanceof) {
        
      // }
    }
  }
}
