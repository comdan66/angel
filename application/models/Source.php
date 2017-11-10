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
  public function updateTitle () {
    if (!(isset ($this->id) && isset ($this->sid) && isset ($this->title) && isset ($this->type) && ($this->type == Source::TYPE_USER) && !$this->title))
      return false;

    $this->CI->load->library ('OALineBot');


    if (!(($oaLineBot = OALineBot::create ()) && ($response = $oaLineBot->bot ()->getProfile ($this->sid)) && $response->isSucceeded () && ($profile = $response->getJSONDecodedBody ()) && isset ($profile['displayName'])))
      return false;
    
    $this->title = $profile['displayName'];
    return $this->save ();
  }

  // public function isManyUser () {
  //   return isset ($this->type) && in_array ($this->type, array (Source::TYPE_GROUP, Source::TYPE_ROOM));
  // }
  // public function isUser () {
  //   return isset ($this->type) && in_array ($this->type, array (Source::TYPE_USER));
  // }

  public static function findOrCreateSource ($event) {
    if (!$sid = $event->getEventSourceId ())
      return null;
    
    $params = array (
      'sid' => $sid,
      'title' => '',
      'type' => Source::getType ($event)
    );

    if (!$source = Source::find ('one', array ('select' => 'id, sid, title, type', 'conditions' => array ('sid = ?', $params['sid']))))
      if (!Source::transaction (function () use (&$source, $params) { return verifyCreateOrm ($source = Source::create (array_intersect_key ($params, Source::table ()->columns))); }))
        return null;
    
    $source->updateTitle ();

    return $source;
  }
  public static function findOrCreateSpeaker ($event) {
    if (!$userId = $event->getUserId ()) return ;
    
    $params = array (
      'sid' => $userId,
      'title' => '',
      'type' => Source::TYPE_USER
    );

    if (!$speaker = Source::find ('one', array ('select' => 'id, sid, title, type', 'conditions' => array ('sid = ?', $params['sid']))))
      if (!Source::transaction (function () use (&$speaker, $params) { return verifyCreateOrm ($speaker = Source::create (array_intersect_key ($params, Source::table ()->columns))); }))
        return null;
    
    $speaker->updateTitle ();

    return $speaker;
  }
















  // public static function getSource ($event, &$say) {
  //   if (!$sid = $event->getEventSourceId ()) return null;

  //   if (!($source = Source::find ('one', array ('select' => 'id, sid, title, type', 'conditions' => array ('sid = ?', $sid)))))
  //     if (!(($params = array ('sid' => $sid, 'title' => '', 'type' => Source::getType ($event))) && Source::transaction (function () use (&$source, $params) { return verifyCreateOrm ($source = Source::create (array_intersect_key ($params, Source::table ()->columns))); })))
  //       return null;

  //   if ($source->isManyUser ()) {
  //     $userId = $event->getUserId ();

  //     if (!($say = Source::find ('one', array ('select' => 'id, sid, title, type', 'conditions' => array ('sid = ?', $userId)))))
  //       if (!(($params = array ('sid' => $userId, 'title' => '', 'type' => Source::TYPE_USER)) && Source::transaction (function () use (&$say, $params) { return verifyCreateOrm ($say = Source::create (array_intersect_key ($params, Source::table ()->columns))); })))
  //         $say = null;

  //     if ($say) $say->updateTitle ();
  //   } else if ($source->isUser ()) {
  //     $say = $source;
  //   }

  //   $source->updateTitle ();

  //   return $source;
  // }

}