<?php 
  global $vistas, $vista, $titularizado, $tipo_grafico;

  $indicador_activo = (isset($_GET['i'])) ? $_GET['i'] : '';
  $cantidad_activa = (isset($_GET['a'])) ? $_GET['a'] : '';
?>
<style>
	select {
		border: none;
    	padding: 5px;
    	margin: 5px 0;
    	border-radius: 6px;
	}
</style>
<form id="voting-filters" action="/asamblea-en-cifras/">
<div class="ol-container mode">
  <div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-2 col-xxl-2 px-4 pt-4 pb-4 bg-dark-gray-fcd">
          <span id="sidebar_control"></span>
          <div class="sticky-md-top-1">
            <div class="filter-bar-title py-5" style="background: url(<?php echo get_stylesheet_directory_uri() .'/images/radiacionOL.png'; ?>); background-repeat:no-repeat; background-size: contain; background-position: center;">
              <h3 class="text-white text-center">Asamblea en Cifras</h3>
            </div>
              <label class="text-white" for="indicador" style="font-size:20px;">Indicadores</label>
              <select class="w-100" id="indicador" name="i">
                <option value="2" <?php echo ($indicador_activo == 2) ? 'selected' : ''; ?>>Proyectos de Ley</option>
                <option value="4" <?php echo ($indicador_activo == 4) ? 'selected' : ''; ?>>Observaciones a Proyectos de Ley</option>
                <option value="7" <?php echo ($indicador_activo == 7) ? 'selected' : ''; ?>>Leyes Aprobadas</option>
                <option value="8" <?php echo ($indicador_activo == 8) ? 'selected' : ''; ?>>Tipo de Votaciones</option>
                <option value="5" <?php echo ($indicador_activo == 5) ? 'selected' : ''; ?>>Pedidos de juicio político</option>
                <option value="3" <?php echo ($indicador_activo == 3) ? 'selected' : ''; ?>>Pedidos de Información</option>
                <option value="6" <?php echo ($indicador_activo == 6) ? 'selected' : ''; ?>>Puntualidad de los legisladores</option>
                <option value="1" <?php echo ($indicador_activo == 1) ? 'selected' : ''; ?>>Ocupación de la Curul</option>
              </select>
              <hr />
              <br />
              <?php
                if ( ! isset($_GET['i'] ) ){
                  get_template_part('template-parts/detalle-asamblea-cifras-filtros');
                }else{
                  if(
                    (isset($_GET['i']) && $_GET['i'] != 8) &&
                    (isset($_GET['i']) && $_GET['i'] != 7)
                  ){
                    get_template_part('template-parts/detalle-asamblea-cifras-filtros');
                  }
                }
              ?>
          </div>
          <div class="row">
            <div class="col-12 mt-5 mb-3 d-flex jusify-content-center align-items-center">
              <button id="btn-xls-asmcifras" class="w-100 p-2 px-3 m-1 d-flex justify-content-around align-items-center br-6 border-0" style="max-width: 100px; margin: 10px auto;" type="button" title="Exportar Excel Asamblea en Cifras"><span class="bold">XLS</span> <i class="fas fa-file-excel fs-20"></i></button>
              <button id="btn-csv-asmcifras" class="w-100 p-2 px-3 m-1 d-flex justify-content-around align-items-center br-6 border-0" style="max-width: 100px; margin: 10px auto;" type="button" title="Exportar Excel Asamblea en Cifras"><span class="bold">CSV</span> <i class="fas fa-file-csv fs-20"></i></button>
            </div>
          </div>
        </div><!-- END Filter -->
        <div class="col-12 col-lg-10 py-4 ps-lg-5">
          <div class="row">
            <div class="col-12 col-lg-10">
              <?php echo '<h3>'. $vistas[$vista][0] . '</h3>'; ?>
            </div>
            <div class="col-12 col-lg-2 d-flex justify-content-end"> 
              <?php if ( $vista != 7 && $vista != 8 ) { ?>
			   <select id="order" name="order" class="me-3">
                <option value="DESC" <?php echo ( isset($_GET['order']) && $_GET['order'] == 'DESC') ? 'selected' : ''; ?>><?php echo $vistas[$vista][4]; ?></option>
                <option value="ASC" <?php echo (isset($_GET['order']) && $_GET['order'] == 'ASC') ? 'selected' : ''; ?>><?php echo $vistas[$vista][5]; ?></option>
              </select>
			  <!--
              <select id="cantidad" name="a">
                <option value="5" <?php echo ($cantidad_activa == 5) ? 'selected' : ''; ?>>Mostrar 5</option>
                <option value="10" <?php echo ($cantidad_activa == 10) ? 'selected' : ''; ?>>Mostrar 10</option>
                <option value="15" <?php echo ($cantidad_activa == 15) ? 'selected' : ''; ?>>Mostrar 15</option>
              </select>
			  -->
              <?php } ?>
            </div>
          </div>
          <div class="row">
            <div class="col-12 grafico-<?php echo $tipo_grafico; ?>">
              <?php 
                $texto_encima = get_field('texto_encima_del_grafico_' . $vistas[$vista][3], 'option'); 
                if ( $texto_encima ) {
                  echo '<div class="mt-3 mb-0">';
                  echo $texto_encima;
                  echo '</div>';
                }
              ?>
              <div id="chartdiv" style="width: 100%; min-height: 500px;"></div>
              <div id="mobile_chart">tablas</div>
              <div class="d-block d-lg-none mt-3 text-center">
                <p><b>La visualización óptima se puede lograr en dispositivos de escritorio</b></p>
              </div>
              <?php 
                $texto_debajo = get_field('texto_debajo_del_grafico_' . $vistas[$vista][3], 'option'); 
                if ( $texto_debajo ) {
                  echo '<div>';
                  echo $texto_debajo;
                  echo '</div>';
                }
              ?>
              <?php 
                $url_fuente = get_field('url_fuente_' . $vistas[$vista][3], 'option'); 
                if ( $url_fuente ) {
                  echo '<p>';
                  echo '<b>Si deseas conocer más, puedes acceder al sitio web de la Asamblea Nacional:</b> <a href="' . $url_fuente['url'] . '">' . $url_fuente['url'] . '</a>';
                  echo '</p>';
                }
				// if ($titularizado) {
        //           echo '<p class="mb-3">';
        //           echo '<b>(*)</b> Es asambleísta titularizado';
        //           echo '</p>';
        //         }
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<script>
  jQuery(document).ready( function($) {
    $('select').change( function() {
      if ( 'b' == $(this).attr('name') ) {
        $('select[name="o"]')[0].selectedIndex = 0;
      }
      if ( 'o' == $(this).attr('name') ) {
        $('select[name="b"]')[0].selectedIndex = 0;
      }
      $('#voting-filters').submit()
    })
  })
</script>