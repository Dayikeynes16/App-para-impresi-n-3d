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
                            {{
                                Intl.NumberFormat("es-MX", {
                                    type: "currency",
                                    currency: "MXN",
                                    minimumFractionDigits: 2,
                                }).format(file.precio)
                            }}
                        </v-card-text>

                        <v-card-actions>
                            <v-row>
                                    <v-col cols="6">
                                        <v-icon
                                            v-model="form.id"
                                            icon="mdi-delete-outline"
                                            @click="open(file.id)">
                                        </v-icon>
                                    </v-col>
                                    <v-col cols="6">
                                        <v-row>
                                            <v-col class="text-right" cols="3">
                                                <v-icon
                                                    circle
                                                    icon="mdi-plus">
                                                </v-icon
                                            ></v-col>
                                        <v-col class="text-center" cols="3">
                                            1
                                        </v-col>
                                        <v-col class="text-left" cols="3"
                                                ><v-icon
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
                    <v-card-title> N. de orden: #{{ orden.id }} </v-card-title>
                    <v-card-text>
                        Total:
                        {{
                            Intl.NumberFormat("es-MX", {
                                style: "currency",
                                currency: "MXN",
                                minimumFractionDigits: 2,
                            }).format(total)
                        }}
                    </v-card-text>
                    <v-card-actions>
                        <v-btn
                            @click="añadirCarrito(orden.id)"
                            icon="mdi-cart"
                        ></v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script setup>
import axios from "axios";
import { ref, onMounted } from "vue";
import { useCartStore } from "../stores/carrito";
const cartStore = useCartStore();
const orden = ref({
    id: null,
    files: [],
});
const props = defineProps({ item: Object });
const emit = defineEmits(["añadido"]);

const token = document
    .querySelector("meta[name='csrf-token']")
    .getAttribute("value");
import { ElMessage, ElMessageBox } from "element-plus";
const visible = ref(false);
const correcto = ref(false);
const total = ref(0);

const form = ref({
    id: null,
});

const traerarchivos = async () => {
    try {
        const { data } = await axios.get("/traerarchivos");

        orden.value = data.data;
        correcto.value = true;
        calcularTotal();
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
    try {
        await axios.post(
            "/eliminarArchivo",
            { id },
            {
                headers: {
                    "X-CSRF-TOKEN": token,
                },
            }
        );
        orden.value.files = orden.value.files.filter((file) => file.id !== id);
        calcularTotal();
    } catch (error) {
        console.error(error);
    }
};
onMounted(async () => {
    await traerarchivos();
});

const open = (id) => {
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
            eliminarArchivo(id);
        })
        .catch(() => {
            ElMessage({
                type: "info",
                message: "Cancelado",
            });
        });
};

const añadirCarrito = async (id) => {
    const { data } = await axios.post(
        "/añadirStlCarrito",
        { id },
        {
            headers: {
                "X-CSRF-TOKEN": token,
            },
        }
    );
    emit("añadido");
    cartStore.fetchCart();
};
</script>
