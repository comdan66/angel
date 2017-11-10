<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class BitcoinChartPicImageUploader extends OrmImageUploader {

  public function getVersions () {
    return array (
        '' => array (),
        'w240' => array ('resize', 240, 240, 'width'),
      );
  }
}