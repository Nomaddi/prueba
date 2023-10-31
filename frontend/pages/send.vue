<template>
  <v-row>
    <v-col>
      <v-card elevation="7">
        <v-card class="pa-2" color="#424242" dark>Plantillas</v-card>
        <div class="pa-4">
          <v-select v-model="item" :items="items" label="Templates" outlined @change="selectTemplate"></v-select>

          <template v-if="template">
            <v-divider class="mb-4"></v-divider>
            <v-alert v-if="template.status !== 'APPROVED'" type="error">Template unapproved and can't be used to send
              messages.</v-alert>

            <v-select v-model="tagSelect" :items="tags" label="Selecciona una etiqueta" outlined item-text="nombre"
              item-value="key" return-object required @change="mostrarContactosPorEtiqueta">
              <template #selection="{ item }">
                <v-chip>
                  <span>{{ item.nombre }}</span>
                </v-chip>
              </template>
            </v-select>
            <!-- Aquí muestras la lista de contactos asociados a la etiqueta seleccionada -->
            <div v-if="contactos.length > 0">
              <h2>Total contactos,con la etiqueta seleccionada : {{ contactos.length }}</h2>

              <h2>Total contactos,con depuración de numeros repetidos : {{ filteredContacts.length }}</h2>
            </div>

            <div class="my-5">
              <h5 class="text-h5">Destinatarios</h5>
              <v-textarea v-model="recipients" outlined hint="Ingrese una destinatario por línea." persistent-hint>
              </v-textarea>
            </div>

            <div v-if="template.header" class="my-5">
              <h5 class="text-h5">Header</h5>
              <p v-if="template.header.format == 'TEXT'">
                {{ template.header.text }}
              </p>
              <v-text-field v-else v-model="header_url" outlined class="mt-2"
                :label="`${template.header.format} URL`"></v-text-field>
            </div>

            <div v-if="template.body" class="my-5">
              <h5 class="text-h5">Body</h5>
              <p class="pre-wrap">{{ template.body }}</p>
              <v-text-field v-for="(placeholder, index) in template.body_placeholders" :key="index"
                v-model="body_placeholders[index]" outlined :label="placeholder.text">
                <template #prepend>
                  <v-tooltip location="bottom">
                    <template #activator="{ props }">
                      <v-icon v-bind="props" icon="mdi-help-circle-outline"></v-icon>
                    </template>

                    I'm a tooltip
                  </v-tooltip>
                </template>

                <template #append-inner>
                  <v-fade-transition leave-absolute>
                    <v-progress-circular v-if="loading" color="info" indeterminate size="24"></v-progress-circular>

                    <img v-else height="24" width="24" src="https://cdn.vuetifyjs.com/images/logos/v-alt.svg" alt="">
                  </v-fade-transition>
                </template>

                <template #append>
                  <v-menu>
                    <template #activator="{ props }">
                      <v-btn v-bind="props" class="mt-n2">
                        <v-icon icon="mdi-menu" start></v-icon>

                        Menu
                      </v-btn>
                    </template>

                    <v-card>
                      <v-card-text class="pa-6">
                        <v-btn color="primary" size="large" variant="text" @click="clickMe">
                          <v-icon icon="mdi-target" start></v-icon>

                          Click me
                        </v-btn>
                      </v-card-text>
                    </v-card>
                  </v-menu>
                </template>
              </v-text-field>
            </div>

            <div v-if="template.footer" class="my-5">
              <h5 class="text-h5">Footer</h5>
              <p class="pre-wrap">{{ template.footer }}</p>
            </div>

            <div v-if="template.buttons" class="my-3">
              <h5 class="text-h5">Buttons</h5>
              <ul>
                <li v-for="(button, index) in template.buttons" :key="index">
                  {{ button.text }} <em>({{ button.type }})</em>
                </li>
              </ul>
            </div>

            <div class="mt-8">
              <v-btn block large color="teal darken-1" dark :loading="sending" @click="send">Send</v-btn>
            </div>
          </template>
        </div>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import Swal from 'sweetalert2';

export default {
  data: () => ({
    item: '',
    template: null,
    templates: [],
    items: [],
    recipients: [],
    body_placeholders: [],
    header_url: null,
    sending: false,
    tagSelect: [],
    tags: [],
    contactos: [],
    contactoFiltrados: [],
    message: 'Hey!',
    loading: false,
  }),
  created() {
    this.loadTemplates();
    this.loadTags();
  },
  computed: {
    filteredContacts() {
      // Crea un conjunto para almacenar números de teléfono únicos
      const uniquePhoneNumbers = new Set();

      // Filtra duplicados y almacena números de teléfono únicos en el conjunto
      const filtered = this.contactos.filter((contacto) => {
        if (!uniquePhoneNumbers.has(contacto.telefono)) {
          uniquePhoneNumbers.add(contacto.telefono);
          return true;
        }
        return false;
      });

      return filtered;
    },
  },
  methods: {
    loadTemplates() {
      this.$axios.get('/message-templates').then(({ data }) => {
        this.items = data.data.map((template, index) => {
          this.templates.push(template)
          return {
            text: `${template.name} (${template.language})`,
            value: index,
          }
        })
      })
    },
    loadTags() {
      this.$axios.get('tags/')
        .then(response => {
          this.tags = response.data;
        })
        .catch(error => {
          alert(error);
          Swal.fire('Error', 'No se pueden cargar los tags', 'error');
        });
    },
    selectTemplate() {
      this.template = this.templates[this.item]
      this.body_placeholders = []
      this.header_url = null
      this.formatTemplate()
    },
    send() {
      this.sending = true
      const payload = {
        recipients: this.recipients,
        header_url: this.header_url,
        header_type: this.template.header?.format || null,
        body_placeholders: this.body_placeholders,
        template_name: this.template.name,
        template_language: this.template.language,
      }
      this.$axios
        .post('/send-message-templates', payload)
        .then(({ data }) => {
          alert('Message(s) sucessfully sent!');
          console.log(payload)
        })
        .catch((err) => {
          alert(err)
        })
        .finally(() => {
          this.sending = false
        })
    },
    formatTemplate() {
      this.template.header = null
      this.template.body = null
      this.template.footer = null
      this.template.buttons = null
      this.template.components.forEach((element) => {
        if (element.type === 'HEADER') this.template.header = element
        else if (element.type === 'BODY') {
          this.template.body = element.text
          this.template.body_placeholders = this.findPlaceholders(element.text)
        } else if (element.type === 'FOOTER')
          this.template.footer = element.text
        else if (element.type === 'BUTTONS')
          this.template.buttons = element.buttons
      })
    },
    findPlaceholders(text) {
      const regexp = /{{(.*?)}}/g
      const exec = text.matchAll(regexp)
      const matches = []

      for (const match of exec) {
        matches.push({ text: match[0], value: '' })
      }
      return matches
    },
    mostrarContactosPorEtiqueta() {
      if (this.tagSelect) {
        this.contactos = this.tagSelect.contactos;
        // console.log(this.contactos);
      } else {
        console.log("na que ver");
      }
    },
    actualizarDestinatarios() {
      // this.recipients = this.filteredContacts.map((contacto) => contacto.telefono).join('\n');

      this.contactoFiltrados = this.filteredContacts;
      this.recipients = this.contactoFiltrados.map((contacto) => contacto.telefono).join('\n');

      console.log(this.contactoFiltrados);
    },
    clickMe() {
      this.loading = true
      this.message = 'Wait for it...'
      setTimeout(() => {
        this.loading = false
        this.message = `You've clicked me!`
      }, 2000)
    },
  },
  watch: {
    filteredContacts: 'actualizarDestinatarios', // Actualizar destinatarios cuando cambie filteredContacts
  },
}


</script>

<style></style>