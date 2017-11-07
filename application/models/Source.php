<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class Source extends OaModel {

  static $table_name = 'sources';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  const TYPE_USER    = 1;
  const TYPE_GROUP   = 2;
  const TYPE_ROOM    = 3;
  const TYPE_OTHER   = 4;

  static $typeNames = array (
    self::TYPE_USER   => '使用者',
    self::TYPE_GROUP  => '群組',
    self::TYPE_ROOM   => '聊天室',
    self::TYPE_OTHER  => '其他',
  );
  
  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public static function getType ($event) {
    if ($event->isUserEvent ()) return Source::TYPE_USER;
    if ($event->isGroupEvent ()) return Source::TYPE_GROUP;
    if ($event->isRoomEvent ()) return Source::TYPE_ROOM;
    return Source::TYPE_OTHER;
  }
  public static function getSource ($event, &$say) {
    if (!$sid = $event->getEventSourceId ()) return null;

    if (!($source = Source::find ('one', array ('select' => 'id, sid, title, type', 'conditions' => array ('sid = ?', $sid)))))
      if (!(($params = array ('sid' => $sid, 'title' => '', 'type' => Source::getType ($event))) && Source::transaction (function () use (&$source, $params) { return verifyCreateOrm ($source = Source::create (array_intersect_key ($params, Source::table ()->columns))); })))
        return null;

    if (in_array ($source->type, array (Source::TYPE_GROUP, Source::TYPE_ROOM))) {
      $userId = $event->getUserId ();

      if (!($say = Source::find ('one', array ('select' => 'id, sid, title, type', 'conditions' => array ('sid = ?', $userId)))))
        if (!(($params = array ('sid' => $userId, 'title' => '', 'type' => Source::TYPE_USER)) && Source::transaction (function () use (&$say, $params) { return verifyCreateOrm ($say = Source::create (array_intersect_key ($params, Source::table ()->columns))); })))
          $say = null;

      if ($say) $say->updateTitle ();
    }

    $source->updateTitle ();

    return $source;
  }
  public function updateTitle () {
    if (!(($this->type == Source::TYPE_USER) && isset ($this->id) && isset ($this->sid) && isset ($this->title) && !$this->title && isset ($this->type)))
      return false;

    $this->CI->load->library ('OALineBot');
    if (!$oaLineBot = OALineBot::create ())
      return false;

    $response = $oaLineBot->bot ()->getProfile ($this->sid);
    if (!$response->isSucceeded ())
      return false;

    $profile = $response->getJSONDecodedBody ();
    $this->title = $profile['displayName'];
    return $this->save ();
        // echo $profile['displayName'];
        // echo $profile['pictureUrl'];
        // echo $profile['statusMessage'];
  }
}