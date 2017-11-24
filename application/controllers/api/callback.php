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

  public function v2 () {
    $this->load->library ('OALineBot');

    foreach (OALineBot::events () as $event) {
      if (!$source = Source::findOrCreateSource ($event))
        continue;
      
      $speaker = Source::findOrCreateSpeaker ($event);
      
      if (!$log = OALineBot::createLog ($source, $speaker, $event))
        continue;

      switch (get_class ($log)) {
        case 'LogJoin':
            OALineBotMsg::create ()->text ('嗨，你好！有想問我的事情可以打「？」或直接輸入「小添屎」我就會出現囉！')->reply ($log);
            OALineBotMsg::create ()->image ('https://pic.ioa.tw/angel/tip/callme.png', 'https://pic.ioa.tw/angel/tip/callme.png')->push ($source);
            break;

        case 'LogLocation':
            OALineBotMsg::create ()->templateConfirm ('確認位置', '請問您要找「' . $log->address . '」附近的店家嗎？', array (
                OALineBotAction::templatePostback ('不是', array ('class' => 'OAPostbackAlley', 'method' => '_')),
                OALineBotAction::templatePostback ('是的', array ('class' => 'OAPostbackAlley', 'method' => 'location', 'params' => array ($log->latitude, $log->longitude))),
              ))->reply ($log);
            break;

        case 'LogFollow':
            $source->linkRichmenu (Richmenu::find_by_id (3));
            OALineBotMsg::create ()->text ('嗨，你好！有想問我的事情可以打「？」或直接輸入「小添屎」我就會出現囉！')->reply ($log);
            OALineBotMsg::create ()->image ('https://pic.ioa.tw/angel/tip/callme.png', 'https://pic.ioa.tw/angel/tip/callme.png')->push ($source);
            
            break;

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
              array ('text' => '美食', 'actions' => array (
                  OALineBotAction::templatePostback ('巷弄推薦美食', array ('class' => 'OAPostback', 'method' => 'alleyFood'), '我想知道巷弄推薦美食'),
                  OALineBotAction::templatePostback ('巷弄隨機美食', array ('class' => 'OAPostback', 'method' => 'alleyFood'), '要想知道巷弄隨機美食'),
                )),
            ))->reply ($log);

          break;
        
        case 'LogPostback':
          $this->load->library ('OAPostback');
          $this->load->library ('OAPostbackAlley');

          if (!is_callable (array ($log->getData ('class'), $log->getData ('method'))))
            break;
          
          $params = $log->getData ('params') ? $log->getData ('params') : array ();
          
          if (in_array ($log->getData ('class'), array ('OAPostback')) && in_array ($log->getData ('method'), array ('myBitcoin', 'myJpy')))
            array_push ($params, $speaker);

          forward_static_call_array (array ($log->getData ('class'), $log->getData ('method')), array_merge (array ($source, $log), $params));
          break;

        default:
          break;
      }
    }
  }
}
