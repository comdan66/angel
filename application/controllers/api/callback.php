<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Callback extends Api_controller {

  public function __construct () {
    parent::__construct ();
  }

  public function test () {
    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump ();
    exit ();;
  }

  public function v2 () {
    $this->load->library ('OALineBot');

    foreach (OALineBot::events () as $event) {
      if (!$source = Source::findOrCreateSource ($event))
        continue;
      
      $speaker = Source::findOrCreateSpeaker ($event);
      
      // if ($source->id != 1)
      //   continue;

      if (!$log = OALineBot::createLog ($source, $speaker, $event))
        continue;

      switch (get_class ($log)) {
        case 'LogText':
          if (!in_array ($log->text, array ('?', '？', '小添屎', '小天使')))
            break;

          OALineBotMsg::create ()->templateButton ('功能列表', '以下是目前的小添屎功能！', array (
              OALineBotAction::templatePostback ('查詢目前比特幣', array ('class' => 'OASearch', 'method' => 'bitcoinNow')),
              OALineBotAction::templatePostback ('比特幣成長圖表', array ('class' => 'OASearch', 'method' => 'bitcoinChart'))
            ))->reply ($log);
          break;
        
        case 'LogPostback':
          $this->load->library ('OASearch');
          if (!is_callable (array ($log->getData ('class'), $log->getData ('method'))))
            break;

          forward_static_call_array (array ($log->getData ('class'), $log->getData ('method')), array_merge (array ($source, $log), $log->getData ('params') ? $log->getData ('params') : array ()));
          break;

        default:
          break;
      }
    }
  }
}
