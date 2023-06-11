$(document).ready(function() {
  $('body').prepend(`
  <div class="loading-overlay">
    <div class="bounce-loader">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
        <div class="bounce3"></div>
    </div>
  </div>
  `);
  if ( $('.ol-archive-htmlmode').length ){
    $('#top-menu-nav, #et-secondary-menu').hide();
  }
  $('.select2-active').select2({ allowClear: true });
  
  $('#btn-csv').click( function(){
    console.log(ol_dom_vars.queried_vars)

    $.ajax({
        url: ol_dom_vars.ajaxurl,
        type: 'GET',
        data: {
          action: 'ol_generate_csv_legisladores',
          activeFilters: ol_dom_vars.queried_vars
        },
        
        xhrFields: {
          responseType: 'blob'
        },
        
        beforeSend: function(){
          $('body').toggleClass('loading-overlay-showing');
        },
        success: function(resp){

          $('body').toggleClass('loading-overlay-showing');
          let fechaActual = new Date()
          
          var a = document.createElement('a');
          var url = window.URL.createObjectURL(resp);
          a.href = url;
          a.download = 'Observatorio-Legislativo-Listado-Asambleistas-'+fechaActual.getDate() + "-" + (fechaActual.getMonth() + 1) + "-" + fechaActual.getFullYear()+'.csv';
          document.body.append(a);
          a.click();
          a.remove();
          window.URL.revokeObjectURL(url);
            
        },
        error: function(xhr,err){
          console.log(err);
          console.log(xhr);
        }
  
    })
  
  })

  $('#btn-xls').click( function(){
    console.log('en al xls')
    $.ajax({
      url: ol_dom_vars.ajaxurl,
      type: 'GET',
      data: {
        action: 'ol_generate_consolidado_miembros_xls',
        activeFilters: ol_dom_vars.queried_vars
      },
      beforeSend: function(){
        $('body').toggleClass('loading-overlay-showing');
      },
      success: function(data){
        console.log(data);
        $('body').toggleClass('loading-overlay-showing');
        var $a = $("<a>");
        $a.attr("href",data.file);
        $("body").append($a);
        $a.attr("download","OL_listado_de_Asambleistas.xls");
        $a[0].click();
        $a.remove();
      },
      error: function(xhr,err){
        console.log(err);
        console.log(xhr);
      }

    })

})

  $('#btn-pdf').click( function(){
    let newLocation = window.location.href + '&htmlmode=true';
    window.open(newLocation, '_blank');
  })
});