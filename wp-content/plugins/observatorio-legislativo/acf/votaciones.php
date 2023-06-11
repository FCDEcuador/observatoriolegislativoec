<?php
add_filter('acf/load_value/key=field_618fe0fb89ecf', 'ol_load_listado_perfiles', 10, 1);
function ol_load_listado_perfiles( $field ) {
  if(!$field){
    $perfiles = new WP_Query([
      'post_type' => 'perfil',
      'posts_per_page' => -1,
      'orderby' => 'post_title',
      'order' => 'ASC',
      'tax_query' => [
        [
          'taxonomy' => 'tipo_perfil',
          'field' => 'slug',
          'terms' => 'legislador'
        ]
      ]
    ]);

    if ( $perfiles->have_posts() ) {
      while( $perfiles->have_posts() ){ $perfiles->the_post();
        $partido_actual_asambleista = get_field('partido_politico', get_the_ID());
        $field[] = [
          'field_6190702e26055' => get_the_title(),
          'field_61906a7f79aeb' => get_the_ID().'-'.$partido_actual_asambleista->term_id,
        ];
      }
    }

    
  }
  return $field;
}

//add_filter('acf/load_field/key=field_61906a9879aee', 'ol_load_listado_suplente', 10, 1);
function ol_load_listado_suplente ( $field ) {
	
	$field['choices'] = array(
        'custom'    => 'My Custom Choice',
        'custom_2'  => 'My Custom Choice 2'
    );
    return $field;
	
}

//add_action('admin_head', 'mostrar_metas_legisladores');
function mostrar_metas_legisladores(){
  $metas = get_field( 'listado_de_votos', $_GET['post']);
  echo '<pre>';
  var_dump($metas);
  echo '</pre>';
  die;
}
