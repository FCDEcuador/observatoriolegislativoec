<?php

/**

 * @author Divi Space

 * @copyright 2017

 */

if (!defined('ABSPATH')) die();



function ds_ct_enqueue_parent() { wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' ); }



function ds_ct_loadjs() {
	global $post;

	wp_enqueue_script( 'ds-theme-script', get_stylesheet_directory_uri() . '/ds-script.js', array( 'jquery' ));

	
  if ( 
    is_single() ||
    is_page('analisis-de-votaciones') ||
    is_page('asamblea-en-cifras') 
  ) {
    wp_enqueue_script( 'amchart-script', 'https://cdn.amcharts.com/lib/4/core.js');
    wp_enqueue_script( 'amchart-xy-script', 'https://cdn.amcharts.com/lib/4/charts.js');
    wp_enqueue_script( 'amchart-animate-script', 'https://cdn.amcharts.com/lib/4/themes/animated.js');
  }

}

add_action( 'wp_enqueue_scripts', 'ds_ct_enqueue_parent' );
add_action( 'wp_enqueue_scripts', 'ds_ct_loadjs' );

include('login-editor.php');

/**
 * Remove Project CPT from Divi custom instalation
 */
add_filter( 'et_project_posttype_args', 'mytheme_et_project_posttype_args', 10, 1 );
function mytheme_et_project_posttype_args( $args ) {
	return array_merge( $args, array(
		'public'              => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => false,
		'show_in_nav_menus'   => false,
		'show_ui'             => false
	));
}


// function tna_edit_taxonomy_args( $args, $tax_slug, $cptui_tax_args ) {
	
// 	// Set to false for all taxonomies created with CPTUI.ç
// 	if ( isset($_GET) && $_GET['post_type'] == 'perfil' ) {
		
// 		if ( 'tipo_perfil' !== $tax_slug ){
// 			$args['meta_box_cb'] = false;
// 		}	
		
// 	}

// 	return $args;
// }
// add_filter( 'cptui_pre_register_taxonomy', 'tna_edit_taxonomy_args', 10, 3 );

function ol_search_cpt( $atts ) {
	$atts = shortcode_atts(array(
        'cpt' => 'post',
        'cantidad' => 12,
    ), $atts, 'search-form-cpt');
	return '
	<div class="filter-bar-container search-container">
		<form action="https://observatoriolegislativo.urbadigital.com/">
		  <input type="hidden" name="post_type" value="' . $atts['cpt'] . '">
		  <input type="text" name="s" value="" placeholder="Buscar">
		  <button type="submit" title="Buscar"><i class="fas fa-search" aria-hidden="true"></i></button>
		</form>
	</div>
	';
}
add_shortcode( 'search-form-cpt', 'ol_search_cpt');