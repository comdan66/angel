<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class OAPostback {
  private static $CI = null;

  public static function CI () {
    if (!(self::$CI === null)) return self::$CI;
    self::$CI =& get_instance ();
    return self::$CI;
  }

  public static function featureBitcoin ($source, $log) {
    OALineBotMsg::create ()->templateButton ('比特幣功能', '比特幣功能列表', array (
            OALineBotAction::templatePostback ('我的比特幣', array ('class' => 'OAPostback', 'method' => 'myBitcoin'), '要看我的比特幣目前幣值！'),
            OALineBotAction::templatePostback ('比特幣目前概況', array ('class' => 'OAPostback', 'method' => 'searchBitcoinNow'), '我選查詢目前 比特幣'),
            OALineBotAction::templatePostback ('比特幣 一日內圖表', array ('class' => 'OAPostback', 'method' => 'viewBitcoinChart', 'params' => array (24)), '我選比特幣 一日內圖表'),
            OALineBotAction::templatePostback ('比特幣 一週內圖表', array ('class' => 'OAPostback', 'method' => 'viewBitcoinChart', 'params' => array (24 * 7)), '我選比特幣 一週內圖表')
          )
      )->reply ($log);
  }
  public static function featureJpy ($source, $log) {
    OALineBotMsg::create ()->templateButton ('日幣功能', '比特幣功能列表', array (
            OALineBotAction::templatePostback ('我的日幣', array ('class' => 'OAPostback', 'method' => 'myJpy'), '要看我的日幣目前幣值！'),
            OALineBotAction::templatePostback ('日幣目前概況', array ('class' => 'OAPostback', 'method' => 'searchJpyNow'), '我選查詢目前 日幣目前概況'),
          )
      )->reply ($log);
  }
  public static function featureGirl ($source, $log) {
      return OALineBotMsg::create ()->text ("目前功能還沒有完成喔！")->reply ($log);
  }
  public static function featureBoy ($source, $log) {
      return OALineBotMsg::create ()->text ("目前功能還沒有完成喔！")->reply ($log);
  }
  public static function featureConfig ($source, $log) {
      return OALineBotMsg::create ()->text ("目前功能還沒有完成喔！")->reply ($log);
  }
  public static function myBitcoin ($source, $log, $speaker) {
    self::CI ()->load->library ('OAFintech');

    if (!$speaker->set)
      return OALineBotMsg::create ()->text ("您還沒有設定日幣數量喔，請找 OA Wu 幫您設定吧！")->reply ($log);

    if (!$price = OAFintech::getBitcoinPrice ())
      return OALineBotMsg::create ()->text ("目前查不到資訊耶.. 😢")->reply ($log);

    $v = round ($speaker->set->bitcoin * $price['sell']);
    $str = ($path = FCPATH . 'temp/lasttime_myBitcoin_' . $speaker->id . '.json') && ($file = read_file ($path)) && ($file = json_decode ($file, true)) && isset ($file['v']) ? $file['v'] != $v ? '您上次查詢可換的金額是新台幣 ' . number_format ($file['v']) . ' 元！' . ($file['v'] > $v ? '跌' : '漲') . '了 ' . number_format (abs ($v - $file['v'])) . ' 元。' : '與上次查詢結果一樣！' : '';
    
    OALineBotMsg::create ()->text ("Hi" . ($speaker->title ? ' ' . $speaker->title : '') . ', 您目前的比特幣有 ' . $speaker->set->bitcoin . ' 元，現在賣出的話可以轉換成新台幣 ' . (number_format ($v)) . ' 喔！' . $str)->reply ($log);
    return $file['v'] != $v ? write_file ($path, json_encode (array ('v' => $v))) : true;
  }
  public static function searchBitcoinNow ($source, $log) {
    self::CI ()->load->library ('OAFintech');
    if ($price = OAFintech::getBitcoinPrice ()) OALineBotMsg::create ()->text ("目前關於比特幣價錢如下：\n" . str_repeat ("=", 18) . "\n賣出：" . oa_number_format ($price['sell']) . "\n買入：" . oa_number_format ($price['buy']) . "\n平均：" . oa_number_format ($price['price']) . "\n" . str_repeat ("-", 24) . "\n" . $price['created_at'])->reply ($log);
    else OALineBotMsg::create ()->text ("目前查不到資訊耶.. 😢")->reply ($log);
  }
  public static function viewBitcoinChart ($source, $log, $limit) {
    self::CI ()->load->library ('OAImagickLineGraph');
    $path = FCPATH . 'temp' . DIRECTORY_SEPARATOR . uniqid (rand () . '_') . '.png';

    if (OAImagickLineGraph::create (680, 340)->setData (array_reverse (array_map (function ($bitcoin) { return array (
      'title' => $bitcoin->created_at->format ("Y-m-d\nH:i"),
      'v1' => $bitcoin->sell,
      'v2' => $bitcoin->buy,
      'v3' => $bitcoin->price,
    ); }, Bitcoin::find ('all', array ('select' => 'sell, buy, price, created_at', 'order' => 'id DESC', 'limit' => $limit)))))->setCntY (20)->setTimesY (5)->setLineInfos ('v1', array ('color' => 'rgba(229, 79, 81, 1.00)', 'title' => '賣出'))->setLineInfos ('v2', array ('color' => 'rgba(90, 125, 200, 1.00)', 'title' => '買入'))->setLineInfos ('v3', array ('color' => 'rgba(78, 190, 183, 1.00)', 'title' => '平均'))->setBgColor ('rgba(255, 255, 255, 1.00)')->save ($path, 'png') && ($params = array ('pic' => '')) && ReplyImage::transaction (function () use ($params, $path, &$obj) { return verifyCreateOrm ($obj = ReplyImage::create (array_intersect_key ($params, ReplyImage::table ()->columns))) && $obj->pic->put ($path); }))
      OALineBotMsg::create ()->image ($obj->pic->url (), $obj->pic->url ('w240'))->reply ($log);
    else
      OALineBotMsg::create ()->text ("目前查不到資訊耶.. 😢")->reply ($log);
  }
  public static function myJpy ($source, $log, $speaker) {
    self::CI ()->load->library ('OAFintech');

    if (!$speaker->set)
      return OALineBotMsg::create ()->text ("您還沒有設定日幣數量喔，請找 OA Wu 幫您設定吧！")->reply ($log);

    if (!$price = OAFintech::getRterJpyInfo ())
      return OALineBotMsg::create ()->text ("目前查不到資訊耶.. 😢")->reply ($log);
    
    $v = round ($speaker->set->jpy * $price['sell']['rate']);
    $str = ($path = FCPATH . 'temp/lasttime_myJpy_' . $speaker->id . '.json') && ($file = read_file ($path)) && ($file = json_decode ($file, true)) && isset ($file['v']) ? $file['v'] != $v ? '您上次查詢可換的金額是新台幣 ' . number_format ($file['v']) . ' 元！' . ($file['v'] > $v ? '跌' : '漲') . '了 ' . number_format (abs ($v - $file['v'])) . ' 元。' : '與上次查詢結果一樣！' : '';

    OALineBotMsg::create ()->text ("Hi" . ($speaker->title ? ' ' . $speaker->title : '') . ', 您目前的日幣有 ' . $speaker->set->jpy . ' 元，現在去「' . $price['sell']['title'] . '」賣出利率最高，可以轉換成新台幣 ' . (number_format ($v)) . ' 喔！' . $str)->reply ($log);

    return $file['v'] != $v ? write_file ($path, json_encode (array ('v' => $v))) : true;
  }

  public static function searchJpyNow ($source, $log) {
    self::CI ()->load->library ('OAFintech');
    if ($price = OAFintech::getRterJpyInfo ())
      OALineBotMsg::create ()->text ("目前最佳日幣匯率如下：\n" . str_repeat ("=", 18) . "\n" . $price['buy']['title'] . "買入為最佳(" . $price['buy']['rate'] . ")，每 1 元新台幣可換 " . round (1 / $price['buy']['rate'], 4) . " 元日幣" . ($price['buy']['memo'] ? "，手續費：" . $price['buy']['memo'] : '') . "\n" . str_repeat ("-", 24) . "\n" . $price['sell']['title'] . "賣出為最佳(" . $price['sell']['rate'] . ")，每 1 元日幣換 " . $price['sell']['rate'] . " 元新台幣" . ($price['buy']['memo'] ? "，手續費：" . $price['buy']['memo'] : '') . "\n" . str_repeat ("-", 24) . "\n" . $price['created_at'])->reply ($log);
    else
      OALineBotMsg::create ()->text ("目前查不到資訊耶.. 😢")->reply ($log);
  }
}

// class OAPostback {
//   public static function alley ($data, $push, $source, $info, $say, $keyword = '') {
//     self::CI ()->load->library ('AlleyGet');

//     if (!$objs = AlleyGet::recommend ()) return ;
//     $push->templateCarousel ('巷弄美食推薦', array_map (function ($obj) {

//       return array (
//             'title' => $obj['title'],
//             'text' => $obj['desc'],
//             'img' => $obj['img'],
//             'actions' => array (
//               OALineBotPush::templateUriAction ('打開巷弄', $obj['link']),
//               OALineBotPush::templateUriAction ('查看網站', $obj['url']),
//               OALineBotPush::templatePostbackAction ('店家位置', json_encode (array ('type' => 'search', 'method' => 'alleyAddress', 'a' => $obj['position']['lat'], 'n' => $obj['position']['lng']))),
//             ));
//     }, $objs));
    
//   }

//   public static function bitcoinHistory ($data, $push, $source, $info, $say, $keyword = '') {

//   }
//   public static function bitcoinNow ($data, $push, $source, $info, $say, $keyword = '') {
//     self::CI ()->load->library ('OAMaicoin');

//     if ($price = OAMaicoin::getBitcoinPrice ())
//       $push->text ("目前關於比特幣價錢如下：\n" . str_repeat ("=", 18) . "\n賣出：" . oa_number_format ($price['sell']) . "\n買入：" . oa_number_format ($price['buy']) . "\n平均：" . oa_number_format ($price['price']) . "\n" . str_repeat ("-", 24) . "\n" . $price['created_at']);
//     else
//       $push->text ("目前查不到資訊耶.. 😢");
//   }
// }