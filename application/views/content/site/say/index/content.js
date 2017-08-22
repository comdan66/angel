/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2016 OA Wu Design
 */
var imgLiquid=imgLiquid||{VER:"0.9.944"};imgLiquid.bgs_Available=!1,imgLiquid.bgs_CheckRunned=!1,imgLiquid.injectCss=".imgLiquid img {visibility:hidden}",function(i){function t(){if(!imgLiquid.bgs_CheckRunned){imgLiquid.bgs_CheckRunned=!0;var t=i('<span style="background-size:cover" />');i("body").append(t),!function(){var i=t[0];if(i&&window.getComputedStyle){var e=window.getComputedStyle(i,null);e&&e.backgroundSize&&(imgLiquid.bgs_Available="cover"===e.backgroundSize)}}(),t.remove()}}i.fn.extend({imgLiquid:function(e){this.defaults={fill:!0,verticalAlign:"center",horizontalAlign:"center",useBackgroundSize:!0,useDataHtmlAttr:!0,responsive:!0,delay:0,fadeInTime:0,removeBoxBackground:!0,hardPixels:!0,responsiveCheckTime:500,timecheckvisibility:500,onStart:null,onFinish:null,onItemStart:null,onItemFinish:null,onItemError:null},t();var a=this;return this.options=e,this.settings=i.extend({},this.defaults,this.options),this.settings.onStart&&this.settings.onStart(),this.each(function(t){function e(){-1===u.css("background-image").indexOf(encodeURI(c.attr("src")))&&u.css({"background-image":'url("'+encodeURI(c.attr("src"))+'")'}),u.css({"background-size":g.fill?"cover":"contain","background-position":(g.horizontalAlign+" "+g.verticalAlign).toLowerCase(),"background-repeat":"no-repeat"}),i("a:first",u).css({display:"block",width:"100%",height:"100%"}),i("img",u).css({display:"none"}),g.onItemFinish&&g.onItemFinish(t,u,c),u.addClass("imgLiquid_bgSize"),u.addClass("imgLiquid_ready"),l()}function d(){function e(){c.data("imgLiquid_error")||c.data("imgLiquid_loaded")||c.data("imgLiquid_oldProcessed")||(u.is(":visible")&&c[0].complete&&c[0].width>0&&c[0].height>0?(c.data("imgLiquid_loaded",!0),setTimeout(r,t*g.delay)):setTimeout(e,g.timecheckvisibility))}if(c.data("oldSrc")&&c.data("oldSrc")!==c.attr("src")){var a=c.clone().removeAttr("style");return a.data("imgLiquid_settings",c.data("imgLiquid_settings")),c.parent().prepend(a),c.remove(),c=a,c[0].width=0,setTimeout(d,10),void 0}return c.data("imgLiquid_oldProcessed")?(r(),void 0):(c.data("imgLiquid_oldProcessed",!1),c.data("oldSrc",c.attr("src")),i("img:not(:first)",u).css("display","none"),u.css({overflow:"hidden"}),c.fadeTo(0,0).removeAttr("width").removeAttr("height").css({visibility:"visible","max-width":"none","max-height":"none",width:"auto",height:"auto",display:"block"}),c.on("error",n),c[0].onerror=n,e(),o(),void 0)}function o(){(g.responsive||c.data("imgLiquid_oldProcessed"))&&c.data("imgLiquid_settings")&&(g=c.data("imgLiquid_settings"),u.actualSize=u.get(0).offsetWidth+u.get(0).offsetHeight/1e4,u.sizeOld&&u.actualSize!==u.sizeOld&&r(),u.sizeOld=u.actualSize,setTimeout(o,g.responsiveCheckTime))}function n(){c.data("imgLiquid_error",!0),u.addClass("imgLiquid_error"),g.onItemError&&g.onItemError(t,u,c),l()}function s(){var i={};if(a.settings.useDataHtmlAttr){var t=u.attr("data-imgLiquid-fill"),e=u.attr("data-imgLiquid-horizontalAlign"),d=u.attr("data-imgLiquid-verticalAlign");("true"===t||"false"===t)&&(i.fill=Boolean("true"===t)),void 0===e||"left"!==e&&"center"!==e&&"right"!==e&&-1===e.indexOf("%")||(i.horizontalAlign=e),void 0===d||"top"!==d&&"bottom"!==d&&"center"!==d&&-1===d.indexOf("%")||(i.verticalAlign=d)}return imgLiquid.isIE&&a.settings.ieFadeInDisabled&&(i.fadeInTime=0),i}function r(){var i,e,a,d,o,n,s,r,m=0,h=0,f=u.width(),v=u.height();void 0===c.data("owidth")&&c.data("owidth",c[0].width),void 0===c.data("oheight")&&c.data("oheight",c[0].height),g.fill===f/v>=c.data("owidth")/c.data("oheight")?(i="100%",e="auto",a=Math.floor(f),d=Math.floor(f*(c.data("oheight")/c.data("owidth")))):(i="auto",e="100%",a=Math.floor(v*(c.data("owidth")/c.data("oheight"))),d=Math.floor(v)),o=g.horizontalAlign.toLowerCase(),s=f-a,"left"===o&&(h=0),"center"===o&&(h=.5*s),"right"===o&&(h=s),-1!==o.indexOf("%")&&(o=parseInt(o.replace("%",""),10),o>0&&(h=.01*s*o)),n=g.verticalAlign.toLowerCase(),r=v-d,"left"===n&&(m=0),"center"===n&&(m=.5*r),"bottom"===n&&(m=r),-1!==n.indexOf("%")&&(n=parseInt(n.replace("%",""),10),n>0&&(m=.01*r*n)),g.hardPixels&&(i=a,e=d),c.css({width:i,height:e,"margin-left":Math.floor(h),"margin-top":Math.floor(m)}),c.data("imgLiquid_oldProcessed")||(c.fadeTo(g.fadeInTime,1),c.data("imgLiquid_oldProcessed",!0),g.removeBoxBackground&&u.css("background-image","none"),u.addClass("imgLiquid_nobgSize"),u.addClass("imgLiquid_ready")),g.onItemFinish&&g.onItemFinish(t,u,c),l()}function l(){t===a.length-1&&a.settings.onFinish&&a.settings.onFinish()}var g=a.settings,u=i(this),c=i("img:first",u);return c.length?(c.data("imgLiquid_settings")?(u.removeClass("imgLiquid_error").removeClass("imgLiquid_ready"),g=i.extend({},c.data("imgLiquid_settings"),a.options)):g=i.extend({},a.settings,s()),c.data("imgLiquid_settings",g),g.onItemStart&&g.onItemStart(t,u,c),imgLiquid.bgs_Available&&g.useBackgroundSize?e():d(),void 0):(n(),void 0)})}})}(jQuery),!function(){var i=imgLiquid.injectCss,t=document.getElementsByTagName("head")[0],e=document.createElement("style");e.type="text/css",e.styleSheet?e.styleSheet.cssText=i:e.appendChild(document.createTextNode(i)),t.appendChild(e)}();
!function(t){"function"==typeof define&&define.amd?define(["jquery"],t):t(jQuery)}(function(t){function e(){var e=a(this),o=n.settings,s=t(this).data("tag");return isNaN(e.datetime)||(0==o.cutoff||r(e.datetime)<o.cutoff)&&(""==s||void 0===s?t(this).text(i(e.datetime)):t(this).data(s,i(e.datetime))),this}function a(e){if(!(e=t(e)).data("timeago")){e.data("timeago",{datetime:n.datetime(e)});var a=t.trim(e.text());n.settings.localeTitle?e.attr("title",e.data("timeago").datetime.toLocaleString()):!(a.length>0)||n.isTime(e)&&e.attr("title")||e.attr("title",a)}return e.data("timeago")}function i(t){return n.inWords(r(t))}function r(t){return(new Date).getTime()-t.getTime()}t.timeago=function(e){return i(e instanceof Date?e:"string"==typeof e?t.timeago.parse(e):"number"==typeof e?new Date(e):t.timeago.datetime(e))};var n=t.timeago;t.extend(t.timeago,{settings:{refreshMillis:6e4,allowFuture:!1,localeTitle:!1,cutoff:0,strings:{prefixAgo:null,prefixFromNow:null,suffixAgo:"ago",suffixFromNow:"from now",seconds:"less than a minute",minute:"about a minute",minutes:"%d minutes",hour:"about an hour",hours:"about %d hours",day:"a day",days:"%d days",month:"about a month",months:"%d months",year:"about a year",years:"%d years",wordSeparator:" ",numbers:[]}},inWords:function(e){function a(a,r){var n=t.isFunction(a)?a(r,e):a,o=i.numbers&&i.numbers[r]||r;return n.replace(/%d/i,o)}var i=this.settings.strings,r=i.prefixAgo,n=i.suffixAgo;this.settings.allowFuture&&e<0&&(r=i.prefixFromNow,n=i.suffixFromNow);var o=Math.abs(e)/1e3,s=o/60,u=s/60,m=u/24,d=m/365,l=o<45&&a(i.seconds,Math.round(o))||o<90&&a(i.minute,1)||s<45&&a(i.minutes,Math.round(s))||s<90&&a(i.hour,1)||u<24&&a(i.hours,Math.round(u))||u<42&&a(i.day,1)||m<30&&a(i.days,Math.round(m))||m<45&&a(i.month,1)||m<365&&a(i.months,Math.round(m/30))||d<1.5&&a(i.year,1)||a(i.years,Math.round(d)),f=i.wordSeparator||"";return void 0===i.wordSeparator&&(f=" "),t.trim([r,l,n].join(f))},parse:function(e){var a=t.trim(e);return a=a.replace(/\.\d+/,""),a=a.replace(/-/,"/").replace(/-/,"/"),a=a.replace(/T/," ").replace(/Z/," UTC"),a=a.replace(/([\+\-]\d\d)\:?(\d\d)/," $1$2"),a=a.replace(/([\+\-]\d\d)$/," $100"),new Date(a)},datetime:function(e){var a=n.isTime(e)?t(e).attr("datetime"):t(e).data("time");return n.parse(a)},isTime:function(e){return"time"===t(e).get(0).tagName.toLowerCase()}});var o={init:function(){var a=t.proxy(e,this);a();var i=n.settings;i.refreshMillis>0&&(this._timeagoInterval=setInterval(a,i.refreshMillis))},update:function(a){var i=n.parse(a);t(this).data("timeago",{datetime:i}),n.settings.localeTitle&&t(this).attr("title",i.toLocaleString()),e.apply(this)},updateFromDOM:function(){t(this).data("timeago",{datetime:n.parse(n.isTime(this)?t(this).attr("datetime"):t(this).attr("title"))}),e.apply(this)},dispose:function(){this._timeagoInterval&&(window.clearInterval(this._timeagoInterval),this._timeagoInterval=null)}};t.fn.timeago=function(t,e){var a=t?o[t]:o.init;if(!a)throw new Error("Unknown function name '"+t+"' for timeago");return this.each(function(){a.call(this,e)}),this},document.createElement("abbr"),document.createElement("time")});
jQuery.timeago.settings.strings={prefixAgo:null,prefixFromNow:"從現在開始",suffixAgo:"之前",suffixFromNow:null,seconds:"不到 1 分鐘",minute:"約 1 分鐘",minutes:"%d 分鐘",hour:"約 1 小時",hours:"%d 小時",day:"約 1 天",days:"%d 天",month:"約 1 個月",months:"%d 個月",year:"約 1 年",years:"%d 年",numbers:[],wordSeparator:""};

$(function () {
  var _id = null, $_msgs = $('#msgs'), $_in = $('#in'), _ids = [], _lo = false;

  $('figure').imgLiquid ({verticalAlign: 'center'});

  function _faz (i) { return i < 10 ? '0' + i : i; }
  function _fgd (date) { var date = typeof date == 'undefined' ? new Date () : date, year = date.getFullYear (), month = date.getMonth () + 1, day = date.getDate (), hour = date.getHours (), min = date.getMinutes (), sec = date.getSeconds (); return year + '-' + _faz (month) + '-' + _faz (day) + ' ' + _faz (hour) + ':' + _faz (min) + ':' + _faz (sec); }
  function _fu2d (t) { return _fgd (new Date (t)); }

  function initMsgFeature ($obj) {
    $obj.find ('figure').imgLiquid ({verticalAlign: 'center'});
    setTimeout (function () { $obj.addClass ('s'); }, 300);
    return $obj;
  }
  function initMsgElement (obj, isme) {
    return $('<div />').addClass (isme ? 'me' : null).append (
      $('<figure />').append (
        $('<img />').attr ('src', '/res/image/avatar.png'))).append (
      $('<b />').attr ('data-time', $.timeago (obj.t)).text ('對方')).append (
      $('<p />').text (obj.c));
  }
  function loadMsgs (a) {
    if (!a && _lo) return false;
    _lo = true;
    console.error (a);
    
    $.ajax ({
      url: 'api/messages/',
      data: {id : _id},
      async: true, cache: false, dataType: 'json', type: 'POST'
    })
    .done (function (result) {
      var l = result.map (function (t, i) {
        if ($.inArray (t.i, _ids) != -1) return null;
        _ids.push (t.i);
        initMsgFeature (initMsgElement (t).appendTo ($_msgs));
        return t
      }).filter (function (t) { return t != null; });
      
      if (l.length) $_msgs.scrollTop ($_msgs[0].scrollHeight);

      _lo = false;
    }.bind ($(this)))
    .fail (function (result) {
      location.reload (true);
    }.bind ($(this)));
  }

  $('#users > a').click (function () {
    $(this).addClass ('a').siblings ().removeClass ('a');
    _id = $(this).data ('id');
    loadMsgs (true);
  }).first ().click ();
  
  $('#fm').submit (function () {
    var val = $_in.val ().trim ();
    if (!val.length) return false;
    initMsgFeature (initMsgElement ({ t: _fu2d (new Date ().getTime ()), c: val }, true).appendTo ($_msgs)); 
    $_msgs.scrollTop ($_msgs[0].scrollHeight);
    $_in.val ('');
    return false;
  });

  setInterval (loadMsgs, 1000)
});