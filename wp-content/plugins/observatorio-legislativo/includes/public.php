<?php 
/**
 * Modify Archive query
 */
add_action('pre_get_posts', 'ol_archive_query', 999);
function ol_archive_query($query){

  if (
    ! is_admin() &&
    $query->is_main_query()
  ){

    if ($query->is_post_type_archive('perfil') ){


      if ( isset($_GET['letter-filter'] ) ) {
        $query->set( 'starts_with', $_GET['letter-filter'] );
      }
		
	  $tipo = 'legislador';
	  if ( isset($_GET['filtered'] ) ) {
	  	if( isset( $_GET['exasambleistas'] ) ){
			$tipo = 'exasambleista';
		}
	  }
      $taxquery = array(
        'relation' => 'AND',
        array(
          'taxonomy' => 'tipo_perfil',
          'field' => 'slug',
          'terms' => $tipo
        )
      );

      if ( isset($_GET['filtered'] ) ) {
        if ( isset( $_GET['circunscripcions'] ) ) {
          $filter = array(
            'taxonomy' => 'circunscripcion',
            'field' => 'term_id',
            'terms' => $_GET['circunscripcions']
          );
          array_push($taxquery, $filter);
        }
        if ( isset( $_GET['partido_politicos'] ) ) {
          $filter = array(
            'taxonomy' => 'partido_politico',
            'field' => 'term_id',
            'terms' => $_GET['partido_politicos']
          );
          array_push($taxquery, $filter);
        }
        if ( isset($_GET['comisions'] ) ) {
          $filter = array(
            'taxonomy' => 'comision',
            'field' => 'term_id',
            'terms' => $_GET['comisions']
          );
          array_push($taxquery, $filter);
        }
        if ( isset( $_GET['generos'] ) ) {
          $filter = array(
            'taxonomy' => 'genero',
            'field' => 'term_id',
            'terms' => $_GET['generos']
          );
          array_push($taxquery, $filter);
        }
        if ( isset( $_GET['bancadas'] ) ) {
          $filter = array(
            'taxonomy' => 'bancada',
            'field' => 'term_id',
            'terms' => $_GET['bancadas']
          );
          array_push($taxquery, $filter);
        }
        if ( isset( $_GET['posicion_politicas'] ) ) {
          $filter = array(
            'taxonomy' => 'posicion_politica',
            'field' => 'term_id',
            'terms' => $_GET['posicion_politicas']
          );
          array_push($taxquery, $filter);
        }
      }else{
        $query->set( 'orderby', 'post_title' );
        $query->set( 'order', 'ASC' );
      }

      $query->set( 'tax_query', $taxquery );
      if ( is_search() || isset ( $_GET['htmlmode']) ){
        $query->set( 'posts_per_page', -1 );
      }
      // $query->set( 'meta_key', 'curul' );
      $query->set( 'order', 'ASC' );
      $query->set( 'orderby', 'post_title' );
    }

    if ($query->is_post_type_archive('votacion') ){
      if ( isset($_GET['filtered_votes'] ) ) {
        $taxquery = array(
          'relation' => 'AND'
        );
        $metaquery = array();
		  
		if ( isset( $_GET['meses'] ) && !empty( $_GET['meses'] ) ) {
		  $annio = ( isset( $_GET['annio'] ) && !empty($_GET['annio']) ) ? $_GET['annio'] : date('Y');
          if ( isset( $_GET['meses'] ) ){
            $filter = array(
              'key' => 'fecha',
              //'value' => array( $annio'-' . $_GET['meses'] . '-01', $annio . '-' . $_GET['meses'] . '-31'),
			  'value' => array( $annio . '-' . $_GET['meses'] . '-01', $annio . '-' . $_GET['meses'] . '-31'),
              'type' => 'date',
              'compare' => 'between'
            );
            array_push($metaquery, $filter);
          }
          $query->set( 'meta_query', $metaquery );
          $query->set( 'order', 'DESC' );
          $query->set( 'orderby', 'meta_value' );
          $query->set( 'posts_per_page', -1 );
			
		$annio = ( isset( $_GET['annio'] ) && !empty($_GET['annio']) ) ? $_GET['annio'] : date('Y');
        }

        if ( isset( $_GET['annio'] ) && !empty( $_GET['annio'] && empty($_GET['meses']) ) ) {
          if ( isset( $_GET['annio'] ) ){
            $filter = array(
              'key' => 'fecha',
              'value' => array( $_GET['annio'] . '-01-01', $_GET['annio'] . '-12-31'),
              'type' => 'date',
              'compare' => 'between'
            );
            array_push($metaquery, $filter);
          }
          $query->set( 'meta_query', $metaquery );
          $query->set( 'order', 'DESC' );
          $query->set( 'orderby', 'meta_value' );
          $query->set( 'posts_per_page', -1 );
        }

        if ( isset( $_GET['temas'] ) ) {
          $filter = array(
            'taxonomy' => 'tema',
            'field' => 'term_id',
            'terms' => $_GET['temas']
          );
          array_push($taxquery, $filter);
        }
        if ( isset( $_GET['subtemas'] ) ) {
          $filter = array(
            'taxonomy' => 'subtema',
            'field' => 'term_id',
            'terms' => $_GET['subtemas']
          );
          array_push($taxquery, $filter);
        }
        if ( isset($_GET['tipos'] ) ) {
          $filter = array(
            'taxonomy' => 'tipo',
            'field' => 'term_id',
            'terms' => $_GET['tipos']
          );
          array_push($taxquery, $filter);
        }
        if ( isset( $_GET['categoria_votacions'] ) ) {
          $filter = array(
            'taxonomy' => 'categoria_votacion',
            'field' => 'term_id',
            'terms' => $_GET['categoria_votacions']
          );
          array_push($taxquery, $filter);
        }
        $query->set( 'tax_query', $taxquery );
      }
    }

  }

}

function ol_posts_where( $where, $query ) {
  global $wpdb;

  $starts_with = esc_sql( $query->get( 'starts_with' ) );

  if ( $starts_with ) {
      $where .= " AND $wpdb->posts.post_title LIKE '$starts_with%'";
  }

  return $where;
}
add_filter( 'posts_where', 'ol_posts_where', 10, 2 );

function ol_get_queried_legisladores($custom_query_args = ''){

  // var_dump($_GET);
  // die;
  $taxquery = array(
    'relation' => 'AND',
    array(
      'taxonomy' => 'tipo_perfil',
      'field' => 'slug',
      'terms' => 'legislador'
    )
  );

  if ( isset( $custom_query_args['activeFilters'] ) ) {
    unset( $custom_query_args['activeFilters']['filtered'] );
    foreach ( $custom_query_args['activeFilters'] as $tax => $terms ) {
      $tax = substr($tax, 0, -1);
      $filter = array(
        'taxonomy' => $tax,
        'field' => 'term_id',
        'terms' => $terms
      );
      array_push($taxquery, $filter);
    }
  }

  $args = array(
    'post_type' => 'perfil',
    'posts_per_page' => -1,
    'order' => 'ASC',
    'orderby' => 'post_title',
    'tax_query' => $taxquery
  );

  // var_dump($taxquery);
  // die;
  return new WP_Query( $args );
}

function ol_get_all_legisladores( $filters = '' ){

  $taxquery = array(
    'relation' => 'AND',
    array(
      'taxonomy' => 'tipo_perfil',
      'field' => 'slug',
      'terms' => 'legislador'
    )
  );

  if ( isset( $filters['c'] ) && ! empty( $filters['c'] ) ) {
    $filter = array(
      'taxonomy' => 'circunscripcion',
      'field' => 'term_id',
      'terms' => $filters['c']
    );
    array_push($taxquery, $filter);
  }

  if ( isset( $filters['o'] ) && ! empty( $filters['o'] ) ) {
    $filter = array(
      'taxonomy' => 'partido_politico',
      'field' => 'term_id',
      'terms' => $filters['o']
    );
    array_push($taxquery, $filter);
  }

  if ( isset( $filters['g'] ) && ! empty( $filters['g'] ) ) {
    $filter = array(
      'taxonomy' => 'genero',
      'field' => 'term_id',
      'terms' => $filters['g']
    );
    array_push($taxquery, $filter);
  }

  if ( isset( $filters['b'] ) && ! empty( $filters['b'] ) ) {
    $filter = array(
      'taxonomy' => 'bancada',
      'field' => 'term_id',
      'terms' => $filters['b']
    );
    array_push($taxquery, $filter);
  }

  $args = array(
    'post_type' => 'perfil',
    'posts_per_page' => -1,
    'tax_query' => $taxquery
  );

  // var_dump($taxquery);
  // die;
  return new WP_Query( $args );
}

function ol_validar_asambleista_en_objeto($a_id, $a_obj){
  return strpos($a_obj, $a_id);
}

function ol_datos_sesiones_asambleista($nombre = '', $votaciones) {
  if ( empty( $nombre ) ) return;

  // return $a_id;
  if ( $votaciones->have_posts() ) {
    $votaciones_posibles = 0;
    $sesiones_asistidas = array();
    $votacion_principal = 0;
    $ausencias_principal = 0;
    $votacion_suplente = 0;
    $ausencias_suplente = 0;
    while ( $votaciones->have_posts() ) {
      $votaciones->the_post();
      $sesion_origen = get_field('sesion_de_origen', get_the_ID());
      $votos = get_field('listado_de_votos', get_the_ID());
      //if (count($votos)>0){
        foreach ( $votos as $voto ) {
          /**
           * sesiones posibles
           */
          if ( $voto['asambleista'] == $nombre ) {
            $votaciones_posibles++;
            if ( !in_array($sesion_origen->ID, $sesiones_asistidas) ){
              $sesiones_asistidas[] = $sesion_origen->ID;
            }

            if ($voto['de']){
              if ($voto['aus']){
                $ausencias_suplente++;
              }else{
                $votacion_suplente++;
              }
            }else{
              if ($voto['au']) {
                $ausencias_principal++;
              }else{
                $votacion_principal++;
              }
            }
            //return($voto);
          }
        }
      //}
    }
    // $asistencia_principal = $votaciones_posibles - $votacion_suplente - $ausencias_principal;
    return $asambleista_datos = array(
      'sesiones_asistidas' => count($sesiones_asistidas),
      'votaciones_posibles' => $votaciones_posibles,
      'asistencia_principal' => $votacion_principal,
      'asistencia_suplente' => $votacion_suplente,
      'ausencia_principal' => $ausencias_principal,
      'ausencia_suplente' => $ausencias_suplente,
      'porcentaje_asistencia' => number_format((($votacion_principal + $votacion_suplente) * 100) / $votaciones_posibles, 2) . '%',
    );
  }

}

/**
 * Votaciones Helpers
 */
function obtener_objeto_votos($votacion_id){
  if ( ! $votacion_id ) return;
  $votos = get_field('listado_de_votos', $votacion_id);
  return $votos;
}

function crear_objeto_bancada(array $bancadas){
  if( ! is_array( $bancadas ) ) return;
  $objeto_bancada = NULL;
  foreach( $bancadas as $bancada ) {
    // $objeto_bancada['bancadas'][] = array(
    //   'bancada_id' => $bancada->term_id,
    //   'bancada_nombre' => $bancada->name,
    //   'bancada_slug' => $bancada->slug,
    // );
    $nombre_corto = get_field('nombre_corto', $bancada->taxonomy . '_' . $bancada->term_id);
    $objeto_bancada['votos'][] = array(
      'bancada_id' => $bancada->term_id,
      'bancada_nombre' => $bancada->name,
      'bancada_nombre_corto' => $nombre_corto,
      'bancada_slug' => $bancada->slug,
      'acumulado' => 0,
      'AU' => 0,
      'SI' => 0,
      'NO' => 0,
      'AB' => 0,
      'BL' => 0,
    );
  }
  return $objeto_bancada;
}

function agrupar_votos_por_bancada( array $votos ){
  if( ! is_array( $votos ) ) return;

  try{
    /**
     * Obtener bancadas
     */
    $bancadas = get_terms(['taxonomy' => 'bancada', 'hide_empty' => false]);
    $objeto_bancada = crear_objeto_bancada($bancadas);
    //return $objeto_bancada;
    /**
     * Ordenar votos por bancada
     */
    if ( $votos ){
      foreach ( $votos as $voto ){
        $info_asambleista = explode('-', $voto['asambleista_obj']);
        $bancada_legislador = get_field('bancada', $info_asambleista[0]);
        $bancada_legislador = $bancada_legislador->slug;
        if (
          $voto['au'] ||
          $voto['AUS']
        ){
          foreach( $objeto_bancada['votos'] as &$bancada ){
            if ( $bancada['bancada_slug'] == $bancada_legislador ){
              $bancada['AU'] += 1;
              $bancada['acumulado'] += 1;
            }
          }
        } else {
          foreach( $objeto_bancada['votos'] as &$bancada ){
            if ( $bancada['bancada_slug'] == $bancada_legislador ){
              $bancada[$voto['voto']] += 1;
              $bancada['acumulado'] += 1;
              //return $bancada;
            }
          }
          //$objeto_bancada[$bancada_legislador]['voto'][$voto['voto']] += 1;
        }
        //return $voto;
        
      }
      //return $bancada_legislador;
      //return $votos;
      return $objeto_bancada;
    }

  }catch(Exception $e){
    echo 'Excepcion caturada: ' . $e->getMessage();
  }

}

function agrupar_votos_por_partido( array $votos ){
  if( ! is_array( $votos ) ) return;

  try{
    /**
     * Obtener partidos
     */
    $bancadas = get_terms(['taxonomy' => 'partido_politico', 'hide_empty' => false]);
    $objeto_bancada = crear_objeto_bancada($bancadas);
    //return $objeto_bancada;
    /**
     * Ordenar votos por bancada
     */
    if ( $votos ){
      foreach ( $votos as $voto ){
        $info_asambleista = explode('-', $voto['asambleista_obj']);
        $bancada_legislador = get_field('partido_politico', $info_asambleista[0]);
        $bancada_legislador = $bancada_legislador->slug;
        if (
          $voto['au'] ||
          $voto['aus']
        ){
          foreach( $objeto_bancada['votos'] as &$bancada ){
            if ( $bancada['bancada_slug'] == $bancada_legislador ){
              $bancada['AU'] += 1;
              $bancada['acumulado'] += 1;
            }
          }
        } else {
          foreach( $objeto_bancada['votos'] as &$bancada ){
            if ( $bancada['bancada_slug'] == $bancada_legislador ){
              $bancada[$voto['voto']] += 1;
              $bancada['acumulado'] += 1;
              //return $bancada;
            }
          }
          //$objeto_bancada[$bancada_legislador]['voto'][$voto['voto']] += 1;
        }
        //return $voto;
        
      }
      if (!isset($_GET) && !isset($_GET['organizacion_politica'])){
        foreach( $objeto_bancada['votos'] as $index => $bancada ){
          if( $bancada['acumulado'] < 12 ) {
            array_splice($objeto_bancada['votos'], $index, 1);
          }
        }
      }
      //return $bancada_legislador;
      //return $votos;
      //array_splice($objeto_bancada['votos'], 8, 1);
      return $objeto_bancada;
    }

  }catch(Exception $e){
    echo 'Excepcion caturada: ' . $e->getMessage();
  }

}

function obtener_voto_legislador( array $votos, $legislador_ID ){
  if( ! is_array( $votos ) ) return;
  try {
    
    foreach ($votos as $index => $voto) {
      
      $voto_asambleista_ID = explode('-', $voto['asambleista_obj']);
      if ($voto_asambleista_ID[0] == $legislador_ID){
        $au = $si = $no = $ab = $bl = 0;
        if (
          $voto['au'] ||
          $voto['aus']
        ){
          $au = 1;
        } else {
          switch($voto['voto']) {
            case 'SI': $si++; break;
            case 'NO': $no++; break;
            case 'AB': $ab++; break;
            case 'BL': $bl++; break;
          }
        }
        $objeto_bancada['votos'][] = array(
          'bancada_id' => $legislador_ID,
          'bancada_nombre' => $voto['asambleista'],
          'bancada_nombre_corto' => $voto['asambleista'],
          'bancada_slug' => $legislador_ID,
          'acumulado' => 0,
          'AU' => $au,
          'SI' => $si,
          'NO' => $no,
          'AB' => $ab,
          'BL' => $bl,
        );
        // echo '<pre>';
        // // var_dump($voto_asambleista_ID[0]);
        // // var_dump($legislador_ID);
        // var_dump($voto);
        // echo '</pre>';
      }
    }

    return $objeto_bancada;
  }
  catch (Exception $e) {
    var_dump($e);
  }

}

function mostrar_datos_single_votacion(){
  global $json_bancada;
  $votacion_id = get_the_ID();
  $titulo = get_the_title();
  $url = get_the_permalink();
  $fecha = get_field('fecha', $votacion_id);
  $sesion = get_field('sesion_de_origen', $votacion_id);
  $resumen = get_the_excerpt();

  $votos = obtener_objeto_votos($votacion_id);
  $votos_por_partido = agrupar_votos_por_partido($votos);
  /**
   * Sacar filas con acumulados menores a 5 votos
   */
  $votos_por_partido_filtrado = ['votos'];

  foreach( $votos_por_partido['votos'] as $index => $voto_partido ){
    if ( $voto_partido['acumulado'] >= 5 ){
      $votos_por_partido_filtrado['votos'][] = $voto_partido;
    }
  }
  // Ordenamiento mayor a menor por acumulado de votos del partido
  $columna_acumulado = array_column($votos_por_partido_filtrado['votos'], 'acumulado');
  array_multisort( $columna_acumulado, SORT_ASC, $votos_por_partido_filtrado['votos']);

  $json_bancada = json_encode($votos_por_partido_filtrado);

  echo sprintf(
    '
	<div class="row ps-lg-5">
		<div class="col-12">
			<h4 style="line-height: 1.5;">%3$s</h4>
		</div>
	</div>
	<div class="row ps-lg-5">
		<div class="col-12 col-lg-6">
			<span class="d-block text-warning px-2">%6$s</span>
    		<p class="px-2">%4$s</p>
    		<span class="text-gray-25 d-block ms-2">%5$s</span>
		</div>
		<div class="col-12 col-lg-3"></div>
		<div class="col-12 col-lg-3">
			<a class="btn-csv me-4" href="#" data-votationid="%1$s"><i class="fas fa-file-csv fs-20" aria-hidden="true"></i></a>
			<a class="btn-xls" href="#" data-votationid="%1$s"><i class="fas fa-file-excel fs-20" aria-hidden="true"></i></a>
		</div>
	</div>
    ',
    $votacion_id,
    $url,
    $titulo,
    $resumen,
    $sesion->post_title,
    $fecha
  );
}

/**
 * AJAX generate CSV
 */
add_action('wp_ajax_nopriv_ol_generate_csv_legisladores', 'ol_generate_csv_legisladores');
add_action('wp_ajax_ol_generate_csv_legisladores', 'ol_generate_csv_legisladores');
function ol_generate_csv_legisladores() {

    $items = ol_get_queried_legisladores($_GET);

    /**
     * Hacer la consulta con los argumentos del GET
     */
    $csv_fields = array();
    $csv_fields[] = 'Asambleísta Principal';
    $csv_fields[] = 'Género';
    $csv_fields[] = 'Organización Política';
    $csv_fields[] = 'Bancada';
    // $csv_fields[] = 'Votaciones consideradas';
    $csv_fields[] = 'Circunscripción';
    $csv_fields[] = 'Comisiones';
    // $csv_fields[] = 'Cargo';
    $csv_fields[] = 'Suplente';

    $output_handle = @fopen( 'php://output', 'w' );
    header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
    header( 'Content-Description: File Transfer' );
    header( 'Content-type: text/csv;charset=UTF-8' );
    header( 'Content-Disposition: attachment; filename=ol_ciudad_documento_cambiar_esto.csv');
    header( 'Expires: 0' );
    header( 'Pragma: public' );
    // Insert header row
    fputcsv( $output_handle, mb_convert_encoding($csv_fields,'UTF-16LE', 'UTF-8') );

    // Parse results to csv format
    if ( $items->have_posts() ) {
      while ( $items->have_posts() ) {
        $items->the_post();
        $titulo = get_the_title();
        $genero = get_field('genero') ? get_field('genero')->name : '';
        $organzacion_politica = get_field('partido_politico') ? get_field('partido_politico')->name : '';
        $bancada = get_field('bancada') ? get_field('bancada')->name : '';
        $circunscripcion = get_field('circunscripcion') ? get_field('circunscripcion')->name : '';
        $comision = get_field('comisiones_permanentes') ? get_field('comisiones_permanentes')->name : '';
        $suplente = get_field('colaborador') ? get_field('colaborador')->post_title : '';
        

        $csv_fields = array();
        $csv_fields[] = $titulo;
        $csv_fields[] = $genero;
        $csv_fields[] = $organzacion_politica;
        $csv_fields[] = $bancada;
        // $csv_fields[] = 'Votaciones consideradas';
        $csv_fields[] = $circunscripcion;
        $csv_fields[] = $comision;
        // $csv_fields[] = 'Cargo';
        $csv_fields[] = $suplente;
        // var_dump(get_the_title());
        fputcsv( $output_handle, mb_convert_encoding($csv_fields,'UTF-16LE', 'UTF-8') );
      }
    }
    // foreach ($items as $item) {
    //   var_dump($item);
    //   $csv_fields=array();
    //   $csv_fields[] = $item['title'];
    //   $csv_fields[] = $item['as'];
    //   $csv_fields[] = $item['au'];
    //   $csv_fields[] = $item['de'];
    //   $csv_fields[] = $item['as'] + $item['au'];
    //   $csv_fields[] = $item['or'];
    //   $csv_fields[] = $item['re'];
    //   $csv_fields[] = $item['ob'];
    //   $csv_fields[] = $item['so'];
    //   //$row = '';
    //   //$row = '';
    //   //$leadArray = (array) $Result; // Cast the Object to an array
    //   // Add row to file
    // }
  //var_dump( $rank );
  

  // Close output file stream
  fclose( $output_handle );
  die;
}

/**
 * AJAX generate XLS
 */
add_action('wp_ajax_nopriv_ol_generate_consolidado_miembros_xls', 'ol_generate_consolidado_miembros_xls');
add_action('wp_ajax_ol_generate_consolidado_miembros_xls', 'ol_generate_consolidado_miembros_xls');
function ol_generate_consolidado_miembros_xls(){

  $miembros = ol_get_queried_legisladores($_GET);

  try{
    if ( file_exists( CBXPHPSPREADSHEET_ROOT_PATH . 'includes/lib/vendor/autoload.php' ) ) {
        //Include PHPExcel
        require_once( CBXPHPSPREADSHEET_ROOT_PATH . 'includes/lib/vendor/autoload.php' );
        //Crear instancia excel
        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        //Logos cabecera

        // var_dump('mas cosas');
        
        //Logo website
        // $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        // $drawing->setPath($city_logo);
        // $drawing->setWidth(180); 
        // $drawing->setResizeProportional(false);
        // $drawing->setHeight(180);
        // $drawing->setCoordinates('B1');
        // $drawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));

        //Logo FCD
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setPath( get_stylesheet_directory() . '/images/FCD-Logo.png');
        $drawing->setWidth(230); 
        $drawing->setResizeProportional(false);
        $drawing->setHeight(100);
        $drawing->setCoordinates('F3');
        $drawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));
        $drawing->setOffsetX(200);
        $drawing->setOffsetY(10);
        //Styles
        $titulostyle = array(
            'font' => [
                        'name' => 'Calibri',
                        'bold' => true,
                        //'italic' => false,
                        //'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLE,
                        'strikethrough' => false,
                        /*'color' => [
                            'rgb' => '808080'
                        ]*/
                    ],
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
            )
        );
        $headerstyle=array('font' => [
            'name' => 'Calibri',
            'bold' => true,
            'strikethrough' => false,
              ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'left'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'right'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    
                ]
            ],
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
            )
        );
        $longstyle=array(
            'font' => [
                'size' =>11
            ]
            ,
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'left'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'right'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    
                ]
            ],
            'alignment' => array(
                // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
                )
        );
        $style=array(
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'left'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'right'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    
                ]
            ],
            'alignment' => array(
                // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
                )
        );

        //Columnas ancho ->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('36');
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('10');
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('25');
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('12');
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('30');
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('35');
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('30');

        //TITULO
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B8','Listado de Asambleístas')
            // ->setCellValue('B9','Período '.PERIOD_BEGINS.' a '.$fecha)
            ->setCellValue('B11','Nombre completo')
            ->setCellValue('C11','Género')
            ->setCellValue('D11','Organización Política')
            ->setCellValue('E11','Bancada')
            ->setCellValue('F11','Circunscripción')
            ->setCellValue('G11','Comisiones')
            ->setCellValue('H11','Suplente');

        //TAMAÑOS
        //$objPHPExcel->getActiveSheet()->getRowDimension(11)->setRowHeight(-1);

        //CONTENIDO
        $i = 11;
        // $ndatos = 30;

        while ( $miembros->have_posts() ){ $miembros->the_post();
            $i++;
            $miembro_ID = get_the_ID();
            $nombres = get_field('nombres');
            $apellidos = get_field('apellidos');
            $nombre_completo = $nombres . ' ' . $apellidos;
            $genero = ( get_field('genero') ) ? get_field('genero')->name : '';
            $organzacion_politica = ( get_field('partido_politico') ) ? get_field('partido_politico')->name : '';
            $bancada = ( get_field('bancada') ) ? get_field('bancada')->name : '';
            $circunscripcion = ( get_field('circunscripcion') ) ? get_field('circunscripcion')->name : '';
            $circunscripcion_hijo = ( get_field('subcircunscripcion') ) ? get_field('subcircunscripcion')->name : '';
            $circunscripcion = ( $circunscripcion === 'Nacional') ? 'Nacional' : $circunscripcion . ' -> ' . $circunscripcion_hijo;
            $comisiones_nombres = [];
            $comisiones = get_field('comisiones_permanentes');
            if ( $comisiones ) {
              foreach ( $comisiones as $comision ) {
                $comisiones_nombres[] = $comision->name;
              }
            }
            $suplente = ( get_field('colaborador') ) ? get_field('colaborador')->post_title : '';
            // var_dump($suplente);
            // echo $comisiones . PHP_EOL;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, html_entity_decode($nombre_completo));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $genero);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, html_entity_decode($organzacion_politica));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, html_entity_decode($bancada));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, html_entity_decode($circunscripcion));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, implode(' - ', $comisiones_nombres));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, html_entity_decode($suplente));
        }

        // Estilos por columna
        $objPHPExcel->setActiveSheetIndex(0);
        //$objPHPExcel->getActiveSheet()->getStyle('B11:'.$col.'11')->applyFromArray($headerstyle);
        $objPHPExcel->getActiveSheet()->getStyle('B11:H11')->applyFromArray($headerstyle);
        $objPHPExcel->getActiveSheet()->getStyle('H12:H'.$i)->applyFromArray($longstyle);
        $objPHPExcel->getActiveSheet()->getStyle('G12:G'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('F12:F'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('E12:E'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('C12:C'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('D12:D'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('B12:B'.$i)->applyFromArray($longstyle);

        //titulo
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B8:F8');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B9:F9');

        //Header styles
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B9')->applyFromArray($titulostyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B8')->applyFromArray($titulostyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('C11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('D11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('E11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('F11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('G11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('H11')->applyFromArray($headerstyle);

        $objPHPExcel->getActiveSheet()->setTitle('Listado de Asambleístas');

        // CABEZERAS
        // Redirect output to a client’s web browser (Xls)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="01simple.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        

        //ESCRITURA
        $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        //ENVIAR JSON CON ARCHIVO
        $response =  array(
                'op' => 'ok',
                'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
            );
        header('Content-type: application/json');
        die(json_encode($response));
        
    }
  } catch (Exception $e) {
      echo $e;
  }
  wp_die();
}

/**
 * AJAX generate CSV Votacion
 */
add_action('wp_ajax_nopriv_ol_generate_csv_votacion', 'ol_generate_csv_votacion');
add_action('wp_ajax_ol_generate_csv_votacion', 'ol_generate_csv_votacion');
function ol_generate_csv_votacion() {

    $votacion_object = get_post($_GET['votacion']);
    $votos = get_field('listado_de_votos', $_GET['votacion']);

    /**
     * Hacer la consulta con los argumentos del GET
     */
    $csv_fields = array();
    $csv_fields[] = 'Votación';
    $csv_fields[] = 'Asambleísta Principal';
    $csv_fields[] = 'Organización Política';
    $csv_fields[] = 'Voto Suplente';
    $csv_fields[] = 'Suplente';
    $csv_fields[] = 'Voto';

    $output_handle = @fopen( 'php://output', 'w' );
    header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
    header( 'Content-Description: File Transfer' );
    header( 'Content-type: text/csv;charset=UTF-8' );
    header( 'Content-Disposition: attachment; filename=ol_ciudad_documento_cambiar_esto.csv');
    header( 'Expires: 0' );
    header( 'Pragma: public' );
    // Insert header row
    fputcsv( $output_handle, mb_convert_encoding($csv_fields,'UTF-16LE', 'UTF-8') );

    // Parse results to csv format
    if ( $votos ) {
      foreach ( $votos as $voto ) {
        $asambleista_obj = explode('-', $voto['asambleista_obj']);
        $organizacion_obj = '';
        if($asambleista_obj){
          $organizacion_id = $asambleista_obj[1];
          $organizacion_obj = get_term( $organizacion_id, 'partido_politico' )->name;
        }
        $votacion = $votacion_object->post_title;
        $nombre = $voto['asambleista'];
        $organzacion_politica = $organizacion_obj;
        $voto_suplente = '';
        $suplente = ( $voto['suplente'] ) ? $voto['suplente']->post_title : '' ;
        if ($suplente) $voto_suplente = 'Si';

        $csv_fields = array();
        $csv_fields[] = $votacion;
        $csv_fields[] = $nombre;
        $csv_fields[] = $organzacion_politica;
        $csv_fields[] = $voto_suplente;
        $csv_fields[] = $suplente;
        $csv_fields[] = (empty($voto['voto'])) ? 'AU' : $voto['voto'];
         fputcsv( $output_handle, mb_convert_encoding($csv_fields,'UTF-16LE', 'UTF-8') );
      }
    }
  // Close output file stream
  fclose( $output_handle );
  die;
}

/**
 * AJAX generate XLS Votacion
 */
add_action('wp_ajax_nopriv_ol_generate_xls_votacion', 'ol_generate_xls_votacion');
add_action('wp_ajax_ol_generate_xls_votacion', 'ol_generate_xls_votacion');
function ol_generate_xls_votacion(){

  $votacion_object = get_post($_GET['votacion']);
  $votos = get_field('listado_de_votos', $_GET['votacion']);

  try{
    if ( file_exists( CBXPHPSPREADSHEET_ROOT_PATH . 'includes/lib/vendor/autoload.php' ) ) {
        //Include PHPExcel
        require_once( CBXPHPSPREADSHEET_ROOT_PATH . 'includes/lib/vendor/autoload.php' );
        //Crear instancia excel
        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        //Logos cabecera
        // Logo website
        // $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        // $drawing->setPath('https://observatoriolegislativo.ec/wp-content/uploads/2021/12/ob.-legislativo-.jpg');
        // $drawing->setWidth(180); 
        // $drawing->setResizeProportional(false);
        // $drawing->setHeight(180);
        // $drawing->setCoordinates('B1');
        // $drawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));

        //Logo FCD
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setPath( get_stylesheet_directory() . '/images/FCD-Logo.png');
        $drawing->setWidth(230); 
        $drawing->setResizeProportional(false);
        $drawing->setHeight(100);
        $drawing->setCoordinates('F3');
        $drawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));
        $drawing->setOffsetX(200);
        $drawing->setOffsetY(10);
        //Styles
        $titulostyle = array(
            'font' => [
                        'name' => 'Calibri',
                        'bold' => true,
                        //'italic' => false,
                        //'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLE,
                        'strikethrough' => false,
                        /*'color' => [
                            'rgb' => '808080'
                        ]*/
                    ],
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
            )
        );
        $headerstyle=array('font' => [
            'name' => 'Calibri',
            'bold' => true,
            'strikethrough' => false,
              ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'left'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'right'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    
                ]
            ],
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
            )
        );
        $longstyle=array(
            'font' => [
                'size' =>11
            ]
            ,
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'left'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'right'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    
                ]
            ],
            'alignment' => array(
                // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
                )
        );
        $style=array(
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'left'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'right'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    
                ]
            ],
            'alignment' => array(
                // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
                )
        );

        //Columnas ancho ->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('36');
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('36');
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('12');
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('36');
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('12');
        
        $fecha_votacion = get_field('fecha', $votacion_object->ID);
        //TITULO
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B8','Listado de Votos')
            ->setCellValue('B9','Votación: '.$votacion_object->post_title)
            ->setCellValue('B10','Fecha Votación: '.$fecha_votacion)
            ->setCellValue('B11','Asambleísta principal')
            ->setCellValue('C11','Organización Política')
            ->setCellValue('D11','Votó el suplente')
            ->setCellValue('E11','Suplente')
            ->setCellValue('F11','Voto');

        //TAMAÑOS
        //$objPHPExcel->getActiveSheet()->getRowDimension(11)->setRowHeight(-1);

        //CONTENIDO
        $i = 11;
        // $ndatos = 30;

        if ( $votos ) {
          foreach ( $votos as $voto ) {
            $i++;
            $asambleista_obj = explode('-', $voto['asambleista_obj']);
            $organizacion_obj = '';
            if($asambleista_obj){
              $organizacion_id = $asambleista_obj[1];
              $organizacion_obj = get_term( $organizacion_id, 'partido_politico' )->name;
            }
            $nombre = $voto['asambleista'];
            $organzacion_politica = $organizacion_obj;
            $voto_suplente = '';
            $suplente = ( $voto['suplente'] ) ? $voto['suplente']->post_title : '' ;
            if ($suplente) $voto_suplente = 'Si';
            $votoliteral = 'Ausente';
            switch ($voto['voto']){
              case 'SI': $votoliteral = 'Si'; break; 
              case 'NO': $votoliteral = 'No'; break; 
              case 'AB': $votoliteral = 'Abstención'; break; 
              case 'BL': $votoliteral = 'Blanco'; break; 
            }

            //$votoreal = ( $voto['au'] && $voto['aus'] )? 'Ausente' : $votoliteral;

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, html_entity_decode($nombre));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, html_entity_decode($organzacion_politica));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $voto_suplente);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, html_entity_decode($suplente));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $votoliteral);
          }
        }

        // Estilos por columna
        $objPHPExcel->setActiveSheetIndex(0);
        //$objPHPExcel->getActiveSheet()->getStyle('B11:'.$col.'11')->applyFromArray($headerstyle);
        $objPHPExcel->getActiveSheet()->getStyle('B11:F11')->applyFromArray($headerstyle);
        $objPHPExcel->getActiveSheet()->getStyle('F12:F'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('E12:E'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('C12:C'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('D12:D'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('B12:B'.$i)->applyFromArray($longstyle);

        //titulo
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B8:F8');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B9:F9');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B10:F10');

        //Header styles
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B9')->applyFromArray($titulostyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B8')->applyFromArray($titulostyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('C11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('D11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('E11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('F11')->applyFromArray($headerstyle);

        $objPHPExcel->getActiveSheet()->setTitle('Totalización votación');

        // CABEZERAS
        // Redirect output to a client’s web browser (Xls)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="01simple.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        

        //ESCRITURA
        $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        //ENVIAR JSON CON ARCHIVO
        $response =  array(
                'op' => 'ok',
                'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
            );
        header('Content-type: application/json');
        die(json_encode($response));
        
    }
  } catch (Exception $e) {
      echo $e;
  }
  wp_die();
}

/**
 * AJAX generate XLS Asamblea en Cifras
 */
add_action('wp_ajax_nopriv_ol_generate_xls_asamblea_cifras', 'ol_generate_xls_asamblea_cifras');
add_action('wp_ajax_ol_generate_xls_asamblea_cifras', 'ol_generate_xls_asamblea_cifras');
function ol_generate_xls_asamblea_cifras(){

  // var_dump($_GET);
  // die;

  try{
    if ( file_exists( CBXPHPSPREADSHEET_ROOT_PATH . 'includes/lib/vendor/autoload.php' ) ) {
        //Include PHPExcel
        require_once( CBXPHPSPREADSHEET_ROOT_PATH . 'includes/lib/vendor/autoload.php' );
        //Crear instancia excel
        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        //Logos cabecera
        // Logo website
        // $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        // $drawing->setPath('https://observatoriolegislativo.ec/wp-content/uploads/2021/12/ob.-legislativo-.jpg');
        // $drawing->setWidth(180); 
        // $drawing->setResizeProportional(false);
        // $drawing->setHeight(180);
        // $drawing->setCoordinates('B1');
        // $drawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));

        //Logo FCD
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setPath( get_stylesheet_directory() . '/images/FCD-Logo.png');
        $drawing->setWidth(230); 
        $drawing->setResizeProportional(false);
        $drawing->setHeight(100);
        $drawing->setCoordinates('F3');
        $drawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));
        $drawing->setOffsetX(200);
        $drawing->setOffsetY(10);
        //Styles
        $titulostyle = array(
            'font' => [
                        'name' => 'Calibri',
                        'bold' => true,
                        //'italic' => false,
                        //'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLE,
                        'strikethrough' => false,
                        /*'color' => [
                            'rgb' => '808080'
                        ]*/
                    ],
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
            )
        );
        $headerstyle=array('font' => [
            'name' => 'Calibri',
            'bold' => true,
            'strikethrough' => false,
              ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'left'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'right'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    
                ]
            ],
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
            )
        );
        $longstyle=array(
            'font' => [
                'size' =>11
            ]
            ,
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'left'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'right'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    
                ]
            ],
            'alignment' => array(
                // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
                )
        );
        $style=array(
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'left'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'right'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    
                ]
            ],
            'alignment' => array(
                // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
                )
        );

        //Columnas ancho ->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('36');
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('20');
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('36');
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('20');
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('20');
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('40');
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('24');
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('24');
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth('16');

        //TITULO
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B8','Asamblea Nacional del Ecuador')
            ->setCellValue('B9','Asamblea en Cifras')
            ->setCellValue('B11','Nombre completo')
            ->setCellValue('C11','Género')
            ->setCellValue('D11','Suplente')
            ->setCellValue('E11','Período Legislativo')
            ->setCellValue('F11','Estado')
            ->setCellValue('G11','Organziación Política')
            ->setCellValue('H11','Bancada')
            ->setCellValue('I11','Circunscripción')
            ->setCellValue('J11','Proyectos de ley')
            ->setCellValue('K11','Observaciones de ley')
            ->setCellValue('L11','Pedidos de juicio político')
            ->setCellValue('M11','Pedidos de información')
            ->setCellValue('N11','Respuesta a pedidos de información')
            ->setCellValue('O11','Número de Atrasos')
            ->setCellValue('P11','Sesiones posibles')
            ->setCellValue('Q11','Asistencia en votaciones de asambleísta principal')
            ->setCellValue('R11','Asistencia en votaciones de asambleísta suplente')
            ->setCellValue('S11','Ausencia en votaciones de asambleísta principal')
            ->setCellValue('T11','Ausencia en votaciones de asambleísta suplente')
            ->setCellValue('U11','Votaciones posibles')
            ->setCellValue('V11','Ocupación de la Curul');

        //TAMAÑOS
        //$objPHPExcel->getActiveSheet()->getRowDimension(11)->setRowHeight(-1);

        //CONTENIDO
        $i = 11;
        // $ndatos = 30;

        $asambleistas = ol_get_all_legisladores($_GET);
        $votaciones = new WP_Query( 
          array(
            'post_type' => 'votacion',
            'posts_per_page' => -1,
            'post_status' => 'publish'
          )
        );

        if ( $asambleistas->have_posts() ) {
          while ( $asambleistas->have_posts() ) {
            $i++;
            $asambleistas->the_post();

            /**
             * obtener los datos del asambleista
             */
            $a_ID = get_the_ID();
            $nombre = get_the_title();
            $genero = (get_field('genero', $a_ID)) ? get_field('genero', $a_ID)->name : '';
            $suplente = (get_field('colaborador', $a_ID)) ? get_field('colaborador', $a_ID)->post_title : '';
            $periodo = '2021-2025';
            $estado = (get_field('exasambleista', $a_ID)) ? 'Ex asambleista' : 'En funciones';
            $organzacion_politica = (get_field('partido_politico', $a_ID)) ? get_field('partido_politico', $a_ID)->name : '';
            $bancada = (get_field('bancada', $a_ID)) ? get_field('bancada', $a_ID)->name : 'Sin bancada';
            $circunscripcion = (get_field('circunscripcion', $a_ID)) ? get_field('circunscripcion', $a_ID)->name : '';

            /**
             * obtener los datos estadisticos
             */
            $proyectos_reformatorios = (get_field('proyectos_reformatorios', $a_ID)) ? get_field('proyectos_reformatorios', $a_ID) : 0;
            $proyectos_leyes_nuevas = (get_field('proyectos_leyes_nuevas', $a_ID)) ? get_field('proyectos_leyes_nuevas', $a_ID) : 0;
            $proyectos = $proyectos_reformatorios + $proyectos_leyes_nuevas;
            $observaciones = get_field('cantidad_de_observaciones', $a_ID);
            $juicios_politicos = get_field('pedidos_de_juicio_politico', $a_ID);
            $pedidos_efectivos = get_field('solicitudes_efectivas', $a_ID);
            $solicitides_efectivas = (get_field('solicitudes_efectivas', $a_ID))?get_field('solicitudes_efectivas', $a_ID):0;
            $solicitides_sin_respuesta = (get_field('solicitudes_sin_respuesta', $a_ID))?get_field('solicitudes_sin_respuesta', $a_ID): 0;
            $pedidos_informacion = 0;
            $pedidos_informacion = $solicitides_efectivas + $solicitides_sin_respuesta;
            $atrasos = get_field('atrasos', $a_ID);

            /**
             * obtener los datos votaciones
             */
            $sesiones = ol_datos_sesiones_asambleista($nombre, $votaciones);
            // var_dump($sesiones);
            // die;

            /**
            * Montar los datos
            */
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, html_entity_decode($nombre));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $genero);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, html_entity_decode($suplente));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $periodo);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $estado);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, html_entity_decode($organzacion_politica));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, html_entity_decode($bancada));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i, html_entity_decode($circunscripcion));
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $proyectos);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$i, $observaciones);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $juicios_politicos);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$i, $pedidos_informacion);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$i, $solicitides_efectivas);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.$i, $atrasos);
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.$i, $sesiones['sesiones_asistidas']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.$i, $sesiones['asistencia_principal']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R'.$i, $sesiones['asistencia_suplente']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S'.$i, $sesiones['ausencia_principal']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('T'.$i, $sesiones['ausencia_suplente']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('U'.$i, $sesiones['votaciones_posibles']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('V'.$i, $sesiones['porcentaje_asistencia']);

          }
        }

        // Estilos por columna
        $objPHPExcel->setActiveSheetIndex(0);
        //$objPHPExcel->getActiveSheet()->getStyle('B11:'.$col.'11')->applyFromArray($headerstyle);
        $objPHPExcel->getActiveSheet()->getStyle('B11:V11')->applyFromArray($headerstyle);
        $objPHPExcel->getActiveSheet()->getStyle('F12:F'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('E12:E'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('C12:C'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('D12:D'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('G12:G'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('H12:H'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('I12:I'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('J12:J'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('K12:K'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('L12:L'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('M12:M'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('N12:N'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('O12:O'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('P12:P'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('Q12:Q'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('R12:R'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('S12:S'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('T12:T'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('U12:U'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('V12:V'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('B12:B'.$i)->applyFromArray($longstyle);

        //titulo
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B8:F8');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B9:F9');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B10:F10');

        //Header styles
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B9')->applyFromArray($titulostyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B8')->applyFromArray($titulostyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('C11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('D11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('E11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('F11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('G11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('H11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('I11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('J11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('K11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('L11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('M11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('N11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('O11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('P11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('Q11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('R11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('S11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('T11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('U11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('V11')->applyFromArray($headerstyle);

        $objPHPExcel->getActiveSheet()->setTitle('Asamblea en Cifras');

        // CABEZERAS
        // Redirect output to a client’s web browser (Xls)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="01simple.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        

        //ESCRITURA
        $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        //ENVIAR JSON CON ARCHIVO
        $response =  array(
                'op' => 'ok',
                'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
            );
        header('Content-type: application/json');
        die(json_encode($response));
        
    }
  } catch (Exception $e) {
      echo $e;
  }
  wp_die();
}

/**
 * AJAX generate CSV Asamblea en Cifras
 */
add_action('wp_ajax_nopriv_ol_generate_csv_asamblea_cifras', 'ol_generate_csv_asamblea_cifras');
add_action('wp_ajax_ol_generate_csv_asamblea_cifras', 'ol_generate_csv_asamblea_cifras');
function ol_generate_csv_asamblea_cifras() {

  /**
   * Hacer la consulta con los argumentos del GET
   */
  $csv_fields = array();
  $csv_fields[] = 'Nombre completo';
  $csv_fields[] = 'Género';
  $csv_fields[] = 'Suplente';
  $csv_fields[] = 'Período Legislativo';
  $csv_fields[] = 'Estado';
  $csv_fields[] = 'Organziación Política';
  $csv_fields[] = 'Bancada';
  $csv_fields[] = 'Circunscripción';
  $csv_fields[] = 'Proyectos de ley';
  $csv_fields[] = 'Observaciones de ley';
  $csv_fields[] = 'Pedidos de juicio político';
  $csv_fields[] = 'Pedidos de información';
  $csv_fields[] = 'Respuesta a pedidos de información';
  $csv_fields[] = 'Número de Atrasos';
  $csv_fields[] = 'Sesiones posibles';
  $csv_fields[] = 'Asistencia en votaciones de asambleísta principal';
  $csv_fields[] = 'Asistencia en votaciones de asambleísta suplente';
  $csv_fields[] = 'Ausencia en votaciones de asambleísta principal';
  $csv_fields[] = 'Ausencia en votaciones de asambleísta suplente';
  $csv_fields[] = 'Votaciones posibles';
  $csv_fields[] = 'Ocupación de la Curul';

  $output_handle = @fopen( 'php://output', 'w' );
  header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
  header( 'Content-Description: File Transfer' );
  header( 'Content-type: text/csv;charset=UTF-8' );
  header( 'Content-Disposition: attachment; filename=ol_ciudad_documento_cambiar_esto.csv');
  header( 'Expires: 0' );
  header( 'Pragma: public' );
  // Insert header row
  fputcsv( $output_handle, mb_convert_encoding($csv_fields,'UTF-16LE', 'UTF-8') );

  $asambleistas = ol_get_all_legisladores($_GET);
  $votaciones = new WP_Query( 
    array(
      'post_type' => 'votacion',
      'posts_per_page' => -1,
      'post_status' => 'publish'
    )
  );

  // Parse results to csv format
  if ( $asambleistas->have_posts() ) {
    while ( $asambleistas->have_posts() ) {
      $asambleistas->the_post();

      /**
       * obtener los datos del asambleista
       */
      $a_ID = get_the_ID();
      $nombre = get_the_title();
      $genero = (get_field('genero', $a_ID)) ? get_field('genero', $a_ID)->name : '';
      $suplente = (get_field('colaborador', $a_ID)) ? get_field('colaborador', $a_ID)->post_title : '';
      $periodo = '2021-2025';
      $estado = (get_field('exasambleista', $a_ID)) ? 'Ex asambleista' : 'En funciones';
      $organzacion_politica = (get_field('partido_politico', $a_ID)) ? get_field('partido_politico', $a_ID)->name : '';
      $bancada = (get_field('bancada', $a_ID)) ? get_field('bancada', $a_ID)->name : 'Sin bancada';
      $circunscripcion = (get_field('circunscripcion', $a_ID)) ? get_field('circunscripcion', $a_ID)->name : '';

      /**
       * obtener los datos estadisticos
       */
      $proyectos_reformatorios = (get_field('proyectos_reformatorios', $a_ID)) ? get_field('proyectos_reformatorios', $a_ID) : 0;
      $proyectos_leyes_nuevas = (get_field('proyectos_leyes_nuevas', $a_ID)) ? get_field('proyectos_leyes_nuevas', $a_ID) : 0;
      $proyectos = $proyectos_reformatorios + $proyectos_leyes_nuevas;
      $observaciones = get_field('cantidad_de_observaciones', $a_ID);
      $juicios_politicos = get_field('pedidos_de_juicio_politico', $a_ID);
      $pedidos_efectivos = get_field('solicitudes_efectivas', $a_ID);
      $solicitides_efectivas = (get_field('solicitudes_efectivas', $a_ID))?get_field('solicitudes_efectivas', $a_ID):0;
      $solicitides_sin_respuesta = (get_field('solicitudes_sin_respuesta', $a_ID))?get_field('solicitudes_sin_respuesta', $a_ID): 0;
      $pedidos_informacion = 0;
      $pedidos_informacion = $solicitides_efectivas + $solicitides_sin_respuesta;
      $atrasos = get_field('atrasos', $a_ID);

      /**
       * obtener los datos votaciones
       */
      $sesiones = ol_datos_sesiones_asambleista($nombre, $votaciones);
      // var_dump($sesiones);
      // die;

      $csv_fields = array();
      $csv_fields[] = $nombre;
      $csv_fields[] = $genero;
      $csv_fields[] = $suplente;
      $csv_fields[] = $periodo;
      $csv_fields[] = $estado;
      $csv_fields[] = $organzacion_politica;
      $csv_fields[] = $bancada;
      $csv_fields[] = $circunscripcion;
      $csv_fields[] = $proyectos;
      $csv_fields[] = $observaciones;
      $csv_fields[] = $juicios_politicos;
      $csv_fields[] = $pedidos_informacion;
      $csv_fields[] = $solicitides_efectivas;
      $csv_fields[] = $atrasos;
      $csv_fields[] = $sesiones['sesiones_asistidas'];
      $csv_fields[] = $sesiones['asistencia_principal'];
      $csv_fields[] = $sesiones['asistencia_suplente'];
      $csv_fields[] = $sesiones['ausencia_principal'];
      $csv_fields[] = $sesiones['ausencia_suplente'];
      $csv_fields[] = $sesiones['votaciones_posibles'];
      $csv_fields[] = $sesiones['porcentaje_asistencia'];
      fputcsv( $output_handle, mb_convert_encoding($csv_fields,'UTF-16LE', 'UTF-8') );
    }
  }
  // Close output file stream
  fclose( $output_handle );
  die;
}

/**
 * AJAX generate XLS General votaciones
 */
add_action('wp_ajax_nopriv_ol_generate_xls_general_votaciones', 'ol_generate_xls_general_votaciones');
add_action('wp_ajax_ol_generate_xls_general_votaciones', 'ol_generate_xls_general_votaciones');
function ol_generate_xls_general_votaciones(){

  try{
    if ( file_exists( CBXPHPSPREADSHEET_ROOT_PATH . 'includes/lib/vendor/autoload.php' ) ) {
        //Include PHPExcel
        require_once( CBXPHPSPREADSHEET_ROOT_PATH . 'includes/lib/vendor/autoload.php' );
        //Crear instancia excel
        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        //Logos cabecera
        // Logo website
        // $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        // $drawing->setPath('https://observatoriolegislativo.ec/wp-content/uploads/2021/12/ob.-legislativo-.jpg');
        // $drawing->setWidth(180); 
        // $drawing->setResizeProportional(false);
        // $drawing->setHeight(180);
        // $drawing->setCoordinates('B1');
        // $drawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));

        //Logo FCD
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setPath( get_stylesheet_directory() . '/images/FCD-Logo.png');
        $drawing->setWidth(230); 
        $drawing->setResizeProportional(false);
        $drawing->setHeight(100);
        $drawing->setCoordinates('F3');
        $drawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));
        $drawing->setOffsetX(200);
        $drawing->setOffsetY(10);
        //Styles
        $titulostyle = array(
            'font' => [
                        'name' => 'Calibri',
                        'bold' => true,
                        //'italic' => false,
                        //'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLE,
                        'strikethrough' => false,
                        /*'color' => [
                            'rgb' => '808080'
                        ]*/
                    ],
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
            )
        );
        $headerstyle=array('font' => [
            'name' => 'Calibri',
            'bold' => true,
            'strikethrough' => false,
              ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'left'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'right'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    
                ]
            ],
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
            )
        );
        $longstyle=array(
            'font' => [
                'size' =>11
            ]
            ,
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'left'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'right'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    
                ]
            ],
            'alignment' => array(
                // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
                )
        );
        $style=array(
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'left'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'right'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    
                ]
            ],
            'alignment' => array(
                // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
                )
        );

        //Columnas ancho ->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('12');
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('36');
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('12');
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('40');
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('20');
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('20');
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('20');

        //TITULO
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B8','Asamblea Nacional del Ecuador')
            ->setCellValue('B9','Detalles de Votaciones')
            ->setCellValue('B11','Fecha')
            ->setCellValue('C11','Sesión')
            ->setCellValue('D11','Votación')
            ->setCellValue('E11','Nombre corto')
            ->setCellValue('F11','Tema')
            ->setCellValue('G11','Subtema')
            // ->setCellValue('H11','Tipo de Votación');
            ->setCellValue('H11','Categorías Generales');

        //TAMAÑOS
        //$objPHPExcel->getActiveSheet()->getRowDimension(11)->setRowHeight(-1);

        //CONTENIDO
        $i = 11;
        // $ndatos = 30;
        $votaciones = new WP_Query( 
          array(
            'post_type' => 'votacion',
            'posts_per_page' => -1,
            'post_status' => 'publish'
          )
        );

        $votaciones_objs = array();

        /**
         * Configurar objeto de votacion
         */
        if ( $votaciones->have_posts() ) {
          while ( $votaciones->have_posts() ) {
            $votaciones->the_post();
            $nombre_corto = get_field('nombre_corto', get_the_ID());
            $fecha = get_field('fecha', get_the_ID());
            $numero = get_field('numero', get_the_ID());
            $sesion_de_origen = get_field('sesion_de_origen', get_the_ID());
            $tema = get_the_terms(get_the_ID(), 'tema');
            $subtema = get_the_terms(get_the_ID(), 'subtema');
            // $tipo = get_the_terms(get_the_ID(), 'tipo');
            $tipos = get_the_terms(get_the_ID(), 'categoria_votacion');
            $tipo = [];
            if ( $tipos ) {
              foreach ( $tipos as $term ){
                if ( $term->parent == 204 ) {
                  array_push($tipo, $term->name);
                }
              }
              $tipo = implode(', ', $tipo);
            }else{
              $tipo = '';
            }

            
            $votaciones_objs[] = array(
              'votacion_fecha' => $fecha,
              'votacion_id' => get_the_ID(),
              'votacion_nombre_corto' => $nombre_corto,
              'orden' => $numero,
              'sesion_id' => $sesion_de_origen->ID,
              'sesion_name' => $sesion_de_origen->post_title,
              'tema' => ($tema) ? $tema[0]->name : '',
              'subtema' => ($subtema) ? $subtema[0]->name : '',
              'tipo' => ($tipo) ? $tipo : '',
            );
          }
        }

        /**
         * Ordenar Objeto de votacion
         */
        $sesion_id = array_column($votaciones_objs, 'sesion_id');
        $orden = array_column($votaciones_objs, 'orden');
        array_multisort($sesion_id, SORT_ASC, $orden, SORT_ASC, $votaciones_objs);

        /**
        * Montar los datos en excel
        */
        foreach($votaciones_objs as $votacion_obj) {
          $i++;
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $votacion_obj['votacion_fecha']);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $votacion_obj['sesion_name']);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $votacion_obj['orden']);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $votacion_obj['votacion_nombre_corto']);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $votacion_obj['tema']);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, $votacion_obj['subtema']);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $votacion_obj['tipo']);
        }

        // Estilos por columna
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getStyle('B11:H11')->applyFromArray($headerstyle);
        $objPHPExcel->getActiveSheet()->getStyle('F12:F'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('E12:E'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('C12:C'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('D12:D'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('G12:G'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('H12:H'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('B12:B'.$i)->applyFromArray($longstyle);

        //titulo
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B8:F8');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B9:F9');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B10:F10');

        //Header styles
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B9')->applyFromArray($titulostyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B8')->applyFromArray($titulostyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('C11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('D11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('E11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('F11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('G11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('H11')->applyFromArray($headerstyle);

        $objPHPExcel->getActiveSheet()->setTitle('Listado Detalle de Votaciones');

        // CABEZERAS
        // Redirect output to a client’s web browser (Xls)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="01simple.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        //ESCRITURA
        $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        //ENVIAR JSON CON ARCHIVO
        $response =  array(
                'op' => 'ok',
                'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
            );
        header('Content-type: application/json');
        die(json_encode($response));

    }
  } catch (Exception $e) {
      echo $e;
  }
  wp_die();
}

/**
 * AJAX generate CSV General votaciones
 */
add_action('wp_ajax_nopriv_ol_generate_csv_general_votaciones', 'ol_generate_csv_general_votaciones');
add_action('wp_ajax_ol_generate_csv_general_votaciones', 'ol_generate_csv_general_votaciones');
function ol_generate_csv_general_votaciones() {

  /**
   * Hacer la consulta con los argumentos del GET
   */
  $csv_fields = array();
  $csv_fields[] = 'Fecha';
  $csv_fields[] = 'Sesión';
  $csv_fields[] = 'Votación';
  $csv_fields[] = 'Nombre corto';
  $csv_fields[] = 'Tema';
  $csv_fields[] = 'Subtema';
  $csv_fields[] = 'Categorías Generales';


  $output_handle = @fopen( 'php://output', 'w' );
  header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
  header( 'Content-Description: File Transfer' );
  header( 'Content-type: text/csv;charset=UTF-8' );
  header( 'Content-Disposition: attachment; filename=ol_ciudad_documento_cambiar_esto.csv');
  header( 'Expires: 0' );
  header( 'Pragma: public' );
  // Insert header row
  fputcsv( $output_handle, mb_convert_encoding($csv_fields,'UTF-16LE', 'UTF-8') );

  $votaciones = new WP_Query( 
    array(
      'post_type' => 'votacion',
      'posts_per_page' => -1,
      'post_status' => 'publish'
    )
  );
  $votaciones_objs = array();

  /**
   * Configurar objeto de votacion
   */
  if ( $votaciones->have_posts() ) {
    while ( $votaciones->have_posts() ) {
      $votaciones->the_post();
      $nombre_corto = get_field('nombre_corto', get_the_ID());
      $fecha = get_field('fecha', get_the_ID());
      $numero = get_field('numero', get_the_ID());
      $sesion_de_origen = get_field('sesion_de_origen', get_the_ID());
      $tema = get_the_terms(get_the_ID(), 'tema');
      $subtema = get_the_terms(get_the_ID(), 'subtema');
      // $tipo = get_the_terms(get_the_ID(), 'tipo');
      $tipos = get_the_terms(get_the_ID(), 'categoria_votacion');
      $tipo = [];
      if ( $tipos ) {
        foreach ( $tipos as $term ){
          if ( $term->parent == 204 ) {
            array_push($tipo, $term->name);
          }
        }
        $tipo = implode(', ', $tipo);
      }else{
        $tipo = '';
      }

      
      $votaciones_objs[] = array(
        'votacion_fecha' => $fecha,
        'votacion_id' => get_the_ID(),
        'votacion_nombre_corto' => $nombre_corto,
        'orden' => $numero,
        'sesion_id' => $sesion_de_origen->ID,
        'sesion_name' => $sesion_de_origen->post_title,
        'tema' => ($tema) ? $tema[0]->name : '',
        'subtema' => ($subtema) ? $subtema[0]->name : '',
        'tipo' => ($tipo) ? $tipo : '',
      );
    }
  }
  /**
   * Ordenar Objeto de votacion
   */
  $sesion_id = array_column($votaciones_objs, 'sesion_id');
  $orden = array_column($votaciones_objs, 'orden');
  array_multisort($sesion_id, SORT_ASC, $orden, SORT_ASC, $votaciones_objs);

  /**
  * Montar los datos en excel
  */
  foreach($votaciones_objs as $votacion_obj) {
    $csv_fields = array();
    $csv_fields[] = $votacion_obj['votacion_fecha'];
    $csv_fields[] = $votacion_obj['sesion_name'];
    $csv_fields[] = $votacion_obj['orden'];
    $csv_fields[] = $votacion_obj['votacion_nombre_corto'];
    $csv_fields[] = $votacion_obj['tema'];
    $csv_fields[] = $votacion_obj['subtema'];
    $csv_fields[] = $votacion_obj['tipo'];
    fputcsv( $output_handle, mb_convert_encoding($csv_fields,'UTF-16LE', 'UTF-8') );
  }
  // Close output file stream
  fclose( $output_handle );
  die;
}

function get_votaciones_asambleista($id){

  if (empty($id)) return;
  $votaciones_objs = [];
  $votaciones = new WP_Query( 
    array(
      'post_type' => 'votacion',
      'posts_per_page' => -1,
      'post_status' => 'publish'
    )
  );

  /**
   * Configurar objeto de votacion
   */
  if ( $votaciones->have_posts() ) {
    while ( $votaciones->have_posts() ) {
      $existe_en_lista = false;
      $votaciones->the_post();
      $nombre_corto = get_field('nombre_corto', get_the_ID());
      $fecha = get_field('fecha', get_the_ID());
      $numero = get_field('numero', get_the_ID());
      $sesion_de_origen = get_field('sesion_de_origen', get_the_ID());
      $tema = get_the_terms(get_the_ID(), 'tema');
      $subtema = get_the_terms(get_the_ID(), 'subtema');
      // $tipo = get_the_terms(get_the_ID(), 'tipo');
      $tipos = get_the_terms(get_the_ID(), 'categoria_votacion');
      $tipo = [];
      if ( $tipos ) {
        foreach ( $tipos as $term ){
          if ( $term->parent == 204 ) {
            array_push($tipo, $term->name);
          }
        }
        $tipo = implode(', ', $tipo);
      }else{
        $tipo = '';
      }

      $el_voto = 'Ausente';

      $vota_suplente = 'Principal';
      $suplente = '';
      $votos = get_field('listado_de_votos', get_the_ID());
      if ( $votos ) {
        foreach ( $votos as $voto ) {
          if ( strpos($voto['asambleista_obj'], $id) !== false ) {
            //var_dump($voto['asambleista_obj'], $asambleista_ID, get_the_ID());
            if ($voto['de']){
              $vota_suplente = 'Suplente';
              $suplente = $voto['suplente']->post_title;
            }
            if (empty($voto['voto']) || $voto['au']){
              $el_voto = 'Ausente';
            }else{
              switch($voto['voto']) {
                case 'SI': $el_voto = 'Si'; break;
                case 'NO': $el_voto = 'No'; break;
                case 'AB': $el_voto = 'Abstención'; break;
                case 'BL': $el_voto = 'Blanco'; break;
              }
            }
            $existe_en_lista = true;
            continue;
          }

        }
      }


      if ($existe_en_lista){
        $votaciones_objs[] = array(
          'votacion_fecha' => $fecha,
          'votacion_id' => get_the_ID(),
          'votacion_nombre_corto' => $nombre_corto,
          'orden' => $numero,
          'sesion_id' => $sesion_de_origen->ID,
          'sesion_name' => $sesion_de_origen->post_title,
          'tema' => ($tema) ? $tema[0]->name : '',
          'subtema' => ($subtema) ? $subtema[0]->name : '',
          'tipo' => ($tipo) ? $tipo : '',
          'suplente' => $suplente,
          'vota_sumplente' => $vota_suplente,
          'el_voto' => $el_voto,

        );
      }
    }
  }

  /**
   * Ordenar Objeto de votacion
   */
  $sesion_id = array_column($votaciones_objs, 'sesion_id');
  $orden = array_column($votaciones_objs, 'orden');
  array_multisort($sesion_id, SORT_ASC, $orden, SORT_ASC, $votaciones_objs);
  return $votaciones_objs;
}

/**
 * AJAX generate XLS General votaciones por asambleista
 */
add_action('wp_ajax_nopriv_ol_generate_xls_general_votaciones_asambleista', 'ol_generate_xls_general_votaciones_asambleista');
add_action('wp_ajax_ol_generate_xls_general_votaciones_asambleista', 'ol_generate_xls_general_votaciones_asambleista');
function ol_generate_xls_general_votaciones_asambleista(){

  try{
    if ( file_exists( CBXPHPSPREADSHEET_ROOT_PATH . 'includes/lib/vendor/autoload.php' ) ) {
        //Include PHPExcel
        require_once( CBXPHPSPREADSHEET_ROOT_PATH . 'includes/lib/vendor/autoload.php' );
        //Crear instancia excel
        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();


        
        //Logos cabecera
        // Logo website
        // $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        // $drawing->setPath('https://observatoriolegislativo.ec/wp-content/uploads/2021/12/ob.-legislativo-.jpg');
        // $drawing->setWidth(180); 
        // $drawing->setResizeProportional(false);
        // $drawing->setHeight(180);
        // $drawing->setCoordinates('B1');
        // $drawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));

        //Logo FCD
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setPath( get_stylesheet_directory() . '/images/FCD-Logo.png');
        $drawing->setWidth(230); 
        $drawing->setResizeProportional(false);
        $drawing->setHeight(100);
        $drawing->setCoordinates('F3');
        $drawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));
        $drawing->setOffsetX(200);
        $drawing->setOffsetY(10);
        //Styles
        $titulostyle = array(
            'font' => [
                        'name' => 'Calibri',
                        'bold' => true,
                        //'italic' => false,
                        //'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLE,
                        'strikethrough' => false,
                        /*'color' => [
                            'rgb' => '808080'
                        ]*/
                    ],
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
            )
        );
        $headerstyle=array('font' => [
            'name' => 'Calibri',
            'bold' => true,
            'strikethrough' => false,
              ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'left'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'right'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    
                ]
            ],
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
            )
        );
        $longstyle=array(
            'font' => [
                'size' =>11
            ]
            ,
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'left'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'right'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    
                ]
            ],
            'alignment' => array(
                // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
                )
        );
        $style=array(
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'left'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'right'=> [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,     
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    
                ]
            ],
            'alignment' => array(
                // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'     => TRUE
                )
        );

        //Columnas ancho ->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('24');
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('36');
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('12');
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('20');
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('12');
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('36');

        /**
         * Datos
         */
        $asambleista_ID = $_GET['profileid'];
        $suplente = (get_field('colaborador', $asambleista_ID)) ? get_field('colaborador', $asambleista_ID)->post_title : '' ;

        //TITULO
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B7','Asamblea Nacional del Ecuador')
            ->setCellValue('B8','Detalle de Votaciones')
            ->setCellValue('B9', 'Asambleísta principal ' . get_the_title($asambleista_ID))
            ->setCellValue('B10','Asambleísta suplente ' . $suplente)
            ->setCellValue('B11','Sesión')
            ->setCellValue('C11','Descripción de la votación')
            ->setCellValue('D11','Votación')
            ->setCellValue('E11','Tipo de asambleísta')
            ->setCellValue('F11','Suplente')
            ->setCellValue('G11','Voto')
            //->setCellValue('G11','Tipo de Votación');
            ->setCellValue('H11','Categorías Generales');

        //TAMAÑOS
        //$objPHPExcel->getActiveSheet()->getRowDimension(11)->setRowHeight(-1);

        //CONTENIDO
        $i = 11;
        // $ndatos = 30;
        
        $votaciones_objs = get_votaciones_asambleista( $asambleista_ID );

        /**
        * Montar los datos en excel
        */
        foreach($votaciones_objs as $votacion_obj) {
          $i++;
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $votacion_obj['sesion_name']);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $votacion_obj['votacion_nombre_corto']);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $votacion_obj['orden']);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $votacion_obj['vota_sumplente']);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $votacion_obj['suplente']);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, $votacion_obj['el_voto']);
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $votacion_obj['tipo']);
        }

        // Estilos por columna
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getStyle('B11:G11')->applyFromArray($headerstyle);
        $objPHPExcel->getActiveSheet()->getStyle('F12:F'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('E12:E'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('D12:D'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('C12:C'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('G12:G'.$i)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('B12:B'.$i)->applyFromArray($longstyle);

        //titulo
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B8:F8');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B9:F9');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B10:F10');

        //Header styles
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B9')->applyFromArray($titulostyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B8')->applyFromArray($titulostyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('C11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('D11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('E11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('F11')->applyFromArray($headerstyle);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('G11')->applyFromArray($headerstyle);

        $objPHPExcel->getActiveSheet()->setTitle('Listado Detalle de Votaciones');

        // CABEZERAS
        // Redirect output to a client’s web browser (Xls)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="01simple.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        //ESCRITURA
        $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        //ENVIAR JSON CON ARCHIVO
        $response =  array(
                'op' => 'ok',
                'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
            );
        header('Content-type: application/json');
        die(json_encode($response));

    }
  } catch (Exception $e) {
      echo $e;
  }
  wp_die();
}

/**
 * AJAX generate CSV General votaciones por asamblenista
 */
add_action('wp_ajax_nopriv_ol_generate_csv_votacion_asambleista', 'ol_generate_csv_votacion_asambleista');
add_action('wp_ajax_ol_generate_csv_votacion_asambleista', 'ol_generate_csv_votacion_asambleista');
function ol_generate_csv_votacion_asambleista() {

    $asambleista_ID = $_GET['profileid'];

    /**
     * Hacer la consulta con los argumentos del GET
     */
    $csv_fields = array();
    $csv_fields[] = 'Sesión';
    $csv_fields[] = 'Descripción de la votación';
    $csv_fields[] = 'Votación';
    $csv_fields[] = 'Tipo de asambleísta';
    $csv_fields[] = 'Suplente';
    $csv_fields[] = 'Voto';
    $csv_fields[] = 'Categorías Generales';

    $output_handle = @fopen( 'php://output', 'w' );
    header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
    header( 'Content-Description: File Transfer' );
    header( 'Content-type: text/csv;charset=UTF-8' );
    header( 'Content-Disposition: attachment; filename=ol_ciudad_documento_cambiar_esto.csv');
    header( 'Expires: 0' );
    header( 'Pragma: public' );
    // Insert header row
    fputcsv( $output_handle, mb_convert_encoding($csv_fields,'UTF-16LE', 'UTF-8') );

    $votaciones_objs = get_votaciones_asambleista( $asambleista_ID );

    // Parse results to csv format
    if ( $votaciones_objs) {
      foreach ( $votaciones_objs as $voto ) {

        $csv_fields = array();
        $csv_fields[] = $voto['sesion_name'];
        $csv_fields[] = $voto['votacion_nombre_corto'];
        $csv_fields[] = $voto['orden'];
        $csv_fields[] = $voto['vota_sumplente'];
        $csv_fields[] = $voto['suplente'];
        $csv_fields[] = $voto['el_voto'];
        $csv_fields[] = $voto['tipo'];
        fputcsv( $output_handle, mb_convert_encoding($csv_fields,'UTF-16LE', 'UTF-8') );

      }
    }
  // Close output file stream
  fclose( $output_handle );
  die;
}


/**
 * Asamblea en Cifra
 */
function get_generos() {
  return get_terms(['taxonomy' => 'genero', 'hide_empty' => false]);
}
function get_partidos() {
  return get_terms(['taxonomy' => 'partido_politico', 'hide_empty' => false]);
}
function get_bancadas() {
  return get_terms(['taxonomy' => 'bancada', 'hide_empty' => false]);
}
function get_circunscripciones( $parent = 0, $exclude = [] ) {
  $args = [
    'taxonomy' => 'circunscripcion',
    'hide_empty' => false,
    'parent' => $parent,
    'exclude' => $exclude
  ];
  return get_terms( $args );
}