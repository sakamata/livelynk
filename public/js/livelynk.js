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
  });
});

// 予定の時間フォームの表示切り替え、今日・明日・明後日は時間指定を表示する
function timeDisplayChange() {
    var selindex = document.getElementById('tumori_when').value;
    if (selindex == 'today' || selindex == 'tomorrow' || selindex == 'dayAfterTomorrow' ) {
        document.getElementById('timeDisplay').style.display = "none";
        document.getElementById('timeDisplay').style.display = "";
    } else {
        document.getElementById('timeDisplay').style.display = "";
        document.getElementById('timeDisplay').style.display = "none";
    }
}
window.onload = timeDisplayChange;
