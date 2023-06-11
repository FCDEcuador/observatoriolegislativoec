<?php
add_shortcode('show-filters', 'ol_show_filters');
function ol_show_filters($atts) {
  $atts = shortcode_atts(
    array(
      'show' => 'all',
      'search' => true
    ),
    $atts,
    'show-filters'
  );

  $html = '';

  $html .= '<div class="filter-bar">';

  if( $atts['search'] ) {
  ob_start();
  ?>
  <div class="filter-bar-container search-container">
    <form action="<?php echo home_url('/'); ?>">
      <input type="hidden" name="post_type" value="perfil">
      <input type="text" name="s" value="<?php echo get_query_var( 's' ); ?>">
      <button type="submit" title="Buscar"><i class="fas fa-search"></i></button>
    </form>
  </div>
  <?php 
  $html .= ob_get_clean();
  }



  /*
  - tipo asambleista
  - genero
  - tipo ?
  - Partido politico
  - Bancada
  - Comisión
  - Cargos
  - Posicion politica
  */
  $taxonomies = array(
    'circunscripcions'   => get_taxonomy( 'circunscripcion' )->labels->singular_name,
    'generos' => get_taxonomy( 'genero' )->labels->singular_name,
    'partido_politicos' => get_taxonomy( 'partido_politico' )->labels->singular_name,
    'bancadas' => get_taxonomy( 'bancada' )->labels->singular_name,
    'comisions' => get_taxonomy( 'comision' )->labels->singular_name,
    'circunscripcions' => get_taxonomy( 'circunscripcion' )->labels->singular_name,
    'posicion_politicas' => get_taxonomy( 'posicion_politica' )->labels->singular_name,
    //'ideologias' => get_taxonomy( 'ideologia' )->labels->singular_name
  );

  $html .= '<form action="' . home_url('perfil') . '/">';
  $html .= '<input type="hidden" name="filtered" value="1">';

  foreach( $taxonomies as $i => $taxonomy ) {
    $terms = get_terms(
      [
        'taxonomy' => substr($i, 0, -1),
        'hide_empty' => false
      ]
    );
    if ( $terms ) {
      $html .='<div class="filter-bar-container mt-4">';
      $html .='<select class="select2-active" name="' . $i . '[]" multiple="multiple" data-placeholder="'. $taxonomy .'" style="width: 100%">';
      foreach( $terms as $z => $term ){
        $selected = '';
        if ( isset($_GET[$i]) && in_array( $term->term_id, $_GET[$i] ) ) $selected = 'selected';
        $html .= '<option value="'. $term->term_id .'" '. $selected .'>' . $term->name . '</option>';
      }
      $html .='</select>';
      $html .='</div>';
    }
  }
  ob_start();
  ?>
  <div class="row mt-4 mb-2">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
      <button class="w-100 me-2 br-6 py-2 bg-accent border-0" type="submit" title="Filtrar">FILTRAR <i class="fas fa-filter"></i></button>
      <button class="br-6 py-2 bg-accent border-0" id="clearFilter" type="button" title="Limpiar"><i class="fas fa-trash"></i></button>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12">
		<label for="exasambleista" class="text-white"><input type="checkbox" id="exasambleista" name="exasambleistas" value="true"<?php echo (isset($_GET['exasambleistas']))? ' checked' : ''; ?>> Mostrar Exasambleístas</label>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12">
      <hr />
    </div>
  </div>
  <div class="row mt-2 mb-2 fs-20">
    <div class="col-md-12 text-center">
      <h4 class="uppercase text-white">Descargar Datos</h4>
    </div>
    <div class="col-md-12 px-4 mb-2 text-center">
      <button id="btn-csv" class="w-100 p-2 px-3 d-flex justify-content-around align-items-center br-6 border-0" style="max-width: 100px; margin: 10px auto;" type="button" title="Exportar CSV"><span class="bold">CSV</span> <i class="fas fa-file-csv fs-20"></i></button>
    </div>
    <div class="col-md-12 px-4 mb-2 text-center">
      <button id="btn-xls" class="w-100 p-2 px-3 d-flex justify-content-around align-items-center br-6 border-0" style="max-width: 100px; margin: 10px auto;" type="button" title="Exportar Excel"><span class="bold">XLS</span> <i class="fas fa-file-excel fs-20"></i></button>
    </div>
    <div class="d-none col-md-12 px-4 mb-2 text-center">
      <button id="btn-pdf" class="w-100 p-2 px-3 d-flex justify-content-around align-items-center br-6 border-0" style="max-width: 100px; margin: 10px auto;" type="button" title="Exportar PDF"><span class="bold">HTML</span> <i class="fas fa-file-code fs-20"></i></button>
    </div>
  </div>
  <?php
  $html .= ob_get_clean();
  $html .='</form>';
  $html .='</div>';
  return $html;
}

add_shortcode('show-filters-votes', 'ol_show_filters_votes');
function ol_show_filters_votes($atts) {
  $atts = shortcode_atts(
    array(
      'show' => 'all',
      'search' => true
    ),
    $atts,
    'show-filters-votes'
  );

  $html = '';

  $html .= '<div class="filter-bar">';
  /*
  if( $atts['search'] ) {
  ob_start();
  ?>
  <div class="filter-bar-container search-container">
    <form action="<?php echo home_url('/'); ?>">
      <input type="hidden" name="post_type" value="perfil">
      <input type="text" name="s" value="<?php echo get_query_var( 's' ); ?>">
      <button type="submit" title="Buscar"><i class="fas fa-search"></i></button>
    </form>
  </div>
  <?php 
  $html .= ob_get_clean();
  }
  */



  /*
  - tipo asambleista
  - genero
  - tipo ?
  - Partido politico
  - Bancada
  - Comisión
  - Cargos
  - Posicion politica
  */
  $taxonomies = array(
    'temas'   => get_taxonomy( 'tema' )->labels->singular_name,
    'subtemas' => get_taxonomy( 'subtema' )->labels->singular_name,
    //'tipos' => get_taxonomy( 'tipo' )->labels->singular_name,
    'categoria_votacions' => get_taxonomy( 'categoria_votacion' )->labels->singular_name,
    'estado_votacions' => get_taxonomy( 'estado_votacion' )->labels->singular_name
  );

  $html .= '<form action="' . home_url('analisis-de-voto') . '/">';
  $html .= '<input type="hidden" name="filtered_votes" value="1">';

  $years = get_years_votaciones();
  if (count($years) > 1){
    ob_start();
  ?>
  <div class="filter-bar-container mt-4">
    <select id="year_selector" class="select2-active" name="annio" style="width: 100%" data-placeholder="Año">
		<option></option>
      <?php 
        foreach($years as $year) {
		  $selected = (isset($_GET['annio']) && $_GET['annio'] == $year) ? 'selected' : '';
          echo '<option value="' . $year . '" ' . $selected . '>' . $year . '</option>';
        }
      ?>
    </select>
  </div>
  <?php
    $html .= ob_get_clean();
  }
  ob_start();
  ?>
  <div class="filter-bar-container mt-4 month-filter <?php echo ( isset($_GET['meses']) && !empty($_GET['meses']) ) ? '' : 'd-none'; ?>">
    <select id="month_selector" class="select2-active" name="meses" style="width: 100%" data-placeholder="Mes" tabindex="9999">
      <option></option>
      <option value="01" <?php echo ( isset($_GET['meses']) && $_GET['meses'] == '01') ? 'selected' : ''; ?>>Enero</option>
      <option value="02" <?php echo ( isset($_GET['meses']) && $_GET['meses'] == '02') ? 'selected' : ''; ?>>Febrero</option>
      <option value="03" <?php echo ( isset($_GET['meses']) && $_GET['meses'] == '03') ? 'selected' : ''; ?>>Marzo</option>
      <option value="04" <?php echo ( isset($_GET['meses']) && $_GET['meses'] == '04') ? 'selected' : ''; ?>>Abril</option>
      <option value="05" <?php echo ( isset($_GET['meses']) && $_GET['meses'] == '05') ? 'selected' : ''; ?>>Mayo</option>
      <option value="06" <?php echo ( isset($_GET['meses']) && $_GET['meses'] == '06') ? 'selected' : ''; ?>>Junio</option>
      <option value="07" <?php echo ( isset($_GET['meses']) && $_GET['meses'] == '07') ? 'selected' : ''; ?>>Julio</option>
      <option value="08" <?php echo ( isset($_GET['meses']) && $_GET['meses'] == '08') ? 'selected' : ''; ?>>Agosto</option>
      <option value="09" <?php echo ( isset($_GET['meses']) && $_GET['meses'] == '09') ? 'selected' : ''; ?>>Septiembre</option>
      <option value="10" <?php echo ( isset($_GET['meses']) && $_GET['meses'] == '10') ? 'selected' : ''; ?>>Octubre</option>
      <option value="11" <?php echo ( isset($_GET['meses']) && $_GET['meses'] == '11') ? 'selected' : ''; ?>>Noviembre</option>
      <option value="12" <?php echo ( isset($_GET['meses']) && $_GET['meses'] == '12') ? 'selected' : ''; ?>>Diciembre</option>
    </select>
  </div>
  <?php
  $html .= ob_get_clean();

  foreach( $taxonomies as $i => $taxonomy ) {
	$argtax = [
		'taxonomy' => substr($i, 0, -1),
        'hide_empty' => false
	];
	  
	if ('categoria_votacions' == $i) $argtax['parent'] = 204;
	  
	//$html .= '<!-- <pre>' . var_dump($argtax) . '</pre> -->';
	 
    $terms = get_terms( $argtax );
    if ( $terms ) {
      $html .='<div class="filter-bar-container mt-4">';
      $html .='<select class="select2-active" name="' . $i . '[]" multiple="multiple" data-placeholder="'. $taxonomy .'" style="width: 100%">';
      foreach( $terms as $z => $term ){
        $selected = '';
        if ( isset($_GET[$i]) && in_array( $term->term_id, $_GET[$i] ) ) $selected = 'selected';
        $html .= '<option value="'. $term->term_id .'" '. $selected .'>' . $term->name . '</option>';
      }
      $html .='</select>';
      $html .='</div>';
    }
  }
  ob_start();
  ?>
  <div class="row mt-4 mb-2">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
      <button class="w-100 me-2 br-6 py-2 bg-accent border-0" type="submit" title="Filtrar">FILTRAR <i class="fas fa-filter"></i></button>
      <button class="br-6 py-2 bg-accent border-0" id="clearFilterVotacion" type="button" title="Limpiar"><i class="fas fa-trash"></i></button>
    </div>
  </div>
  <!--
  <div class="row mb-2">
    <div class="col-12">
      <hr />
    </div>
  </div>
  <div class="row mt-2 mb-2 fs-20">
    <div class="col-md-12 text-center">
      <h4 class="uppercase text-white">Descargar Informe</h4>
    </div>
    <div class="col-md-12 px-4 mb-2 text-center">
      <button id="btn-csv" class="w-100 p-2 px-3 d-flex justify-content-around align-items-center br-6 border-0" style="max-width: 100px; margin: 10px auto;" type="button" title="Exportar CSV"><span class="bold">CSV</span> <i class="fas fa-file-csv fs-20"></i></button>
    </div>
    <div class="col-md-12 px-4 mb-2 text-center">
      <button id="btn-xls" class="w-100 p-2 px-3 d-flex justify-content-around align-items-center br-6 border-0" style="max-width: 100px; margin: 10px auto;" type="button" title="Exportar Excel"><span class="bold">XLS</span> <i class="fas fa-file-excel fs-20"></i></button>
    </div>
    <div class="d-none col-md-12 px-4 mb-2 text-center">
      <button id="btn-pdf" class="w-100 p-2 px-3 d-flex justify-content-around align-items-center br-6 border-0" style="max-width: 100px; margin: 10px auto;" type="button" title="Exportar PDF"><span class="bold">HTML</span> <i class="fas fa-file-code fs-20"></i></button>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col-12">
      <hr />
    </div>
  </div>
  -->
  <?php
  $html .= ob_get_clean();
  $html .='</form>';
  $html .='</div>';
  return $html;
}


add_shortcode('ol-carousel', 'ol_show_carousel');
function ol_show_carousel($atts){
  $atts = shortcode_atts(
    array(
      'name' => '',
    ),
    $atts,
    'ol-carousel'
  );

  if ( empty( $atts['name'] ) ) return;

  $perfiles = new WP_Query([
    'post_type' => 'perfil',
    'posts_per_page' => 15,
    'orberby' => 'rand',
    'tax_query' => array(
      'relation' => 'AND',
      array(
        'taxonomy' => 'tipo_perfil',
        'field' => 'slug',
        'terms' => 'legislador'
      )
    )
  ]);

  if (!$perfiles) return;
  
  $html = '';
  ob_start();
  ?>
  <div class="owl-carousel owl-theme">
  <?php while( $perfiles->have_posts() ) { $perfiles->the_post(); ?>
      <div class="item item-profile">
        <?php if ( has_post_thumbnail() ){ ?>
        <div class="item-profile__thumbnail">
            <img class="img-fluid" src="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'medium' ); ?>" alt="<?php echo get_the_title(); ?>">

        </div>
        <?php } ?>
        <div class="item-profile__content">
          <h4><?php echo get_the_title(); ?></h4>
          <div class="perfil-action">
            <a class="btn" href="<?php echo get_the_permalink(); ?>">Ver más</a>
          </div>
        </div>
      </div>
  <?php } ?>
  </div>
  <?php
  $html .= ob_get_clean();

  return $html;

}

add_shortcode('ol-carousel-posts', 'ol_show_carousel_posts');
function ol_show_carousel_posts($atts){
  $atts = shortcode_atts(
    array(
      'item-title' => 'Entrada',
      'exclude-terms' => '',
      'terms_id' => ''
    ),
    $atts,
    'ol-carousel-posts'
  );

  
  $query = [
    'post_type' => 'post',
    'posts_per_page' => 15,
  ];
  if ( !empty( $atts['terms_id'] ) ) {
    $query['category__in'] = explode(',',$atts['terms_id']);
  };
  if ( !empty( $atts['exclude-terms'] ) ) {
    $query['category__not_in'] = explode(',',$atts['exclude-terms']);
  };

  $posts = new WP_Query($query);

  if (!$posts) return;
  
  $html = '';
  ob_start();
  ?>
  <div class="owl-carousel owl-theme">
  <?php while( $posts->have_posts() ) { $posts->the_post(); ?>
      <div class="item item-container item-carousel-post">
        <div class="item-type text-center uppercase bold">
          <span><?php echo $atts['item-title']; ?></span>
        </div>
        <div class="item-content">
          <h4><?php echo get_the_title(); ?></h4>
          <p><?php echo get_the_excerpt(); ?></p>
        </div>
        <div class="item-action text-center">
          <a class="btn link-boton-naranja" href="<?php echo get_the_permalink(); ?>">Ver ms</a>
        </div>
      </div>
  <?php } ?>
  </div>
  <?php
  $html .= ob_get_clean();

  return $html;

}

function get_years_votaciones(){
  $args = array(
    'post_type' => 'votacion',
    'posts_per_page' => -1
  );
  $votaciones = new WP_Query( $args );
  $years = array();
  if ( $votaciones->have_posts() ){
    while ( $votaciones->have_posts() ){ $votaciones->the_post();
      $fecha = get_field('fecha', get_the_ID());
      $matches = null;
      preg_match_all('!\d{4}!', $fecha, $matches);
      if ($matches[0]){
        if ( ! in_array($matches[0][0], $years) ){
          $years[] = $matches[0][0];
        }
      }

    }
  }
  sort($years);
  return $years;
}