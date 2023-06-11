<?php 
  get_header(); 
  global $wp_query;

  /**
   * verificar si es un filtro real para analisis.
   */
  $filtro_real = false;
  if (
    isset ( $_GET['filtered_votes'] ) &&
    (
      isset ( $_GET['temas'] ) || 
      isset ( $_GET['subtemas'] ) || 
      isset ( $_GET['tipos'] ) || 
      isset ( $_GET['categoria_votacions'] )
    )
  ) {
    $filtro_real = false;
  }
?>
<style>
  #vote-list-form li:hover{
    background: #f7f7f7;
  }
</style>
<div class="ol-container">
  <div class="container-fluid">
    <div class="row">
      <!-- Filter -->
      <div class="col-md-4 col-lg-2 col-xxl-2 px-4 pt-4 pb-4 bg-dark-gray-fcd">
        <div class="sticky-md-top-1">
          <h3 class="uppercase bolder py-1 text-center text-white">Registro de Votos</h3>
          <p class="text-center text-white">Pleno de la Asamblea Nacional</p>
          <div class="filter-bar-title" style="background-url: url(<?php echo get_stylesheet_directory_uri() .'/images/radiacionOL.png'; ?>);">
            <h3 class="uppercase bolder py-5 text-center text-white">Filtrar Votaciones</h3>
            <?php echo do_shortcode('[show-filters-votes]'); ?>
            <div class="row">
              <div class="col-12 mt-3 mb-3 d-flex jusify-content-center align-items-center">
                <button id="btn-xls-general" class="w-100 p-2 px-3 m-1 d-flex justify-content-around align-items-center br-6 border-0" style="max-width: 100px; margin: 10px auto;" type="button" title="Exportar Excel Detalle Votaciones"><span class="bold">XLS</span> <i class="fas fa-file-excel fs-20"></i></button>
                <button id="btn-csv-general" class="w-100 p-2 px-3 m-1 d-flex justify-content-around align-items-center br-6 border-0" style="max-width: 100px; margin: 10px auto;" type="button" title="Exportar Excel Detalle Votaciones"><span class="bold">CSV</span> <i class="fas fa-file-csv fs-20"></i></button>
              </div>
            </div>
          </div>
        </div>
      </div><!-- END Filter -->
      <div class="col-12 col-lg-10 py-5 ps-lg-5 pt-3">		  
        <div class="row">
          <div class="col-12 col-md-6">
			<?php if ($filtro_real) { ?>
            <button class="me-2 br-6 text-white py-2 bg-secondary border-0" id="select-all-votes" type="button">Seleccionar todas</button>
			<button class="me-2 br-6 py-2 bg-accent border-0" id="analize-votes" type="button"><strong>Analizar</strong></button>
			  <?php } ?>
          </div>
		  <div class="col-12 col-md-6">
			  <p class="align-items-end d-flex justify-content-end">
				  <strong>Votaciones encontradas: </strong>&nbsp;<?php echo $wp_query->found_posts; ?>
			  </p>
		  </div>
        </div>
        <?php
        //var_dump($wp_query->request);
          if ( have_posts() ) {
            echo '<form id="vote-list-form" action="' . home_url('analisis-de-votaciones') . '/">';
            echo '<ul class="list-unstyled">';
            while ( have_posts() ){
              the_post();
              $votacion_id = get_the_ID();
              $titulo = get_the_title();
              $url = get_the_permalink();
              $fecha = get_field('fecha', $votacion_id);
			  $aprobado = (get_field('aprobado', $votacion_id))?'<strong>APROBADO</strong>': '';
			  $numero = get_field('numero', $votacion_id);
              $sesion = get_field('sesion_de_origen', $votacion_id);
              $resumen = get_the_excerpt();

              echo sprintf(
                '<li class="mt-3 border-top pt-2" data-votationid="%1$s">
                  <div class="row">
                    <div class="col-1 %8$s">
                      <input type="checkbox" name="obj_votacion[]" value="%1$s" />
                    </div>
                    <div class="%7$s">
                      <a href="%2$s"><h4 class="fs-20 mb-0">%3$s</h4></a>
                      <div class="row">
                        <div class="col-12 col-lg-6">
                        <span class="text-black-25 d-block fs-14 lh-1">Votación %9$s - %5$s</span>
                          <span class="text-warning fs-14 lh-1">%6$s</span>
                        %4$s
                      </div>
                      <div class="col-12 col-lg-3 fs-14 lh-1">%10$s</div>
                      <div class="col-12 col-lg-1 d-flex justify-content-start align-items-center">
                        <a class="btn-csv me-4" href="#" data-votationid="%1$s"><i class="fas fa-file-csv fs-20" aria-hidden="true"></i></a>
                        <a class="btn-xls" href="#" data-votationid="%1$s"><i class="fas fa-file-excel fs-20" aria-hidden="true"></i></a>
                      </div>
                      </div>
                    </div>
                  </div>
                </li>',
                $votacion_id,
                $url,
                $titulo,
                ($resumen) ? '<p class="px-2">' . $resumen . '</p>' : '',
                $sesion->post_title,
                $fecha,
                ($filtro_real == true) ? 'col-11' : 'col-12',
                ($filtro_real == true) ? '' : 'd-none',
                $numero,
                $aprobado
              );
            }
          echo '</ul>';
          echo '</form>';
			?>
		  <div class="row mt-4">
			  <div class="col-12 text-center">
				<?php
				  if ( function_exists( 'wp_pagenavi' ) )
					wp_pagenavi();
				  else
					get_template_part( 'includes/navigation', 'index' );
				?>
			  </div>
		  </div>
		  <?php 

          }else{
            echo '<p>No existen eventos de votaciones.</p>';
          }
        ?>
		  
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>
<script>
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
  $('.btn-csv').click( function(e){
    e.preventDefault();
    var dataVotacion = $(this).data('votationid')
    $.ajax({
        url: ol_dom_vars.ajaxurl,
        type: 'GET',
        data: {
          action: 'ol_generate_csv_votacion',
          votacion: dataVotacion
        },
        xhrFields: {
          responseType: 'blob'
        },
        beforeSend: function(){
          $('body').toggleClass('loading-overlay-showing');
        },
        success: function(resp){

          $('body').toggleClass('loading-overlay-showing');
          // let fechaActual = new Date()

          var a = document.createElement('a');
          var url = window.URL.createObjectURL(resp);
          a.href = url;
          a.download = 'Observatorio-Legislativo-Listado-Votacion-' + dataVotacion + '.csv';
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

  $('.btn-xls').click( function(e){
    e.preventDefault();
    var dataVotacion = $(this).data('votationid')
    $.ajax({
      url: ol_dom_vars.ajaxurl,
      type: 'GET',
      data: {
        action: 'ol_generate_xls_votacion',
        votacion: dataVotacion
      },
      beforeSend: function(){
        $('body').toggleClass('loading-overlay-showing');
      },
      success: function(data){
        $('body').toggleClass('loading-overlay-showing');
        var $a = $("<a>");
        $a.attr("href",data.file);
        $("body").append($a);
        $a.attr("download","Observatorio-Legislativo-Listado-Votacion-" + dataVotacion + ".xls");
        $a[0].click();
        $a.remove();
      },
      error: function(xhr,err){
        console.log(err);
        console.log(xhr);
      }

    })

  })

  $('#btn-xls-general').click( function(e){
    e.preventDefault();
    let params = (new URL(document.location)).searchParams
    let genero = params.get('g')
    let partido = params.get('o')
    let bancada = params.get('b')
    $.ajax({
      url: ol_dom_vars.ajaxurl,
      type: 'GET',
      data: {
        action: 'ol_generate_xls_general_votaciones',
        g: genero,
        o: partido,
        b: bancada
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
        $a.attr("download","OL_listado_Detalle_Votaciones.xls");
        $a[0].click();
        $a.remove();
      },
      error: function(xhr,err){
        console.log(err);
        console.log(xhr);
      }

    })

  })
  $('#btn-csv-general').click( function(e){
    e.preventDefault();
    let params = (new URL(document.location)).searchParams
    let genero = params.get('g')
    let partido = params.get('o')
    let bancada = params.get('b')
    $.ajax({
        url: ol_dom_vars.ajaxurl,
        type: 'GET',
        data: {
          action: 'ol_generate_csv_general_votaciones',
          g: genero,
          o: partido,
          b: bancada
        },
        xhrFields: {
          responseType: 'blob'
        },
        beforeSend: function(){
          $('body').toggleClass('loading-overlay-showing');
        },
        success: function(resp){

          $('body').toggleClass('loading-overlay-showing');
          // let fechaActual = new Date()

          var a = document.createElement('a');
          var url = window.URL.createObjectURL(resp);
          a.href = url;
          a.download = 'OL_listado_Detalle_Votaciones.csv';
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
	/**
	 * For filterring by date
	 * */
	$('#year_selector').on('change', function(e){
		$('#month_selector').select2("val", "")
		if ( e.target.value.length ) {
			$('.month-filter').removeClass('d-none')
		}else{
			$('.month-filter').addClass('d-none')
		}
	})
})
</script>