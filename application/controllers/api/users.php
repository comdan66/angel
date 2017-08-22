<?php defined ('BASEPATH') OR exit ('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

class Send extends Api_controller {

  public function __construct () {
    parent::__construct ();
  }

  /**
   * @api {get} /users 取得使用者
   * @apiGroup User
   *
   * @apiSuccess {String}   id          User ID
   * @apiSuccess {String}   title       使用者名稱
   *
   * @apiSuccessExample {json} Success Response:
   *     HTTP/1.1 200 OK
   *     [
   *         {
   *             "id": "U...",
   *             "title": "吳政賢"
   *         }
   *     ]
   *
   * @apiError   {String}    message     錯誤原因
   *
   * @apiErrorExample {json} Error-Response:
   *     HTTP/1.1 405 Error
   *     {
   *         "message": "參數錯誤"
   *     }
   */
  public function users () {
    return $this->output_json (array_map (function ($source) {
      return array ('id' => $source->sid, 'title' => $source->title);
    }, Source::find ('all', array ('select' => 'title, sid', 'conditions' => array ('status = ? AND title != ?', Source::STATUS_JOIN, '')))));
  }
}
