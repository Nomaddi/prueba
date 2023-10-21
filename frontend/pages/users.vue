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
                  <th class="white--text">ID</th>
                  <th class="white--text">NOMBRE</th>
                  <th class="white--text">TELEFONO</th>
                  <th class="white--text">CIUDAD</th>
                  <th class="white--text text-center">ACCIONES</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="contactoItem in contactos" :key="contactoItem.id">
                  <td>{{ contactoItem.id }}</td>
                  <td>{{ contactoItem.nombre }}</td>
                  <td>{{ contactoItem.telefono }}</td>
                  <td>
                    <v-chip class="ma-2" v-for="tag in contactoItem.tags" :key="tag.id" :color="tag.color">
                      {{ tag.nombre }}
                    </v-chip>
                  </td>

                  <!-- <td><v-chip class="ma-2" :color="tagsItem.color">{{ tagsItem.color }}</v-chip></td> -->

                  <td class="text-center">
                    <v-btn small fab dark color="#00BCD4"
                      @click="formEditar(contactoItem.id, contactoItem.nombre, contactoItem.telefono, contactoItem.tags)">
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
            <v-form @submit.prevent="guardar()">
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" md="12">
                      <v-text-field v-model="contacto.id" hidden></v-text-field>
                      <v-text-field v-model="contacto.nombre" label="Nombre" placeholder="Ingrese nombre de contacto" solo
                        required></v-text-field>
                    </v-col>
                    <v-col cols="12" md="12">
                      <vue-tel-input-vuetify v-model="contacto.telefono" :placeholder="'Ingrese su número de teléfono'"
                        label="Telefono" solo required></vue-tel-input-vuetify>
                    </v-col>
                    <v-col cols="12" md="12">
                      <label for="" style="font-size: 20px;">Tags</label>
                      <br>
                      <br>
                      <v-select v-model="contacto.ciudad" :items="tags" label="Seleccione un elemento" item-text="nombre"
                        multiple>
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
                  </v-row>
                </v-container>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-grey" dark @click="dialog = false">Cancelar</v-btn>
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
      contactos: [],
      tags: [],
      dialog: false,
      operacion: '',
      contacto: {
        id: null,
        nombre: '',
        telefono: '',
        ciudad: []
      },
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
      const numeroCompleto = '+57' + this.contacto.telefono;

      // Mapear los nombres de las ciudades a sus IDs
      const ciudadIds = this.contacto.ciudad.map(nombre => {
        const tag = this.tags.find(tag => tag.nombre === nombre);
        return tag ? tag.id : null;
      });

      const parametros = {
        nombre: this.contacto.nombre,
        telefono: numeroCompleto,
        ciudad: ciudadIds // Enviar IDs en lugar de nombres
      };

      this.$axios.post('contactos/', parametros)
        .then(response => {
          this.mostrar();
          this.dialog = false;
        })
        .catch(error => {
          alert(error);
          Swal.fire('Error', 'No se pudo crear el contacto', 'error');
        });

      this.contacto.nombre = "";
      this.contacto.telefono = "";
      this.contacto.ciudad = [];
    },
    editar() {
      // Mapear los nombres de las ciudades a sus IDs
      const ciudadIds = this.contacto.ciudad.map(nombre => {
        const tag = this.tags.find(tag => tag.nombre === nombre);
        return tag ? tag.id : null;
      });

      const parametros = { nombre: this.contacto.nombre, telefono: this.contacto.telefono, ciudad: ciudadIds, id: this.contacto.id };
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
      if (this.operacion === 'crear') {
        this.crear();
      }
      if (this.operacion === 'editar') {
        this.editar();
      }
      this.dialog = false;
    },
    formNuevo() {
      this.dialog = true;
      this.operacion = 'crear';
      this.contacto.nombre = '';
      this.contacto.telefono = '';
      this.contacto.ciudad = [];
    },
    formEditar(id, nombre, telefono, tags) {
      this.contacto.id = id;
      this.contacto.nombre = nombre;
      this.contacto.telefono = telefono.toString(); // Convierte a cadena
      this.contacto.ciudad = tags.map(tag => tag.nombre); // Mapea los nombres de las ciudades
      this.dialog = true;
      this.operacion = 'editar';
    }
  }
}
</script>

<style>
/* .user-list {
  height: 82vh;
} */
</style>
