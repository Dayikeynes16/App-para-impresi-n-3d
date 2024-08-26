<template>
    <v-container class="mt-5 pt-0">
        <v-row class="mt-0 pt-0">
            <v-col class="text-center"><h2>Cotizaciones</h2></v-col>
        </v-row>
       
        <v-row>
            <v-col align="center">
                <v-card class="" max-width="600px">
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
                    <v-card-actions v-if="resultado">
                        <v-row>
                            <v-col cols="12">
                                Costo estimado: {{ formatCurrency(files.total) }}
                            </v-col>
                            <v-col cols="12">
                                Tiempo de Impresión: {{ Math.round(files.minutos) }} min
                            </v-col>
                            <v-col cols="12">
                                <v-btn block variant="outlined" append-icon="mdi-cart" @click="router.push({name: 'cotizar'})">Comprar</v-btn>
                            </v-col>
                        </v-row>
                        
                    </v-card-actions>
                </v-card>
                <v-overlay :model-value="loading" class="align-center justify-center">
                    <v-progress-circular color="primary" size="64" indeterminate></v-progress-circular>
                </v-overlay>
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
const resultado = ref(false);
const loading = ref(false);
const errorMessage = ref("");
const dialog = ref(false);
const actualizarLista = ref(false);
const totales = ref({
    cantidad: 0,
    total: 0,
    files: [],
});
const files = ref({});



const cotizar = async (file) => {
    loading.value = true;
    errorMessage.value = "";

    const formData = new FormData();
    formData.append("file", file.file);
    try {
        const { data } = await axios.post("/quick-quote", formData);
        loading.value = false;
        resultado.value = true;
        actualizarLista.value = true;
        files.value = (data.data); 
    } catch (error) {
        loading.value = false;
        if (error.response && error.response.data && error.response.data.message) {
            errorMessage.value = error.response.data.message;
        }
        dialog.value = true;
    }
};


</script>