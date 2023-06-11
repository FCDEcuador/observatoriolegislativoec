<?php 
  global $json_bancada; 
  global $votaciones_objeto;
  $mode = 1;
  $height = 600;
  if ( isset( $_GET['mode'] ) ) {
    $mode = $_GET['mode'];
  }
  if ( isset ( $_GET['organizacion_politica'] ) ) {
    $partido = get_term_by('slug', ($_GET['organizacion_politica']) ? : 'pachakutik', 'partido_politico');
  }
  $asambleistas = ol_get_all_legisladores();
?>
<style>
  .mode .mode:not(.main) {display: none;}
  .mode.mode-1 .mode-1 { display: block; }
  .mode.mode-2 .mode-2 { display: block; }

</style>
<div class="ol-container mode mode-<?php echo $mode; ?>">
  <div class="container-fluid">
    <div class="row">
      <!-- Filter -->
      <div class="col-md-2 col-xxl-2 px-4 pt-4 pb-4 bg-dark-gray-fcd">
        <span id="sidebar_control"></span>
        <div class="sticky-md-top-1">
          <div class="filter-bar-title py-5" style="background: url(<?php echo get_stylesheet_directory_uri() .'/images/radiacionOL.png'; ?>); background-repeat:no-repeat; background-size: contain; background-position: center;">
            <h3 class="text-white text-center">Detalle de la votación</h3>
          </div>
          <?php if ( is_page() ) { ?>
          <form id="voting-filters" action="/analisis-de-votaciones/">
            <?php foreach($_GET['obj_votacion'] as $obj_votacion){ ?>
            <input type="hidden" name="obj_votacion[]" value="<?php echo $obj_votacion; ?>">
            <?php } ?>
            <div class="mode main">
              <label class="text-white" for="mode">Modalidad de análisis</label> 
              <select class="w-100 form-control main" name="mode" id="mode">
                <option value="1" <?php echo ( isset( $_GET['mode'] ) && $_GET['mode'] == 1 ) ? 'selected': ''; ?>>Analizar por Organización Política</option>
                <option value="2" <?php echo ( isset( $_GET['mode'] ) && $_GET['mode'] == 2 ) ? 'selected': ''; ?>>Analizar por Asambleísta</option>
              </select>
            </div>
            <div class="mode mode-1">
              <label class="text-white" for="organizacion_politica">Organización Política</label>
              <select id="organizacion_politica" class="w-100 form-control" name="organizacion_politica">
                <option value="">Seleccione una</option>
                <?php 
                  $organizaciones = get_terms(['taxonomy' => 'partido_politico', 'hide_empty' => false]);
                  foreach ( $organizaciones as $org ) {
                    $selected = ( isset($_GET['organizacion_politica'] ) && $_GET['organizacion_politica'] == $org->slug ) ? 'selected' : '';
                    echo '<option value="' . $org->slug . '" '. $selected .'>' . $org->name . '</option>';
                  }
                ?>
              </select>
            </div>
            <div class="mode mode-2">
              <label class="text-white" for="organizacion_politica">Asambleísta</label>
              <select id="asambleista" class="w-100 form-control" name="asambleista">
                <option value="">Seleccione uno</option>
                <?php 
                  while ( $asambleistas->have_posts() ) { $asambleistas->the_post();
                    $selected = ( isset($_GET['asambleista'] ) && $_GET['asambleista'] == get_the_ID() ) ? 'selected' : '';
                    echo '<option value="' . get_the_ID() . '" '. $selected .'>' . get_the_title() . '</option>';
                  }
                ?>
              </select>
            </div>
            <br />
            <!--<button type="submit">Mostrar</button>-->
          </form>
          <?php } ?>
          <p class="text-center"><a href="/analisis-de-voto/" class="text-white">Volver a Analisis de Voto</a></p>
        </div>
      </div><!-- END Filter -->
      <div class="col-12 col-md-10 py-4">
        <?php 
        if ( is_single() ) {
          mostrar_datos_single_votacion();
          echo '<hr class="my-3" />';
          echo '<div id="chartdiv" style="width: 100%; min-height: 600px;"></div>';
        }
        if ( is_page() ) {
          if ( isset( $_GET ) ) {
            /**
             * SI estamos en el escenario 1
             */
            $new_set_votacion = null;
            switch($mode) {
              case 1 : 
                echo '<h4>Votaciones realizadas por la Organización Política: ' . $partido->name . '</h4>';
                foreach ( $votaciones_objeto as $i => $votacion_datos ) {
                  foreach( $votacion_datos['detalle'] as $j => $votos ) {
                    foreach ( $votos as $k => $voto ){
                      if ( $partido->slug == $voto['bancada_slug'] ){
                        $votaciones_objeto[$i]['detalle']['votos'][$k]['votacion_id'] = $votacion_datos['votacion_id'];
                        $votaciones_objeto[$i]['detalle']['votos'][$k]['votacion_nombre'] = $votacion_datos['nombre'];
                        $votaciones_objeto[$i]['detalle']['votos'][$k]['votacion_sesion'] = $votacion_datos['sesion'];
                        $votaciones_objeto[$i]['detalle']['votos'][$k]['votacion_fecha'] = $votacion_datos['fecha'];
                        $votaciones_objeto[$i]['detalle']['votos'][$k]['votacion_url'] = $votacion_datos['url'];
                        $new_set_votacion[] = $votaciones_objeto[$i]['detalle']['votos'][$k];
                      }
                    }
                  }
                }
                // echo '<pre>';
                // var_dump($votaciones_objeto);
                // // var_dump($new_set_votacion);
                // echo '</pre>';
                $json_bancada = json_encode( $new_set_votacion );
                break;
              case 2: 
                $height = 200;
                if ( empty( $_GET['asambleista'] ) ) {
                  echo '<p>Seleccione un Asambleísta en la sección izquierda.</p>';
                }else{
                  echo '<h4>Votaciones realizadas por el asambleísta: ' . get_the_title( $_GET['asambleista'] ) . '</h4>';
                  foreach ( $votaciones_objeto as $i => $votacion_datos ) {
                    foreach( $votacion_datos['detalle'] as $j => $votos ) {
                      foreach ( $votos as $k => $voto ){
                        $votaciones_objeto[$i]['detalle']['votos'][$k]['votacion_id'] = $votacion_datos['votacion_id'];
                        $votaciones_objeto[$i]['detalle']['votos'][$k]['votacion_nombre'] = $votacion_datos['nombre'];
                        $votaciones_objeto[$i]['detalle']['votos'][$k]['votacion_sesion'] = $votacion_datos['sesion'];
                        $votaciones_objeto[$i]['detalle']['votos'][$k]['votacion_fecha'] = $votacion_datos['fecha'];
                        $votaciones_objeto[$i]['detalle']['votos'][$k]['votacion_url'] = $votacion_datos['url'];
                        $new_set_votacion[] = $votaciones_objeto[$i]['detalle']['votos'][$k];
                      }
                    }
                  }
                  // echo '<pre>';
                  // // var_dump($_GET);
                  // var_dump($votaciones_objeto);
                  // echo '</pre>';
                  // $new_set_votacion = null;
                  $json_bancada = json_encode( $new_set_votacion );
                    break;
                }
            }
            echo '<div id="chartdiv" style="width: 100%; min-height: ' . $height . 'px;"></div>';
          }
        }
        /*
        ?>
        <?php if( $new_set_votacion ) { ?>
        <hr />
        <h5>Votaciones representadas</h5>
        <ol >
        <?php 
          foreach( $new_set_votacion as $elemento ) {
            echo '<li type="1"><a href="' . $elemento['votacion_url'] . '" target="_blank">' . $elemento['votacion_nombre'] .  '</a><br /><span class="text-warning">' . $elemento['votacion_fecha'] . '</span><br />' . $elemento['votacion_sesion'] .'</li>';
          }
        ?>
        </ol>
        <?php } 
        */?>
      </div>
    </div>
  </div>
</div>
<script>
  jQuery(document).ready( function($) {
    $('select').change( function() {
      $('#voting-filters').submit()
    })
  })
</script>