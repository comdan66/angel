<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Keyword extends OaModel {

  static $table_name = 'keywords';

  static $has_one = array (
  );

  static $has_many = array (
    array ('contents', 'class_name' => 'KeywordContent'),
  );

  static $belongs_to = array (
  );
  const TYPE_ALL   = 1;
  const TYPE_USER  = 2;
  const TYPE_GROUP = 3;
  const TYPE_ROOM  = 4;

  static $typeNames = array (
    self::TYPE_ALL   => '全部',
    self::TYPE_USER  => '一對一',
    self::TYPE_GROUP => '群組',
    self::TYPE_ROOM  => '聊天室',
  );
  const METHOD_TEXT             = 1;
  const METHOD_ALLEY_KEYWORD    = 2;
  const METHOD_YOUTUBE          = 3;
  const METHOD_FLICKR           = 4;
  const METHOD_ALLEY_RECOMMEND  = 5;
  // const METHOD_WEATHER = 4;

  static $methodNames = array (
    self::METHOD_TEXT             => '回應文字',
    self::METHOD_ALLEY_KEYWORD    => '巷弄 關鍵字 ',
    self::METHOD_YOUTUBE          => 'Youtube 影片 關鍵字 ',
    self::METHOD_FLICKR           => 'Flicker 相片 關鍵字 ',
    self::METHOD_ALLEY_RECOMMEND  => '巷弄 推薦 ',
    // self::METHOD_WEATHER  => '天氣地圖',
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function destroy () {
    if ($this->contents)
      foreach ($this->contents as $content)
        if (!$content->destroy ())
          return false;

    return $this->delete ();
  }
}