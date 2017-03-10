<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class WeatherGet {

  public function __construct () {
  }

  public static function getByLatLng ($lat, $lng) {
    $key = Cfg::setting ('google', ENVIRONMENT, 'server_key');

    if (!(($data = json_decode (file_get_contents ('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lng . '&language=zh-TW' . ($key ? '&key=' . $key : '')), true)) && isset ($data['results'][0]['address_components']) && ($data = $data['results'][0]['address_components'])))
      return false;

    for ($i = 0, $c = count ($data), $postal_code = '0'; $i < $c; $i++)
      if (in_array ('postal_code', $data[$i]['types']) && ($postal_code = $data[$i]['long_name'])) break;
    if (!$postal_code) return false;
    
    $data = json_decode (file_get_contents ('https://works.ioa.tw/weather/api/all.json'), true);
    $id = 0;
    $city = '';
    $name = '';

    foreach ($data as $value)
      foreach ($value['towns'] as $town)
        if (($town['postal'] == $postal_code) && ($id = $town['id']) && ($city = $value['name']) && ($name = $town['name']))
          break;

    if (!($id && $city && $name)) return false;
    
    $data = json_decode (file_get_contents ('https://works.ioa.tw/weather/api/weathers/' . $id . '.json'), true);
    $img = 'https://works.ioa.tw/weather/img/weathers/card/' . $data['img'];
    $title = $city . ''. $name . ' 目前的天氣概況';
    $desc = '目前 ' . $data['desc'] . '，氣溫 ' . $data['temperature'] . '°C，相對濕度 ' . $data['humidity'] . '%，累積降雨量 ' . $data['rainfall'] . 'mm，' . ($data['specials'] ? '此處已經發佈 ' . implode (',', column_array ($data['specials'], 'title')) : ('今天太陽將於 ' . $data['sunset'] . ' 落下，請把握今日時光喔！'));
    $url = 'https://works.ioa.tw/weather/towns/' . $city . '-' . $name . '.html';

    return array (
        'title' => $title,
        'desc' => $desc,
        'img' => $img,
        'url' => $url,
      );
  }
}
