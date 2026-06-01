$(function() {

  $('[data-skin]').on('click', function(e) {
    e.preventDefault();
    var skin = $(this).data('skin');
    $('#style-skin').attr('href', 'assets/css/skin/skin-'+ skin +'.min.css');
  });

  // Sidebar-boxed: Try it section
  $('#sb-left-side').on('click', function() {
    $('.sidebar-boxed').removeClass('sidebar-right');
  });

  $('#sb-right-side').on('click', function() {
    $('.sidebar-boxed').addClass('sidebar-right');
  });

  $('#sb-skin-light').on('click', function() {
    $('.sidebar-boxed').removeClass('sidebar-dark');
  });

  $('#sb-skin-dark').on('click', function() {
    $('.sidebar-boxed').addClass('sidebar-dark');
  });

});



var $darkbox = $("<div/>",{id:"darkbox"}).on("click", function(){
  $(this).removeClass("on");
}).appendTo("body");
$('img[data-darkbox]').css({cursor:"pointer"}).on("click",function(){
  var o=this.getBoundingClientRect();
  $darkbox.css({
      transition:"0s",
      backgroundImage:"url("+this.src+")",
      left:o.left, top:o.top,
      height:this.height, width:this.width
  });
  setTimeout(function(){
      $darkbox.css({transition:".8s"}).addClass("on"); 
  },5);
});


