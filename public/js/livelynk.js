'use strict';

$(function(){
  $('header .action').on('click', function() {
    $('.logout:not(:animated)', this).slideToggle('fast');
  });
});
