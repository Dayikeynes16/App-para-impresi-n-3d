<template>
    <v-container class="mt-0 pt-0">
        <v-row class="mt-0 pt-0"><v-col><h2>Cotizaciones</h2></v-col></v-row>
        <v-row >
            <v-col class="mt-0 pt-0">
                <v-card elevation="1" class="rounded-lg mt-0 pt-0">
                <v-card-text >
                    <v-row class="text-center" align="center">
                        <v-col cols="5"><h5>Seleccionados: {{ totales.cantidad }}</h5></v-col>
                        <v-col cols="5"><h5>Total {{ formatCurrency(parseFloat(totales.total)) }}</h5></v-col>
                        <v-col cols="2">
                            <v-btn
                            variant="tonal"
                            @click="agregarCarrito"
                            icon="mdi-cart">
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
            </v-col>
        </v-row>
        <v-row>
            <v-col >
                <v-card >
                    <v-card-title> Sube tu archivo aquí </v-card-title>
                    <v-card-subtitle>Verifica que tu archivo tenga la escala correcta.</v-card-subtitle>
                    <v-card-text>
                        <el-upload class="upload-demo" drag :http-request="cotizar" ref="loadform"
                            accept=".stl" :headers="{
                                'X-CSRF-TOKEN': token,
                            }" :auto-upload="true">

                            <el-icon class="el-icon--upload">
                                <upload-filled />
                            </el-icon>

                            <div class="el-upload__text">
                                Arrastra tu archivo o
                                <em>haz click para subir</em>
                            </div>
                            <template #tip>
                                <div class="el-upload__tip">
                                    Archivos STL de menos de 30 MB
                                </div>
                            </template>
                        </el-upload>
                    </v-card-text>
                </v-card>
                <v-overlay :model-value="loading" class="align-center justify-center">
                    <v-progress-circular color="primary" size="64" indeterminate></v-progress-circular>
                </v-overlay>
            </v-col>
            <v-col v-if="resultado" cols="8">
                <v-card-subtitle>Aquí podrá ver todas las cotizaciones que ha realizado, puedes seleccionar y agregarlas al carrito.</v-card-subtitle>
                <filesCard @añadido="limpiarArchivos"  @datosCarrito="totales = $event" :update="actualizarLista" @actualizado="actualizarLista = $event"></filesCard>
            </v-col>
        </v-row>

        <v-dialog v-model="dialog" width="auto">
            <v-card max-width="400" prepend-icon="mdi-alert"
                text="Lamentamos estos problemas, el archivo que has subido es incompatible"
                title="Error con el archivo">
                <template v-slot:actions>
                    <v-btn class="ms-auto" text="Ok" @click="dialog = false"></v-btn>
                </template>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script setup>
import formatCurrency from "../../composables/formatNumberToCurrency";
import { useCartStore } from '../../stores/carrito';
import {  ref } from "vue";
import axios from "@/axios.js";
import { UploadFilled } from "@element-plus/icons-vue";
import filesCard from "../../Components/UploadedUserFiles.vue";
import { ElMessage } from 'element-plus'

const cartStore = useCartStore();
const loadform = ref();
const resultado = ref(true);
const loading = ref(false);
const errorMessage = ref("");
const dialog = ref(false);
const actualizarLista = ref(false);
const totales = ref({
    cantidad: 0,
    total: 0,
    files: []
});

const agregarCarrito = async () => {
    await axios.post("/carrito/agregar", {
        producto_id: 1,
        cantidad: 1,
        files: totales.value.files,
    })
    .then(() => {
         cartStore.fetchCart()
         ElMessage({
            message: 'Producto añadido',
            type: 'success',
        })

    })
    
};

const cotizar = async (file) => {
    loading.value = true;
    errorMessage.value = "";

    const formData = new FormData();
    formData.append('file', file.file);
   
    axios.post("/cotizacion/cotizar",formData)
        .then(() => {
            loading.value = false;
            actualizarLista.value = true
        })

     .catch((error) => {
        loading.value = false;
        
        errorMessage.value = error.response.data.message;

        dialog.value = true;
     })
};


</script>