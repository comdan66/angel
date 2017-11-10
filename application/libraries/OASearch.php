<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class OASearch {
  private static $CI = null;

  public static function CI () {
    if (!(self::$CI === null)) return self::$CI;
    self::$CI =& get_instance ();
    return self::$CI;
  }
  public static function bitcoinNow ($source, $log) {
    self::CI ()->load->library ('OAMaicoin');
    if ($price = OAMaicoin::GetBitcoinPrice ())
      OALineBotMsg::create ()->text ("目前關於比特幣價錢如下：\n" . str_repeat ("=", 18) . "\n賣出：" . oa_number_format ($price['sell']) . "\n買入：" . oa_number_format ($price['buy']) . "\n平均：" . oa_number_format ($price['price']) . "\n" . str_repeat ("-", 24) . "\n" . $price['created_at'])->reply ($log);
    else
      OALineBotMsg::create ()->text ("目前查不到資訊耶.. 😢")->reply ($log);
  }
  public static function bitcoinChart ($source, $log) {
    self::CI ()->load->library ('OAImagickLineGraph');
    $path = FCPATH . 'temp' . DIRECTORY_SEPARATOR . uniqid (rand () . '_') . '.png';

    if (OAImagickLineGraph::create (640, 320)->setData (array_reverse (array_map (function ($bitcoin) { return array ('title' => $bitcoin->created_at->format ('H:i'), 'value' => $bitcoin->price); }, Bitcoin::find ('all', array ('order' => 'id DESC', 'limit' => 8)))))->setCntY (20)->setTimesY (5)->setLineColor ('rgba(93, 193, 227, 1.00)')->setBgColor ('rgba(255, 255, 255, 1.00)')->save ($path, 'png') && ($params = array ('pic' => '')) && BitcoinChart::transaction (function () use ($params, $path, &$obj) { return verifyCreateOrm ($obj = BitcoinChart::create (array_intersect_key ($params, BitcoinChart::table ()->columns))) && $obj->pic->put ($path); }))
      OALineBotMsg::create ()->image ($obj->pic->url (), $obj->pic->url ('w240'))->reply ($log);
    else
      OALineBotMsg::create ()->text ("目前查不到資訊耶.. 😢")->reply ($log);
  }
}
// class OASearch {
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

//     if ($price = OAMaicoin::GetBitcoinPrice ())
//       $push->text ("目前關於比特幣價錢如下：\n" . str_repeat ("=", 18) . "\n賣出：" . oa_number_format ($price['sell']) . "\n買入：" . oa_number_format ($price['buy']) . "\n平均：" . oa_number_format ($price['price']) . "\n" . str_repeat ("-", 24) . "\n" . $price['created_at']);
//     else
//       $push->text ("目前查不到資訊耶.. 😢");
//   }
// }