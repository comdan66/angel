<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

use LINE\LINEBot;

use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\VideoMessage;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\Event\MessageEvent\ImageMessage;
use LINE\LINEBot\Event\MessageEvent\AudioMessage;
use LINE\LINEBot\Event\MessageEvent\FileMessage;

use LINE\LINEBot\Event\JoinEvent;
use LINE\LINEBot\Event\LeaveEvent;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\UnfollowEvent;
use LINE\LINEBot\Event\PostbackEvent;

class Log extends OaModel {

  static $table_name = 'logs';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('source', 'class_name' => 'Source'),
  );

  const STATUS_INIT     = 1;
  const STATUS_CONTENT  = 2;
  const STATUS_MATCH    = 3;
  const STATUS_RESPONSE = 4;
  const STATUS_SUCCESS  = 5;

  static $statusNames = array (
    self::STATUS_INIT     => '不回應',
    self::STATUS_CONTENT  => '獲取內容',
    self::STATUS_MATCH    => '符合內容',
    self::STATUS_RESPONSE => '回應內容',
    self::STATUS_SUCCESS  => '回應成功',
  );
  
  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public static function getEventInstanceof ($event, &$callback) {
    if ($event instanceof TextMessage) { $callback = function ($event, $log, &$obj) { $params = array ('log_id' => $log->id, 'reply_token' => $event->getReplyToken () ? $event->getReplyToken () : '', 'message_id' => $event->getType () == 'message' ? $event->getMessageId () : '', 'text' => $event->getText ()); return LogText::transaction (function () use (&$obj, &$log, $params) { return verifyCreateOrm ($obj = LogText::create (array_intersect_key ($params, LogText::table ()->columns))) && $log->setStatus (Log::STATUS_CONTENT); }); }; return 'TextMessage'; };
    if ($event instanceof ImageMessage) { $callback = function ($event, $log, &$obj) { $params = array ('log_id' => $log->id, 'reply_token' => $event->getReplyToken () ? $event->getReplyToken () : '', 'message_id' => $event->getType () == 'message' ? $event->getMessageId () : '', 'file' => ''); return LogImage::transaction (function () use (&$obj, &$log, $params) { return verifyCreateOrm ($obj = LogImage::create (array_intersect_key ($params, LogImage::table ()->columns))) && $obj->putFile2S3 () && $log->setStatus (Log::STATUS_CONTENT); }); }; return 'ImageMessage'; };
    if ($event instanceof VideoMessage) { $callback = function ($event, $log, &$obj) { $params = array ('log_id' => $log->id, 'reply_token' => $event->getReplyToken () ? $event->getReplyToken () : '', 'message_id' => $event->getType () == 'message' ? $event->getMessageId () : '', 'file' => ''); return LogVideo::transaction (function () use (&$obj, &$log, $params) { return verifyCreateOrm ($obj = LogVideo::create (array_intersect_key ($params, LogVideo::table ()->columns))) && $obj->putFile2S3 () && $log->setStatus (Log::STATUS_CONTENT); }); }; return 'VideoMessage'; };
    if ($event instanceof AudioMessage) { $callback = function ($event, $log, &$obj) { $params = array ('log_id' => $log->id, 'reply_token' => $event->getReplyToken () ? $event->getReplyToken () : '', 'message_id' => $event->getType () == 'message' ? $event->getMessageId () : '', 'file' => ''); return LogAudio::transaction (function () use (&$obj, &$log, $params) { return verifyCreateOrm ($obj = LogAudio::create (array_intersect_key ($params, LogAudio::table ()->columns))) && $obj->putFile2S3 () && $log->setStatus (Log::STATUS_CONTENT); }); }; return 'AudioMessage'; };
    if ($event instanceof LocationMessage) { $callback = function ($event, $log, &$obj) { $params = array ('log_id' => $log->id, 'reply_token' => $event->getReplyToken () ? $event->getReplyToken () : '', 'message_id' => $event->getType () == 'message' ? $event->getMessageId () : '', 'title' => $event->getTitle (), 'address' => $event->getAddress (), 'latitude' => $event->getLatitude (), 'longitude' => $event->getLongitude ()); return LogLocation::transaction (function () use (&$obj, &$log, $params) { return verifyCreateOrm ($obj = LogLocation::create (array_intersect_key ($params, LogLocation::table ()->columns))) && $log->setStatus (Log::STATUS_CONTENT); }); }; return 'LocationMessage'; };
    if ($event instanceof StickerMessage) { $callback = function ($event, $log, &$obj) { $params = array ('log_id' => $log->id, 'reply_token' => $event->getReplyToken () ? $event->getReplyToken () : '', 'message_id' => $event->getType () == 'message' ? $event->getMessageId () : '', 'package_id' => $event->getPackageId (), 'sticker_id' => $event->getStickerId ()); return LogSticker::transaction (function () use (&$obj, &$log, $params) { return verifyCreateOrm ($obj = LogSticker::create (array_intersect_key ($params, LogSticker::table ()->columns))) && $log->setStatus (Log::STATUS_CONTENT); }); }; return 'StickerMessage'; };
    if ($event instanceof FileMessage) { $callback = function ($event, $log, &$obj) { $params = array ('log_id' => $log->id, 'reply_token' => $event->getReplyToken () ? $event->getReplyToken () : '', 'message_id' => $event->getType () == 'message' ? $event->getMessageId () : '', 'name' => $event->getFileName (), 'size' => $event->getFileSize (), 'file' => ''); return LogFile::transaction (function () use (&$obj, &$log, $params) { return verifyCreateOrm ($obj = LogFile::create (array_intersect_key ($params, LogFile::table ()->columns))) && $obj->putFile2S3 () && $log->setStatus (Log::STATUS_CONTENT); }); }; return 'FileMessage'; };
    if ($event instanceof PostbackEvent) { $callback = function ($event, $log, &$obj) { $params = array ('log_id' => $log->id, 'reply_token' => $event->getReplyToken () ? $event->getReplyToken () : '', 'data' => $event->getPostbackData (), 'params' => $event->getPostbackParams () ? json_encode ($event->getPostbackParams ()) : ''); return LogPostback::transaction (function () use (&$obj, &$log, $params) { return verifyCreateOrm ($obj = LogPostback::create (array_intersect_key ($params, LogPostback::table ()->columns))) && $log->setStatus (Log::STATUS_CONTENT); }); }; return 'PostbackEvent'; };
    if ($event instanceof FollowEvent) { $callback = function ($event, $log, &$obj) { $params = array ('log_id' => $log->id, 'reply_token' => $event->getReplyToken () ? $event->getReplyToken () : ''); return LogFollow::transaction (function () use (&$obj, &$log, $params) { return verifyCreateOrm ($obj = LogFollow::create (array_intersect_key ($params, LogFollow::table ()->columns))) && $log->setStatus (Log::STATUS_CONTENT); }); }; return 'FollowEvent'; };
    if ($event instanceof JoinEvent) { $callback = function ($event, $log, &$obj) { $params = array ('log_id' => $log->id, 'reply_token' => $event->getReplyToken () ? $event->getReplyToken () : ''); return LogJoin::transaction (function () use (&$obj, &$log, $params) { return verifyCreateOrm ($obj = LogJoin::create (array_intersect_key ($params, LogJoin::table ()->columns))) && $log->setStatus (Log::STATUS_CONTENT); }); }; return 'JoinEvent'; };
    if ($event instanceof UnfollowEvent) { $callback = function ($event, $log, &$obj) { $params = array ('log_id' => $log->id); return LogUnfollow::transaction (function () use (&$obj, &$log, $params) { return verifyCreateOrm ($obj = LogUnfollow::create (array_intersect_key ($params, LogUnfollow::table ()->columns))) && $log->setStatus (Log::STATUS_CONTENT); }); }; return 'UnfollowEvent'; };
    if ($event instanceof LeaveEvent) { $callback = function ($event, $log, &$obj) { $params = array ('log_id' => $log->id); return LogLeave::transaction (function () use (&$obj, &$log, $params) { return verifyCreateOrm ($obj = LogLeave::create (array_intersect_key ($params, LogLeave::table ()->columns))) && $log->setStatus (Log::STATUS_CONTENT); }); }; return 'LeaveEvent'; };

    $callback = null;
    return '';
  }
  public static function createAndInfo ($source, $event, &$info) {
    $params = array (
      'source_id' => $source->id,
      'instanceof' => Log::getEventInstanceof ($event, $callback),
      'timestamp' => $event->getTimestamp (),
      'status' => Log::STATUS_INIT);

    if (!Log::transaction (function () use (&$log, $params) { return verifyCreateOrm ($log = Log::create (array_intersect_key ($params, Log::table ()->columns))); }))
      return false;

    if ($callback && is_callable ($callback))
      return $callback ($event, $log, $info) ? $log : false;

    return false;
  }
  public function setStatus ($status) {
    if (!(isset ($this->id, $this->status) && in_array ($status, array_keys (Log::$statusNames)))) return false;
    $this->status = $status;
    return $this->save ();
  }
}