<template>
    <v-container>
        <v-row>
            <v-col cols="4">
                <v-card color="grey-lighten-4">
                    <v-card-title> Sube tu archivo aquí </v-card-title>
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
            <v-col cols="8">
                <filesCard @añadido="limpiarArchivos" v-if="resultado"></filesCard>
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
import { useRouter } from 'vue-router';
const router = useRouter();

import { onMounted, ref } from "vue";
import axios from "axios";
import { UploadFilled } from "@element-plus/icons-vue";
const token = document
    .querySelector("meta[name='csrf-token']")
    .getAttribute("value");
const loadform = ref();
const resultado = ref(false);
const costo = ref("");
const tiempo_impresion = ref(0);
const loading = ref(false);
const errorMessage = ref("");
const dialog = ref(false);

import filesCard from "../Components/files-card.vue";

const cotizar = async (file) => {
    loading.value = true;
    resultado.value = false;

    errorMessage.value = "";

    const formData = new FormData();
    formData.append('file', file.file);
    try {
        const {data} = await axios.post("/calculate",formData,
            {
                headers: {
                    "X-CSRF-TOKEN": token,
                    "Content-Type": "multipart/form-data",
                },
            }
        );
        loading.value = false;
        resultado.value = true;
    } catch (error) {
        loading.value = false;
        if (
            error.response &&
            error.response.data &&
            error.response.data.message
        ) {
            errorMessage.value = error.response.data.message;
        }
        resultado.value = true;
        dialog.value = true;
    }
};

const limpiarArchivos = () => {
    resultado.value = false;
};

const traerarchivos = async () => {
    const { data } = await axios.get('/traerarchivos');
    if (data.data.files.length) {
        resultado.value = true;

    } else {
        resultado.value = false;
    }
};

onMounted(() => {
    traerarchivos();
});
</script>