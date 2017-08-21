<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

class Migration_Add_sources extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `sources` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,

        `type` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '狀態，1 使用者，2 群組，3 聊天室',
        `sid` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '來源 ID',
        `memo` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '備註',
        `status` tinyint(1) unsigned NOT NULL DEFAULT 2 COMMENT '狀態，1 離開，2 加入',

        `updated_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '更新時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`),
        KEY `sid_index` (`sid`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `sources`;"
    );
  }
}