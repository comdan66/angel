<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Messages extends Api_controller {

  public function __construct () {
    parent::__construct ();
  }

  public function index () {
    if (!(($id = OAInput::post ('id')) && ($source = Source::find ('one', array ('select' => 'id', 'conditions' => array ('sid = ? AND status = ? AND title != ?', $id, Source::STATUS_JOIN, ''))))))
      return $this->output_error_json ('參數錯誤');

    $limit = 10;
    if (!$log = Log::find ('all', array ('select' => 'id', 'order' => 'id DESC', 'limit' => $limit, 'conditions' => array ('source_id = ? AND type = ? AND message_type IN (?)', $source->id, 'message', array ('text')))))
      return $this->output_json (array ());

    if (!$ids = column_array ($log, 'id'))
      return $this->output_json (array ());

    if (!$texts = LogText::find ('all', array ('select' => 'id,text, log_id', 'include' => array ('log2'), 'order' => 'id DESC', 'limit' => $limit, 'conditions' => array ('log_id IN (?)', $ids))))
      return $this->output_json (array ());

    return $this->output_json (array_map (function ($text) {
      return array (
          'i' => $text->id,
          'c' => $text->text,
          't' => $text->log2->created_at->format ('Y-m-d H:i:s')
        );
    }, array_reverse ($texts)));
  }
}
