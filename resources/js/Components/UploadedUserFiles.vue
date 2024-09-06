<template>
    <v-row class="text-center mt-0 pt-0 mb-2">
        <v-col cols="3"><h6>Archivos</h6></v-col>
        <v-col cols="2" class="text-center ml-6"><h6>Piezas</h6></v-col>
        <v-col cols="2" class="text-center ml-6"><h6>Tiempo de Impresión</h6></v-col>
        <v-col cols="2" class="text-center ml-6"><h6>Precio</h6></v-col>
        <v-col cols="2" class="text-center"></v-col>
    </v-row>
    <row v-for="file in files" :key="file.id">
        <v-card class="mb-3">
            <v-card-text>
                <v-row align="center">
                    <v-col class="ma-0 pa-0" cols="1">
                        <v-checkbox color="success" v-model="enviarCarrito" :value="file.id" hide-details>
                        </v-checkbox>
                    </v-col>
                    <v-col cols="3">
                        <h5>{{ file.nombre }}</h5>
                    </v-col>
                    <v-col cols="2" class="text-center">
                        <v-row>
                            <v-col class="text-right" cols="4">
                                <v-icon @click="restarCantidad(file)" circle icon="mdi-minus"></v-icon>
                            </v-col>
                            <v-col class="text-center" cols="4">
                                {{ file.cantidad }}
                            </v-col>
                            <v-col class="text-left" cols="4">
                                <v-icon @click="sumarCantidad(file)" circle icon="mdi-plus"></v-icon>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="2" class="text-center">
                        {{ file.minutos }} min
                    </v-col>
                    <v-col cols="2" class="text-center">
                        {{ formatCurrency(file.total) }}
                    </v-col>
                    <v-col cols="1" class="text-center">
                        <v-icon v-model="form.id" color="danger" variant="tonal" icon="mdi-delete-outline" @click="open(file)">
                        </v-icon>
                    </v-col>
                    <v-col cols="1">
                        <v-icon
                        @click="file.dialog = true"
                        color="surface-variant"
                        icon="mdi-eye"
                        variant="flat"
                        ></v-icon>
                        <v-dialog  max-width="500" v-model="file.dialog">
                            
                                <v-card rounded="5" elevation="15" color="black" class="pa-0 ma-0">
                                    <STLViewer :stl="file"></STLViewer>
                                    <v-card-actions>
                                        <v-btn color="white" append-icon="mdi-close-circle" @click="file.dialog = false">Cerrar</v-btn>          
                                    </v-card-actions>
                                </v-card>
        
                        </v-dialog>
                    </v-col>
                </v-row>
                <v-row>
                   
                 
                </v-row>
            </v-card-text>
        </v-card>
    </row>
</template>

<script setup>
import axios from "@/axios.js";
import { ref, onMounted, watch } from "vue";
import formatCurrency from "../composables/formatNumberToCurrency";
import { ElMessage, ElMessageBox } from "element-plus";
import STLViewer from './STLViewer.vue';

const files = ref([]);
const props = defineProps({ item: Object, update: Boolean });
const emit = defineEmits(["anadido", "noFiles", "datosCarrito", "actualizado"]);
const enviarCarrito = ref([]);
const form = ref({
    id: null,
});

const calcularTotal = () => {
    if (enviarCarrito.value.length === 0) {
        emit('datosCarrito', { cantidad: 0, total: 0 });
    }

    let selectedFiles = files.value.filter(file => enviarCarrito.value.includes(file.id))

    let total = selectedFiles.map(file => file.total).reduce((a,b) => a+b, 0);
    let itemsCarrito = files.value.filter(file => enviarCarrito.value.includes(file.id));



    emit('datosCarrito', { cantidad: enviarCarrito.value.length, total: total, files: itemsCarrito });
};

const traerarchivos = async () => {
    axios.get("/cotizacion/archivo-cotizados")
        .then(({ data }) => {
            files.value = data.data;
            calcularTotal();
            emit("actualizado", false);
        })
        .catch((error) => {
            console.error(error);
        });
};


watch(() => enviarCarrito.value, (newValue, oldValue) => {
    calcularTotal();
});
watch(() => props.update, (newValue, oldValue) => {
    traerarchivos();
});

const eliminarArchivo = async (id) => {
    axios.post(`/cotizacion/delete/${id}`).then(() => {
        traerarchivos();
    });
};

onMounted(async () => {
    await traerarchivos();
});

const open = (file) => {
    ElMessageBox.confirm("¿Está seguro de eliminar?", "Confirmar", {
        confirmButtonText: "OK",
        cancelButtonText: "Cancelar",
        type: "warning",
    })
        .then(() => {
            ElMessage({
                type: "success",
                message: "Elemento eliminado",
            });
            eliminarArchivo(file.id);
        })
        .catch(() => {
            ElMessage({
                type: "info",
                message: "Cancelado",
            });
        });
};

const restarCantidad = async (item) => {
    if (item.cantidad > 1) {
        item.cantidad--;
        axios.post("/cotizacion/update", {
            id: item.id,
            cantidad: item.cantidad,
        })
        .then(() => {
            traerarchivos();
        });
    }
};

const sumarCantidad = async (item) => {
    item.cantidad++;
    axios.post("/cotizacion/update", { id: item.id, cantidad: item.cantidad })
    .then(() => {
        traerarchivos();
    });
};
</script>
