<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class OAPostbackAlley {
  private static $CI = null;

  public static function CI () {
    if (!(self::$CI === null)) return self::$CI;
    self::$CI =& get_instance ();
    return self::$CI;
  }
  public static function richmenuFood ($source, $log) {
    return OALineBotMsg::create ()
      ->templateButton ('巷弄美食', '您想要找哪一種美食？', array (
          OALineBotAction::templatePostback ('推薦商品', array ('class' => 'OAPostbackAlley', 'method' => 'recommend')),
          OALineBotAction::templatePostback ('鄰近店家', array ('class' => 'OAPostbackAlley', 'method' => 'huge')),
        ))
      ->reply ($log);
  }
  public static function richmenuGift ($source, $log) {
    return OALineBotMsg::create ()
      ->templateButton ('優惠類型', '您想要知道哪一種優惠？', array (
          OALineBotAction::templatePostback ('超值優惠', array ('class' => 'OAPostbackAlley', 'method' => 'banners')),
          OALineBotAction::templatePostback ('分類優惠', array ('class' => 'OAPostbackAlley', 'method' => 'tigers')),
        ))
      ->reply ($log);
  }
  public static function contactSort ($id = 0) {
    $reasons = $id ? Reason::find ('all', array ('order' => 'sort DESC', 'conditions' => array ('reason_id = ? AND message != ?', $id, ''))) : Reason::find ('all', array ('include' => array ('reasons'), 'order' => 'sort DESC', 'conditions' => array ('reason_id = ?', 0)));
    if (!$id) {
      $reasons = array_filter ($reasons, function ($r) {
        return array_filter(array_map (function ($s) {
          return $s->message;
        }, $r->reasons));
      });
    }
    $u = 3;

    $tmp = array ();
    for ($i = 0; $i < count ($reasons); $i += $u)
      if (($t = array_slice ($reasons, $i, $u)) && (!$i || count ($t) == $u))
        array_push ($tmp, $t);

    return $tmp;
  }
  public static function richmenuContact ($source, $log, $id = 0) {
    if ($r = Reason::find ('one', array ('conditions' => array ('id = ? AND reason_id != ?', $id, 0))))
      return OALineBotMsg::create ()->text ($r->message)->reply ($log);

    if (!$objs = OAPostbackAlley::contactSort ($id)) 
      return OALineBotMsg::create ()->text ('客服人員忙線中..')->reply ($log);

    if (!$id)
      OALineBotMsg::create ()
        ->text ('嗨,您好，請問有什麼可以為您服務的呢？')
        ->reply ($log);

    return OALineBotMsg::create ()
      ->templateCarousel ('服務項目', array_map (function ($obj) {
        return array (
            'text' => '您的問題是？',
            'actions' => array_map (function ($t) {
              return OALineBotAction::templatePostback ($t->title, array ('class' => 'OAPostbackAlley', 'method' => 'richmenuContact', 'params' => array ($t->id)));
            }, $obj)
          );
      }, $objs))
      ->push ($log);
  }
  public static function richmenuConfig ($source, $log) {
    return OALineBotMsg::create ()->text ('目前功能還沒有完成喔！')->reply ($log);
  }

  public static function recommend ($source, $log) {
    self::CI ()->load->library ('AlleyGet');

    if (!$objs = AlleyGet::recommend ()) return OALineBotMsg::create ()->text ('目前沒有什麼推薦商品喔！')->reply ($log);

    return OALineBotMsg::create ()->templateCarousel ('推薦商品', array_map (function ($obj) {
      return array (
          'img' => $obj['img'],
          'title' => $obj['title'],
          'text' => $obj['text'],
          'actions' => array (
              OALineBotAction::templateUri ('打開巷弄', $obj['link']),
              OALineBotAction::templateUri ('商品粉專', $obj['url']),
              OALineBotAction::templatePostback ('商品介紹', array ('class' => 'OAPostbackAlley', 'method' => 'detail', 'params' => array ($obj['id']))),
            )
        );
    }, $objs))->reply ($log);
  }
  public static function banners ($source, $log) {
    self::CI ()->load->library ('AlleyGet');

    if (!$objs = AlleyGet::banners ()) return OALineBotMsg::create ()->text ('目前沒有什麼超值優惠喔！')->reply ($log);

    return OALineBotMsg::create ()->templateImageCarousel ('超值優惠', array_map (function ($obj) {
      return array (
          $obj['img'],
          OALineBotAction::templateUri ($obj['title'], $obj['link']),
        );
    }, $objs))->reply ($log);
  }
  public static function tigers ($source, $log) {
    self::CI ()->load->library ('AlleyGet');
    if (!$objs = AlleyGet::tigers ()) return OALineBotMsg::create ()->text ('目前沒有什麼分類優惠喔！')->reply ($log);

    return OALineBotMsg::create ()->templateImageCarousel ('分類優惠', array_map (function ($obj) {
      return array (
          $obj['img'],
          OALineBotAction::templateUri ($obj['title'], $obj['link']),
        );
    }, $objs))->reply ($log);
  }
  public static function detail ($source, $log, $id) {
    self::CI ()->load->library ('AlleyGet');
    if (!$obj = AlleyGet::products ($id)) return OALineBotMsg::create ()->text ('疑？找不到這像商品耶..')->reply ($log);

    $str = catStr ($obj['title'], 42) . "\n" . $obj['stars'] . "(" . $obj['score'] . ")";

    return OALineBotMsg::create ()
      ->templateButton ('店家資訊', $str, array (
          OALineBotAction::templateUri ('打開巷弄', $obj['link']),
          OALineBotAction::templateUri ('店家粉專', $obj['url']),
          OALineBotAction::templatePostback ('查看地圖', array ('class' => 'OAPostbackAlley', 'method' => 'maps', 'params' => array ($obj['id']))),
        ), $obj['img'], $obj['store'])
      ->push ($log);
  }
  public static function maps ($source, $log, $id) {
    self::CI ()->load->library ('AlleyGet');
    if (!$obj = AlleyGet::products ($id)) return OALineBotMsg::create ()->text ('疑？找不到這家店耶..')->reply ($log);

    $address = ($address = $obj['address'] ? $obj['address'] : getAddress ($lat, $lng)) ? $address : '';

    return OALineBotMsg::create ()
      ->image (staticmap ($obj['position']['lat'], $obj['position']['lng'], '1024x512'), staticmap ($obj['position']['lat'], $obj['position']['lng'], '512x256'))
      ->push ($log) && OALineBotMsg::create ()
      ->location ($obj['store'], $address, $obj['position']['lat'], $obj['position']['lng'])
      ->push ($log);
  }
  public static function huge ($source, $log) {
    return OALineBotMsg::create ()
      ->image ('https://pic.ioa.tw/angel/tip/location.png')
      ->push ($log) && OALineBotMsg::create ()
      ->text ('請打開 + ，然後點選「位置資訊」把要找的位置傳給我吧！')
      ->push ($log);
  }
  public static function products ($source, $log, $id) {
    self::CI ()->load->library ('AlleyGet');
    if (!$objs = AlleyGet::stores ($id)) return OALineBotMsg::create ()->text ('目前沒有任何相關商品喔。')->reply ($log);
    
    return OALineBotMsg::create ()->templateCarousel ('相關商品', array_map (function ($obj) {
      return array (
          'img' => $obj['img'],
          'title' => $obj['store'],
          'text' => $obj['title'],
          'actions' => array (
              OALineBotAction::templateUri ('打開巷弄', $obj['link']),
              OALineBotAction::templateUri ('商品粉專', $obj['url']),
              OALineBotAction::templatePostback ('商品介紹', array ('class' => 'OAPostbackAlley', 'method' => 'detail', 'params' => array ($obj['id']))),
            )
        );
    }, $objs))->reply ($log);
  }
  public static function location ($source, $log, $lat, $lng) {
    self::CI ()->load->library ('AlleyGet');
    if (!$objs = AlleyGet::stores (null, $lat, $lng)) return OALineBotMsg::create ()->text ('附近沒有相關的店家喔。')->reply ($log);
    
    return OALineBotMsg::create ()->templateCarousel ('鄰近店家', array_map (function ($obj) {
      return array (
          'img' => $obj['img'],
          'title' => $obj['title'],
          'text' => $obj['stars'] . "(" . $obj['score'] . ")",
          'actions' => array (
              OALineBotAction::templateUri ('打開巷弄', $obj['link']),
              OALineBotAction::templatePostback ('相關商品', array ('class' => 'OAPostbackAlley', 'method' => 'products', 'params' => array ($obj['id']))),
            )
        );
    }, $objs))->reply ($log);
  }
}
