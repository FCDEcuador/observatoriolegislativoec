jQuery(document).ready( function($){

  $('.owl-carousel').owlCarousel({
    loop:true,
    responsiveClass:true,
    responsive:{
        0:{
            items:1,
            nav:true,
            margin:10,
        },
        600:{
            items:3,
            nav:false,
            margin:60,
        },
        1000:{
            margin:80,
        }
    }
  })
  $('#clearFilter').click(function(){
    $('.select2-active').val(null).trigger('change');
	  window.location.href = 'https://observatoriolegislativo.ec//perfil/';
  })
  $('#clearFilterVotacion').click(function(){
    $('.select2-active').val(null).trigger('change');
	  window.location.href = 'https://observatoriolegislativo.ec//analisis-de-voto/';
  })
  $('#select-all-votes').click( function(){
    $('input[type="checkbox"]').prop( "checked", true );
  })
  $('#analize-votes').click(function(){
    var checkedCount = 0;
    $.each($('input[type="checkbox"]'), function(index, value){
      if ( $(this).is(':checked') ){
        checkedCount++;
      }
    })
    if ( checkedCount > 1 ) {
      var respuesta = confirm('¿Está seguro que desea analizar las votaciones seleccionadas?');
      if ( respuesta ){
        document.getElementById('vote-list-form').submit();
      }
    }else{
      alert('Necesita seleccionar al menos 2 votaciones para analizar en grupo sino haga clic en el titulo de la votación para ver en analizar de la misma.');
    }

  })
})
