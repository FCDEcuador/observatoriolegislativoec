const app = Vue.createApp({
  data() {
    return {
      pageTitle: 'Observatorio Legislativo',
      activo: false,
      legisladores: 136,
      asistencia: 0,
      listaLegisladores: []
    }
  },
  beforeMount() {
    async function searchOnAPI(url = "") {
      // Pace.restart();

      const response = await fetch(url, {
        method: "GET",
        mode: "cors",
        cache: "no-cache",
        headers: {
          "Content-Type": "application/json",
        },
        //referrerPolicy: 'no-referrer',
      });

      if (response.status === 200) {
        return response.json();
      }

      // Show error ?
      return {};
    }
    searchOnAPI("http://localhost/observatorio/wp-json/wp/v2/perfil?per_page=100").then(
      (listaLegisladores) => {
        if (listaLegisladores) {
          this.listaLegisladores = listaLegisladores;
          console.log(listaLegisladores);
        }
      }
    );
  },
  methods: {},
  component() {
    'listado',
    {
      template: `aqui`,
      data() {
        return{
          title: 'Es un titulo'
        }
      }
    }
  }
})

const mountedApp = app.mount('#vueApp')