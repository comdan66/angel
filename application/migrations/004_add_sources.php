<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_sources extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `sources` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,

        `sid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '來源 ID',
        `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
        `type` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '狀態，1 使用者，2 群組，3 聊天室',

        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`),
        KEY `sid_index` (`sid`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `sources`;"
    );
  }
}