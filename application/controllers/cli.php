<?php if (!defined ('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Cli extends Oa_controller {

  public function __construct () {
    parent::__construct ();
    
    if (!$this->input->is_cli_request ()) {
      echo 'Request 錯誤！';
      exit ();
    }

    ini_set ('memory_limit', '2048M');
    ini_set ('set_time_limit', 60 * 60);
    ob_start ();
  }
  private function errorToAdmin ($err) {
    $this->load->library ('OALineBot');
    $adminSourceId = array (1);

    foreach (column_array (Source::find ('all', array ('select' => 'sid', 'conditions' => array ('id IN (?)', $adminSourceId ? $adminSourceId : array (0)))), 'sid') as $source) {
      OALineBotPush::create (OALineBot::create ()->bot ())
        ->setSource ($source)
        ->text ('123');
    }
  }
  public function bitcoins () {
    $this->load->library ('OAMaicoin');
    
    if (!(($params = OAMaicoin::GetBitcoinPrice ()) && Bitcoin::transaction (function () use ($params) { return verifyCreateOrm (Bitcoin::create (array_intersect_key ($params, Bitcoin::table ()->columns))); })))
      $this->errorToAdmin ('取得比特幣錯誤 >"<');      
  }
}