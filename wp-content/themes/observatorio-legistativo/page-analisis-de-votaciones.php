<?php 
  get_header(); 
  global $wp_query;
  $json_bancada = '';
  $votaciones_objeto = null;
  
  $args = array(
    'post_type' => 'votacion',
    'post__in' => $_GET['obj_votacion']
  );
  $votaciones = new WP_Query( $args );

  if ( $votaciones->have_posts() ){
    while ( $votaciones->have_posts() ) { $votaciones->the_post();
      $votacion_id = get_the_ID();
      $titulo = get_the_title();
      $url = get_the_permalink();
      $fecha = get_field('fecha', $votacion_id);
      $sesion = get_field('sesion_de_origen', $votacion_id);
      $resumen = get_the_excerpt();

      $votos = obtener_objeto_votos($votacion_id);

      $mode = 1;
      if ( isset ( $_GET['mode'] ) ){
        $mode = $_GET['mode'];
      }
      switch ($mode) {
        case 1:
          $detalle_de_votos = agrupar_votos_por_partido($votos);
          break;
        case 2: 
          //$detalle_de_votos = agrupar_votos_por_partido($votos);
          $detalle_de_votos = obtener_voto_legislador($votos, $_GET['asambleista']);
          break;
      }

      $votaciones_objeto[] = array(
        'votacion_id' => 'votacion-' . get_the_ID(),
        'nombre' => $titulo,
        'sesion' => $sesion->post_title,
        'fecha' => $fecha,
        'url' => $url,
        'detalle' => $detalle_de_votos
      );
    }
    get_template_part( '/template-parts/detalle-analisis-votos' );
    wp_reset_postdata();
  }

  //var_dump($json_bancada);
?>
<!-- Chart code -->
<script>
  var votacionesData = '<?php echo $json_bancada; ?>';
votacionesData = JSON.parse(votacionesData);
console.log(votacionesData)
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

// Create chart instance
var chart = am4core.create("chartdiv", am4charts.XYChart);


// Add data
chart.data = votacionesData;

// Create axes
var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
categoryAxis.dataFields.category = "votacion_nombre";
categoryAxis.renderer.grid.template.location = 0;
categoryAxis.tooltip.label.maxWidth = 200;
//https://www.amcharts.com/docs/v4/tutorials/managing-width-and-spacing-of-column-series/
categoryAxis.tooltip.label.wrap = true;
let label = categoryAxis.renderer.labels.template;
label.wrap = true;
label.truncate = true;
label.maxWidth = 120;
label.tooltipText = "{category}";
// https://www.amcharts.com/docs/v4/tutorials/wrapping-and-truncating-axis-labels/


var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
valueAxis.renderer.inside = true;
valueAxis.renderer.labels.template.disabled = true;
valueAxis.min = 0;

// Create series
function createSeries(field, name, color) {
  
  // Set up series
  var series = chart.series.push(new am4charts.ColumnSeries());
  series.name = name;
  series.tooltip.label.wrap = true;
  series.dataFields.valueY = field;
  series.tooltip.label.width = 300;
  series.dataFields.categoryX = "votacion_nombre";
  series.sequencedInterpolation = true;
  series.columns.template.width = am4core.percent(70);
  series.columns.template.stroke = am4core.color(color);
  series.columns.template.fill = am4core.color(color);
  
  // Make it stacked
  series.stacked = true;
  
  // Configure columns
  series.columns.template.width = am4core.percent(60);
  series.columns.template.tooltipText = "[bold]{name}[/]\n[font-size:14px]{categoryX}: {valueY}";
  
  // Add label
  var labelBullet = series.bullets.push(new am4charts.LabelBullet());
  labelBullet.label.text = "{valueY}";
  labelBullet.locationY = 0.5;
  labelBullet.label.hideOversized = true;
  
  return series;
}

createSeries("SI", "Si", '#7AC74F');
createSeries("NO", "No", '#D7CDCC');
createSeries("AB", "Abstención", '#FFBC42');
createSeries("AU", "Ausente", '#0A2239');
createSeries("BL", "Blanco", '#BDFFFD');

// Legend
chart.legend = new am4charts.Legend();

}); // end am4core.ready()
</script>
<?php get_footer(); ?>