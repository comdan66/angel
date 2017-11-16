<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_richmenu_actions extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `richmenu_actions` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `richmenu_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Richmenu ID',
        `x` smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT 'X 位置',
        `y` smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT 'Y 位置',
        `width` smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT '封面寬度',
        `height` smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT '封面高度',
        
        `action_type` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '反應類型，1 Message, 2 Uri, 3 Postback, 4 Picker',
        `text` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Text',
        `uri` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'URL',
        `data` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Postback Data',
        
        `action_pick_type` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Pick 類型，1 Datetime, 2 Date, 3 Time',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `richmenu_actions`;"
    );
  }
}