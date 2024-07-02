<template>
    <v-card>
        <v-card-text>
            <v-row>
                <p>¿A dónde se enviará? Elige</p>
            </v-row>
            <v-row>
                <v-radio-group>
                    <v-col>
                        <v-radio
                            @click="recogerSucursal(1)"
                            value="yes"
                            label="Recolección en sucursal"
                        ></v-radio>
                        <v-card-subtitle v-if="sucursalEntrega"
                            >Se te enviará un correo con las indicaciones al
                            finalizar tu compra</v-card-subtitle
                        >
                    </v-col>
                    <v-col>
                        <v-radio
                            @click="recogerSucursal(2)"
                            value="no"
                            label="Envío a domicilio"
                        ></v-radio>
                    </v-col>
                </v-radio-group>
            </v-row>
            <div v-if="conDirecciones">
                <v-select
                    :disabled="!sucursal"
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
                    Parece que no has añadido ninguna direccion aun
                </v-card-subtitle> 
            </div>
        </v-card-text>
        <v-card-actions elevation="1">
            <v-row>
                <v-col class="d-flex justify-start" cols="6">
                    <v-icon
                        @click="emitPasos(1)"
                        icon="mdi-arrow-left"
                    ></v-icon>
                </v-col>
                <v-col class="d-flex justify-end" cols="6">
                    <v-icon
                        @click="direccionSeleccionada"
                        icon="mdi-arrow-right"
                    ></v-icon>
                </v-col>
            </v-row>
        </v-card-actions>
    </v-card>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useCartStore } from "../stores/carrito";

const token = document
    .querySelector("meta[name='csrf-token']")
    .getAttribute("value");

const sucursal = ref(false);
const emit = defineEmits(["pasos"]);
const selectedDireccion = ref(null);
const Direcciones = ref([]);
const cartStore = useCartStore();
const conDirecciones = ref(false)
const sucursalEntrega = ref(false);

const getDirecciones = async () => {
    try {
        const { data } = await axios.get("/getDirecciones");
        Direcciones.value = data;
        console.log("hola");
        if (Direcciones.value.length < 1) {
            conDirecciones.value = false
        } else {
            conDirecciones.value = true
        }
    } catch (error) {
        console.error("Error fetching directions:", error);
    }
};

onMounted(() => {
    getDirecciones();
});

const mappedDirecciones = computed(() => {
    return Direcciones.value.map((direccion) => ({
        nombre: `${direccion.nombre} - ${direccion.direccion}`,
        id: direccion.id,
    }));
});

const recogerSucursal = (choose) => {
 
    sucursalEntrega.value = choose === 1;
    sucursal.value = choose === 2;
};

const emitPasos = (value) => {
    emit("pasos", value);
};

const direccionSeleccionada = async () => {
    // if (!selectedDireccion.value ) {
    //     alert(
    //         "Por favor selecciona una dirección o elige recolección en sucursal."
    //     );
    //     return;
    // }

    try {
        if(sucursalEntrega.value === true){
            await axios.post(
            "/direccionEntregaSucursal",
            {
                headers: {
                    "X-CSRF-TOKEN": token,
                },
            }
        );

        }else{
            await axios.post(
            "/direccionEntrega",
            { direccion_id: selectedDireccion.value },
            {
                headers: {
                    "X-CSRF-TOKEN": token,
                },
            }
        );
        }
        
        emit("pasos", 3);
    } catch (error) {
        console.error("Error setting delivery address:", error);
    }
};
</script>
