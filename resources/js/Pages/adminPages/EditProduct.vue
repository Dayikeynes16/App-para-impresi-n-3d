<template>
    <v-container>
            <v-col cols="12">

            <v-stepper next-text="siguiente" prev-text="atras" :items="['Fotos', 'Archivos', 'Información']">
                <template v-slot:item.1>
                    <v-card subtitle="Añade o elimina fotos" flat>
                        <v-row>
                            <v-col cols="6">
                                <v-card elevation="0">
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
                                                <v-col class="text-right mb-0 pb-0">
                                                    <v-icon
                                                        color="danger"
                                                        icon="mdi-delete"
                                                        @click="eliminarImagen(imagen.id)"
                                                    ></v-icon>
                                                </v-col>
                                            </v-card-actions>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </v-col>
                        </v-row>
                    </v-card>
                </template>
                  <template v-slot:item.2>
                    <v-card subtitle="Modifica los archivos" >
                        <v-row>
                            <v-col cols="6">
                                <v-card elevation="0">
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
                                <v-card elevation="0" >
                                    <v-card-title>Archivos</v-card-title>
                                    <v-card-text class="overflow-y-auto" style="max-height: 300px;">
                                          
                                                    <v-row align="center" v-for="archivo in archivos">
                                                        <v-col cols="12">
                                                          <v-card>
                                                            <v-row class="ma-4">
                                                                <v-col cols="9" class="">
                                                                    {{ archivo.nombre }}
                                                                </v-col>
                        
                                                                <v-col cols="3" align="center" class="text-right">
                                                                    <v-icon
                                                                        color="danger"
                                                                        icon="mdi-Delete"
                                                                        @click="
                                                                        eliminarArchivo(archivo.id)"
                                                                    ></v-icon>
                                                                </v-col>
                                                            </v-row>
                                                          </v-card>
                                                        </v-col>
                                                    </v-row>
                                         
                                            </v-card-text>
                                        <v-divider></v-divider>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-card>
                  </template>
                  <template v-slot:item.3>
                    <v-card flat>
                        <v-card>
                            <v-card-title>Información del producto</v-card-title>
                            <v-card-text>
                                <v-text-field
                                    variant="outlined"
                                    v-model="product.name"
                                    label="Nombre"
                                    required
                                    :error-messages="errorMessages.name"
                                ></v-text-field>
                                <v-text-field
                                    variant="outlined"
                                    v-model="product.description"
                                    label="Descripción"
                                    required
                                    :error-messages="errorMessages.description"
                                ></v-text-field>
                                <v-text-field
                                    v-model="product.price"
                                    variant="outlined"
                                    label="Precio"
                                    required
                                    type="number"
                                    :error-messages="errorMessages.price"
                                ></v-text-field>
                                <v-btn block variant="outlined" @click="update" color="primary"
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
import axios from "@/axios.js";
import { UploadFilled} from "@element-plus/icons-vue";
import { ElMessage } from 'element-plus'


const product = ref({});
const errorMessages = ref({});
const route = useRoute();
const Imagenes = ref([]);
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
            { id }        );
        Imagenes.value = Imagenes.value.filter((imagen) => imagen.id !== id);
    } catch (error) {
        alert("Error al eliminar el archivo");
    }
};

const guardarImagen = async (file) => {
    try {
        const formData = new FormData();
        formData.append("image", file.file);
        formData.append("producto_id", id.value);

        const { data } = await axios.post("/guardarImagen", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        Imagenes.value.push(data.data);
    } catch (error) {
        ElMessage.error('Ha ocurrido un error al guardar la imagen');
    }
};

const guardarSTL = async (file) => {
    try {
        const formData = new FormData();
        formData.append("file", file.file);
        formData.append("producto_id", id.value);
        
        const { data } = await axios.post("/guardarSTLproducto", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        traerArchivos(id.value)
        ElMessage({
            message: 'Archivo guardado correctamente',
            type: 'success',
        })
        
    } catch (error) {
        ElMessage.error('Ocurrió un problema al guardar el STL');
    }
};


const eliminarArchivo = async (id) => {
    try {
        await axios.post(
            "/borrarArchivo",
            { id },
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