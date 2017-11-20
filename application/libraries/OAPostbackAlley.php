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
    
  }
  public static function richmenuGift ($source, $log) {
    
  }
  public static function richmenuContact ($source, $log) {
    
  }
  public static function richmenuConfig ($source, $log) {
    
  }
}
