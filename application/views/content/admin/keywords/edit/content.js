/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 * @link        http://www.ioa.tw/
 */

$(function () {
  var $method = $('#method');
  var $tip = $('#tip');
  var $keyowrd_content = $('#keyowrd_content');
  var $add = $('._add');

  function check () {
    if ($method.val () == '1') {
      $tip.removeClass ('s');
      $keyowrd_content.removeClass ('h');
      $('#keyowrd_content input, #keyowrd_content button').prop ('disabled', false);
    } else {
      $tip.addClass ('s');
      $keyowrd_content.addClass ('h');
      $('#keyowrd_content input, #keyowrd_content button').prop ('disabled', true);
    }
  }
  $method.change (check);
  check ();

  function add (text) {
    return $('<div />').addClass ('contents').append (
      $('<input />').attr ('type', 'text').attr ('placeholder', '請輸入回覆內容..').attr ('name', 'contents[]').val (text)).append (
      $('<button />').attr ('type', 'button').text ('-').click (function () {
        $(this).parent ().remove ();
      })).insertBefore ($add.parent ());
  }
  $add.data ('val').forEach (add);
  $add.click (function () { add (''); }).click ();
});