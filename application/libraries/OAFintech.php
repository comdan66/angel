<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class OAFintech {
  private static function cache ($key, $time, $callback) {
    if (!is_callable ($callback)) return array ();

    if (($path = FCPATH . 'temp/cache_' . $key . '.json') && ($file = read_file ($path)) && ($file = json_decode ($file, true)) && isset ($file['t']) && isset ($file['v']) && $file['t'] + $time > time ())
      return $file['v'];

    write_file ($path, json_encode (array ('t' => time (), 'v' => $v = $callback ())));
    return $v;
  }
  private static function userAgent () {
    $t = array (
      'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
      'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.76 Safari/537.36',
      'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
      'Mozilla/5.0 (Linux; Android 4.3; Nexus 7 Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
      'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
    );
    return $t[array_rand ($t)];
  }
  public static function getBitcoinPrice ($t = 600) {echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
    return self::cache (str_replace ('::', '_', __METHOD__), $t, function () {
      $url = "https://api.maicoin.com/v1/prices/twd";
      $options = array (
        CURLOPT_URL => $url,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_HEADER => false,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => self::userAgent (),
      );

      $ch = curl_init ($url);
      curl_setopt_array ($ch, $options);
      $data = curl_exec ($ch);
      curl_close ($ch);

      if (!($data && ($data = json_decode ($data, true)) && isset ($data['success']) && isset ($data['errors']) && isset ($data['sell_price']) && isset ($data['buy_price']) && isset ($data['price']) && $data['success'] && !$data['errors'] && is_numeric ($data['sell_price']) && is_numeric ($data['buy_price']) && is_numeric ($data['price']))) return array ();

      return array (
          'sell' => $data['sell_price'],
          'buy' => $data['buy_price'],
          'price' => $data['price'],
          'created_at' => date ('Y-m-d H:i:s'),
        );
    });
  }
  public static function getRterJpyInfo ($t = 600) {
    return self::cache (str_replace ('::', '_', __METHOD__), $t, function () {
      $url = "https://tw.rter.info/json.php?t=currency&q=cash&iso=JPY&_=" . time ();

      $options = array (
        CURLOPT_URL => $url,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_HEADER => false,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => self::userAgent (),
      );

      $ch = curl_init ($url);
      curl_setopt_array ($ch, $options);
      $data = curl_exec ($ch);
      curl_close ($ch);

      if (!($data && ($data = json_decode ($data, true)) && isset ($data['data']) && ($data = $data['data']))) return array ();
      $buys = array_values ($data);
      usort ($buys, function ($a, $b) { return $a[2] > $b[2]; });

      $sells = array_values ($data);
      usort ($sells, function ($a, $b) { return $a[1] < $b[1]; });
      
      $CI =& get_instance ();
      $CI->load->library ('phpQuery');

      $query = phpQuery::newDocument ($buys[0][0]);
      $buyBank = pq ("a", $query)->text ();
      $buyRate = $buys[0][2];
      $buyMemo = $buys[0][4];
      
      $query = phpQuery::newDocument ($sells[0][0]);
      $sellBank = pq ("a", $query)->text ();
      $sellRate = $sells[0][1];
      $sellMemo = $sells[0][4];

      return array (
          'buy' => array ('title' => $buyBank, 'rate' => $buyRate, 'memo' => $buyMemo),
          'sell' => array ('title' => $sellBank, 'rate' => $sellRate, 'memo' => $sellMemo),
          'created_at' => date ('Y-m-d H:i:s'),
        );
    });
  }
}