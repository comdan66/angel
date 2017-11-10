<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_foods extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `foods` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `log_image_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'LogImage ID',
        `source_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Source ID',
        `say_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'say ID',

        `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '標題',
        `step` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '步驟',
        `score` tinyint(1) unsigned NOT NULL DEFAULT 2 COMMENT '評價，1 難吃，2 普通，3 好吃',

        `cover` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '照片',
        
        `latitude` double DEFAULT NULL COMMENT '緯度',
        `longitude` double DEFAULT NULL COMMENT '經度',
        `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '住址',

        `datetime_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '設定時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`),
        KEY `step_index` (`step`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `foods`;"
    );
  }
}