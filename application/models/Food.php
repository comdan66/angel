<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Food extends OaModel {

  static $table_name = 'foods';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  const SCORE_1 = 1;
  const SCORE_2 = 2;
  const SCORE_3 = 3;


  static $scoreNames = array (
    self::SCORE_1 => '難吃',
    self::SCORE_2 => '普通',
    self::SCORE_3 => '好吃',
  );

  const STEP_1 = 1;
  const STEP_2 = 2;
  const STEP_3 = 3;
  const STEP_4 = 4;
  const STEP_5 = 5;
  const STEP_6 = 6;
  const STEP_7 = 7;
  const STEP_8 = 8;
  const STEP_9 = 9;
  const STEP_10 = 10;
  const STEP_FINISH = Food::STEP_10;

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmImageUploader::bind ('cover', 'FoodCoverImageUploader');
  }
  public function staticmap () {
    return staticmap ($this->latitude, $this->longitude);
  }
  public function push ($push, $source, $info, $say, $ignore = false) {
    switch ($this->step) {
      case Food::STEP_1:
        $push->text ('好的，我不會儲存的！😁');
        break;

      case Food::STEP_2:
        $push->templateButton ('偵測到疑似美食！', '請問你 PO 的這張是美食照片嗎？', array (
            OALineBotPush::templatePostbackAction ('這是美食', json_encode (array ('type' => 'newFood', 'id' => $this->id, 'step' => Food::STEP_3)), $source->isManyUser () ? '我選這是美食' : ''),
            OALineBotPush::templatePostbackAction ('不是美食', json_encode (array ('type' => 'newFood', 'id' => $this->id, 'step' => Food::STEP_1)), $source->isManyUser () ? '我選不是美食' : ''),
          ), $this->cover->url ('w240'));
        break;
      

      case Food::STEP_3:
        $push->templateConfirm ('儲存美食', '是否儲存？', array (
          OALineBotPush::templatePostbackAction ('不用', json_encode (array ('type' => 'newFood', 'id' => $this->id, 'step' => Food::STEP_4)), $source->isManyUser () ? '我選不用' : ''),
          OALineBotPush::templatePostbackAction ('儲存', json_encode (array ('type' => 'newFood', 'id' => $this->id, 'step' => Food::STEP_5)), $source->isManyUser () ? '' : ''),
        ));
        break;
      
      case Food::STEP_4:
        $push->text ('了解，下次我會加強偵測能力的！😁');
        break;
      
      case Food::STEP_5:
        $push->text ('那請告訴我這道美食的名稱吧！');
        break;

      case Food::STEP_6:
        $push->templateConfirm ('確認名稱', '他叫做「' . $info->text . '」?', array (
          OALineBotPush::templatePostbackAction ('重填名稱', json_encode (array ('type' => 'newFood', 'id' => $this->id, 'step' => Food::STEP_5, 'title' => $info->text)), $source->isManyUser () ? '我選重填' : ''),
          OALineBotPush::templatePostbackAction ('名稱沒錯', json_encode (array ('type' => 'newFood', 'id' => $this->id, 'step' => Food::STEP_7, 'title' => $info->text)), $source->isManyUser () ? '我選沒錯' : ''),
          // OALineBotPush::templatePostbackAction ('略過填寫', json_encode (array ('type' => 'newFood', 'id' => $this->id, 'step' => Food::STEP_7, 'title' => '')), $source->isManyUser () ? '我選略過' : ''),
        ));
        break;

      case Food::STEP_7:
        $push->text ("這個美食地點在哪？");
        $push->text ("請點開下方的 ➕，然後選擇位置資訊，來定位這張美食位置！");
        $push->image ('https://pic.ioa.tw/angel/tip/location.png', 'https://pic.ioa.tw/angel/tip/location.png');
        break;

      case Food::STEP_8:
        $push->templateButton ('美食評分', '請問你覺得這份美食好吃嗎？', array (
          OALineBotPush::templatePostbackAction ('好吃 ★★★★★', json_encode (array ('type' => 'newFood', 'id' => $this->id, 'score' => Food::SCORE_3, 'step' => Food::STEP_9)), $source->isManyUser () ? '我覺得好吃！' : ''),
          OALineBotPush::templatePostbackAction ('普通 ★★★☆☆', json_encode (array ('type' => 'newFood', 'id' => $this->id, 'score' => Food::SCORE_2, 'step' => Food::STEP_9)), $source->isManyUser () ? '我覺得普通。' : ''),
          OALineBotPush::templatePostbackAction ('難吃 ★☆☆☆☆', json_encode (array ('type' => 'newFood', 'id' => $this->id, 'score' => Food::SCORE_1, 'step' => Food::STEP_9)), $source->isManyUser () ? '我覺得難吃..' : ''),
        ));
        break;

      case Food::STEP_9:
        $push->templateConfirm ('選擇時間', '請問你哪時候吃的？', array (
          OALineBotPush::templateDatetimePickerAction ('選擇時間', json_encode (array ('type' => 'newFood', 'id' => $this->id, 'step' => Food::STEP_10))),
          OALineBotPush::templatePostbackAction ('就是今天',       json_encode (array ('type' => 'newFood', 'id' => $this->id, 'step' => Food::STEP_10)), $source->isManyUser () ? '就是今天' : ''),
        ));
        break;

      case Food::STEP_10:
        $push->text ('已經幫你儲存囉！😊');
        break;
    }
  }
  public static function newFood ($data, $push, $source, $info, $say) {
    if (!($data['id'] && ($food = Food::find ('one', array ('conditions' => array ('id = ?', $data['id'])))))) return;
    if (in_array ($food->step, array (Food::STEP_1, Food::STEP_4, Food::STEP_FINISH))) return;
    if ($food->say_id != $say->id) { $push->text ('ㄟ！這不是你的美食，別亂點！😠'); return ; }

    switch ($data['step']) {
      case Food::STEP_1:
        if (!($food->step == Food::STEP_2)) { if ($food->step != $data['step']) $push->text ('疑？這任務是不是步驟錯了？🤔'); $food->push ($push, $source, $info, $say); return ; }
        
        $food->step = $data['step'];
        if (Food::transaction (function () use ($food) { return $food->save (); })) $food->push ($push, $source, $info, $say);
        else $push->text ('哭哭，資料庫處理錯誤惹.. 😢');        
        break;

      case Food::STEP_3:
        if (!($food->step == Food::STEP_2)) { if ($food->step != $data['step']) $push->text ('疑？這任務是不是步驟錯了？🤔'); $food->push ($push, $source, $info, $say); return ; }

        $food->step = $data['step'];
        if (Food::transaction (function () use ($food) { return $food->save (); })) $food->push ($push, $source, $info, $say);
        else $push->text ('哭哭，資料庫處理錯誤惹.. 😢');        
        break;
      
      case Food::STEP_4:
        if (!($food->step == Food::STEP_3)) { if ($food->step != $data['step']) $push->text ('疑？這任務是不是步驟錯了？🤔'); return ; }
        
        $food->step = $data['step'];
        if (Food::transaction (function () use ($food) { return $food->save (); })) $food->push ($push, $source, $info, $say);
        else $push->text ('哭哭，資料庫處理錯誤惹.. 😢');        
        break;
      
      case Food::STEP_5:
        if (!($food->step == Food::STEP_3 || $food->step == Food::STEP_6)) { if ($food->step != $data['step']) $push->text ('疑？這任務是不是步驟錯了？🤔'); $food->push ($push, $source, $info, $say); return ; }
        $food->step = $data['step'];
        if (Food::transaction (function () use ($food) { return $food->save (); })) $food->push ($push, $source, $info, $say);
        else $push->text ('哭哭，資料庫處理錯誤惹.. 😢');        
        break;
      
      case Food::STEP_6:
        if (!($food->step == Food::STEP_5)) { if ($food->step != $data['step']) $push->text ('疑？這任務是不是步驟錯了？🤔'); $food->push ($push, $source, $info, $say); return ; }
        $food->step = $data['step'];
        if (Food::transaction (function () use ($food) { return $food->save (); })) $food->push ($push, $source, $info, $say);
        else $push->text ('哭哭，資料庫處理錯誤惹.. 😢');        
        break;
      
      case Food::STEP_7:
        if (!($food->step == Food::STEP_6)) { if ($food->step != $data['step']) $push->text ('疑？這任務是不是步驟錯了？🤔'); $food->push ($push, $source, $info, $say); return ; }
        $food->step = $data['step'];
        $food->title = isset ($data['title']) ? $data['title'] : '';
        if (Food::transaction (function () use ($food) { return $food->save (); })) $food->push ($push, $source, $info, $say);
        else $push->text ('哭哭，資料庫處理錯誤惹.. 😢');        
        break;

      case Food::STEP_8:
        if (!($food->step == Food::STEP_7)) { if ($food->step != $data['step']) $push->text ('疑？這任務是不是步驟錯了？🤔'); $food->push ($push, $source, $info, $say); return ; }
        $CI =& get_instance ();
        $CI->load->library ('OAGoogleMapsTool');
        
        $food->step = $data['step'];
        $food->latitude = $info->latitude;
        $food->longitude = $info->longitude;
        $food->address = ($food->address = OAGoogleMapsTool::getAddress ($info->latitude, $info->longitude)) ? $food->address : $info->address;
        if (Food::transaction (function () use ($food) { return $food->save (); })) $food->push ($push, $source, $info, $say);
        else $push->text ('哭哭，資料庫處理錯誤惹.. 😢');        
        break;

      case Food::STEP_9:
        if (!($food->step == Food::STEP_8)) { if ($food->step != $data['step']) $push->text ('疑？這任務是不是步驟錯了？🤔'); $food->push ($push, $source, $info, $say); return ; }
        $food->step = $data['step'];
        $food->score = isset ($data['score']) && in_array ($data['score'], array_keys (Food::$scoreNames)) ? $data['score'] : '';
        if (Food::transaction (function () use ($food) { return $food->save (); })) $food->push ($push, $source, $info, $say);
        else $push->text ('哭哭，資料庫處理錯誤惹.. 😢');        
        break;

      case Food::STEP_10:
        if (!($food->step == Food::STEP_9)) { if ($food->step != $data['step']) $push->text ('疑？這任務是不是步驟錯了？🤔'); $food->push ($push, $source, $info, $say); return ; }
        $food->step = $data['step'];
        $food->datetime_at = ($info->params && ($info->params = json_decode ($info->params, true)) && isset ($info->params['datetime'])) ? DateTime::createFromFormat ('Y-m-d\TH:i', $info->params['datetime'])->format ('Y-m-d h:i:00') : date ('Y-m-d H:i:00');
        if (Food::transaction (function () use ($food) { return $food->save (); })) $food->push ($push, $source, $info, $say);
        else $push->text ('哭哭，資料庫處理錯誤惹.. 😢');        
        break;
    }
  }
  public static function viewFood ($data, $push, $source, $info, $say) {
    switch ($data['step']) {
      case 1:
        if (!$foods = Food::find ('all', array ('order' => 'id DESC', 'limit' => 10, 'conditions' => array ('step = ?', Food::STEP_FINISH)))) { $push->text ('你還沒有收藏任何美食耶！😄'); break; }
        $push->templateCarousel ('最新十筆美食！', array_map (function ($food) use ($source) {
            return array (
              'title' => $food->title,
              'text' => $food->score == Food::SCORE_3 ? '★★★★★' : ($food->score == Food::SCORE_2 ? '★★★☆☆' : '★☆☆☆☆'),
              'img' => $food->cover->url (),
              'actions' => array (
                OALineBotPush::templatePostbackAction ('地點在哪？', json_encode (array ('type' => 'viewFood', 'id' => $food->id, 'step' => 2)), $source->isManyUser () ? '我要看「' . $food->title . '」地點在哪！' : ''),
                OALineBotPush::templatePostbackAction ('何時新增？', json_encode (array ('type' => 'viewFood', 'id' => $food->id, 'step' => 3)), $source->isManyUser () ? '我要看「' . $food->title . '」何時新增' : '')
              ));
          }, $foods));
      case 2:
        if (!($data['id'] && ($food = Food::find ('one', array ('conditions' => array ('id = ? AND step = ?', $data['id'], Food::STEP_FINISH)))))) return;
        $push->image ($food->staticmap (), $food->staticmap ());
        $push->text ($food->title . '的地點在：' . $food->address);
        break;
      case 3:
        if (!($data['id'] && ($food = Food::find ('one', array ('conditions' => array ('id = ? AND step = ?', $data['id'], Food::STEP_FINISH)))))) return;
        $push->text ($food->title . '是在 ' . $food->datetime_at->format ('Y.m.d H:i') . ' 新增的！');
        break;
    }
  }
}