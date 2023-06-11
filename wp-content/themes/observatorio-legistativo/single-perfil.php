<?php 
  get_header();
  global $post;
  $legislador_ID = $post->ID;

  // echo '<pre>';
  // var_dump($legislador_ID);
  // echo '</pre>';

  $tema_activo = (isset($_GET['tema'])) ? $_GET['tema'] : '';
  $subtema_activo = (isset($_GET['subtema'])) ? $_GET['subtema'] : '';

  /**
   * Fact checking
   */
  $fact_check = array(
    [
      'titulo' => 'Impreciso',
      'valor' => (get_field('impreciso'))?:0,
      'color' => '#FFDA22'
    ],
    [
      'titulo' => 'Verídico',
      'valor' => (get_field('veridico'))?:0,
      'color' => '#2AAC20'
    ],
    [
      'titulo' => 'Falso',
      'valor' => (get_field('falso'))?:0,
      'color' => '#FE3030'
    ],
    [
      'titulo' => 'Verdad a medias',
      'valor' => (get_field('verdad_a_medias'))?:0,
      'color' => '#DC3EFA'
    ],
    [
      'titulo' => 'Indeterminado',
      'valor' => (get_field('indeterminado'))?:0,
      'color' => '#888888'
    ],
    [
      'titulo' => 'Vago',
      'valor' => (get_field('vago'))?:0,
      'color' => '#FD7824'
    ]
  );
  /**
   * Analisis del voto
   */
  
  $temas = get_terms(['taxonomy' => 'tema','hide_empty' => false]);
  $subtemas = get_terms(['taxonomy' => 'subtema','hide_empty' => false]);

  $tema = get_taxonomy( 'tema' );
  $subtema = get_taxonomy( 'subtema' );

  $taxquery = array(
    'relation' => 'AND'
  );

  if ( isset( $_GET['tema'] ) && ! empty( $_GET['tema'] ) ) {
    $filter = array(
      'taxonomy' => 'tema',
      'field' => 'term_id',
      'terms' => $_GET['tema']
    );
    array_push($taxquery, $filter);
  }
  if ( isset( $_GET['subtema'] ) && ! empty( $_GET['subtema'] ) ) {
    $filter = array(
      'taxonomy' => 'subtema',
      'field' => 'term_id',
      'terms' => $_GET['subtema']
    );
    array_push($taxquery, $filter);
  }

  $args = array(
    'post_type' => 'votacion',
    'posts_per_page' => 5
  );

  if ( isset( $_GET ) ) {
    $args = array(
      'post_type' => 'votacion',
      'posts_per_page' => 5,
      'tax_query' => $taxquery
    );
  }

  $votaciones = new WP_Query($args);

  if ( $votaciones->have_posts() ) {    
    while ( $votaciones->have_posts() ) {
      $votaciones->the_post();

      $votacion_id = get_the_ID();
      $titulo = get_the_title();
      $url = get_the_permalink();
      $fecha = get_field('fecha', $votacion_id);
      $sesion = get_field('sesion_de_origen', $votacion_id);
      $resumen = get_the_excerpt();

      $votos = obtener_objeto_votos($votacion_id);

      // echo '<pre>';
      // var_dump($votos);
      // echo '</pre>';
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
        }
      }

      $votaciones_objeto[] = array(
        'votacion_id' => 'votacion-' . get_the_ID(),
        'nombre' => $titulo,
        'sesion' => $sesion->post_title,
        'fecha' => $fecha,
        'url' => $url,
        'AU' => $au,
        'SI' => $si,
        'NO' => $no,
        'AB' => $ab,
        'BL' => $bl,
      );
    }
    $json_bancada = json_encode( $votaciones_objeto );
    wp_reset_postdata();
  }

	$cargo = '';
	switch ( get_field('circunscripcion')->name ){
		case 'Exterior':
			$cargo = 'Asambleísta por ' . get_field('subcircunscripcion')->name;
			break;
		case 'Nacional':
			$cargo = 'Asambleísta Nacional';
			break;
		case 'Provincial':
			$cargo = 'Asambleísta por la Provincia de ' . get_field('subcircunscripcion')->name;
			break;
	}
	$cal_cargo = (get_field('cargo_en_el_concejo')) ? get_field('cargo_en_el_concejo'): '';
	echo '<!--<pre>'; var_dump($cal_cargo); echo '</pre>-->';

?>
<div class="loading-overlay">
  <div class="bounce-loader">
      <div class="bounce1"></div>
      <div class="bounce2"></div>
      <div class="bounce3"></div>
  </div>
</div>
<div class="ol-container ol-single mb-5">
  <div class="ol-archive__hero-banner" style="background-image: url(<?php echo get_stylesheet_directory_uri() . '/images/pleno-asamblea1160.jpg'; ?>); background-size: cover; background-position: center;">
    <div class="container">
      <div class="row">
        <div class="col-md-12 pt-5 pb-5 lh-1">
          <p class="fw-bolder text-center text-white uppercase fs-36 pb-2"><?php echo get_field('nombres'); ?> <?php echo get_field('apellidos'); ?></p>
          <p class="text-center text-white uppercase fs-20"><?php echo $cargo; ?></p>
        </div>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row pt-5">
      <div class="col-md-4 col-lg-4">
        <?php
          if ( has_post_thumbnail() ) :
            echo the_post_thumbnail( 'large', ['class' => 'img-fluid w-100'] );
          endif;
        ?>
      </div>
      <div class="col-md-8 col-lg-8 profile-info-placeholder text-fcd-gray">
        <div class="row">
          <div class="col-md-9">
            <h1 class="fs-34 bolder text-accent"><?php echo get_field('nombres'); ?> <?php echo get_field('apellidos'); ?></h1>
            <span class="d-block fs-32 mb-2 bold text-dark"><?php echo $cargo; ?></span>
            <span class="d-block fs-24 mb-2 bold">Curul <?php echo get_field('curul'); ?></span>
            <?php echo (get_field('colaborador')) ? '<span class="d-block fs-20 mb-5">Suplente: ' . get_field('colaborador')->post_title . '</span>' : ''; ?>
          </div>
          <div class="col-md-3">
          <?php
            $partido_logo = '';
            if ( get_field('partido_politico') ):
              $partido_logo = get_field('logo_del_partido','partido_politico_' . get_field('partido_politico')->term_id);
          ?>
            <img class="d-block m-auto" src="<?php echo $partido_logo['sizes']['medium']; ?>">
          <?php endif; ?>
          </div>
        </div>
        <div class="row">

        </div>
		<?php echo (!empty($cal_cargo)) ? '<p class="">Cargo en el Consejo de Administración Legislativa: ' . $cal_cargo . '</p>' : ''; ?>
		  <ul>
          <?php
            $comisiones = '';
            $comisiones = get_field('comisiones_permanentes');
            if ( $comisiones ) :
              foreach ( $comisiones as $comision ) :
                $cargo = 'Integrante';
                $presidente = get_field('presidente', $comision->taxonomy . '_' . $comision->term_id);
                $vicepresidente = get_field('vicepresidente', $comision->taxonomy . '_' . $comision->term_id);
                if ( ! empty( $presidente ) && $post->ID == $presidente->ID ){
                  $cargo = 'Presidente';
                }else if( ! empty( $vicepresidente ) && $post->ID == $vicepresidente->ID ){
                  $cargo = 'Vicepresidente';
                }
                echo sprintf(
                  '<li>%1$s de la Comisión Permanente de %2$s</li>',
                  $cargo,
                  $comision->name
                );
              endforeach;
            endif;
          ?>
          <?php echo (get_field('correo_electronico')) ? '<li><a href="mailto:">' . get_field('correo_electronico') . '</a></li>' : ''; ?>
          <?php echo (get_field('posicion_politica')) ? '<li>Posición política: ' . get_field('posicion_politica')->name . '</li>' : ''; ?>
        </ul>
        <ul class="list-inline profile-social-icons my-4 text-fcd-gray d-flex align-items-center">
          <?php echo (get_field('website')) ? '<li class="list-inline-item fs-42 me-3"><a href="' . get_field('website') . '"><i class="fas fa-link"></i></a></li>' : ''; ?>
          <?php echo (get_field('twitter')) ? '<li class="list-inline-item fs-42 me-3"><a href="' . get_field('twitter') . '"><i class="fab fa-twitter-square"></i></a></li>' : ''; ?>
          <?php echo (get_field('faceboook')) ? '<li class="list-inline-item fs-42 me-3"><a href="' . get_field('faceboook') . '"><i class="fab fa-facebook-square"></i></a></li>' : ''; ?>
          <?php echo (get_field('instagram')) ? '<li class="list-inline-item fs-42 me-3"><a href="' . get_field('instagram') . '"><i class="fab fa-instagram"></i></a></li>' : ''; ?>
          <li class="list-inline-item link-radiografia d-none d-lg-block">
			      <a target="_blank" href="<?php echo get_field('link_radiografia_politica', get_the_ID()); ?>" class="me-2 br-6 p-3 bg-accent border-0 uppercase bold fs-14 btn-equipo-trabajo text-center my-3" title="Link de Radiografía Política"><i class="fas fa-link" aria-hidden="true"></i> Radiografía Política</a>
          </li>
        </ul>
		<a target="_blank" href="<?php echo get_field('link_radiografia_politica', get_the_ID()); ?>" class="me-2 br-6 p-3 bg-accent border-0 uppercase bold fs-14 btn-equipo-trabajo text-center d-block d-md-none" title="Link de Radiografía Política"><i class="fas fa-link" aria-hidden="true"></i> Radiografía Política</a>
      </div>
    </div>
  </div>
	<?php
		$asistente_1 = get_field('asesor_1');
		if ( $asistente_1 ) {
			$email_asistente_1 = get_field('correo_electronico', $asistente_1->ID );
		}
		$asistente_2 = get_field('asesor_2');
		if ( $asistente_2 ) {
			$email_asistente_2 = get_field('correo_electronico', $asistente_2->ID );
		}
		$asistente_3 = get_field('asistente_1');
		if ( $asistente_3 ) {
			$email_asistente_3 = get_field('correo_electronico', $asistente_3->ID ); 
		}
		$asistente_4 = get_field('asistente_2');
		if ( $asistente_4 ) {
			$email_asistente_4 = get_field('correo_electronico', $asistente_4->ID );
		}
	?>
<style>
  body li.item-voto {
    border: 1px solid lightgray!important;
  }
  .item-voto:hover,
  body li.item-voto.item-active {
    box-shadow: 0px 7px 6px -8px #b3b3b3;
    cursor: pointer;
    border: 1px solid gray!important;
  }
  #voto-pop {
    min-height: 260px;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    border: 8px solid transparent;
    font-size: 40px;
    position: relative;
    border-radius: 16px;
    text-align: center;
    line-height: 1;
    padding: 10px;
  }
</style>
<div class="et_pb_section et_section_regular bg-secondary-alt mt-5 pt-0">
  <div class="et_pb_row pt-0">
    <div class="et_pb_column et_pb_column_4_4 et_pb_column_3  et_pb_css_mix_blend_mode_passthrough et-last-child">
        <div class="et_pb_module et_pb_tabs ol-tabs ol-tabs-perfil border-0">
          <ul class="et_pb_tabs_controls clearfix">
              <li class="et_pb_tab_0 bg-secondary-alt et_pb_tab_active" style="height: 48.7969px;"><a href="#" class="text-secondary uppercase fs-24 bolder">Fact-Check</a></li>
              <li class="et_pb_tab_1 bg-secondary-alt" style="height: 48.7969px;"><a id="analisis-btn" href="#" class="text-secondary uppercase fs-24 bolder">Análisis del voto</a></li>
			        <li class="et_pb_tab_2 bg-secondary-alt" style="height: 48.7969px;"><a href="#" class="text-secondary uppercase fs-24 bolder">Equipo de trabajo</a></li>
          </ul>
          <div class="et_pb_all_tabs bg-secondary-alt">
              <div class="et_pb_tab et_pb_tab_0 clearfix et_pb_active_content">
                <div class="et_pb_tab_content">
					<?php
					$link = get_field('enlace_fc', 'option');
					if ( $link ) {
					?>
					<div class="row">
						<div class="col-12 d-flex justify-content-end">
							<a class="me-2 br-6 px-3 py-1 bg-accent border-0 uppercase bold fs-14 btn-equipo-trabajo text-center" href="<?php echo $link['url'] ; ?>"><?php echo $link['title'] ; ?></a>
						</div>
					</div>
					<?php } ?>
					<?php
					$texto_superior_fc = get_field('texto_superior_fc', 'option');
					if ( $texto_superior_fc ) {
					?>
					<div class="row my-3">
						<div class="col-12 d-flex justify-content-end">
							<?php echo $texto_superior_fc; ?>
						</div>
					</div>
					<?php } ?>
                  <div id="charfact" style="width: 100%; min-height: 350px;"></div>
					<?php
					$texto_inferior_fc = get_field('texto_inferior_fc', 'option');
					if ( $texto_inferior_fc ) {
					?>
					<div class="row my-3">
						<div class="col-12 d-flex justify-content-end">
							<?php echo $texto_inferior_fc; ?>
						</div>
					</div>
					<?php } ?>
                </div>
              </div>
              <div class="et_pb_tab et_pb_tab_1 clearfix <?php echo ( isset( $_GET['filtered'] ) ) ? 'et-pb-active-slide' : ''; ?>">
                <div class="et_pb_tab_content">
                  <form id="voting-filters" action="/perfil/<?php echo $post->post_name; ?>/">
                    <input type="hidden" name="filtered" value="1">
                    <div class="container">
                      <div class="row">
                        <?php 
                        /**
                         * Comentado para efectos futuros
                         */
                        /*
                        <div class="col-md-4 col-lg-3 col-xxl-2 px-4 pt-4 pb-4 bg-dark-gray-fcd">
                          <div class="filter-bar-title py-5" style="background: url(<?php echo get_stylesheet_directory_uri() .'/images/radiacionOL.png'; ?>); background-repeat:no-repeat; background-size: contain; background-position: center;">
                            <h3 class="text-white text-center">Análisis de voto</h3>
                          </div>
                          <h4 class="text-white">Filtros</h4>
                          <label class="text-white" for="tema">Tema</label>
                          <select class="w-100" id="tema" name="tema">
                            <option value>Ninguno</option>
                            <?php foreach( $temas as $tema ){ ?>
                              <option value="<?php echo $tema->term_id; ?>" <?php echo ($tema_activo == $tema->term_id) ? 'selected' : ''; ?>><?php echo $tema->name; ?></option>
                              <?php } ?>
                            </select>
                            <br />
                          <label class="text-white" for="tema">Subtema</label>
                          <select class="w-100" id="subtema" name="subtema">
                            <option value>Ninguno</option>
                            <?php foreach( $subtemas as $subtema ){ ?>
                            <option value="<?php echo $subtema->term_id; ?>" <?php echo ($subtema_activo == $subtema->term_id) ? 'selected' : ''; ?>><?php echo $subtema->name; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        */ 
                        ?>
            <div class="col-12 py-4 ps-md-5">
							<?php
							$texto_superior_av = get_field('texto_superior_av', 'option');
							if ( $texto_superior_av ) {
							?>
							<div class="row my-3">
								<div class="col-12 d-flex justify-content-end">
									<?php echo $texto_superior_av; ?>
								</div>
							</div>
							<?php } ?>
              <div class="row">
								<div class="col-12 d-flex justify-content-end align-items-center">
                  <button id="btn-xls-asambleista" data-profileid="<?php echo get_the_ID(); ?>" class="w-100 p-2 px-3 d-flex justify-content-around align-items-center br-6 border-0" style=" max-width: 100px;" type="button" title="Exportar Excel General de Votaciones de <?php echo get_the_title(); ?>"><span class="bold">XLS</span> <i class="fas fa-file-excel fs-20" aria-hidden="true"></i></button>
                  <button id="btn-csv-asambleista" data-profileid="<?php echo get_the_ID(); ?>" class="w-100 p-2 px-3 d-flex justify-content-around align-items-center br-6 border-0 ms-3" style=" max-width: 100px;" type="button" title="Exportar CSV General de Votaciones de <?php echo get_the_title(); ?>"><span class="bold">CSV</span> <i class="fas fa-file-csv fs-20" aria-hidden="true"></i></button>
								</div>
							</div>
              <?php 
                /**
                 * Comentado para efectos futuros
                 */
                /*
                <div id="charanalisis" style="width: 100%; min-height: 350px;"></div>
                */
                ?>
                <!-- NUEVA INTEGRACION  -->
                <div class="row my-5 mt-3">
                  <div class="col-12 col-lg-9">
                    <?php
                      $votaciones = get_field('votaciones');
                      $hay_votaciones_meta = false;
                      if ( $votaciones ) {
                        foreach ( $votaciones as $index => $votacion ) {
                          if ($votacion['votacion']){
                            $hay_votaciones_meta = true;
                            if (0 == $index){
                              echo '<h4>Seleccionar votación sobre proyectos de ley o resoluciones</h4>';
                              echo '<ul class="d-flex flex-column justify-content-start align-items-start">';
                            }
                            //echo '<pre>'; var_dump($votacion); echo '</pre>'; 
                            $votos = get_field('listado_de_votos', $votacion['votacion']->ID);
                            $res = array();
                            $delego = '';
                            foreach( $votos as $voto ) {
                              if ( $voto['asambleista'] == get_the_title() ){
                                //var_dump($voto);
                                if ( $voto['de'] ) {
                                  $delego = ' <span style="font-size:12px; color: orange;"><i class="fas fa-exclamation-triangle"></i> Voto suplente</span>';
                                }
                                if ( $voto['au'] ) {
                                  $res = array(
                                    'color'=> 'gray',
                                    'titulo' => 'Ausencia'
                                  );
                                }else{
                                  switch ( $voto['voto'] ) {
                                    case 'SI':
                                      $res = array(
                                        'color'=> '#2CAC5B',
                                        'titulo' => 'A Favor'
                                      );
                                      break;
                                    case 'NO':
                                      $res = array(
                                        'color'=> '#F20000',
                                        'titulo' => 'En Contra'
                                      );
                                      break;
                                    case 'AB':
                                      $res = array(
                                        'color'=> 'gray',
                                        'titulo' => 'Abstención'
                                      );
                                      break;
                                    case 'BL':
                                      $res = array(
                                        'color'=> 'gray',
                                        'titulo' => 'Blanco'
                                      );
                                      break;
                                  }
                                }
                                continue;
                              }
                            }

                            $nombre = (get_field('nombre_corto', $votacion['votacion']->ID))?: $votacion['votacion']->post_title;
                            echo '<li class="w-100 bg-light my-1 px-4 py-1 rounded br-6 bg-accent item-voto border" data-voto=\'' . json_encode($res) . '\'>' . $nombre . ' ' . $delego . '</li>';
                          }

                        }
                        echo '</ul>';
                        if ( $hay_votaciones_meta == false ) {
                          ?>
                    <div style="min-height: 350px;" class="w-100 align-items-center d-flex fs-26 justify-content-center">
                        <strong>No se han analizado votaciones del legislador.</strong>
                    </div>
                          <?php 
                        }

                      }else{
                    ?>
                    <div style="min-height: 350px;" class="w-100 align-items-center d-flex fs-26 justify-content-center">
                        <strong>No se han analizado votaciones del legislador.</strong>
                    </div>
                    <?php } ?>
                  </div>
                  <div class="col-12 col-lg-3 mt-3 mt-md-0">
                    <?php
                      $link = get_field('enlace_av', 'option');
                      if ( $link ) {
                    ?>
                    <div class="row">
                      <div class="col-12 d-flex justify-content-between">
                        <a class="w-100 br-6 px-3 py-1 bg-accent border-0 uppercase bold fs-14 btn-equipo-trabajo text-center" href="<?php echo $link['url'] ; ?>"><?php echo $link['title'] ; ?></a>
                      </div>
                    </div>
                    <?php } ?>
                    <div class="row">
                      <div class="col-12 d-flex justify-content-end">
                        <div id="voto-pop"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- END NUEVA INTEGRACION  -->
							<?php
							$texto_inferior_av = get_field('texto_inferior_av', 'option');
							if ( $texto_inferior_av ) {
							?>
							<div class="row my-3">
								<div class="col-12 d-flex justify-content-end">
									<?php echo $texto_inferior_av; ?>
								</div>
							</div>
							<?php } ?>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
			        </div>
              <div class="et_pb_tab et_pb_tab_2 clearfix">
                <div class="et_pb_tab_content">
                  <div class="row">
                    <div class="col-md-4">
                      <?php if ($asistente_1): ?>
                      <p><b class="d-block"><span class="fs-24">Asesor 1:</span> <br /><?php echo $asistente_1->post_title; ?></b>
                      <span><a href="mailto:<?php echo $email_asistente_1; ?>"><?php echo $email_asistente_1; ?></a></span></p>
                      <?php endif; ?>
                      <?php if ($asistente_2): ?>
                        <p><b class="d-block"><span class="fs-24">Asesor 2:</span> <br /><?php echo $asistente_2->post_title; ?></b>
                        <span><a href="mailto:<?php echo $email_asistente_2; ?>"><?php echo $email_asistente_2; ?></a></span></p>
                      <?php endif; ?>
                      <?php if ($asistente_3): ?>
                        <p><b class="d-block"><span class="fs-24">Asistente 1:</span> <br /><?php echo $asistente_3->post_title; ?></b>
                        <span><a href="mailto:<?php echo $email_asistente_3; ?>"><?php echo $email_asistente_3; ?></a></span></p>
                      <?php endif; ?>
                      <?php if ($asistente_4): ?>
                      <p><b class="d-block"><span class="fs-24">Asistente 2:</span> <br /><?php echo $asistente_4->post_title; ?></b>
                              <span><a href="mailto:<?php echo $email_asistente_4; ?>"><?php echo $email_asistente_4; ?></a></span></p>
						<?php endif; ?>
                    </div>
                      <?php 
                        $exasesores = get_field('exasesores');
                        if ( $exasesores ) {
						  echo '<div class="col-md-4 mt-3 mt-lg-0">';
                          foreach ( $exasesores as $index => $value ) {
                            echo '
                            <p>
                              <b>
                                <span class="fs-24">Exasesor:</span><br />
                                ' . $value['nombre_del_exasesor'] . '
                              </b>
                            </p>
                            ';
                          }
						  echo '</div>';
                        }
					  
					  
                        $exasistentes = get_field('exasistentes');
                        if ( $exasistentes ) {
						 echo '<div class="col-md-4">';
                          foreach ( $exasistentes as $index2 => $value ) {
                            echo '
                            <p>
                              <b>
                                <span class="fs-24">Exasistente:</span><br />
                                ' . $value['nombre_del_exasistente'] . '
                              </b>
                            </p>
                            ';
                          }
						echo '</div>';
                        }
                      ?>
                  </div>
				  <div class="row">
					<div class="col-12">
					  <p class="fs-12">
                        <strong style="color: #FFC307;"><?php echo get_field('disclaimer', 'option'); ?></strong>
                      </p>
					</div>  
				  </div>
                </div>
              </div>
          </div>
        </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>

<script>

    jQuery(document).ready( function($) {
      const queryString = window.location.search;
      const urlParams = new URLSearchParams(queryString);
      const code = urlParams.get('filtered')

      $('#btn-csv-asambleista').click( function(e){
        var dataProfile = $(this).data('profileid')
        e.preventDefault();
        $.ajax({
            url: ol_dom_vars.ajaxurl,
            type: 'GET',
            data: {
              action: 'ol_generate_csv_votacion_asambleista',
              profileid: dataProfile
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
       * XLS Generation AJAX
       */
      $('#btn-xls-asambleista').click( function(e){
        e.preventDefault();
        var dataProfile = $(this).data('profileid')
        // console.log(dataProfile)
        $.ajax({
          url: ol_dom_vars.ajaxurl,
          type: 'GET',
          data: {
            action: 'ol_generate_xls_general_votaciones_asambleista',
            profileid: dataProfile
          },
          beforeSend: function(){
            $('body').toggleClass('loading-overlay-showing');
          },
          success: function(data){
            $('body').toggleClass('loading-overlay-showing');
            var $a = $("<a>");
            $a.attr("href",data.file);
            $("body").append($a);
            $a.attr("download","Observatorio-Legislativo-Votacion-Asambleista-<?php echo $post->post_name; ?>.xls");
            $a[0].click();
            $a.remove();
          },
          error: function(xhr,err){
            console.log(err);
            console.log(xhr);
          }

        })

      })

      if ( code == 1 ){
        $('#analisis-btn').click();
        $('html, body').animate({
          scrollTop: $("#analisis-btn").offset().top - 150
        });
        
      }


      $('select').change( function() {
        if ( 'tema' == $(this).attr('name') ) {
          $('select[name="subtema"]')[0].selectedIndex = 0;
        }
        if ( 'subtema' == $(this).attr('name') ) {
          $('select[name="tema"]')[0].selectedIndex = 0;
        }
        $('#voting-filters').submit()
      })

      $('li.item-voto').click(function(){
        var data = $(this).data('voto')
        $('li.item-voto').removeClass('item-active')
        $(this).addClass('item-active');
        $('#voto-pop').css('display', 'none')
        if (Object.keys(data).length == 0) return;
        $('#voto-pop').css(
          {
            'border-color' : data.color,
            'color' : data.color,
            'display': 'none'
          }
        ).html('<span>' + data.titulo  + '</span>').fadeIn(200);

      })


    })

    var datos = '<?php echo json_encode($fact_check); ?>';
    var datosA = '<?php echo json_encode($votaciones_objeto); ?>';
    datos = JSON.parse(datos);
    datosA = JSON.parse(datosA);

    // console.log(datosA)

    var sumatoriaFact = 0;
    datos.forEach( obj => {
      // console.log(obj.valor)
      sumatoriaFact += parseInt( obj.valor )
    })
	
	if ( window.innerWidth < 800 ) {
		var charfact = document.getElementById('charfact');
		charfact.style.height = '600px';
	}

    // console.log(sumatoriaFact)

    am4core.ready(function() {

    // Themes begin
    am4core.useTheme(am4themes_animated);
    // Themes end

    
    if ( sumatoriaFact == 0 ){
		
		$('#charfact')
			.addClass('align-items-center d-flex fs-26 justify-content-center')
			.html('<strong>No se ha realizado fact-checking al discurso de este asambleísta.</strong>');
	}else{
	// Create chart instance	
	var chart = am4core.create("charfact", am4charts.PieChart);
		
	var legendPosition = 'right';
	var lFs = 30;
	var strokeWidth = 2;
	if ( window.innerWidth < 800 ) {
		legendPosition = 'bottom';
		lFs = 18;
		strokeWidth = 1;
	}

    // Add and configure Series
    var pieSeries = chart.series.push(new am4charts.PieSeries());
    pieSeries.dataFields.value = "valor";
    pieSeries.dataFields.category = "titulo";
	pieSeries.labels.template.text = "{category}: {value.value}";
	pieSeries.slices.template.tooltipText = "{category}: {value.value}";

    var container = new am4core.Container();
    container.parent = pieSeries;
    container.horizontalCenter = "middle";
    container.verticalCenter = "middle";
    container.width = am4core.percent(40) / Math.sqrt(2);
    container.fill = "white";

    var label = new am4core.Label();
    label.parent = container;
    label.text = "TOTAL: " + sumatoriaFact;
    label.horizontalCenter = "middle";
    label.verticalCenter = "middle";
    label.fontSize = lFs;

    // Let's cut a hole in our Pie chart the size of 30% the radius
    chart.innerRadius = am4core.percent(30);

    // Put a thick white border around each Slice
    pieSeries.slices.template.stroke = am4core.color("#fff");
    pieSeries.slices.template.strokeWidth = strokeWidth;
    pieSeries.slices.template.strokeOpacity = 1;
    pieSeries.slices.template.propertyFields.fill = "color";
    pieSeries.labels.template.disabled = true;

    // Create a base filter effect (as if it's not there) for the hover to return to
    var shadow = pieSeries.slices.template.filters.push(new am4core.DropShadowFilter);
    shadow.opacity = 0;

    // Create hover state
    var hoverState = pieSeries.slices.template.states.getKey("hover"); // normally we have to create the hover state, in this case it already exists

    // Slightly shift the shadow and make it more prominent on hover
    var hoverShadow = hoverState.filters.push(new am4core.DropShadowFilter);
    hoverShadow.opacity = 0.7;
    hoverShadow.blur = 5;

    // Add a legend
    var legend = chart.legend = new am4charts.Legend();
    legend.position = legendPosition
	legend.valueLabels.template.text = "{value.value}";

    chart.data = datos;
		
	} // end if has sumatoria



    /**
     * Analisis del voto
     */
    var chart2 = am4core.create("charanalisis", am4charts.XYChart);
    chart2.data = datosA;

    // Create axes
    var categoryAxis = chart2.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "nombre";
    categoryAxis.renderer.grid.template.location = 0;
    categoryAxis.tooltip.label.maxWidth = 200;
    //https://www.amcharts.com/docs/v4/tutorials/managing-width-and-spacing-of-column-series/
    categoryAxis.tooltip.label.wrap = true;
    let label2 = categoryAxis.renderer.labels.template;
    label2.wrap = true;
    label2.truncate = true;
    label2.maxWidth = 120;
    label2.tooltipText = "{category}";
    // https://www.amcharts.com/docs/v4/tutorials/wrapping-and-truncating-axis-labels/


    var valueAxis = chart2.yAxes.push(new am4charts.ValueAxis());
    valueAxis.renderer.inside = true;
    valueAxis.renderer.labels.template.disabled = true;
    valueAxis.min = 0;

    // Create series
    function createSeries(field, name, color) {
      
      // Set up series
      var series = chart2.series.push(new am4charts.ColumnSeries());
      series.name = name;
      series.tooltip.label.wrap = true;
      series.dataFields.valueY = field;
      series.tooltip.label.width = 300;
      series.dataFields.categoryX = "nombre";
      series.sequencedInterpolation = true;
      series.columns.template.width = am4core.percent(70);
      series.columns.template.stroke = am4core.color(color);
      series.columns.template.fill = am4core.color(color);
      
      // Make it stacked
      series.stacked = true;
      
      // Configure columns
      series.columns.template.width = am4core.percent(60);
      series.columns.template.tooltipText = "[bold]{name}[/]\n[font-size:14px]{categoryX}: {valueY}";
      
      // // Add label
      // var labelBullet = series.bullets.push(new am4charts.LabelBullet());
      // labelBullet.label.text = "{valueY}";
      // labelBullet.locationY = 0.5;
      // labelBullet.label.hideOversized = true;
      
      return series;
    }

    createSeries("SI", "Si", '#7AC74F');
    createSeries("NO", "No", '#D7CDCC');
    createSeries("AB", "Abstención", '#FFBC42');
    createSeries("AU", "Ausente", '#0A2239');
    createSeries("BL", "Blanco", '#BDFFFD');

    // Legend
    chart2.legend = new am4charts.Legend();

    }); // end am4core.ready()
  </script>