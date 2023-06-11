<?php get_header(); ?>
<div class="loading-overlay">
  <div class="bounce-loader">
      <div class="bounce1"></div>
      <div class="bounce2"></div>
      <div class="bounce3"></div>
  </div>
</div>
<?php
  global $wp_query;
  $json_bancada = '';
  $votaciones_objeto = null;
  $ranking_amount = (isset($_GET['a'])) ? $_GET['a'] : 7;
  $ranking_order = (isset($_GET['order'])) ? $_GET['order'] : 'DESC';
  $titularizado = false;

  $vistas = array(
    '1' => [
      'Indicador Ocupación de la Curul - Top ' . $ranking_amount,
      'ocupacion_curul',
      'Ha ocupado su curul',
      'oc',
      'Alta ocupación',
      'Baja ocupación'
    ],
    '2' => [
      'Indicador Proyectos de Ley - Top ' . $ranking_amount,
      'proyectos_presentados',
      'Proyectos presentados',
      'pl',
      'Más proyectos de Ley',
      'Menos proyectos de Ley'
    ],
    '3' => [
      'Indicador Pedidos de Información - Top ' . $ranking_amount,
      'solicitudes_presentadas',
      'Solicitudes presentadas',
      'pi',
      'Más pedidos de información',
      'Menos pedidos de información'
    ],
    '4' => [
      'Indicador Observaciones a Proyectos de Ley - Top ' . $ranking_amount,
      'observaciones_presentadas',
      'Observaciones presentadas',
      'op',
      'Más observaciones',
      'Menos observaciones'
    ],
    '5' => [
      'Indicador Pedidos de juicio político - Top ' . $ranking_amount,
      'juicios_politicos',
      'Juicios polítios solicitados',
      'jp',
      'Más pedidos de juicio político',
      'Menos pedidos de juicio político'
    ],
    '6' => [
      'Indicador Puntualidad de los Legisladores (Atrasos) - Top ' . $ranking_amount,
      'retrasos',
      'Atrasos',
      'pu',
      'Más puntuales',
      'Menos puntuales'
    ],
    '7' => [
      'Indicador Leyes Aprobadas',
      'leyes_aprobadas',
      'Leyes Aprobadas',
      'la'
    ],
    '8' => [
      'Indicador Tipos de Votación',
      'tipos_votacion',
      'Votación',
      'tv'
    ],
  );
  $legisladores_activos = ol_get_all_legisladores($_GET);
  $listado_legisladores_metas = array();
  $vista = ( isset($_GET['i']) ) ? $_GET['i'] : 2;
  $tipo_grafico = 'barras';

  switch ($vista) {
    /***
     * INICIO 1
     */
    case 1 :
      $args = array(
        'post_type' => 'votacion',
        'posts_per_page' => -1,
        'post_status' => 'publish'
      );
      $votaciones = new WP_Query( $args );
  
      if ( $votaciones->have_posts() ){
        while ( $votaciones->have_posts() ) { $votaciones->the_post();
          $votos[] = get_field('listado_de_votos', get_the_ID());
        }
      }
      
      if ( $legisladores_activos->have_posts() ){
        while ( $legisladores_activos->have_posts() ) {
          $legisladores_activos->the_post();
          $id_legislador = get_the_ID();        
          $nombre = get_the_title();
  
          $participacion = 0;
          $ocupacion = 0;
          $porcentaje = 0;
          $ausencias_principal = 0;
          $votacion_suplente = 0;
          $ausencias_suplente = 0;
          foreach( $votos as $votacion ) {
            foreach ( $votacion as $voto ) {
              // if ( strpos( $voto['asambleista_obj'], strval($id_legislador) ) !== false ) {
              if ( $voto['asambleista'] == $nombre ) {
                //( ! empty( $voto['voto'] ) ) ? $ocupacion++ : NULL;
                if ($voto['au']) $ausencias_principal++;
                if ($voto['de']) $votacion_suplente++;
                if ($voto['aus']) $ausencias_suplente++;
                $participacion++;
              }
            }
          } 
          $ocupacion = $participacion - ($ausencias_principal + $ausencias_suplente);
  
          $porcentaje = $ocupacion * 100 / $participacion;
  
          $partido = get_the_terms(get_the_ID(), 'partido_politico');
          $partido_thumbnail = get_field('logo_del_partido', 'partido_politico_' . $partido[0]->term_id);

          $nombre = ( get_field('titularizado') ) ? '* ': '';
          $nombre .= get_the_title();
  
          $listado_legisladores_metas[] = array(
            'thumbnail_url' => get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ),
            'nombre' => $nombre,
            'link' => get_the_permalink(),
            'partido' => $partido[0]->name,
            'partido_thumbnail' => $partido_thumbnail['sizes']['thumbnail'],
            'participacion' => $participacion,
            'ocupacion' => $ocupacion,
            'ocupacion_curul' => number_format($porcentaje, 2)
          );
  
        }
  
      }
      /**
       * FIN
       */
      break;
    /***
    * INICIO 2
    */
    case 2 :
    case 3 :
    case 4 :
    case 5 :
    case 6 :
        /**
       * Obtener todos los asambleistas y traer los fields
       * armar array para poder ordenar
       * hacer sumas y restas
       */
      //var_dump($_GET);
      
      $legisladores_filtrados = ol_get_all_legisladores($_GET);
      //var_dump($legisladores_filtrados);
      if ( $legisladores_filtrados->have_posts() ){
        while( $legisladores_filtrados->have_posts() ) {
          $legisladores_filtrados->the_post();

          $proyectos_reformados = (get_field('proyectos_reformatorios'))?:0;
          $proyectos_leyes_nuevas = (get_field('proyectos_leyes_nuevas'))?:0;
          $proyectos_presentados = $proyectos_reformados + $proyectos_leyes_nuevas;
          $solicitudes_efectivas = (get_field('solicitudes_efectivas'))?:0;
          $solicitudes_sin_respuesta = (get_field('solicitudes_sin_respuesta'))?:0;
          $solicitudes_presentadas = $solicitudes_efectivas + $solicitudes_sin_respuesta;
          $observaciones_presentadas = (get_field('cantidad_de_observaciones'))?:0;
          $juicios_politicos = (get_field('pedidos_de_juicio_politico'))?:0;
          $atrasos = (get_field('atrasos'))?:0;

          /**
           * obtener partido politico
           */
          $partido = get_the_terms(get_the_ID(), 'partido_politico');
          $partido_thumbnail = get_field('logo_del_partido', 'partido_politico_' . $partido[0]->term_id);
			
		      $nombre = ( get_field('titularizado') ) ? '* ': '';
          $nombre .= get_the_title();

          $listado_legisladores_metas[] = array(
            'thumbnail_url' => get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ),
            'nombre' => $nombre,
            'link' => get_the_permalink(),
            'partido' => $partido[0]->name,
            'partido_thumbnail' => $partido_thumbnail['sizes']['thumbnail'],
            'proyectos_reformatorios' => $proyectos_reformados,
            'proyectos_leyes_nuevas' => $proyectos_leyes_nuevas,
            'proyectos_presentados' => $proyectos_presentados,
            'solicitudes_efectivas' => $solicitudes_efectivas,
            'solicitudes_sin_respuesta' => $solicitudes_sin_respuesta,
            'solicitudes_presentadas' => $solicitudes_presentadas,
            'observaciones_presentadas' => $observaciones_presentadas,
            'juicios_politicos' => $juicios_politicos,
            'retrasos' => $atrasos,
          );
          $listado_legisladores_metas = apply_filters( 'listado_legisladores_meta', $listado_legisladores_metas );
        }
      }
      wp_reset_postdata();
      //var_dump($listado_legisladores_metas);
        /**
       * FIN
       */
      break;
    case 7 :
      /***
       * INICIO
       */
      $tipo_grafico = 'circular';
      $datos = array(
        [
          'titulo' => 'Parte de la agenda parlamentaria',
          'cantidad' => get_field('parte_de', 'option'),
          'color' => '#FFC307'
        ],
        [
          'titulo' => 'Fuera de la agenda parlamentaria',
          'cantidad' => get_field('fuera_de', 'option'),
          'color' => '#5A5A5A'
        ]
      );
      /**
       * FIN
       */
      break;
    case 8 :
      /***
       * INICIO
       */
      $tipo_grafico = 'circular';
      $datos = array();
      $tipos_votacion = get_terms(['taxonomy' => 'tipo', 'hide_empty' => false]);
      $general = get_term_by( 'slug', 'general', 'categoria_votacion');
      $hijos_general = get_terms([
        'taxonomy' => 'categoria_votacion',
        'hide_empty' => false,
        'parent' => $general->term_id
      ]);
      // echo '<pre>';
      // var_dump($tipos_votacion);
      // echo '</pre>';
      if($hijos_general){
        foreach($hijos_general as $tipo){
          $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
          $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
          $datos[] = array(
            'titulo' => $tipo->name,
            'cantidad' => $tipo->count,
            'color' => $color
          );
        }
      }
      /**
       * FIN
       */
      break;
  }


if ( $tipo_grafico == 'barras') { 
  foreach ( $listado_legisladores_metas as $index => $row ) {
    if ( $row[$vistas[$vista][1]] == 0) {
      unset($listado_legisladores_metas[$index]);
    }
  }
  $columna = array_column($listado_legisladores_metas, $vistas[$vista][1]);
  $columna2 = array_column($listado_legisladores_metas, 'nombre');
  // var_dump($columna2);
  // die;
  if ($ranking_order == 'ASC'){
    array_multisort($columna, SORT_ASC, $columna2, SORT_ASC, $listado_legisladores_metas);
    // array_multisort($columna, SORT_ASC, $listado_legisladores_metas);
  }else{
    array_multisort($columna, SORT_DESC, $columna2, SORT_ASC, $listado_legisladores_metas);
    // array_multisort($columna, SORT_DESC, $listado_legisladores_metas);
  }
  $ranking = array_splice($listado_legisladores_metas, 0, $ranking_amount, true);
  foreach ($ranking as $elemento) {
    if ( strpos($elemento['nombre'], '*') !== false ) {
      $titularizado = true;
    }
  }
  get_template_part( '/template-parts/detalle-asamblea-cifras' );
  wp_reset_postdata();
  // echo '<pre>';
  // var_dump($ranking);
  // echo '</pre>';
  
  //var_dump($json_bancada);
?>
  <!-- Chart code -->
  <script>
    var votacionesData = '<?php echo json_encode($ranking); ?>';
    var view = '<?php echo $vistas[$vista][1]; ?>';
    var viewItemtitle = '<?php echo $vistas[$vista][2]; ?>';
    votacionesData = JSON.parse(votacionesData);
    // console.log(votacionesData)

    /**
     * Dibujar la tabla para moviles
     */
    htmllist = '';
    $('#mobile_chart').html('')
    htmllist += '<ul>';
    $.each(votacionesData, function(index, val){
      htmllist += `<li>
      <div class="row border rounded m-1 mb-3 py-2">
        <div class="col-6 text-center position-relative">
          <span class="position-badge"><b>${ index + 1 }</b></span>
          <img class="img-fluid" src="${ val.thumbnail_url }" alt="${ val.nombre }">
        </div>
        <div class="col-6 d-flex flex-column justify-content-center">
          <h1 class="text-center">${ val[view] }${ (view == 'ocupacion_curul') ? '%' : '' }</h1>
        </div>
        <div class="col-12">
        <h3>${ val.nombre }</h3>
            <p><b>${ val.partido }</b></p>
        </div>
      </div>
      </li>`;
    })
    htmllist += '</ul>';
    $('#mobile_chart').html(htmllist)

    am4core.ready(function() {

    // Themes begin
    am4core.useTheme(am4themes_animated);
    // Themes end

    // Create chart instance
    var chart = am4core.create("chartdiv", am4charts.XYChart);

    chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

    chart.paddingBottom = 30;


    // Add data
    chart.data = votacionesData;

    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "nombre";
    categoryAxis.renderer.grid.template.strokeOpacity = 0;
    categoryAxis.renderer.minGridDistance = 80;
    categoryAxis.renderer.labels.template.dy = 40;
    categoryAxis.renderer.tooltip.dy = 0;
	
    var fs = 12;
    var mw = 160;
    var imgW = 60;
    var imgH = 60;
    var img2H = 120;
    var pixelRadius = 10;
    var pixelHeight = 30;
    var circleRadius = 30;
    var ctWidth = 80;
    if (window.innerWidth < 800){
	  	fs = 10;
	  	mw = 80;
		imgW = 40;
		imgH = 40;
		img2H = 80;
		pixelRadius = 5;
		pixelHeight = 5;
		circleRadius = 15;
		ctWidth = 40;
	}

    let label = categoryAxis.renderer.labels.template;
    label.wrap = true;
    label.truncate = true;
    label.maxWidth = mw;
    label.fontSize = fs
    label.textAlign = 'middle';
    label.html = '<a style="line-height: 12px!important;" href="{link}">{nombre}</a>';

    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.renderer.inside = true;
    valueAxis.renderer.labels.template.fillOpacity = 0.3;
    valueAxis.renderer.grid.template.strokeOpacity = 0;
    valueAxis.min = 0;
    valueAxis.cursorTooltipEnabled = false;
    valueAxis.renderer.baseGrid.strokeOpacity = 0;

    var tooltipHtml = "<center><h4><strong>{valueY.value}</strong></h4> <h8><p>" + viewItemtitle + "</p></h8></center>";
    if ( 'ocupacion_curul' == view ) {
      tooltipHtml = `
        <center>
          <h5>${ viewItemtitle }</h5>
          <h3><strong>{valueY.value}%</strong></h3>
        </center>`;
    }

    var series = chart.series.push(new am4charts.ColumnSeries);
    series.dataFields.valueY = view;
    series.dataFields.categoryX = "nombre";
    series.tooltipText = "{valueY.value}";
    series.tooltipHTML = tooltipHtml;
    series.tooltip.pointerOrientation = "vertical";
    series.tooltip.dy = 10;
    series.columnsContainer.zIndex = 100;
		
	  var labelBullet = series.bullets.push(new am4charts.LabelBullet());
    labelBullet.locationX = 0.5;
    labelBullet.locationY = 0.5;
    labelBullet.label.fontSize = 16;
    labelBullet.label.text = "{valueY.value}";
    labelBullet.label.fill = am4core.color("#000");
    labelBullet.label.hideOversized = true;

    var columnTemplate = series.columns.template;
    columnTemplate.width = am4core.percent(ctWidth);
    columnTemplate.maxWidth = 66;
    columnTemplate.column.cornerRadius(60, 60, 10, 10);
    columnTemplate.strokeOpacity = 0;


    series.heatRules.push({ 
      target: columnTemplate, 
      property: "fill", 
      dataField: "valueY", 
      min: am4core.color("#5A5A5A"), 
      max: am4core.color("#FFC307") 
    });
    series.mainContainer.mask = undefined;
	
	/*
	if ( 'solicitudes_presentadas' == view ) {
      tooltipHtml = "<center><h4><strong>{valueY.value}</strong></h4> <h8><p>Solicitudes sin respuesta</p></h8></center>";
      var seriesSibling = chart.series.push(new am4charts.ColumnSeries);
      seriesSibling.dataFields.valueY = 'solicitudes_sin_respuesta';
      seriesSibling.dataFields.categoryX = "nombre";
      seriesSibling.tooltipText = "{valueY.value}";
      seriesSibling.tooltipHTML = tooltipHtml;
      seriesSibling.tooltip.pointerOrientation = "vertical";
      seriesSibling.tooltip.dy = 10;
      seriesSibling.columnsContainer.zIndex = 90;

      var labelBulletSibling = seriesSibling.bullets.push(new am4charts.LabelBullet());
      labelBulletSibling.locationX = 0.5;
      labelBulletSibling.locationY = 0.5;
      labelBulletSibling.label.fontSize = 16;
      labelBulletSibling.label.text = "{valueY.value}";
      labelBulletSibling.label.fill = am4core.color("#000");
      labelBulletSibling.label.hideOversized = true;

      var columnTemplateSibling = seriesSibling.columns.template;
      //columnTemplateSibling.width = am4core.percent(100);
      columnTemplateSibling.column.cornerRadius(60, 60, 10, 10);
      columnTemplateSibling.strokeOpacity = 0;
	  columnTemplateSibling.maxWidth = 30;

      seriesSibling.heatRules.push({ 
        target: columnTemplateSibling, 
        property: "fill", 
        dataField: "valueY", 
        min: am4core.color("#5A5A5A"), 
        max: am4core.color("#FFC307") 
      });
      seriesSibling.mainContainer.mask = undefined;
    }
	*/

    var cursor = new am4charts.XYCursor();
    chart.cursor = cursor;
    cursor.lineX.disabled = true;
    cursor.lineY.disabled = true;
    cursor.behavior = "none";

    var bullet = columnTemplate.createChild(am4charts.CircleBullet);
    bullet.circle.radius = circleRadius;
    bullet.valign = "bottom";
    bullet.align = "center";
    bullet.isMeasured = true;
    bullet.mouseEnabled = false;
    bullet.verticalCenter = "bottom";
    bullet.interactionsEnabled = false;

    var hoverState = bullet.states.create("hover");
		
    var outlineCircle = bullet.createChild(am4core.Circle);
    outlineCircle.adapter.add("radius", function (radius, target) {
        var circleBullet = target.parent;
        return circleBullet.circle.pixelRadius + pixelRadius;
    })

    var image = bullet.createChild(am4core.Image);
    image.width = imgW;
    image.height = imgH;
    image.horizontalCenter = "middle";
    image.verticalCenter = "middle";
    image.propertyFields.href = "thumbnail_url";

    image.adapter.add("mask", function (mask, target) {
        var circleBullet = target.parent;
        return circleBullet.circle;
    })

    var image2 = bullet.createChild(am4core.Image);
    image2.width = img2H;
    //image2.height = 30;
    image2.horizontalCenter = "end";
    image2.verticalCenter = "end";
    image2.propertyFields.href = "partido_thumbnail";

    var previousBullet;
    chart.cursor.events.on("cursorpositionchanged", function (event) {
        var dataItem = series.tooltipDataItem;

        if (dataItem.column) {
            var bullet = dataItem.column.children.getIndex(1);

            if (previousBullet && previousBullet != bullet) {
                previousBullet.isHover = false;
            }

            if (previousBullet != bullet) {

                var hs = bullet.states.getKey("hover");
                hs.properties.dy = -bullet.parent.pixelHeight + pixelHeight;
                bullet.isHover = true;

                previousBullet = bullet;
            }
        }
    })

    chart.scrollbarX = new am4core.Scrollbar();
    chart.scrollbarX.disabled = false;
    if($(window).width()<=600){
        //chart.dx=0;
        chart.scrollbarX.disabled = false;
    }
    else{
        //chart.dx=60;
        chart.scrollbarX.disabled = false;
    }

    }); // end am4core.ready()
  </script>
<?php 
} // END if barras
if ( $tipo_grafico == 'circular' ) {
  get_template_part( '/template-parts/detalle-asamblea-cifras' );
  wp_reset_postdata();

  ?>
  <script>
    var datos = '<?php echo json_encode($datos); ?>';
    datos = JSON.parse(datos);
	  var sumatoriaFact = 0;
    $.each(datos, function(index,value){
      sumatoriaFact += parseInt(value.cantidad)
    })

    am4core.ready(function() {

    // Themes begin
    am4core.useTheme(am4themes_animated);
    // Themes end

    // Create chart instance
    var chart = am4core.create("chartdiv", am4charts.PieChart);

    // Add and configure Series
    var pieSeries = chart.series.push(new am4charts.PieSeries());
    pieSeries.dataFields.value = "cantidad";
    pieSeries.dataFields.category = "titulo";

    // Let's cut a hole in our Pie chart the size of 30% the radius
    chart.innerRadius = am4core.percent(30);

    // Put a thick white border around each Slice
    pieSeries.slices.template.stroke = am4core.color("#fff");
    pieSeries.slices.template.strokeWidth = 2;
    pieSeries.slices.template.strokeOpacity = 1;
    pieSeries.slices.template.propertyFields.fill = "color";
    pieSeries.slices.template
      // change the cursor on hover to make it apparent the object can be interacted with
      .cursorOverStyle = [
        {
          "property": "cursor",
          "value": "pointer"
        }
      ];
    //pieSeries.legendSettings.labelText = "{titulo}";

    // pieSeries.alignLabels = true;
    // pieSeries.labels.template.bent = true;
    // pieSeries.labels.template.radius = 3;
    // pieSeries.labels.template.padding(0,0,0,0);
    pieSeries.labels.disabled = true;
	pieSeries.labels.text = '';
  
	let pieLabel = pieSeries.labels.template;
    pieLabel.wrap = true;
    pieLabel.truncate = false;
    pieLabel.maxWidth = 120;
    pieLabel.html = '<center><strong>{titulo}</<strong><h3>{cantidad}</h3></center>';

    pieSeries.labels.template.disabled = true;

    pieSeries.ticks.template.disabled = true;

    // Create a base filter effect (as if it's not there) for the hover to return to
    var shadow = pieSeries.slices.template.filters.push(new am4core.DropShadowFilter);
    shadow.opacity = 0;

    // Create hover state
    var hoverState = pieSeries.slices.template.states.getKey("hover"); // normally we have to create the hover state, in this case it already exists

    // Slightly shift the shadow and make it more prominent on hover
    var hoverShadow = hoverState.filters.push(new am4core.DropShadowFilter);
    hoverShadow.opacity = 0.7;
    hoverShadow.blur = 5;
		
	var container = new am4core.Container();
    container.parent = pieSeries;
    container.horizontalCenter = "middle";
    container.verticalCenter = "middle";
    container.width = am4core.percent(40) / Math.sqrt(2);
    container.fill = "white";

    var labelDona = new am4core.Label();
    labelDona.parent = container;
    labelDona.text = "TOTAL: " + sumatoriaFact;
    labelDona.horizontalCenter = "middle";
    labelDona.verticalCenter = "middle";
    labelDona.fontSize = 18;
    labelDona.html = '<center><strong>Total</<strong><h2>' + sumatoriaFact + '</h2></center>';

    // Add a legend
    var legend = chart.legend = new am4charts.Legend();
    legend.position = 'bottom';

    chart.data = datos

    }); // end am4core.ready()
  </script>
  <?php 
}
?>
<?php get_footer(); ?>
<script>
  $(document).ready(function(){

    /**
     * CVS Asamblea en cifras
     */
    $('#btn-csv-asmcifras').click( function(e){
      var dataProfile = $(this).data('profileid')
      e.preventDefault();
      let params = (new URL(document.location)).searchParams
      let genero = params.get('g')
      let partido = params.get('o')
      let bancada = params.get('b')
      $.ajax({
          url: ol_dom_vars.ajaxurl,
          type: 'GET',
          data: {
            action: 'ol_generate_csv_asamblea_cifras',
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
            a.download = 'Observatorio-Legislativo-Votacion-Asambleista-<?php echo $post->post_name; ?>.csv';
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
     * XLS Asamblea en cifras
     */
    $('#btn-xls-asmcifras').click( function(e){
      e.preventDefault();
      let params = (new URL(document.location)).searchParams
      let genero = params.get('g')
      let partido = params.get('o')
      let bancada = params.get('b')
      $.ajax({
        url: ol_dom_vars.ajaxurl,
        type: 'GET',
        data: {
          action: 'ol_generate_xls_asamblea_cifras',
          g: genero,
          o: partido,
          b: bancada
        },
        beforeSend: function(){
          $('body').toggleClass('loading-overlay-showing');
        },
        success: function(data){
          console.log(data)
          
          var $a = $("<a>");
          $a.attr("href",data.file);
          $("body").append($a);
          $a.attr("download","OL_Asamblea_en_Cifras.xls");
          $a[0].click();
          $a.remove();

          $('body').toggleClass('loading-overlay-showing');
        },
        error: function(xhr,err){
          console.log(err);
          console.log(xhr);
          alert('Se ha producido un error, intente mas tarde. Gracias')
          $('body').toggleClass('loading-overlay-showing');
        }
  
      })
  
    })
  })
</script>