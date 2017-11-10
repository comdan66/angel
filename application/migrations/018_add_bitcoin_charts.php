<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_bitcoin_charts extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `bitcoin_charts` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `pic` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '照片',
        `created_at` datetime NOT NULL DEFAULT '" . date ('Y-m-d H:i:s') . "' COMMENT '新增時間',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `bitcoin_charts`;"
    );
  }
}