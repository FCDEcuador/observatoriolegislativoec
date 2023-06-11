jQuery(document).ready(function($){
  $('.acf-repeater td').removeClass('order')
  $('#llenar_si').click(function(){
    $('input[value="SI"]').prop('checked', true)
  })
  $('#llenar_no').click(function(){
    $('input[value="NO"]').prop('checked', true)
  })

  var asambleista = $('.item_asambleista');
  var votosSI = $('td:not(.acf-hidden) input[value="SI"]:checked');
  var votosNO = $('td:not(.acf-hidden) input[value="NO"]:checked');
  var votosAB = $('td:not(.acf-hidden) input[value="AB"]:checked');
  var votosBL = $('td:not(.acf-hidden) input[value="BL"]:checked');
  var votosAU = $('.checkb_au input[type="checkbox"]:checked');
  var counterHTML = 'No hay nada que contar.';
  var totalVotos = votosSI.length + votosNO.length + votosBL.length + votosAB.length;
  console.log(totalVotos);

  if ( (totalVotos) != 0   ){

    counterHTML = 'Votos(' + (totalVotos) + ') = SI: <strong><span style="color:green">' + votosSI.length + '</span></strong>' + 
    ' - NO: <strong><span style="color:red">' + votosNO.length + '</span></strong>' + 
    ' - AB: <strong><span style="color:blue">' + votosAB.length + '</span></strong>' + 
    ' - BL: <strong>' + votosBL.length + '</strong>';

  }

  $('#people_counter').text('Asambleístas listados: ' + (asambleista.length - 1));
  $('#au_counter').text('Ausencias: ' + votosAU.length);

  $('#votes_counter').html(counterHTML)
})