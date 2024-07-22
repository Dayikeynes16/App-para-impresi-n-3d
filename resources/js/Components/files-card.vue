<template>
    <div v-if="correcto">
        <v-row>
            <v-col aria-colspan="4">
                <div v-for="file in orden.files" :key="file.id">
                    <v-card style="margin-bottom: 20px">
                        <v-card-title>
                            {{ file.nombre }}
                        </v-card-title>
                        <v-card-text>
                            El costo es de
                            {{formatCurrency(file.total)}}
                        </v-card-text>

                        <v-card-actions>
                            <v-row>
                                    <v-col cols="6">
                                        <v-icon
                                            v-model="form.id"
                                            icon="mdi-delete-outline"
                                            @click="open(file)">
                                        </v-icon>
                                    </v-col>
                                    <v-col cols="6">
                                        <v-row>
                                            <v-col class="text-right" cols="3">
                                                <v-icon @click="sumarCantidad(file)"
                                                    circle
                                                    icon="mdi-plus">
                                                </v-icon
                                            ></v-col>
                                        <v-col class="text-center" cols="3">
                                            {{ file.cantidad }}
                                        </v-col>
                                        <v-col class="text-left" cols="3"
                                                ><v-icon @click="restarCantidad(file)"
                                                    circle
                                                    icon="mdi-minus"
                                                ></v-icon> 
                                            </v-col>

                                        </v-row>      
                                </v-col>

                              
                            </v-row>
                        </v-card-actions>
                    </v-card>
                </div>
            </v-col>
            <v-col cols="6">
                <v-card>
                    <v-card-title> N. de orden: # </v-card-title>
                    <v-card-text>
                        Total:
                        {{
                            Intl.NumberFormat("es-MX", {
                                style: "currency",
                                currency: "MXN",
                                minimumFractionDigits: 2,
                            }).format()
                        }}
                    </v-card-text>
                    <v-card-actions>
                        <v-btn
                            @click="añadirCarrito()"
                            icon="mdi-cart"
                        ></v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script setup>
import axios from "@/axios.js";
import { ref, onMounted } from "vue";
import { useCartStore } from "../stores/carrito";
import formatCurrency from '../composables/formatNumberToCurrency'


const cartStore = useCartStore();
const orden = ref({
    id: null,
    files: [],
});
const props = defineProps({ item: Object });
const emit = defineEmits(["añadido"]);

import { ElMessage, ElMessageBox } from "element-plus";
const visible = ref(false);
const correcto = ref(false);
const total = ref(0);

const form = ref({
    id: null,
});

const traerarchivos = async () => {
    try {
        const { data } = await axios.get("/cotizacion/archivo-cotizados");

        orden.value.files = data.data;
        correcto.value = true;
      
    } catch (error) {
        console.error(error);
    }
};



const calcularTotal = () => {
    total.value = orden.value
        .map((file) => file.precio)
        .reduce((a, b) => a + b, 0);
};

const eliminarArchivo = async (id) => {

        axios.post(`/cotizacion/delete/${id}`)
            .then(() => {
                traerarchivos()
            })

    
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



const añadirCarrito = async () => {
    const { data } = await axios.post("/carrito/agregar", {
        producto_id: 1,
        cantidad: 1,
        files: orden.value.files
    });
    emit("añadido");
    cartStore.fetchCart();
};

const restarCantidad = async (item) => {
    if (item.cantidad > 1) {
        item.cantidad--;
        axios
            .post("/cotizacion/update", {
            id: item.id,
            cantidad: item.cantidad,
        });
        updateCart();
    }
};

const sumarCantidad = async (item) => {
    item.cantidad++;
    axios
        .post(
        "/cotizacion/update",
        { id: item.id, cantidad: item.cantidad }
    );

    fetchProductosCarrito();
};
</script>
