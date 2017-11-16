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

  public function x () {

  }
  public function test () {

    $this->load->library ('OAFintech');

    echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    var_dump ($price = OAFintech::getRterJpyInfo ());
    var_dump (round (1 / $price['buy']['rate'], 4));
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

          OALineBotMsg::create ()->templateCarousel ('小添屎功能列表', array (
              array ('text' => '比特幣功能', 'actions' => array (
                  OALineBotAction::templatePostback ('我的比特幣', array ('class' => 'OAPostback', 'method' => 'myBitcoin'), '要看我的比特幣目前幣值！'),
                  OALineBotAction::templatePostback ('比特幣目前概況', array ('class' => 'OAPostback', 'method' => 'searchBitcoinNow'), '我選查詢目前 比特幣'),
                )),
              array ('text' => '日幣功能', 'actions' => array (
                  OALineBotAction::templatePostback ('我的日幣', array ('class' => 'OAPostback', 'method' => 'myJpy'), '要看我的日幣目前幣值！'),
                  OALineBotAction::templatePostback ('日幣目前概況', array ('class' => 'OAPostback', 'method' => 'searchJpyNow'), '我選查詢目前 日幣目前概況'),
                )),
              array ('text' => '圖表', 'actions' => array (
                  OALineBotAction::templatePostback ('比特幣 一日內圖表', array ('class' => 'OAPostback', 'method' => 'viewBitcoinChart', 'params' => array (24)), '我選比特幣 一日內圖表'),
                  OALineBotAction::templatePostback ('比特幣 一週內圖表', array ('class' => 'OAPostback', 'method' => 'viewBitcoinChart', 'params' => array (24 * 7)), '我選比特幣 一週內圖表')
                )),
            ))->reply ($log);

          // OALineBotMsg::create ()->templateButton ('比特幣功能列表', '以下是目前的小添屎比特幣功能！', array (
          //     OALineBotAction::templatePostback ('查詢目前比特幣', array ('class' => 'OAPostback', 'method' => 'bitcoinNow'), '我選查詢目前比特幣'),

          //     OALineBotAction::templatePostback ('比特幣 半天內圖表', array ('class' => 'OAPostback', 'method' => 'bitcoinChart', 'params' => array (12)), '我選比特幣 半天內圖表'),
          //     OALineBotAction::templatePostback ('比特幣 一日內圖表', array ('class' => 'OAPostback', 'method' => 'bitcoinChart', 'params' => array (24)), '我選比特幣 一日內圖表'),
          //     OALineBotAction::templatePostback ('比特幣 一週內圖表', array ('class' => 'OAPostback', 'method' => 'bitcoinChart', 'params' => array (24 * 7)), '我選比特幣 一週內圖表')
          //   ))->reply ($log);

          // OALineBotMsg::create ()->templateCarousel ('小添屎功能列表', array (
          //     array ('text' => '我的幣值', 'actions' => array (
          //         OALineBotAction::templatePostback ('比特幣', array ('class' => 'OAPostback', 'method' => 'myBitcoin'), '要看我的比特幣目前幣值！'),
          //         OALineBotAction::templatePostback ('日幣', array ('class' => 'OAPostback', 'method' => 'myJpy'), '要看我的日幣目前幣值！')
          //       )),
          //     array ('text' => '錢錢匯率', 'actions' => array (
          //         OALineBotAction::templatePostback ('比特幣', array ('class' => 'OAPostback', 'method' => 'searchBitcoinNow'), '我選查詢目前 比特幣'),
          //         OALineBotAction::templatePostback ('日幣', array ('class' => 'OAPostback', 'method' => 'searchJpyNow'), '我選查詢目前 日幣'),
          //       )),
          //     array ('text' => '比特幣走勢', 'actions' => array (
          //         OALineBotAction::templatePostback ('比特幣 一日內圖表', array ('class' => 'OAPostback', 'method' => 'viewBitcoinChart', 'params' => array (24)), '我選比特幣 一日內圖表'),
          //         OALineBotAction::templatePostback ('比特幣 一週內圖表', array ('class' => 'OAPostback', 'method' => 'viewBitcoinChart', 'params' => array (24 * 7)), '我選比特幣 一週內圖表')
          //       )),
          //   ))->reply ($log);
          break;
        
        case 'LogPostback':
          $this->load->library ('OAPostback');
          if (!is_callable (array ($log->getData ('class'), $log->getData ('method'))))
            break;
          
          $params = $log->getData ('params') ? $log->getData ('params') : array ();
          if (in_array ($log->getData ('method'), array ('myBitcoin', 'myJpy')))
            array_push ($params, $speaker);

          forward_static_call_array (array ($log->getData ('class'), $log->getData ('method')), array_merge (array ($source, $log), $params));
          break;

        default:
          break;
      }
    }
  }
}
