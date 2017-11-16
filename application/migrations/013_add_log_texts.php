<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_log_texts extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `log_texts` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `source_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Source ID',
        `speaker_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Speaker Source ID',
        `reply_token` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '回覆 Token',
        `message_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '訊息 ID',
        `text` text NOT NULL COMMENT '訊息內容',
        `timestamp` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '時間',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `log_texts`;"
    );
  }
}