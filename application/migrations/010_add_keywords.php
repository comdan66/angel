<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Migration_Add_keywords extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `keywords` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        
        `pattern` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'pattern',
        `weight` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '權重',
        `type` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '分類，1 全部，2 一對一，3 群組，4 聊天室',
        `method` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '分類，1 回應文字，2 巷弄，3 Youtube 影片，4 Flicker 相片',
        
        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`),
        KEY `weight_index` (`weight`),
        KEY `type_index` (`type`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `keywords`;"
    );
  }
}