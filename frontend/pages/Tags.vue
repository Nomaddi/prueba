<template>
    <v-row>
        <v-col>
            <v-card elevation="7" class="tags-list">
                <v-card class="pa-2" color="#424242" dark>Lista de tags</v-card>
                <!--<h2 class="text-center">CRUD usando APIREST con Node JS</h2>-->
                <!-- Botón CREAR -->
                <v-flex class="text-center align-center">
                    <v-btn class="mx-2 mt-4" fab dark color="#00B0FF" @click="formNuevo()"><v-icon
                            dark>mdi-plus</v-icon></v-btn>
                </v-flex>

                <v-card class="mx-auto mt-5 ml-5 mr-5 mb-5" color="transparent" max-width="1280" elevation="8">

                    <!-- Tabla y formulario -->
                    <v-simple-table class="mt-5">
                        <template #default>
                            <thead>
                                <tr class="indigo darken-4">
                                    <th class="white--text">ID</th>
                                    <th class="white--text">NOMBRE</th>
                                    <th class="white--text">COLOR</th>
                                    <th class="white--text text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="tagsItem in tags" :key="tagsItem.id">
                                    <td>{{ tagsItem.id }}</td>
                                    <td>{{ tagsItem.nombre }}</td>
                                    <td><v-chip class="ma-2" :color="tagsItem.color">
                                            {{ tagsItem.color }}
                                        </v-chip>
                                    </td>
                                    <td class="text-center">
                                        <!-- Botón de Edición -->
                                        <v-btn small fab dark color="#00BCD4"
                                            @click="formEditar(tagsItem.id, tagsItem.nombre, tagsItem.color)">
                                            <v-icon>mdi-pencil</v-icon>
                                        </v-btn>
                                        <!-- Botón de Eliminación -->
                                        <v-btn small fab dark color="#E53935" @click="borrar(tagsItem.id)">
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
                        <v-card-title class="blue darken-2 white--text">tags</v-card-title>
                        <v-form>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" md="12">
                                            <v-text-field v-model="tag.id" hidden></v-text-field>
                                            <v-text-field v-model="tag.nombre" label="Nombre" solo required></v-text-field>
                                            <div>
                                                Selecione un color para la etiqueta
                                            </div>
                                            <br>
                                            <v-color-picker v-model="tag.color" @input="updateColor" />
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue-grey" dark @click="dialog = false">Cancelar</v-btn>
                                <v-btn color="blue darken-2" dark type="submit" @click="guardar()">Guardar</v-btn>
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
// import VueTheMask from 'vue-the-mask';
export default {

    data() {
        return {
            tags: [],
            dialog: false,
            operacion: '',
            tag: {
                id: null,
                nombre: '',
                color: '#000000',
            },

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
        },
        crear() {
            const parametros = { nombre: this.tag.nombre, color: this.tag.color };
            this.$axios.post('tags/', parametros)
                .then(response => {
                    this.mostrar();
                });
            this.tag.nombre = "";
            this.tag.color = "";
        },
        editar() {
            const parametros = { nombre: this.tag.nombre, color: this.tag.color, id: this.tag.id };

            this.$axios.put('tags/' + this.tag.id, parametros)
                .then(response => {
                    this.mostrar();
                })
                .catch((err) => {
                    alert(err)
                })
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
                            this.mostrar();
                        });
                    Swal.fire('¡Eliminado!', '', 'success');
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
            this.tag.color = '';

        },
        formEditar(id, nombre, color) {
            this.tag.id = id;
            this.tag.nombre = nombre;
            this.tag.color = color;
            this.dialog = true;
            this.operacion = 'editar';
        },
    }
}
</script>

<style>
.tags-list {
    height: 82vh;
}
</style>