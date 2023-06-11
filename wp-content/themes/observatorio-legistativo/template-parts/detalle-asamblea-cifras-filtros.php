<?php
  $genero_activo = (isset($_GET['g'])) ? $_GET['g'] : '';
  $partido_activo = (isset($_GET['o'])) ? $_GET['o'] : '';
  $bancada_activo = (isset($_GET['b'])) ? $_GET['b'] : '';

  $asambleistas = ol_get_all_legisladores();
  $generos = get_generos();
  $partidos = get_partidos();
  $bancadas = get_bancadas();
?>
<h4 class="text-white">Filtros</h4>
<label class="text-white" for="genero">Género</label>
<select class="w-100" id="genero" name="g">
  <option value>Ninguno</option>
  <?php foreach( $generos as $genero ){ ?>
  <option value="<?php echo $genero->term_id; ?>" <?php echo ($genero_activo == $genero->term_id) ? 'selected' : ''; ?>><?php echo $genero->name; ?></option>
  <?php } ?>
</select>
<br />
<label class="text-white" for="organizacion">Organización Política</label>
<select class="w-100" id="organizacion" name="o">
  <option value>Ninguna</option>
  <?php foreach( $partidos as $partido ){ ?>
  <option value="<?php echo $partido->term_id; ?>" <?php echo ($partido_activo == $partido->term_id) ? 'selected' : ''; ?>><?php echo $partido->name; ?></option>
  <?php } ?>
</select>
<br />
<label class="text-white" for="circunscripcion">Bancada</label>
<select name="b" class="w-100" id="bancada">
  <option value>Ninguna</option>
  <?php foreach( $bancadas as $bancada ){ ?>
  <option value="<?php echo $bancada->term_id; ?>" <?php echo ($bancada_activo == $bancada->term_id) ? 'selected' : ''; ?>><?php echo $bancada->name; ?></option>
  <?php } ?>
</select>