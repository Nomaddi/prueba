<template>
  <v-row>
    <v-col>
      <v-card elevation="7" class="tags-list">
        <v-card class="pa-2 d-flex mb-6 ac" color="#424242" dark>
          Lista de tags
          <v-btn dark color="#00B0FF" @click="formNuevo()" class="ml-auto"><v-icon dark>mdi-plus</v-icon> Agregar
            etiqueta</v-btn>
        </v-card>
        <!-- <v-card class="mx-auto mt-5 ml-5 mr-5 mb-5" color="transparent" max-width="1280" elevation="8">
          <v-simple-table class="mt-5">
            <template #default>
              <thead>
                <tr class="indigo darken-4">
                  <th class="white--text">ID</th>
                  <th class="white--text">NOMBRE</th>
                  <th class="white--text">COLOR</th>
                  <th class="white--text">DESCRIPCION</th>
                  <th class="white--text text-center">ACCIONES</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="tagsItem in tags" :key="tagsItem.id">
                  <td>{{ tagsItem.id }}</td>
                  <td>{{ tagsItem.nombre }}</td>
                  <td>{{ tagsItem.descripcion }}</td>
                  <td><v-chip class="ma-2" :color="tagsItem.color">{{ tagsItem.color }}</v-chip></td>
                  <td class="text-center">
                    <v-btn small fab dark color="#00BCD4"
                      @click="formEditar(tagsItem.id, tagsItem.nombre, tagsItem.descripcion, tagsItem.color)">
                      <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn small fab dark color="#E53935" @click="borrar(tagsItem.id)">
                      <v-icon>mdi-delete</v-icon>
                    </v-btn>
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card> -->
        <v-card>
          <v-card-title>
            Tags
            <v-spacer></v-spacer>
            <v-text-field v-model="search" append-icon="mdi-magnify" label="Search" single-line
              hide-details></v-text-field>
          </v-card-title>
          <v-data-table :headers="headers" :items="tags" :search="search" class="elevation-1">
            <template v-slot:[`item.colores`]="{ item }">
              <v-chip class="ma-2" :color="item.color">{{ item.color }}</v-chip>
            </template>
            <template v-slot:[`item.actions`]="{ item }">
              <v-btn small fab dark color="#00BCD4"
                @click="formEditar(item.id, item.nombre, item.descripcion, item.color)">
                <v-icon>mdi-pencil</v-icon>
              </v-btn>
              <v-btn small fab dark color="#E53935" @click="borrar(item.id)">
                <v-icon>mdi-delete</v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </v-card>
        <v-dialog v-model="dialog" max-width="700">
          <v-card>
            <v-card-title class="blue darken-2 white--text">Tags</v-card-title>
            <v-form @submit.prevent="guardar()">
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" md="12">
                      <v-text-field v-model="tag.id" hidden></v-text-field>
                      <v-text-field v-model="tag.nombre" label="Nombre" solo required></v-text-field>
                      <v-text-field v-model="tag.descripcion" label="Descripcion" solo required></v-text-field>
                      <div>Seleccione un color para la etiqueta</div>
                      <br>
                      <v-color-picker v-model="tag.color" />
                      <div>Color seleccionado: {{ tag.color }}</div>
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
      search: '',
      tags: [],
      dialog: false,
      operacion: '',
      tag: {
        id: null,
        nombre: '',
        descripcion: '',
        color: '#000000',
      },
      headers: [
        {
          text: 'Nombre',
          align: 'start',
          value: 'nombre',
        },
        { text: 'Descripcion', value: 'descripcion' },
        { text: 'Color', value: 'colores' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],

    }
  },
  created() {
    this.mostrar()
  },
  methods: {
    mostrar() {
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
      const parametros = { nombre: this.tag.nombre, descripcion: this.tag.descripcion, color: this.tag.color };
      this.$axios.post('tags/', parametros)
        .then(response => {
          this.mostrar();
          this.dialog = false; // Cierra el diálogo después de crear con éxito.
        })
        .catch(error => {
          alert(error);
          Swal.fire('Error', 'No se pudo crear el tag', 'error');
        });
      this.tag.nombre = "";
      this.tag.color = "";
    },
    editar() {
      const parametros = { nombre: this.tag.nombre, descripcion: this.tag.descripcion, color: this.tag.color, id: this.tag.id };
      this.$axios.put('tags/' + this.tag.id, parametros)
        .then(response => {
          this.mostrar();
          this.dialog = false; // Cierra el diálogo después de editar con éxito.
        })
        .catch(error => {
          alert(error);
          Swal.fire('Error', 'No se pudo editar el tag', 'error');
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
                Swal.fire('Advertencia', response.data.message, 'warning');

                // También puedes mostrar información sobre los contactos relacionados
                if (response.data.related_contacts) {
                  console.log('Contactos relacionados:', response.data.related_contacts);
                }
              }
            })
            .catch(() => {
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
      this.tag.nombre = '';
      this.tag.descripcion = '';
      this.tag.color = '';

    },
    formEditar(id, nombre, descripcion, color) {
      this.tag.id = id;
      this.tag.nombre = nombre;
      this.tag.descripcion = descripcion;
      this.tag.color = color;
      this.dialog = true;
      this.operacion = 'editar';
    },
  }
}
</script>

<style>
.ac {
  align-items: center;
}
</style>
