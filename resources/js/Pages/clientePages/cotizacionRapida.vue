<template>
    <v-container class="mt-5 pt-0">
        <v-row class="mt-0 pt-0">
            <v-col class="text-center"><h2>Cotizaciones</h2></v-col>
        </v-row>
       
        <v-row>
            <v-col>
                <v-card>
                    <v-card-title>Sube tu archivo aquí</v-card-title>
                    <v-card-text>
                        <el-upload
                            class="upload-demo"
                            drag
                            :http-request="cotizar"
                            ref="loadform"
                            accept=".stl"
                            :headers="{'X-CSRF-TOKEN': token}"
                            :auto-upload="true"
                        >
                            <el-icon class="el-icon--upload">
                                <upload-filled />
                            </el-icon>

                            <div class="el-upload__text">
                                Arrastra tu archivo o <em>haz click para subir</em>
                            </div>
                            <template #tip>
                                <div class="el-upload__tip">Archivos STL de menos de 30 MB</div>
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
                <v-row class="text-center mt-0 pt-0 mb-2">
                    <v-col cols="5"><h6>Archivos</h6></v-col>
                    <v-col cols="3" class="text-center ml-6"><h6>Piezas</h6></v-col>
                    <v-col cols="2" class="text-center ml-6"><h6>Precio</h6></v-col>
                    <v-col cols="1" class="text-center"></v-col>
                </v-row>
                <v-row v-for="file in files" :key="file.nombre">
                    
                        <v-col cols="12">
                            <v-card>
                                <v-card-text>
                                    <v-row align="center">
                                        <v-col cols="4">
                                            {{ file.nombre }}
                                        </v-col>
                                        <v-col cols="3">
                                            <v-row>
                                                <v-col cols="4">
                                                    <v-icon icon="mdi-minus"></v-icon>
                                                </v-col>
                                                <v-col cols="4">
                                                    {{ file.piezas }}

                                                </v-col>
                                                <v-col cols="4">
                                                    <v-icon icon="mdi-plus"></v-icon>

                                                </v-col>
                                            </v-row>
                                        </v-col>
                                        <v-col cols="3">
                                            {{ file.precio * file.piezas }}
                                        </v-col>
                                        <v-col cols="2">
                                            <v-icon icon="mdi-delete" @click="removeFile(file)"></v-icon>
                                        </v-col>

                                    </v-row>
                                </v-card-text>
                            </v-card>

                        </v-col>
                </v-row>
            </v-col>
        </v-row>

        <v-dialog v-model="dialog" width="auto">
            <v-card max-width="400" prepend-icon="mdi-alert" text="Lamentamos estos problemas, el archivo que has subido es incompatible" title="Error con el archivo">
                <template v-slot:actions>
                    <v-btn class="ms-auto" text="Ok" @click="dialog = false"></v-btn>
                </template>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script setup>
import { useRouter } from "vue-router";
import formatCurrency from "../../composables/formatNumberToCurrency";
import { useCartStore } from "../../stores/carrito";
import { ref } from "vue";
import axios from "@/axios.js";
import { UploadFilled } from "@element-plus/icons-vue";

const cartStore = useCartStore();
const router = useRouter();
const loadform = ref();
const resultado = ref(true);
const loading = ref(false);
const errorMessage = ref("");
const dialog = ref(false);
const actualizarLista = ref(false);
const totales = ref({
    cantidad: 0,
    total: 0,
    files: [],
});
const files = ref([]);

const agregarCarrito = async () => {
    axios
        .post("/carrito/agregar", {
            producto_id: 1,
            cantidad: 1,
            files: totales.value.files,
        })
        .then(() => {
            cartStore.fetchCart();
        });
};

const cotizar = async (file) => {
    loading.value = true;
    errorMessage.value = "";

    const formData = new FormData();
    formData.append("file", file.file);
    try {
        const { data } = await axios.post("/calculate", formData);
        loading.value = false;
        actualizarLista.value = true;
        files.value.push(data.data); 
    } catch (error) {
        loading.value = false;
        if (error.response && error.response.data && error.response.data.message) {
            errorMessage.value = error.response.data.message;
        }
        dialog.value = true;
    }
};

const removeFile = (file) => {
    const index = files.value.indexOf(file);
    if (index > -1) {
        files.value.splice(index, 1);
    }
};
</script>