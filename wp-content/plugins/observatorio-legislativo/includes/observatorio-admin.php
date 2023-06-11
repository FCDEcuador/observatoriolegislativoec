<div class="wrap">
  <div id="vueApp">
    <h1>{{ pageTitle }}</h1>
    <p v-if="activo">Activo</p>
    <p v-else>Suspendido</p>
    <p v-if="legisladores == asistencia">Todos</p>
    <p v-else-if="(legisladores - asistencia) < 80">Asistencia aprobada({{ asistencia }} legisladores)</p>
    <p v-else-if="(legisladores - asistencia) < 80 && (legisladores - asistencia) > 0">Poca Asistencia({{ asistencia }} legisladores)</p>
    <p v-else="">Ausencia total</p>
    <listado></listado>
    <!-- <div>{{ listaLegisladores }}</div> -->

  </div>
</div>