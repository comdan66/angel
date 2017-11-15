/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */

$(function () {
  var $choice = $('#choice-box');

  var choice = {
    k: 'angel-richmenus-' + $choice.data ('id') + '-users',
    get: function () { var a = window.fns._fsg (this.k); return a ? a : {}; },
    set: function (a) { window.fns._fss (this.k, a); return this; },
    has: function (id) { var a = this.get (); return typeof a[id] != 'undefined'; },
    add: function (id, obj) { var a = this.get (); a[id] = obj; return this.set (a); },
    del: function (id) { var a = this.get (); a[id] = null; delete a[id]; this.set (a); },
    clean: function () { this.set ({}); },
  };

  var $content = $('#choice-box > div');
  var $headerD = $('#choice-box > header > div');
  var $headerL = $('#choice-box #_b').prop ('checked', window.fns._fsg ('angel-richmenus-min') ? true : false).change (function () { window.fns._fss ('angel-richmenus-min', $(this).prop ('checked')); });
  var $headerA = $('#choice-box > header > a').click (function () { if (!confirm ('確定移除全部？')) return; choice.clean (); $('input[type="checkbox"][data-id][data-title]').prop ('checked', false); updateBox (); });

  function removeItem () { $('input[type="checkbox"][data-id="' + $(this).data ('id') + '"][data-title]').prop ('checked', false); choice.del ($(this).data ('id')); updateBox (); }
  function updateBox () { var l = Object.values (choice.get ()); $headerD.attr ('data-cnt', l.length); $content.empty ().append (l.map (function (t) { return $('<span />').append ($('<a />').addClass ('icon-bin').data ('id', t.id).click (removeItem)).append ($('<b />').text (t.title)).append ($('<input />').attr ('type', 'hidden').attr ('name', 'ids[]').val (t.id)); })); $choice.attr ('class', l.length ? 's' : ''); if (!l.length) window.fns._fss ('angel-richmenus-min', false); }
  updateBox ();

  $('input[type="checkbox"][data-id][data-title]').each (function () { $(this).prop ('checked', choice.has ($(this).data ('id'))); $(this).click (function () { if ($(this).prop ('checked')) choice.add ($(this).data ('id'), {id: $(this).data ('id'), title: $(this).data ('title')}); else choice.del ($(this).data ('id')); updateBox (); }); });
  $choice.submit (function () { choice.clean (); });
});