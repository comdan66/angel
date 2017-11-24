<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_reasons extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `reasons` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `reason_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Reason ID',
        `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '回覆 Token',
        `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '回覆 Token',
        `message` text NOT NULL COMMENT '訊息內容',
        `sort` smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT '排列順序，前至後 DESC',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `reasons`;"
    );
  }
}