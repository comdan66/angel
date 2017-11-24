<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class AlleyGet {

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
  public static function deepLink ($id) {
    return 'https://contentalley.friday.tw/link?id=' . $id;
  }
  public static function changeImageUrl ($url) {
    return str_replace ('http://imagealley.friday.tw/', 'https://s3-ap-northeast-1.amazonaws.com/imagealley.friday.tw/', str_replace ('http://contentalley.friday.tw/link', 'https://contentalley.friday.tw/link', $url));
  }
  public static function tigers () {
    $url = "https://apialley.friday.tw/api/3.0/home";

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

    $data = json_decode ($data, true);
    if (!(isset ($data['items']) && ($data = $data['items']))) return array ();
    
    $objs = array ();
    foreach ($data as $value)
      if (($value['type'] == 'tigers') && ($objs = $value['data']))
        break;

    return array_filter (array_map (function ($obj) {
      return array (
          'img' => AlleyGet::changeImageUrl ($obj['image']),
          'title' => $obj['title'],
          'link' => AlleyGet::deepLink ($obj['parameter']),
        );
    }, $objs), function ($itme) {
      return isHttps ($itme['img']) && isHttps ($itme['link']) && $itme['title'];
    });
  }
  public static function banners () {
    $url = "https://apialley.friday.tw/api/3.0/home";

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

    $data = json_decode ($data, true);
    if (!(isset ($data['items']) && ($data = $data['items']))) return array ();
    
    $objs = array ();
    foreach ($data as $value)
      if (($value['type'] == 'banners') && ($objs = $value['data']))
        break;

    return array_filter (array_map (function ($obj) {
      return array (
          'img' => AlleyGet::changeImageUrl ($obj['image']),
          'title' => $obj['title'],
          'link' => $obj['action'] != 'url' ? $obj['action'] == 'detail' ? AlleyGet::deepLink ($obj['parameter']) : '' : $obj['action'],
        );
    }, $objs), function ($itme) {
      return isHttps ($itme['img']) && isHttps ($itme['link']) && $itme['title'];
    });
  }

  public static function recommend () {
    $url = "https://apialley.friday.tw/api/3.0/product/recommend";

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

    $data = json_decode ($data, true);
    if (!(isset ($data['items']) && ($data = $data['items']))) return array ();
    
    return array_filter (array_map (function ($item) {
      return array (
          'id' => $item['productId'],
          'img' => AlleyGet::changeImageUrl ($item['originImage']),
          'title' => $item['productName'],
          'text' => ($item['story']) ? $item['story'] : $item['storeName'],
          'address' => $item['address'],
          'position' => array ('lat' => $item['latitude'], 'lng' => $item['longitude']),
          'url' => AlleyGet::changeImageUrl ($item['webSite']),
          'link' => AlleyGet::deepLink ($item['productId'])
        );
    }, $data), function ($item) {
      return isHttps ($item['img']) && isHttps ($item['url']);
    });
  }
  
  public static function stars ($score) { $str = ''; for ($i = 0; $i < floor ($score); $i++) $str .= '★'; for ($i; $i < 5; $i++) $str .= '☆'; return $str; }
  public static function score ($scoreLevel) { $score = 0; foreach ($scoreLevel as $key => $value) $score += $key * $value; return ($t = array_sum ($scoreLevel)) ? $score / $t : 0; }
  
  public static function products ($id = 0, $lat = null, $lng = null) {
    $url = "https://apialley.friday.tw/api/3.0/product";

    $q = array ();
    
    if ($id) $q['givenId'] = $id;
    if ($lat && $lng) { $q['latitude'] = $lat; $q['longitude'] = $lng; }
    if ($q) $url .= '?' . http_build_query ($q);

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

    $data = json_decode ($data, true);
    
    if (!(isset ($data['items']) && ($data = $data['items']))) return $id ? null : array ();

    $data = array_filter (array_map (function ($item) {
      return array (
          'id' => $item['productId'],
          'title' => $item['productName'],
          'text' => ($item['story']) ? $item['story'] : $item['storeName'],
          'address' => $item['address'],
          'position' => array ('lat' => $item['latitude'], 'lng' => $item['longitude']),
          'img' => AlleyGet::changeImageUrl ($item['originImage']),
          'url' => AlleyGet::changeImageUrl ($item['webSite']),
          'link' => AlleyGet::deepLink ($item['productId']),
          'score' => $t = round (AlleyGet::score ($item['scoreLevel']), 1),
          'stars' => AlleyGet::stars ($t),
          'store' => $item['storeName'],
        );
    }, $data), function ($item) {
      return isHttps ($item['img']) && isHttps ($item['url']);
    });

    return $data ? $id ? $data[0] : $data : array ();
  }
  public static function stores ($id = 0, $lat = null, $lng = null) {
    $url = $id ? "https://apialley.friday.tw/api/3.0/product" : "https://apialley.friday.tw/api/3.0/store/list";

    $q = array ();
    
    if ($id) $q['storeId'] = $id;
    if ($lat && $lng) { $q['latitude'] = $lat; $q['longitude'] = $lng; }
    if ($q) $url .= '?' . http_build_query ($q);


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

    $data = json_decode ($data, true);
    
    if (!(isset ($data['items']) && ($data = $data['items']))) return array ();
    
    $data = $id ? array_filter (array_map (function ($item) {
      return array (
          'id' => $item['productId'],
          'title' => $item['productName'],
          'text' => ($item['story']) ? $item['story'] : $item['storeName'],
          'address' => $item['address'],
          'position' => array ('lat' => $item['latitude'], 'lng' => $item['longitude']),
          'img' => AlleyGet::changeImageUrl ($item['originImage']),
          'url' => AlleyGet::changeImageUrl ($item['webSite']),
          'link' => AlleyGet::deepLink ($item['productId']),
          'score' => $t = round (AlleyGet::score ($item['scoreLevel']), 1),
          'stars' => AlleyGet::stars ($t),
          'store' => $item['storeName'],
        );
    }, $data), function ($item) {
      return isHttps ($item['img']) && isHttps ($item['url']);
    }) : array_filter (array_map (function ($item) {
      return array (
          'id' => $item['storeId'],
          'title' => $item['storeName'],
          'address' => $item['address'],
          'position' => array ('lat' => $item['latitude'], 'lng' => $item['longitude']),
          'img' => AlleyGet::changeImageUrl ($item['image']),
          'link' => AlleyGet::deepLink ($item['storeId']),
          'score' => $t = round ($item['score'], 1),
          'stars' => AlleyGet::stars ($t),
        );
    }, $data), function ($item) {
      return isHttps ($item['img']);
    });

    return $data;
  }
  // public static function search ($keyword) {
  //   $url = "https://apialley.friday.tw/api/2.0/product/search/?keyword=" . $keyword;

  //   $options = array (
  //     CURLOPT_URL => $url,
  //     CURLOPT_TIMEOUT => 60,
  //     CURLOPT_HEADER => false,
  //     CURLOPT_MAXREDIRS => 10,
  //     CURLOPT_AUTOREFERER => true,
  //     CURLOPT_CONNECTTIMEOUT => 30,
  //     CURLOPT_RETURNTRANSFER => true,
  //     CURLOPT_FOLLOWLOCATION => true,
  //     CURLOPT_USERAGENT => self::userAgent (),
  //   );

  //   $ch = curl_init ($url);
  //   curl_setopt_array ($ch, $options);
  //   $data = curl_exec ($ch);
  //   curl_close ($ch);

  //   $data = json_decode ($data, true);
  //   if (!(isset ($data['items']) && $data['items'])) return array ();

  //   return array_slice (array_filter (array_map (function ($item) {
  //     return array (
  //         'title' => $item['productName'],
  //         'desc' => ($item['story']) ? $item['story'] : $item['address'],
  //         'img' => AlleyGet::changeImageUrl ($item['originImage']),
  //         'url' => AlleyGet::changeImageUrl ($item['webSite']),
  //       );
  //   }, $data['items']), function ($t) {
  //     return isHttps ($item['img']) && isHttps ($t['url']);
  //   }), 0, 5);
  // }
  // public static function products ($lat, $lng) {
  //   $url = "https://apialley.friday.tw/api/2.0/product/?latitude=" . $lat . "&longitude=" . $lng;

  //   $options = array (
  //     CURLOPT_URL => $url,
  //     CURLOPT_TIMEOUT => 60,
  //     CURLOPT_HEADER => false,
  //     CURLOPT_MAXREDIRS => 10,
  //     CURLOPT_AUTOREFERER => true,
  //     CURLOPT_CONNECTTIMEOUT => 30,
  //     CURLOPT_RETURNTRANSFER => true,
  //     CURLOPT_FOLLOWLOCATION => true,
  //     CURLOPT_USERAGENT => self::userAgent (),
  //   );

  //   $ch = curl_init ($url);
  //   curl_setopt_array ($ch, $options);
  //   $data = curl_exec ($ch);
  //   curl_close ($ch);

  //   $data = json_decode ($data, true);
  //   if (!(isset ($data['items']) && $data['items'])) return array ();
    
  //   return array_slice (array_filter (array_map (function ($item) {
  //     return array (
  //         'title' => $item['productName'],
  //         'desc' => ($item['story']) ? $item['story'] : $item['address'],
  //         'img' => AlleyGet::changeImageUrl ($item['originImage']),
  //         'url' => AlleyGet::changeImageUrl ($item['webSite']),
  //       );
  //   }, $data['items']), function ($t) {
  //     return isHttps ($t['img']) && isHttps ($t['url']);
  //   }), 0, 5);
  // }
}
