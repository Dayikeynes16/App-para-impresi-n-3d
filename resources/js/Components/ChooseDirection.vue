<template>
    <v-card subtitle="¿A dónde se enviará? Elige">
        <v-card-text>
    
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
            <v-row v-if="envioOption === 'domicilio'">
                <v-col cols="9"  >
               
                    <v-select
                        :error-messages="errors.direccion_id"
                        :disabled="envioOption !== 'domicilio'"
                        v-model="selectedDireccion"
                        :items="mappedDirecciones"
                        item-title="nombre"
                        item-value="id"
                        label="Selecciona una dirección"
                    >
                    </v-select>
         
                </v-col>
                <v-col cols="3">
                    <v-btn block variant="tonal" @click="dialog = true">Agregar nueva dirección</v-btn>
                </v-col>
            </v-row>
          
            <div v-if="!conDirecciones">
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
    <v-dialog
      v-model="dialog"
      width="500"
    >
      <v-card
        prepend-icon="mdi-google-maps"
        title="Agregar una dirección"
      >
        <v-card-text>
            <Direcciones @cancelado="dialog = false" @añadido="getDirecciones()"></Direcciones>
        </v-card-text>
      </v-card>
    </v-dialog>
</template>
<script setup>
import { ref, onMounted, computed, watch } from "vue";
import axios from "@/axios.js";
import Direcciones from "./AddDirectons.vue";
const emit = defineEmits(["pasos","direccion"]);
const selectedDireccion = ref(null);
const direcciones = ref([]);
const conDirecciones = ref(false);
const envioOption = ref("");
const dialog = ref(false)
const errors = ref([])
const getDirecciones = async () => {
    try {
        const { data } = await axios.get("/getDirecciones");
        direcciones.value = data;
        conDirecciones.value = direcciones.value.length > 0;
        dialog.value = false
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

const emitPasos = (value) => {
    emit("pasos", value);
};

const direccionSeleccionada = async () => {
    if (envioOption.value === "domicilio") {
            axios.post(
                "/direccionEntrega",
                { direccion_id: selectedDireccion.value }
            ).then(({data}) => {
                emit("pasos", 3);
                emit("direccion", {
                    es_recoleccion: false,
                    direccion: data.direccion
                })

            })
            .catch((error) => {
                errors.value = error.response.data.errors
            })
        } else {
            axios.post("/direccionEntregaSucursal")
            .then(({data}) => {
                emit("pasos", 3);
                emit("direccion", {
                    es_recoleccion: true,
                    direccion: {}
                })
            })
        }  
 
};

</script>