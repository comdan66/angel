<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class YoutubeGet {

  public function __construct () {
  }

  public static function search ($options = array ()) {
    $CI  =& get_instance ();
    $CI->load->library ('Google/Google');

    $client = new Google_Client ();
    $client->setDeveloperKey (Cfg::setting ('google', ENVIRONMENT, 'server_key'));
    $youtube = new Google_Service_YouTube ($client);

    try {
      return array_map (function ($item) {
        return YoutubeGet::googleSearchResultSnippetFormat ($item);
      }, $youtube->search->listSearch ('id, snippet', array_merge (array (
                      'type' => 'video'
                    ), $options))->items);
    } catch (Exception $e) {
      return array ();
    }
  }
  public static function googleSearchResultSnippetFormat ($item) {
    $sizes = array ('getDefault', 'getHigh', 'getMaxres', 'getMedium', 'getStandard');
    $id = is_a ($item, 'Google_Service_YouTube_SearchResult') ? $item->id->videoId : (is_a ($item, 'Google_Service_YouTube_Video') ? $item->id : '');

    return $id && isset ($item->snippet) ? array (
          'id' => $id,
          'content' => isset ($item->snippet->content) ? $item->snippet->content : '',
          'title' => isset ($item->snippet->title) ? $item->snippet->title : '',
          'tags' => isset ($item->snippet->tags) ? $item->snippet->tags : array (),
          'publishedAt' => isset ($item->snippet->publishedAt) ? $item->snippet->publishedAt : '',
          'thumbnails' => isset ($item->snippet->thumbnails) ? array_values (array_filter (array_map (function ($size) use ($item) {
              if (!method_exists ($item->snippet->thumbnails, $size))
                return null;
      
              $thumbnail = call_user_func_array (array ($item->snippet->thumbnails, $size), array ());
      
              if (!isset ($thumbnail->url))
                return null;

              return array_merge (array ('url' => $thumbnail->url), isset ($thumbnail->width) && isset ($thumbnail->height) ? array (
                    'width' => $thumbnail->width,
                    'height' => $thumbnail->height
                  ) : array ());
            }, $sizes))) : array (),
        ) : array ();
  }

}
