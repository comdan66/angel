<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_richmenus extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `richmenus` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `rid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Richmenu ID',
        `name` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名稱',
        `text` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '在 Bar 上顯示名稱',
        `selected` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '是否預設，1 否，2 是',
        `cover` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '封面',
        `width` smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT '封面寬度',
        `height` smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT '封面高度',
        `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '是否更新，1 否，2 是',
        `is_d4` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '是否為預設，1 否，2 是',
        PRIMARY KEY (`id`),
        KEY `rid_index` (`rid`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `richmenus`;"
    );
  }
}