<template>
    <v-card >
        <v-card-text>
            <v-row>
                <p>¿A dónde se enviará? Elige</p>
            </v-row>
            <v-row>
                <v-radio-group>
                    <v-col cols="">
                        <v-radio @click="recogerSucursal(1)" value="yes" label="Recolección en sucursal"></v-radio>
                        <v-card-subtitle v-if="sucursal">Se te enviara un correo con las indicaciones al finalizar tu compra</v-card-subtitle>
                    </v-col>
                    <v-col cols="">
                        <v-radio @click="recogerSucursal(2)" value="no" label="Envio a domicilio"></v-radio>
                    </v-col>
                </v-radio-group>
            </v-row>

            <v-select
                :disabled="sucursal"
                v-model="selectedDireccion"
                :items="mappedDirecciones"
                item-title="nombre"
                item-value="id"
                label="Selecciona una dirección"
            >
            </v-select>
        </v-card-text>
    </v-card>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";

const sucursal=ref(false)

const selectedDireccion = ref(null);
const Direcciones = ref([]);

const getDirecciones = async () => {
    try {
        const { data } = await axios.get("/getDirecciones");
        Direcciones.value = data;
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



const recogerSucursal = (choose)=>{
    if (choose===1){
        sucursal.value = true
    }else{
        sucursal.value = false
    }
   
}
</script>
