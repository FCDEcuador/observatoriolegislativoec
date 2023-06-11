app.component(
  'lista-perfiles',
  {
    template: `aqui`,
    data() {
      return{
        title: 'Es un titulo'
      }
    }
    /*template:`
    <ul v-if="listaLegisladores.length > 0">
      <li v-for="perfil in listaLegisladores" :key="perfil.id">
        <a :href="perfil.link" target="_blank">
          <h2>{{ perfil.title.rendered + ' - ' + perfil.id }}</h2>
        </a>
        <strong> Curul: <span>{{ ( perfil.acf.curul.length > 0 ) ? perfil.acf.curul.length : 'No tiene' }}</span></strong>
      </li>
    </ul>
    `*/
  }
)