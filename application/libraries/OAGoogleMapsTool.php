<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class OAGoogleMapsTool {
  public static function getAddress ($lat, $lng) {
    $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lng . '&sensor=false&language=zh-TW';
    if (!(($data = @file_get_contents ($url)) && ($data = json_decode ($data, true)) && isset ($data['status']) && ($data['status'] == 'OK') && isset ($data["results"][0]["formatted_address"]) && $data["results"][0]["formatted_address"])) return '';
    return $data["results"][0]["formatted_address"];
  }
}