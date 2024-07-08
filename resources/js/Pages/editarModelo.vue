<template>
    <v-container>
            <v-col cols="12">

            <v-stepper :items="['Fotos', 'Archivos', 'Informació']">
                <template v-slot:item.1>
                    <v-card subtitle="Añade o elimina fotos" flat>
                        <v-row>
                            <v-col cols="6">
                                <v-card color="grey-lighten-2">
                                    <v-card-title>Agregar imágenes</v-card-title>
                                    <v-card-text>
                                        <el-upload
                                            class="upload-demo"
                                            drag
                                            :http-request="guardarImagen"
                                            ref="loadform"
                                            accept=".jpeg,.jpg,.png,.gif"
                                            :headers="{ 'X-CSRF-TOKEN': token }"
                                            :auto-upload="true"
                                        >
                                            <el-icon class="el-icon--upload">
                                                <upload-filled />
                                            </el-icon>
                                            <div class="el-upload__text">
                                                Arrastra tu archivo o
                                                <em>haz click para subir</em>
                                            </div>
                                            <template #tip>
                                                <div class="el-upload__tip">
                                                    Archivos de imagen de menos de 30 MB
                                                </div>
                                            </template>
                                        </el-upload>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                            <v-col cols="6">
                                <v-row class="overflow-container">
                                    <v-col cols="6" v-for="imagen in Imagenes" :key="imagen.id">
                                        <v-card>
                                            <v-img :src="imagen.url"></v-img>
                                            <v-card-actions>
                                                <el-button
                                                    type="danger"
                                                    icon="mdi-Delete"
                                                    circle
                                                    @click="eliminarImagen(imagen.id)"
                                                ></el-button>
                                            </v-card-actions>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </v-col>
                        </v-row>
                    </v-card>
                </template>
                  <template v-slot:item.2>
                    <v-card subtitle="Modifica los archivos" flat>
                        <v-row>
                            <v-col cols="6">
                                <v-card color="grey-lighten-2">
                                    <v-card-title>Agregar más archivos</v-card-title>
                                    <v-card-text>
                                        <el-upload
                                            class="upload-demo"
                                            drag
                                            :http-request="guardarSTL"
                                            ref="loadform"
                                            accept=".stl"
                                            :headers="{ 'X-CSRF-TOKEN': token }"
                                            :auto-upload="true"
                                        >
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
                            </v-col>
                            <v-col cols="6">
                                <v-card>
                                    <v-card-title>Archivos</v-card-title>
                                    <v-list v-for="archivo in archivos">
                                            <v-list-item class="text-right">
                                                <v-row>
        
                                                <v-col cols="6">
                                                    {{ archivo.nombre }}
                                                </v-col>
        
                                                <v-col cls="6">
                                                    <v-icon
                                                        icon="mdi-Delete"
                                                        @click="
                                                            eliminarArchivo(archivo.id)
                                                        "
                                                    >
                                                    </v-icon>
                                                </v-col>
                                            </v-row>

        
                                            </v-list-item>
                                            <v-divider></v-divider>


                                    </v-list>
                                </v-card>
                            </v-col>



                        </v-row>


                    </v-card>
                  </template>
                  <template v-slot:item.3>
                    <v-card subtitle="Informacion del producto" flat>
                        <v-card color="grey-lighten-2">
                            <v-card-title>Actualizar Producto</v-card-title>
                            <v-card-subtitle
                                >Modifica los campos necesarios</v-card-subtitle
                            >
                            <v-card-text>
                                <v-text-field
                                    v-model="product.name"
                                    label="Nombre"
                                    required
                                    :error-messages="errorMessages.name"
                                ></v-text-field>
                                <v-text-field
                                    v-model="product.description"
                                    label="Descripción"
                                    required
                                    :error-messages="errorMessages.description"
                                ></v-text-field>
                                <v-text-field
                                    v-model="product.price"
                                    label="Precio"
                                    required
                                    type="number"
                                    :error-messages="errorMessages.price"
                                ></v-text-field>
                                <v-btn @click="update" color="primary"
                                    >Guardar</v-btn
                                >
                            </v-card-text>
                        </v-card>


                    </v-card>
                  </template>

            </v-stepper>
            
            
        </v-col>
    </v-container>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import { Delete, UploadFilled } from "@element-plus/icons-vue";

import ListaDeArchivos from "../Components/ListaDeArchivos.vue";
import { VList } from "vuetify/components";

const product = ref({});
const errorMessages = ref({});
const route = useRoute();
const Imagenes = ref([]);
const token = document
    .querySelector("meta[name='csrf-token']")
    .getAttribute("value");
const router = useRouter();
const id = ref();
const archivos = ref([]);

const update = async () => {
    try {
        await axios.post(`/productos/${route.params.id}`, product.value);
        router.push({ name: "editarcatalogo" });
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errorMessages.value = error.response.data.errors;
        }
    }
};

const traerImagenes = async (id) => {
    const { data } = await axios.get("/getImagenes", { params: { id } });
    Imagenes.value = data;
};

const eliminarImagen = async (id) => {
    try {
        await axios.post(
            "/eliminarImagen",
            { id },
            { headers: { "X-CSRF-TOKEN": token } }
        );
        Imagenes.value = Imagenes.value.filter((imagen) => imagen.id !== id);
    } catch (error) {
        alert("Error al eliminar el archivo");
    }
};

const guardarImagen = async (file) => {
    const formData = new FormData();
    formData.append("image", file.file);
    formData.append("producto_id", id.value);

    try {
        const { data } = await axios.post("/guardarImagen", formData, {
            headers: {
                "X-CSRF-TOKEN": token,
                "Content-Type": "multipart/form-data",
            },
        });
        Imagenes.value.push(data.data);
    } catch (error) {
        console.error(error);
    }
};

const guardarSTL = async (file) => {
    const formData = new FormData();
    formData.append("file", file.file);
    formData.append("producto_id", id.value);

    try {
        const { data } = await axios.post("/guardarSTLproducto", formData, {
            headers: {
                "X-CSRF-TOKEN": token,
                "Content-Type": "multipart/form-data",
            },
        });
        if (data.data === 200) {
            console.log("esperandojshshs");
            await traerArchivos(id.value);
        }
    } catch (error) {
        console.error(error);
    }
};

const eliminarArchivo = async (id) => {
    try {
        await axios.post(
            "/eliminarArchivo",
            { id },
            { headers: { "X-CSRF-TOKEN": token } }
        );
        archivos.value = archivos.value.filter((file) => file.id !== id);
    } catch (error) {
        alert("Error al eliminar el archivo");
    }
};

const traerArchivos = async (id) => {
    try {
        const { data } = await axios.get("/traerArchivos", { params: { id } });
        archivos.value = data;
    } catch (error) {
        console.error("Error fetching data:", error);
    }
};

onMounted(async () => {
    const { data } = await axios.get(`/productos/${route.params.id}`);
    product.value = data.data;
    id.value = product.value.id;
    traerImagenes(id.value);
    traerArchivos(id.value);
});
</script>
<style scoped>
.overflow-container {
    max-height: 400px; 
    overflow-y: auto;
    overflow-x: hidden;
}
</style>