<?php 
/*
Plugin Name: Soporte Observatorio Legislativo FCD
Plugin URI: https://observatoriolegislativo.com
Description: Custom made plugin for FCD.
Version: 1.0.0
Author: Urbadigital
Author URI: https://urbadigital.com
Text Domain: ol
Domain Path: /lang
*/

defined( 'CBXPHPSPREADSHEET_ROOT_PATH' ) or define( 'CBXPHPSPREADSHEET_ROOT_PATH', plugin_dir_path( __FILE__ ) );

include 'includes/functions.php';
include 'includes/admin.php';
include 'includes/public.php';
include 'includes/shortcodes.php';
include 'acf/votaciones.php';

function ol_selectively_enqueue_script( $hook ) {
  global $typenow;
  global $wp_query;
  wp_enqueue_style('ob-bootstrap', plugins_url('css/bootstrap/css/bootstrap-grid.css', __FILE__), NULL, NULL, NULL);
  wp_enqueue_style('ob-bootstrap-u', plugins_url('css/bootstrap/css/bootstrap-utilities.css', __FILE__), NULL, NULL, NULL);
  wp_enqueue_style('ob-public', plugins_url('css/style.css', __FILE__), NULL, NULL, NULL);
  if ( ! is_admin() ){
    wp_enqueue_script('ob-public-icons', 'https://kit.fontawesome.com/62a2726089.js', NULL, NULL, NULL);
    wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', NULL, NULL, NULL);
    wp_enqueue_script( 'select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'));
    wp_enqueue_style('owlcarousel', plugins_url('owl-carousel/assets/owl.carousel.min.css', __FILE__), NULL, NULL, NULL);
    wp_enqueue_style('owlcarousel-theme', plugins_url('owl-carousel/assets/owl.theme.default.min.css', __FILE__), NULL, NULL, NULL);
    wp_enqueue_script( 'owlcarousel-js', plugins_url('owl-carousel/owl.carousel.min.js', __FILE__), array('jquery'));
    wp_enqueue_script( 'ol-public-js', plugins_url('js/public.js', __FILE__), array('jquery'));
    if ( is_post_type_archive('perfil') ) {
      // var_dump($_SERVER);
      // die;
      wp_enqueue_script( 'perfil-js', plugins_url('js/archive-perfil.js', __FILE__), array('jquery'));
    }
	wp_localize_script( 'ol-public-js', 'ol_dom_vars',
      array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'queried_vars' => ( isset($_GET) ) ? $_GET : ''
      )
    );
  }else{
    // wp_enqueue_style('ob-admin', plugins_url('css/style.css', __FILE__), NULL, NULL, NULL);

    if ( 'votacion' == $typenow ) {
      wp_enqueue_style('votacion', plugins_url('css/votaciones.css', __FILE__), NULL, NULL, NULL);
      wp_enqueue_script( 'votacion-js', plugins_url('js/votaciones.js', __FILE__), array('jquery'));
    }
    
    if ( 'toplevel_page_observatorio' === $hook ){
      wp_enqueue_script( 'main-vue', 'https://unpkg.com/vue@next', '');
      wp_enqueue_script( 'ol-vue', plugins_url('app/ol-vue.js', __FILE__), array('main-vue'), '', true);
      // $components = glob(__DIR__ . '/app/components/*.js');
      // foreach ( $components as $index => $filename ){
      //   wp_enqueue_script( 'ol-vue-'. $index, plugins_url('app/components/' . basename($filename), __FILE__), array('ol-vue'), '', true);
      // }
    }
  
  }
}
add_action( 'admin_enqueue_scripts', 'ol_selectively_enqueue_script' );
add_action( 'wp_enqueue_scripts', 'ol_selectively_enqueue_script' );

/**
 * ACF site admin page
 */

add_action('acf/init', 'my_acf_op_init');
function my_acf_op_init() {
	if( function_exists('acf_add_options_page') ) {

		$menu_item = acf_add_options_page(array(
			'page_title' 	=> 'Observatorio Legislativo Configuraciones',
			'menu_title'	=> 'OL Configuraciones',
			'menu_slug' 	=> 'ol-conf',
			'capability'	=> 'edit_posts',
			'redirect'		=> false
		));
		
	}
}

/**
 * Observatorio pagina de sistema
 */
add_action( 'admin_menu', function(){
  add_menu_page( 'API Observatorio Legislativo', 'OL API', 'manage_options', 'observatorio', 'ol_main_page', NULL, 99);
  function ol_main_page(){
    include 'includes/observatorio-admin.php';
  }
});




/***************************/
// add_action('init', 'view_things');
function view_things(){
  $perfiles = new WP_Query([
    'post_type' => 'perfil',
    'posts_per_page' => -1,
    'tax_query' => [
      [
        'taxonomy' => 'tipo_perfil',
        'field' => 'slug',
        'terms' => 'legislador'
      ]
    ]
  ]);
  // $field = [
  //   [
  //     'field_61906a7f79aeb' => 441,
  //     'field_61906a9879aee' => 5673,
  //   ],
  //   [
  //     'field_61906a7f79aeb' => 432,
  //     'field_61906a9879aee' => 5670,
  //   ]
  // ];
  foreach ( $perfiles->posts as $perfil ){
    var_dump($perfil);
    $field[] = [
      'field_61906a7f79aeb' => $perfil->ID,
    ];
  }
  // var_dump($field);
  die;
}

// add_shortcode('show-data', 'mostrar_grafico');
// function mostrar_grafico($atts){
// 	$atts = shortcode_atts(
// 		array(
// 			'char-id' => 'char-div-1',
// 			'char-height' => '500px',
// 			'tipo' => 'cantidad-quejas',
// 			'origen' => 'https://origin-de-datos.com',
// 			'titulo' => 'Datos Estadísticos',
// 		), $atts, 'show-data' );
// 	// Conecte a API
// 	// Valide datos
// 	// retorne error
// 	// retorno frontend
// 	$html = '
// 		<div class="mostrando-grafico" data-origin="' . $atts['origen'] . '" data-type="' . $atts['tipo'] . '">
// 			<h1>' . $atts['titulo'] . '</h1>
// 			<p>' . $atts['tipo'] . '</p>
// 			<p>' . $atts['char-id'] . '</p>
// 			<!-- amchart instance -->
// 			<script>
// 				am5.ready(function() {

// 				// Create root element
// 				// https://www.amcharts.com/docs/v5/getting-started/#Root_element
// 				var root = am5.Root.new("' . $atts['tipo'] . '");


// 				// Set themes
// 				// https://www.amcharts.com/docs/v5/concepts/themes/
// 				root.setThemes([
// 				  am5themes_Animated.new(root)
// 				]);


// 				// Create chart
// 				// https://www.amcharts.com/docs/v5/charts/xy-chart/
// 				var chart = root.container.children.push(am5xy.XYChart.new(root, {
// 				  panX: false,
// 				  panY: false,
// 				  wheelX: "panX",
// 				  wheelY: "zoomX",
// 				  layout: root.verticalLayout
// 				}));


// 				// Add legend
// 				// https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
// 				var legend = chart.children.push(
// 				  am5.Legend.new(root, {
// 					centerX: am5.p50,
// 					x: am5.p50
// 				  })
// 				);

// 				var data = [{
// 				  "year": "2021",
// 				  "europe": 2.5,
// 				  "namerica": 2.5,
// 				  "asia": 2.1,
// 				  "lamerica": 1,
// 				  "meast": 0.8,
// 				  "africa": 0.4
// 				}, {
// 				  "year": "2022",
// 				  "europe": 2.6,
// 				  "namerica": 2.7,
// 				  "asia": 2.2,
// 				  "lamerica": 0.5,
// 				  "meast": 0.4,
// 				  "africa": 0.3
// 				}, {
// 				  "year": "2023",
// 				  "europe": 2.8,
// 				  "namerica": 2.9,
// 				  "asia": 2.4,
// 				  "lamerica": 0.3,
// 				  "meast": 0.9,
// 				  "africa": 0.5
// 				}]


// 				// Create axes
// 				// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
// 				var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
// 				  categoryField: "year",
// 				  renderer: am5xy.AxisRendererX.new(root, {
// 					cellStartLocation: 0.1,
// 					cellEndLocation: 0.9
// 				  }),
// 				  tooltip: am5.Tooltip.new(root, {})
// 				}));

// 				xAxis.data.setAll(data);

// 				var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
// 				  renderer: am5xy.AxisRendererY.new(root, {})
// 				}));


// 				// Add series
// 				// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
// 				function makeSeries(name, fieldName) {
// 				  var series = chart.series.push(am5xy.ColumnSeries.new(root, {
// 					name: name,
// 					xAxis: xAxis,
// 					yAxis: yAxis,
// 					valueYField: fieldName,
// 					categoryXField: "year"
// 				  }));

// 				  series.columns.template.setAll({
// 					tooltipText: "{name}, {categoryX}:{valueY}",
// 					width: am5.percent(90),
// 					tooltipY: 0
// 				  });

// 				  series.data.setAll(data);

// 				  // Make stuff animate on load
// 				  // https://www.amcharts.com/docs/v5/concepts/animations/
// 				  series.appear();

// 				  series.bullets.push(function () {
// 					return am5.Bullet.new(root, {
// 					  locationY: 0,
// 					  sprite: am5.Label.new(root, {
// 						text: "{valueY}",
// 						fill: root.interfaceColors.get("alternativeText"),
// 						centerY: 0,
// 						centerX: am5.p50,
// 						populateText: true
// 					  })
// 					});
// 				  });

// 				  legend.data.push(series);
// 				}

// 				makeSeries("Europe", "europe");
// 				makeSeries("North America", "namerica");
// 				makeSeries("Asia", "asia");
// 				makeSeries("Latin America", "lamerica");
// 				makeSeries("Middle East", "meast");
// 				makeSeries("Africa", "africa");


// 				// Make stuff animate on load
// 				// https://www.amcharts.com/docs/v5/concepts/animations/
// 				chart.appear(1000, 100);

// 				}); // end am5.ready()
// 				</script>
// 			<!-- amchats instande END -->
// 			<div style="height:'. $atts['char-height'] .';" class="chartdiv" id="' . $atts['tipo'] . '"></div>
// 		</div>
// 	';
// 	return $html;
// }
