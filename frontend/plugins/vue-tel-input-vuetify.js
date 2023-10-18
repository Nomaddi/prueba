import Vue from 'vue';
import VueTelInputVuetify from 'vue-tel-input-vuetify/lib';

import Vuetify from 'vuetify';

Vue.use(Vuetify);

const vuetify = new Vuetify({
  theme: {
    dark: false, // Habilitar o deshabilitar el modo oscuro
    themes: {
      light: {
        primary: '#1976D2', // Color principal
        secondary: '#424242', // Color secundario
      },
      dark: {
        primary: '#90CAF9',
        secondary: '#E0E0E0',
      },
    },
  },
  
});

Vue.use(VueTelInputVuetify, {
  vuetify, // Importa la instancia de Vuetify
});

export default vuetify;
