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
  public static function alley ($data, $push, $source, $info, $say, $keyword = '') {
    self::CI ()->load->library ('AlleyGet');

    if (!$objs = AlleyGet::recommend ()) return ;
    $push->templateCarousel ('巷弄美食推薦', array_map (function ($obj) {

      return array (
            'title' => $obj['title'],
            'text' => $obj['desc'],
            'img' => $obj['img'],
            'actions' => array (
              OALineBotPush::templateUriAction ('打開巷弄', $obj['link']),
              OALineBotPush::templateUriAction ('查看網站', $obj['url']),
              OALineBotPush::templatePostbackAction ('店家位置', json_encode (array ('type' => 'search', 'method' => 'alleyAddress', 'a' => $obj['position']['lat'], 'n' => $obj['position']['lng']))),
            ));
    }, $objs));
    
  }

  public static function bitcoinHistory ($data, $push, $source, $info, $say, $keyword = '') {
    self::CI ()->load->library ('OAImagickLineGraph');

    $path = FCPATH . 'temp' . DIRECTORY_SEPARATOR . uniqid (rand () . '_') . '.png';
    $bitcoins = array_map (function ($bitcoin) { return array ('title' => $bitcoin->created_at->format ('H:i'), 'value' => $bitcoin->price); }, Bitcoin::find ('all', array ('order' => 'id DESC', 'limit' => 8)));
    $bitcoins = array_reverse ($bitcoins);
    // rsort ($bitcoins);

    $img = OAImagickLineGraph::create (640, 320);
    $img->setData ($bitcoins);
    $img->setCntY (20);
    $img->setTimesY (5);
    $img->setLineColor ('rgba(93, 193, 227, 1.00)');
    $img->setBgColor ('rgba(255, 255, 255, 1.00)');
    $img->save ($path, 'png');

    if (!(($params = array ('pic' => '', 'type' => BitcoinHistory::TYPE_1)) && BitcoinHistory::transaction (function () use ($params, $path, &$obj) { return verifyCreateOrm ($obj = BitcoinHistory::create (array_intersect_key ($params, BitcoinHistory::table ()->columns))) && $obj->pic->put ($path); })))
      return;

    $push->image ($obj->pic->url (), $obj->pic->url ('w240'));

  }
  public static function bitcoinNow ($data, $push, $source, $info, $say, $keyword = '') {
    self::CI ()->load->library ('OAMaicoin');

    if ($price = OAMaicoin::GetBitcoinPrice ())
      $push->text ("目前關於比特幣價錢如下：\n" . str_repeat ("=", 18) . "\n賣出：" . oa_number_format ($price['sell']) . "\n買入：" . oa_number_format ($price['buy']) . "\n平均：" . oa_number_format ($price['price']) . "\n" . str_repeat ("-", 24) . "\n" . $price['created_at']);
    else
      $push->text ("目前查不到資訊耶.. 😢");
  }
}