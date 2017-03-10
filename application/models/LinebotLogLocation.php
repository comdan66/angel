<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;

class LinebotLogLocation extends OaLineModel {

  static $table_name = 'linebot_log_locations';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('log', 'class_name' => 'LinebotLog'),
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function reply ($bot, MessageBuilder $build) {
    $this->log->setStatus (LinebotLog::STATUS_RESPONSE);
    $response = $bot->replyMessage ($this->log->reply_token, $build);

    if (!$response->isSucceeded ()) return false;
    $this->log->setStatus (LinebotLog::STATUS_SUCCESS);
    return true;
  }
  public function searchProducts ($bot) {
    $this->log->setStatus (LinebotLog::STATUS_MATCH);
    $builder = new TemplateMessageBuilder ('有個問題需要被解答！', new ConfirmTemplateBuilder ('請問您是要知道這附近的？', array (new MessageTemplateActionBuilder ('天氣概況', '我想知道這附近的天氣概況(' . $this->latitude . ', ' . $this->longitude . ')'), new MessageTemplateActionBuilder ('美食店家', '我想知道這附近的美食(' . $this->latitude . ', ' . $this->longitude . ')'))));
    return $this->reply ($bot, $builder);
  }
}