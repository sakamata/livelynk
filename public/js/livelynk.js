'use strict';

$(function(){
  $('header .action').on('click', function() {
    $('.logout:not(:animated)', this).slideToggle('fast');
  });
  $('.comp-box-container>.comp-box').each((i, e) => {
    var elem = $('.name>.text', e);
    var textMax = 15;
    var afterText = '…';
    var nameText = elem.text();;
    var trimText = nameText.substr(0, textMax);
    if (nameText.length > textMax) {
      elem.html(trimText + afterText);
    }
    elem.css({visibility:'visible'});
  });
});
