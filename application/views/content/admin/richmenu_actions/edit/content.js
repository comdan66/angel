/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

$(function () {
  function randerAction1 (val) {
    return $('<div />').addClass ('row').addClass ('nl').append (
      $('<b />').addClass ('need').text ('訊息')).append (
      $('<input />').attr ('type', 'text').attr ('name', 'text').attr ('placeholder', '請輸入訊息..').attr ('maxlength', 300).attr ('pattern', '.{1,300}').prop ('required', true).attr ('title', '輸入訊息!').val (typeof val === 'undefined' ? '' : val));
  }
  function randerAction2 (val) {
    return $('<div />').addClass ('row').addClass ('nl').append (
      $('<b />').addClass ('need').text ('網址')).append (
      $('<input />').attr ('type', 'text').attr ('name', 'uri').attr ('placeholder', '請輸入網址..').attr ('maxlength', 1000).attr ('pattern', '.{1,1000}').prop ('required', true).attr ('title', '輸入網址!').val (typeof val === 'undefined' ? '' : val));
  }
  function randerAction3 (val) {
    return $('<div />').addClass ('row').addClass ('nl').append (
      $('<b />').addClass ('need').text ('回傳資料')).append (
      $('<input />').attr ('type', 'text').attr ('name', 'data').attr ('placeholder', '請輸入點擊回傳資料..').attr ('maxlength', 300).attr ('pattern', '.{1,300}').prop ('required', true).attr ('title', '輸入點擊回傳資料!').val (typeof val === 'undefined' ? '' : val));
  }
  function randerAction4 (options, val, data) {
    var arr = []; for (var key in options) arr.push ({'key': key, 'value': options[key]});

    return $('<div />').addClass ('row').addClass ('nl').append (
      $('<b />').addClass ('need').text ('時間類型')).append (
      $('<select />').attr ('name', 'action_pick_type').append (
        arr.map (function (t, i) {
          return $('<option />').text (t.value).attr ('value', t.key);
        }))).add (randerAction3 (data));
  }

  var $actionTypeArea = $('#action_type_area');
  $('#action_type').change (function () {
    $actionTypeArea.empty ();
    if ($(this).val () == 1) $actionTypeArea.append (randerAction1 ($(this).data ('text')));
    else if ($(this).val () == 2) $actionTypeArea.append (randerAction2 ($(this).data ('uri')));
    else if ($(this).val () == 3) $actionTypeArea.append (randerAction3 ($(this).data ('data')));
    else $actionTypeArea.append (randerAction4 ($(this).data ('picks'), $(this).data ('action_pick_type'), $(this).data ('data')));
  }).change ();

});