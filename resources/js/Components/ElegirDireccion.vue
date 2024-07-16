<template>
    <v-card>
        <v-card-text>
            <v-row>
                <p>¿A dónde se enviará? Elige</p>
            </v-row>
            <v-row>
                <v-radio-group v-model="envioOption">
                    <v-col>
                        <v-radio
                            value="sucursal"
                            label="Recolección en sucursal"
                        ></v-radio>
                        <v-card-subtitle v-if="envioOption === 'sucursal'">
                            Se te enviará un correo con las indicaciones al
                            finalizar tu compra
                        </v-card-subtitle>
                    </v-col>
                    <v-col>
                        <v-radio
                            value="domicilio"
                            label="Envío a domicilio"
                        ></v-radio>
                    </v-col>
                </v-radio-group>
            </v-row>
            <div v-if="conDirecciones">
                <v-select
                    :disabled="envioOption !== 'domicilio'"
                    v-model="selectedDireccion"
                    :items="mappedDirecciones"
                    item-title="nombre"
                    item-value="id"
                    label="Selecciona una dirección"
                >
                </v-select>
            </div>
            <div v-else>
                <v-card-subtitle>
                    Parece que no has añadido ninguna dirección aun
                </v-card-subtitle> 
            </div>
        </v-card-text>
        <v-card-actions elevation="1">
            <v-row>
                <v-col class="d-flex justify-start" cols="6">
                    <v-icon
                        @click="emitPasos(1)"
                    >mdi-arrow-left</v-icon>
                </v-col>
                <v-col class="d-flex justify-end" cols="6">
                    <v-icon
                        @click="direccionSeleccionada"
                    >mdi-arrow-right</v-icon>
                </v-col>
            </v-row>
        </v-card-actions>
    </v-card>
</template>
<script setup>
import { ref, onMounted, computed, watch } from "vue";
import axios from "axios";
import { useCartStore } from "../stores/carrito";

const token = document
    .querySelector("meta[name='csrf-token']")
    .getAttribute("value");

const sucursal = ref(false);
const emit = defineEmits(["pasos"]);
const selectedDireccion = ref(null);
const direcciones = ref([]);
const cartStore = useCartStore();
const conDirecciones = ref(false);
const envioOption = ref("");

const getDirecciones = async () => {
    try {
        const { data } = await axios.get("/getDirecciones");
        direcciones.value = data;
        conDirecciones.value = direcciones.value.length > 0;
    } catch (error) {
        console.error("Error fetching directions:", error);
    }
};

onMounted(() => {
    getDirecciones();
});

const mappedDirecciones = computed(() => {
    return direcciones.value.map((direccion) => ({
        nombre: `${direccion.nombre} - ${direccion.direccion}`,
        id: direccion.id,
    }));
});

const recogerSucursal = async () => {
  
        try {
            const { data } = await axios.post(
                "/direccionEntregaSucursal",
                {},
                {
                    headers: {
                        "X-CSRF-TOKEN": token,
                    },
                }
            );
            cartStore.domicilio = false
        } catch (error) {
            console.error("Error during pickup option selection:", error);
        }
    
};

const emitPasos = (value) => {
    emit("pasos", value);
};

const direccionSeleccionada = async () => {
    try {
        if (envioOption.value === "domicilio") {
            await axios.post(
                "/direccionEntrega",
                { direccion_id: selectedDireccion.value },
                {
                    headers: {
                        "X-CSRF-TOKEN": token,
                    },
                }
            );
            cartStore.fetchCart();
        }
        emit("pasos", 3);
    } catch (error) {
        console.error("Error setting delivery address:", error);
    }
};

watch(envioOption, (newValue) => {
    if (newValue === "sucursal") {
        recogerSucursal();
    }
});
</script>