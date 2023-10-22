<template>
  <v-row>
    <v-col>
      <v-card elevation="7" class="user-list">
        <v-card class="pa-2" color="#424242" dark>Lista de contactos</v-card>
        <v-flex class="text-center align-center">
          <v-btn class="mx-2 mt-4" fab dark color="#00B0FF" @click="formNuevo()"><v-icon dark>mdi-plus</v-icon></v-btn>
        </v-flex>

        <v-card class="mx-auto mt-5 ml-5 mr-5 mb-5" color="transparent" max-width="1280" elevation="8">
          <v-simple-table class="mt-5">
            <template #default>
              <thead>
                <tr class="indigo darken-4">
                  <th class="white--text">Nombre</th>
                  <th class="white--text">Apellido</th>
                  <th class="white--text">Correo</th>
                  <th class="white--text">Celular</th>
                  <th class="white--text">Etiquetas</th>
                  <th class="white--text">Notas</th>
                  <th class="white--text text-center">Acción</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="contactoItem in contactos" :key="contactoItem.id">
                  <td>{{ contactoItem.nombre }}</td>
                  <td>{{ contactoItem.apellido }}</td>
                  <td>{{ contactoItem.correo }}</td>
                  <td>{{ contactoItem.telefono }}</td>
                  <td>
                    <v-chip class="ma-2" size="x-small" v-for="tag in contactoItem.tags" :key="tag.id" :color="tag.color">
                      {{ tag.nombre }}
                    </v-chip>
                  </td>
                  <td>{{ contactoItem.notas }}</td>


                  <!-- <td><v-chip class="ma-2" :color="tagsItem.color">{{ tagsItem.color }}</v-chip></td> -->

                  <td class="text-center">
                    <v-btn small fab dark color="#00BCD4"
                      @click="formEditar(contactoItem.id, contactoItem.nombre, contactoItem.apellido, contactoItem.correo, contactoItem.telefono, contactoItem.tags, contactoItem.notas,)">
                      <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn small fab dark color="#E53935" @click="borrar(contactoItem.id)">
                      <v-icon>mdi-delete</v-icon>
                    </v-btn>
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card>

        <!-- Componente de Diálogo para CREAR y EDITAR -->
        <v-dialog v-model="dialog" max-width="700">
          <v-card>
            <v-card-title class="blue darken-2 white--text">Contactos</v-card-title>
            <v-form @submit.prevent="guardar()" ref="formValidate" lazy-validation>
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" md="12">
                      <v-text-field v-model="contacto.id" hidden></v-text-field>
                      <v-text-field v-model="contacto.nombre" :rules="nombreRules" label="Nombre"
                        placeholder="Ingrese nombre de contacto" solo required></v-text-field>
                    </v-col>
                    <v-col cols="12" md="12">
                      <v-text-field v-model="contacto.apellido" label="Apellido"
                        placeholder="Ingrese apellido de contacto" solo></v-text-field>
                    </v-col>
                    <v-col cols="12" md="12">
                      <v-text-field v-model="contacto.correo" :rules="correoRules" label="Correo"
                        placeholder="Ingrese correo de contacto" solo required></v-text-field>
                    </v-col>
                    <v-col cols="12" md="12">
                      <vue-tel-input-vuetify outlined v-model="contacto.telefono" :rules="telefonoRules"
                        v-bind="bindProps" @onInput="onInput" :placeholder="'Ingrese su número de teléfono'"
                        label="Telefono" solo required></vue-tel-input-vuetify>
                    </v-col>
                    <v-col cols="12" md="12">
                      <v-select v-model="contacto.ciudad" :rules="tagRules" :items="tags" label="Agrega una etiqueta"
                        item-text="nombre" multiple required>
                        <template #selection="{ item, index }">
                          <v-chip v-if="index < 3">
                            <span>{{ item.nombre }}</span>
                          </v-chip>
                          <span v-if="index === 3" class="text-grey text-caption align-self-center">
                            (+{{ contacto.ciudad.length - 3 }} others)
                          </span>
                        </template>
                      </v-select>
                    </v-col>
                    <v-col cols="12" md="12">
                      <v-text-field v-model="contacto.notas" label="Notas" placeholder="Ingrese alguna nota"
                        solo></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <!-- <v-btn color="blue-grey" dark @click="dialog = false">Cancelar</v-btn> -->
                <v-btn color="blue-grey" dark @click="cancelar()">Cancelar</v-btn>
                <v-btn color="blue darken-2" dark type="submit">Guardar</v-btn>
              </v-card-actions>
            </v-form>
          </v-card>
        </v-dialog>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      bindProps: {
        enabledCountryCode: true,
        autocomplete: "true",
      },
      phone: {
        isValid: false,
        country: undefined,
      },
      contactos: [],
      tags: [],
      dialog: false,
      operacion: '',
      contacto: {
        id: null,
        nombre: '',
        apellido: '',
        correo: '',
        telefono: '',
        ciudad: [],
        notas: '',
      },
      nombreRules: [
        v => !!v || 'Campo es obligatorio',
        v => (v && v.length <= 10) || 'Name must be less than 10 characters',
      ],
      correoRules: [
        v => !!v || 'Correo es obligatorio',
        v => (/.+@.+/.test(v)) || 'Correo invalido',
      ],
      telefonoRules: [
        v => !!v || 'Celular es obligatorio',
        v => (/[0-9-]+/.test(v)) || 'Un celular correcto debe contener más de 10 numeros',
        v => (v && this.phone.isValid) || 'No es valido',
      ],
      tagRules: [
        v => (v && v.length > 0) || 'Debe selecionar una etiqueta',
      ]
    }
  },
  created() {
    this.mostrar();
    this.mostrar2();
  },
  methods: {
    mostrar() {
      this.$axios.get('contactos/')
        .then(response => {
          this.contactos = response.data;
        })
        .catch(error => {
          alert(error);
          Swal.fire('Error', 'No se pueden cargar los contactos', 'error');
        });
    },
    mostrar2() {
      this.$axios.get('tags/')
        .then(response => {
          this.tags = response.data;
        })
        .catch(error => {
          alert(error);
          Swal.fire('Error', 'No se pueden cargar los tags', 'error');
        });
    },
    crear() {
      // Mapear los nombres de las ciudades a sus IDs
      const ciudadIds = this.contacto.ciudad.map(nombre => {
        const tag = this.tags.find(tag => tag.nombre === nombre);
        return tag ? tag.id : null;
      });

      const parametros = {
        nombre: this.contacto.nombre,
        apellido: this.contacto.apellido,
        correo: this.contacto.correo,
        telefono: this.contacto.telefono,
        ciudad: ciudadIds, // Enviar IDs en lugar de nombres
        notas: this.contacto.notas,
      };

      this.$axios.post('contactos/', parametros)
        .then(response => {
          this.mostrar();
          this.dialog = false;
        })
        .catch(error => {
          alert(error);
          Swal.fire('Error', 'No se pudo crear el contacto', 'error');
          this.$refs.formValidate.resetValidation();
        });

      this.contacto.nombre = "";
      this.contacto.apellido = "";
      this.contacto.correo = "";
      this.contacto.telefono = "";
      this.contacto.ciudad = [];
    },
    editar() {
      // Mapear los nombres de las ciudades a sus IDs
      const ciudadIds = this.contacto.ciudad.map(nombre => {
        const tag = this.tags.find(tag => tag.nombre === nombre);
        return tag ? tag.id : null;
      });

      const parametros = { nombre: this.contacto.nombre, apellido: this.contacto.apellido, correo: this.contacto.correo, telefono: this.contacto.telefono, ciudad: ciudadIds, notas: this.contacto.notas, id: this.contacto.id };
      this.$axios.put('contactos/' + this.contacto.id, parametros)
        .then(response => {
          this.mostrar();
          this.dialog = false;
        })
        .catch(error => {
          alert(error);
          Swal.fire('Error', 'No se pudo editar el contacto', 'error');
        });
    },
    borrar(id) {
      Swal.fire({
        title: '¿Confirma eliminar el registro?',
        confirmButtonText: `Confirmar`,
        showCancelButton: true,
      }).then((result) => {
        if (result.isConfirmed) {
          this.$axios.delete('tags/' + id)
            .then(response => {
              if (response.data.success) {
                Swal.fire('¡Eliminado!', response.data.message, 'success');
                this.mostrar();
              } else {
                // Si no se pudo eliminar, muestra la advertencia
                Swal.fire('Error', response.data.message, 'error');

                // También puedes mostrar información sobre los contactos relacionados
                if (response.data.related_contacts) {
                  console.log('Contactos relacionados:', response.data.related_contacts);
                }
              }
            })
            .catch(error => {
              alert(error);
              Swal.fire('Error', 'No se pudo eliminar la etiqueta', 'error');
            });
        } else if (result.isDenied) {
          alert(result.isDenied);
        }
      });
    },
    guardar() {
      if (this.$refs.formValidate.validate()) {
        if (this.operacion === 'crear') {
          this.crear();
        }
        if (this.operacion === 'editar') {
          this.editar();
        }
        this.dialog = false;
      }

    },
    formNuevo() {
      this.dialog = true;
      this.operacion = 'crear';
      this.contacto.nombre = '';
      this.contacto.apellido = '';
      this.contacto.correo = '';
      this.contacto.telefono = '';
      this.contacto.ciudad = [];
      this.contacto.notas = '';
    },
    formEditar(id, nombre, apellido, correo, telefono, tags, notas) {
      this.contacto.id = id;
      this.contacto.nombre = nombre;
      this.contacto.apellido = apellido;
      this.contacto.correo = correo;
      this.contacto.telefono = telefono.toString(); // Convierte a cadena
      this.contacto.ciudad = tags.map(tag => tag.nombre); // Mapea los nombres de las ciudades
      this.contacto.notas = notas;
      this.dialog = true;
      this.operacion = 'editar';
    },
    onInput({ number, isValid, country }) {
      this.phone.isValid = isValid;
      this.phone.country = country;
      this.contacto.telefono = number.international;
    },
    cancelar() {
      this.$refs.formValidate.resetValidation()
      this.dialog = false;
    }
  }
}
</script>

<style>
</style>
