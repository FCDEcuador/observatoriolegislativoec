<?php get_header(); 
global $wp_query;
// var_dump($wp_query->request);
// var_dump($_REQUEST);
// var_dump($_SERVER['QUERY_STRING']);
// parse_str($_SERVER['QUERY_STRING'], $query_string);
// if ( array_key_exists( 'letter-filter', $query_string ) ){
//   unset($query_string['letter-filter']);
// }
// var_dump($query_string);
// die;
$mostrar_hero = get_field('mostrar_hero', 'option');
$hero_image_de_fondo = get_field('hero_image_de_fondo', 'option');
$hero_titulo = (get_field('hero_titulo', 'option')) ?: 'Directorio Legislativo';
$hero_background = '';
if ( $hero_image_de_fondo ) :
  $hero_background = ' style="background: url(' . $hero_image_de_fondo . '); background-size: cover; background-position: center;"';
endif;
$display = ( isset ( $_GET['htmlmode'] ) ) ? ' d-none' : '';
?>
<div class="ol-container ol-archive<?php echo ($display) ? ' ol-archive-htmlmode' : ''; ?>">
  <?php if ($mostrar_hero) : ?>
  <div class="ol-archive__hero-banner"<?php echo $hero_background; ?>>
    <div class="container">
      <div class="row">
        <div class="col-md-12 pt-5 pb-5">
          <h1 class="fw-bolder text-center text-white uppercase fs-60 bold"><?php echo $hero_titulo; ?></h1>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
  <div class="az-filter-section bg-accent pt-3 pb-3 d-none d-md-block">
    <div class="container<?php echo $display; ?>">
      <div class="row">
        <div class="col-md-10 offset-md-2">
          <?php
            // $query_vars = '';
            // if( $query_string ) {
            //   foreach ( $query_string as $index => $argument ){
            //     $query_vars .= '&' . $index . '=' . $argument[0];
            //   }
            //   // $query_vars = '&'; . $_SERVER['QUERY_STRING'];
            // }
          ?>
           <ul class="az-items">
             <?php foreach (range('A', 'Z') as $char) : ?>
              <li><a class="pe-3 text-dark bolder" href="<?php echo get_post_type_archive_link( 'perfil' ); ?>?filtered=1&letter-filter=<?php echo $char; ?>"><?php echo $char; ?></a></li>
             <?php endforeach; ?>
           </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row">
      <!-- Filter -->
      <div class="col-md-4 col-lg-3 col-xxl-2 px-4 pt-4 pb-4 bg-dark-gray-fcd<?php echo $display; ?>">
        <div class="sticky-md-top-1">
          <div class="filter-bar-title pt-3 pb-3" style="background: url(<?php echo get_stylesheet_directory_uri() .'/images/radiacionOL.png'; ?>); background-repeat:no-repeat; background-size: contain; background-position: center;">
            <h3 class="uppercase bolder py-5 text-center text-white">Busca a tu<br />Asambleísta</h3>
          </div>
          <?php echo do_shortcode('[show-filters]'); ?>
        </div>
      </div><!-- END Filter -->
      <!-- Main feed -->
      <div class="<?php echo ($display) ? 'col-md-12' : 'col-md-8 col-lg-9 col-xxl-10'; ?> pt-4 pb-4">
        <?php if ( have_posts() ){ ?>
        <?php 
           if ( is_search() ) : 
            global $wp_query;
        ?>
        <div class="row g-3 mt-3 pt-3">
          <div class="col-md-12">
            <h4 class="ta-r text-secondary fs-16"><?php echo $wp_query->post_count; ?> resultado<?php echo ($wp_query->post_count > 1) ? 's' : ''; ?> para: "<?php echo get_search_query( 's' ); ?>"</h4>
          </div>
        </div>
        <?php endif; ?>
        <div class="row g-3 <?php echo (!is_search()) ? 'mt-3 pt-3' : ''; ?>">
        <?php while ( have_posts() ) { the_post(); ?>
          <div class="col-lg-6 col-xxl-4">
            <a href="<?php echo get_the_permalink(); ?>">
              <div class="perfil-placeholder p-3">
                <div class="row">
                  <div class="col-md-5 pt-3 text-center">
                    <?php if ( has_post_thumbnail() ){ ?>
                      <?php echo get_the_post_thumbnail( get_the_ID(), 'thumbnail', array('class' => 'img-fluid m-auto d-block') ); ?>
                    <?php } ?>
                  </div>
                  <div class="col-md-7 d-flex flex-column justify-content-between align-items-center">
                    <div class="perfil-content text-center">
                      <!-- <h2 class="fs-18 bolder text-fcd-gray pb-0"><?php echo get_the_title(); ?></h2> -->
                      <?php 
                        $nombres = get_field('nombres');
                        $apellidos = get_field('apellidos');
                        $nombre_completo = $nombres . ' ' . $apellidos;
                        
                        if ( empty( $nombres ) || empty( $apellidos) ) {
                          $nombre_completo = get_the_title();
                        }
                      ?>
                      <h2 class="fs-18 bolder text-fcd-gray pb-0"><?php echo $nombre_completo; ?></h2>
                      <span class="d-block text-fcd-gray bolder fs-14">Asambleísta <?php echo get_field('circunscripcion')->name; ?></span>
                      <span class="d-block text-fcd-gray fs-14">Curul <?php echo get_field('curul'); ?></span>
                      <div class="perfil-content-partido">
                        <?php //var_dump( get_field('partido_politico') ); ?>
                        <?php //var_dump( get_field('logo_del_partido','partido_politico_' . get_field('partido_politico')->term_id) ); ?>
                        <?php if( get_field('partido_politico') ): ?>
                        <?php if( $logo = get_field('logo_del_partido','partido_politico_' . get_field('partido_politico')->term_id) ): ?>
                          <?php //var_dump($logo['sizes']['medium']); ?>
                        <img width="100" src="<?php echo $logo['sizes']['medium']; ?>">
                        <?php endif; ?>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
        <?php } // endwhile ?>
        </div>
        <div class="row mt-4">
          <div class="col-12 text-center">
            <?php
              if ( function_exists( 'wp_pagenavi' ) )
                wp_pagenavi();
              else
                get_template_part( 'includes/navigation', 'index' );
            ?>
          </div>
        </div>
        <?php }else{ ?>
          <div class="row g-3 mt-5 pt-3">
            <div class="col-md-12">
              <h1 class="fs-36">No se han encontrados datos para: <br />"<span class="text-accent"><?php echo get_search_query( 's' ); ?></span>"</h1>
            </div>
          </div>
        <?php } //endif ?>
      </div><!-- END Main feed -->
    </div>
  </div>
</div>
<?php get_footer(); ?>