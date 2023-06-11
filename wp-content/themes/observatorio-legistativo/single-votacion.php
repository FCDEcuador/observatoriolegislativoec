<?php 
get_header(); 
global $wp_query;
$json_bancada = '';
get_template_part( '/template-parts/detalle-analisis-votos' );

?>
<script>
var votacionesData = '<?php echo $json_bancada; ?>';
votacionesData = JSON.parse(votacionesData);

am4core.ready(function() {

  // Themes begin
  am4core.useTheme(am4themes_animated);
  // Themes end

  // Create chart instance
  var chart = am4core.create("chartdiv", am4charts.XYChart);

  // Add data
  chart.data = votacionesData.votos;

  chart.legend = new am4charts.Legend();
  chart.legend.position = "bottom";
	
  var fs = 16;
  var mw = 200;
  if (window.innerWidth < 800){
	  fs = 10;
	  mw = 100;
  }
  // Create axes
  var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
      categoryAxis.dataFields.category = "bancada_nombre";
      categoryAxis.renderer.grid.template.opacity = 0;
      categoryAxis.renderer.labels.template.fontSize = fs;
      categoryAxis.renderer.labels.template.textAlign = 'end';
      categoryAxis.renderer.labels.template.maxWidth = mw;
      categoryAxis.renderer.labels.template.wrap = true
      categoryAxis.renderer.labels.template.truncate = false;

  var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
      valueAxis.min = 0;
      valueAxis.renderer.grid.template.opacity = 0.8;
      valueAxis.renderer.ticks.template.strokeOpacity = 0;
      valueAxis.renderer.ticks.template.stroke = am4core.color("#000");
      valueAxis.renderer.ticks.template.length = 80;
      valueAxis.renderer.line.strokeOpacity = 0.8;
      valueAxis.renderer.baseGrid.disabled = true;
      valueAxis.renderer.minGridDistance = 80;

  // Create series
  function createSeries(field, name, color) {
    var series = chart.series.push(new am4charts.ColumnSeries());
    series.dataFields.valueX = field;
    series.dataFields.categoryY = "bancada_nombre";
    series.stacked = true;
    series.name = name;
    series.columns.template.stroke = am4core.color(color);
    series.columns.template.strokeOpacity = 0;
    series.columns.template.fill = am4core.color(color);
    
    var labelBullet = series.bullets.push(new am4charts.LabelBullet());
    labelBullet.locationX = 0.5;
    labelBullet.label.fontSize = 12;
    labelBullet.label.text = "{valueX}";
    labelBullet.label.fill = am4core.color("#fff");
    labelBullet.label.hideOversized = true;
  }
  var hidden_si = hidden_no = hidden_ab = hidden_au = hidden_bl = true;
  votacionesData.votos.forEach(element => {
    if ( element.SI > 0 && hidden_si) {
      createSeries("SI", "Si", '#7AC74F');
      hidden_si = false;
    }
    if ( element.NO > 0 && hidden_no) {
      createSeries("NO", "No", '#D7CDCC');
      hidden_no = false;
    }
    if ( element.AB > 0 && hidden_ab) {
      createSeries("AB", "Abstención", '#FFBC42');
      hidden_ab = false;
    }
    if ( element.AU > 0 && hidden_au) {
      createSeries("AU", "Ausente", '#0A2239');
      hidden_au = false;
    }
    if ( element.BL > 0 && hidden_bl) {
      createSeries("BL", "Blanco", '#BDFFFD');
      hidden_bl = false;
    }
  });
  
}); // end am4core.ready()
</script>
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
        console.log(data);
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
})
</script>