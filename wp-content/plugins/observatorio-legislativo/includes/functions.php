<?php
function get_perfil_relation_terms($post_id, $tax){
  if ( ! $post_id && ! $tax ) return;
  $terms = get_the_terms( $post_id, $tax );
  if ( empty( $terms ) ) return;
  return $terms;
}

function get_perfil_edad($post_id){
  if (!$post_id) return;
  $edad = get_field('fecha_de_nacimiento', $post_id);
  if ( empty( $edad ) ) return;
  $edad = explode("/", $edad);
  $edad = (date("md", date("U", mktime(0, 0, 0, $edad[0], $edad[1], $edad[2]))) > date("md")
  ? ((date("Y") - $edad[2]) - 1)
  : (date("Y") - $edad[2]));
  return $edad;
}
