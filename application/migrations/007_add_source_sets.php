<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Migration_Add_source_sets extends CI_Migration {
  public function up () {
    $this->db->query (
      "CREATE TABLE `source_sets` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `source_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Source ID',
        `bitcoin` double NOT NULL DEFAULT '0' COMMENT '比特幣',
        `jpy` double NOT NULL DEFAULT '0' COMMENT '日幣',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    );
  }
  public function down () {
    $this->db->query (
      "DROP TABLE `source_sets`;"
    );
  }
}