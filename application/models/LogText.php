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
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;

class LogText extends OaLineModel {

  static $table_name = 'log_texts';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
    array ('log', 'class_name' => 'Log'),
  );
  const KEYWORD = 'k';

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);
  }
  public function reply ($bot, MessageBuilder $build) {
    if (!$build) return false;

    $this->log->setStatus (Log::STATUS_RESPONSE);
    $response = $bot->replyMessage ($this->log->reply_token, $build);

    if (!$response->isSucceeded ()) return false;
    $this->log->setStatus (Log::STATUS_SUCCESS);
    return true;
  }

  public static function regex ($pattern, $str) {
    
    $pattern = !preg_match ('/\(\?P<' . LogText::KEYWORD . '>.+\)/', $pattern) ? '/(?P<' . LogText::KEYWORD . '>(' . $pattern . '))/i' : ('/(' . $pattern . ')/i');

    preg_match_all ($pattern, $str, $result);
    if (!(isset ($result[LogText::KEYWORD]) && $result[LogText::KEYWORD])) return array ();
    return array_filter (preg_split ('/[\s,]+/', $result[LogText::KEYWORD][0]), function ($t) { return $t; });
  }

  private function match () {
    $type = array (Keyword::TYPE_ALL);
    switch ($this->log->source_type) {
      case 'user': array_push ($type, Keyword::TYPE_USER); break;
      case 'group': array_push ($type, Keyword::TYPE_GROUP); break;
      case 'room': array_push ($type, Keyword::TYPE_ROOM); break;
      default: $type = array (Keyword::TYPE_ALL); break;
    }
    $conditions = array ('type IN (?)', $type);

    $limit = 10;
    $total = Keyword::count (array ('conditions' => $conditions));

    for ($offset = 0; $offset < $total; $offset += $limit)
      foreach (Keyword::find ('all', array ('select' => 'id,pattern, method, weight', 'order' => 'weight DESC', 'include' => array ('contents'), 'limit' => $limit, 'offset' => $offset, 'conditions' => $conditions)) as $keyword)
        if (($keys = LogText::regex ($keyword->pattern, $this->text)) && ($keyword->weight = $keyword->weight + 1) && ($keyword->save ()))
          return array (
              'keys' => $keys,
              'keyword' => $keyword,
            );
        
    return array ();
  }
  private function replyFlickr ($keys) {
    $this->CI->load->library ('CreateDemo');
    if (!$datas = CreateDemo::pics (4, 5, $keys)) return new TextMessageBuilder ('哭哭，找不到你想要的 ' . implode (' ', $keys) . ' 耶..');

    return new TemplateMessageBuilder (mb_strimwidth (implode (',', $keys) . ' 來囉！', 0, 198 * 2, '…','UTF-8'), new CarouselTemplateBuilder (array_map (function ($data) {
        return new CarouselColumnTemplateBuilder (
          mb_strimwidth ($data['title'], 0, 18 * 2, '…','UTF-8'),
          mb_strimwidth ($data['title'], 0, 28 * 2, '…','UTF-8'),
          $data['url'],
          array (new UriTemplateActionBuilder (mb_strimwidth ('我要看 ' . $data['title'], 0, 8 * 2, '…','UTF-8'), $data['page']))
      ); }, $datas)));
  }
  private function replyYoutube ($keys) {
    $this->CI->load->library ('YoutubeGet');
    if (!$datas = YoutubeGet::search (array ('q' => implode (' ', $keys), 'maxResults' => rand (3, 5)))) return new TextMessageBuilder ('哭哭，找不到你想要的 ' . implode (' ', $keys) . ' 耶..');

    return new TemplateMessageBuilder (mb_strimwidth (implode (',', $keys) . ' 來囉！', 0, 198 * 2, '…','UTF-8'), new CarouselTemplateBuilder (array_map (function ($data) {
      return new CarouselColumnTemplateBuilder (
        mb_strimwidth ($data['title'], 0, 18 * 2, '…','UTF-8'),
        mb_strimwidth ($data['title'], 0, 28 * 2, '…','UTF-8'),
        $data['thumbnails'][count ($data['thumbnails']) - 1]['url'],
        array (new UriTemplateActionBuilder (mb_strimwidth ('我要聽 ' . $data['title'], 0, 8 * 2, '…','UTF-8'), 'https://www.youtube.com/watch?v=' . $data['id']))
      );
    }, $datas)));
  }
  private function replyAlleyKeyword ($keys) {
    $this->CI->load->library ('AlleyGet');
    if (!$datas = AlleyGet::search (implode (' ', $keys))) return null;


    return new TemplateMessageBuilder (mb_strimwidth ('123' . ' 來囉！', 0, 190 * 2, '…','UTF-8'), new CarouselTemplateBuilder (array_map (function ($data) {

// LogText::trace ('===> ' . mb_strimwidth (implode (',', $keys) . ' 來囉！', 0, 198 * 2, '…','UTF-8'));
// LogText::trace ('===> ' . mb_strimwidth ($data['title'], 0, 18 * 2, '…','UTF-8'));
// LogText::trace ('===> ' . mb_strimwidth ($data['desc'], 0, 28 * 2, '…','UTF-8'));
// LogText::trace ('===> ' . $data['img']);
// LogText::trace ('===> ' . mb_strimwidth ('我要吃 ' . $data['title'], 0, 8 * 2, '…','UTF-8'));
// LogText::trace ('===> ' . $data['url']);

      return new CarouselColumnTemplateBuilder (
        mb_strimwidth ('123', 0, 15 * 2, '…','UTF-8'),
        mb_strimwidth ('123', 0, 25 * 2, '…','UTF-8'),
        'http://imagealley.friday.tw/data/list/788/8788.jpg',
        array (new UriTemplateActionBuilder (mb_strimwidth ('我要吃 ' . '123', 0, 5 * 2, '…','UTF-8'), '1234'))
      );
    }, $datas)));
  }
  private function replyAlleyReCommend ($keys) {
    $this->CI->load->library ('AlleyGet');
    if (!$datas = AlleyGet::recommend ()) return null;

    return new TemplateMessageBuilder (mb_strimwidth (implode (',', $keys) . ' 來囉！', 0, 198 * 2, '…','UTF-8'), new CarouselTemplateBuilder (array_map (function ($data) {
      return new CarouselColumnTemplateBuilder (
        mb_strimwidth ($data['title'], 0, 18 * 2, '…','UTF-8'),
        mb_strimwidth ($data['desc'], 0, 28 * 2, '…','UTF-8'),
        $data['img'],
        array (new UriTemplateActionBuilder (mb_strimwidth ('我要吃 ' . $data['title'], 0, 8 * 2, '…','UTF-8'), $data['url']))
      );
    }, $datas)));
  }
  private function replyText ($contents) {
    return new TextMessageBuilder ($contents[array_rand ($contents)]->text);
  }
  public function compare ($bot) {
    if (!isset ($this->text)) return false;
    if (!$match = $this->match ()) return false;
    $this->log->setStatus (Log::STATUS_MATCH);
    
    switch ($match['keyword']->method) {
      
      case Keyword::METHOD_TEXT:
        return $this->reply ($bot, $this->replyText ($match['keyword']->contents));
        break;
      case Keyword::METHOD_ALLEY_KEYWORD:
        return $this->reply ($bot, $this->replyAlleyKeyword ($match['keys']));
        break;
      
      case Keyword::METHOD_YOUTUBE:
        return $this->reply ($bot, $this->replyYoutube ($match['keys']));
        break;
      
      case Keyword::METHOD_FLICKR:
        return $this->reply ($bot, $this->replyFlickr ($match['keys']));
        break;
      
      case Keyword::METHOD_ALLEY_RECOMMEND:
        return $this->reply ($bot, $this->replyAlleyReCommend ($match['keys']));
        break;
      
      default:
        return false;
        break;
    }
    return false;
  }

  public function searchLocation ($bot) {
    $pattern = '附近的?美食\s*\((?P<' . LogText::KEYWORD . '>(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?))\)';

    if (!(isset ($this->text) && ($keys = LogText::regex ($pattern, $this->text)))) return false;

    $this->log->setStatus (Log::STATUS_MATCH);
    $this->CI->load->library ('AlleyGet');

    if (!$datas = AlleyGet::products ($keys[0], $keys[1])) return $this->reply ($bot, new TextMessageBuilder ('哭哭，這附近沒什麼美食耶..'));

    $builder = new TemplateMessageBuilder (mb_strimwidth ('附近好吃的美食來囉！', 0, 198 * 2, '…','UTF-8'), new CarouselTemplateBuilder (array_map (function ($store) {
        return new CarouselColumnTemplateBuilder (
          mb_strimwidth ($store['title'], 0, 18 * 2, '…','UTF-8'),
          mb_strimwidth ($store['desc'], 0, 28 * 2, '…','UTF-8'),
          $store['img'],
          array (new UriTemplateActionBuilder (mb_strimwidth ('我要吃 ' . $store['title'], 0, 8 * 2, '…','UTF-8'), $store['url']))
        );
      }, $datas)));

    return $this->reply ($bot, $builder);
  }
  public function searchWeather ($bot) {
    $pattern = '附近的?天氣.*\s*\((?P<' . LogText::KEYWORD . '>(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?))\)';

    if (!(isset ($this->text) && ($keys = LogText::regex ($pattern, $this->text)))) return false;

    $this->log->setStatus (Log::STATUS_MATCH);
    $this->CI->load->library ('WeatherGet');

    if (!$datas = WeatherGet::getByLatLng ($keys[0], $keys[1])) return $this->reply ($bot, new TextMessageBuilder ('哭哭，目前沒有此處的資料耶..'));

    $builder = new TemplateMessageBuilder (mb_strimwidth ($datas['title'], 0, 8 * 2, '…','UTF-8'), new ButtonTemplateBuilder (mb_strimwidth ($datas['title'], 0, 18 * 2, '…','UTF-8'), mb_strimwidth ($datas['desc'], 0, 28 * 2, '…','UTF-8'), $datas['img'], array (new UriTemplateActionBuilder ('詳細內容', $datas['url']))));

    return $this->reply ($bot, $builder);
  }
}