<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'manage_perfil_posts_columns', 'ol_filter_posts_columns' );
function ol_filter_posts_columns( $columns ) {
  $columns['new-title'] = __( 'Perfil' );
  $columns['thumb'] = __( 'PIC' );
  $columns['tipo'] = __( 'Tipo' );
  $columns['curul'] = __( 'Curul' );
  $columns['partido'] = __( 'Partido' );
  $columns['bancada'] = __( 'Bancada' );
  unset($columns['date']);
  unset($columns['title']);
  
  return $columns;
}
add_action( 'manage_perfil_posts_custom_column', 'ol_perfil_column', 10, 2);
function ol_perfil_column( $column, $post_id ) {
  
  if ( 'new-title' === $column ) {
    $edad = (get_perfil_edad($post_id)) ? ' - (' . get_perfil_edad($post_id) . 'a)' : '';
    $edit_link = get_edit_post_link($post_id);

    echo '<b><a href="' . $edit_link . '">' . get_the_title($post_id) . $edad . '</a></b>';
  }
  if ( 'thumb' === $column ) {
    echo get_the_post_thumbnail( $post_id, array(50, 50) );
  }
  if ( 'tipo' === $column ) {
    $terms = get_perfil_relation_terms( $post_id, 'tipo_perfil' );
    if ( $terms ) {
      echo $terms[0]->name;
    }
  }
  if ( 'curul' === $column ) {
    $curul = get_field( 'curul', $post_id );
    echo $curul;
  }
  if ( 'partido' === $column ) {
    $terms = get_perfil_relation_terms( $post_id, 'partido_politico' );
    if ( $terms ) {
      echo $terms[0]->name;
    }
  }
  if ( 'bancada' === $column ) {
    $terms = get_perfil_relation_terms( $post_id, 'bancada' );
    if ( $terms ) {
      echo $terms[0]->name;
    }
  }
}


// add_filter( 'display_post_states', 'ecs_add_post_state', 10, 2 );
// function ecs_add_post_state( $post_states, $post ) {
//     if( $post->ID == YOUR-ID-HERE ) {
//         $post_states[] = 'Profile edit page';
//     }
//     return $post_states;
// }

// class ACF_Hider {

//     public function __construct() {
//         add_action( 'plugins_loaded', array( $this, 'init' ) );
//     }

//     public function init() {
//         // If ACF isn't installed/activated, don't do anything.
//         // Additionally, if we're not on an admin page none of this is needed.
//         if ( ! class_exists('acf') || ! is_admin() ) {
//             return;
//         }

//         $this->hide_menu_item();
//         $this->redirect();
//     }

//     public function hide_menu_item() {
//         // Remove the ACF main item from the admin menu.
//         add_filter( 'acf/settings/show_admin', '__return_false' );
//     }

//     public function redirect() {
//         // If the user tries to access an ACF page directly, redirect them to the Dashboard.
//         if ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'acf-field-group' ) {
//             wp_redirect( admin_url() );
//             die();
//         }
//     }

// }
// new ACF_Hider();
