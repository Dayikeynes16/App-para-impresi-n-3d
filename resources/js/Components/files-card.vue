<template>
    <v-row class="text-center mt-0 pt-0 mb-2">
        <v-col cols="5"><h6>Archivos</h6> </v-col>
        <v-col cols="3" class="text-center ml-6"><h6>Piezas</h6></v-col>
        <v-col cols="2" class="text-center ml-6"> <h6>Precio</h6></v-col>
        <v-col cols="1" class="text-center"></v-col>
    </v-row>
    <row v-for="file in files" :key="file.id">
        <v-card class="mb-3">
            <v-card-text>
                <v-row align="center">
                    <v-col class="ma-0 pa-0" cols="1" >
                        <v-checkbox color="success" v-model="enviarCarrito" :value="file.id" hide-details >
                        </v-checkbox> 
                    </v-col>
                    <v-col cols="5">
                        <h5> {{ file.nombre }} </h5>
                    </v-col>
                    <v-col cols="3"  class="text-center">
                        <v-row>
                            <v-col class="text-right" cols="3">
                                <v-icon @click="restarCantidad(file)" circle
                                icon="mdi-minus"></v-icon>
                            </v-col>
                            <v-col class="text-center" cols="3">
                                {{ file.cantidad }}
                                </v-col>
                                <v-col class="text-left" cols="3">
                                    <v-icon @click="sumarCantidad(file)"  circle icon="mdi-plus">
                                    </v-icon>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="2"  class="text-center" >
                        {{ formatCurrency(file.total) }}
                    </v-col>
                    <v-col cols="1"  class="text-center">
                        <v-icon v-model="form.id" color="danger"  variant="tonal" icon="mdi-delete-outline" @click="open(file)">
                        </v-icon>
                    </v-col>
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

const files = ref([]);
const props = defineProps({ item: Object });
const emit = defineEmits(["anadido", "noFiles", "datosCarrito"]);
const enviarCarrito = ref([]);
const form = ref({
    id: null,
});

const calcularTotal =  () => {
    if(enviarCarrito.value.length === 0){
        emit('datosCarrito', { cantidad: 0, total: 0}) 
    }

    let total = 0;
    let itemsCarrito = [];
    enviarCarrito.value.forEach(element => {
        let carrito = files.value.find((file) => {
            return file.id === element
        })
        total += carrito.total;
        itemsCarrito.push(carrito)
    });

    emit('datosCarrito', { cantidad: enviarCarrito.value.length, total: total, files: itemsCarrito }) 
}

const traerarchivos = async () => {

    axios.get("/cotizacion/archivo-cotizados")
        .then(({data}) => {
            files.value = data.data;
            calcularTotal()
        })
        .catch((error) => {
            console.error(error);
        })
};
const seleccionar = (item) => {
    enviarCarrito.value.push(item);
}

watch(() => enviarCarrito.value, (newValue, oldValue) => {
    calcularTotal()
})

const eliminarArchivo = async (id) => {
    axios.post(`/cotizacion/delete/${id}`).then(() => {
        traerarchivos();
    });
};

onMounted(async () => {
    await traerarchivos();
});

const open = (file) => {
    ElMessageBox.confirm("¿Esta seguro de eliminar?", "Confirmar", {
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
            traerarchivos()
        })
    }
};

const sumarCantidad = async (item) => {
    item.cantidad++;
    axios.post("/cotizacion/update", { id: item.id, cantidad: item.cantidad })
    .then(() => {
        traerarchivos()
    })
};
</script>
