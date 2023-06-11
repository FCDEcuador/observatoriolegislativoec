// Custom JS goes here ------------
$(document).ready(function(){
  $('.select2-active').select2({ allowClear: true });
  $('.btn-equipo-trabajo').click( function(){
    $('.ol-equipo-trabajo-placeholder').toggle();
  })
})