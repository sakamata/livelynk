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

// 予定の時間フォームの表示切り替え、今日明日のレベルは時間指定を表示する
function timeDisplayChange() {
    var selindex = document.getElementById('tumori_when').selectedIndex;
    if (selindex == 0 || selindex == 1 || selindex == 2 || selindex == 3 ) {
        document.getElementById('timeDisplay').style.display = "none";
        document.getElementById('timeDisplay').style.display = "";
    } else {
        document.getElementById('timeDisplay').style.display = "";
        document.getElementById('timeDisplay').style.display = "none";
    }
}
window.onload = timeDisplayChange;
