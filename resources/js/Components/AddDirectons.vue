<template>
   
            
                    <v-form @submit.prevent="submitDireccion">
                        <v-text-field
                            label="Asigna un nombre a esta dirección"
                            v-model="form.nombre"
                            :error-messages="errorMessages.nombre"
                        >
                        </v-text-field>
                        <v-text-field
                            label="Nombre de quien recibe"
                            v-model="form.destinatario"
                            :error-messages="errorMessages.destinatario"
                        >
                        </v-text-field>
                        <v-text-field
                            label="Ingrese su dirección completa"
                            v-model="form.direccion"
                            :error-messages="errorMessages.direccion"
                        >
                        </v-text-field>
                        <v-text-field
                            v-model="form.telefono"
                            label="Número telefónico"
                            maxlength="10"
                            :error-messages="errorMessages.telefono"
                            @input="
                                form.telefono = form.telefono
                                    .replace(/[^0-9]/g, '')
                                    .slice(0, 10)
                            "
                        >
                        </v-text-field>

                        <v-select
                            v-model="form.estado"
                            label="Selecciona el estado"
                            :items="estadosDeMexico"
                            :error-messages="errorMessages.estado"
                        >
                        </v-select>
                        <v-text-field
                            maxlength="5"
                            label="Ingresa tu código postal"
                            :error-messages="errorMessages.codigo_postal"
                            v-model="form.codigo_postal"
                        >
                        </v-text-field>
                        <v-textarea
                            v-model="form.referencias"
                            label="Añada referencias del lugar"
                            :error-messages="errorMessages.referencias"
                        >
                        </v-textarea>
                        <v-row>
                            <v-col class="text-left" cols="6">
                                <v-btn variant="tonal" @click="closeDialog()" color="danger" prepend-icon="mdi-close">Cancelar</v-btn>
                            </v-col>
                            <v-col class="text-right" cols="6">
                                <v-btn variant="tonal" prepend-icon="mdi-Check-Outline" type="submit">
                                    Guardar</v-btn>
                            </v-col>
                        </v-row>
                    
                    </v-form>
        
</template>

<script setup>
import { ref, watch } from "vue";
import axios from "@/axios.js";
import { useRouter } from "vue-router";

const props = defineProps({ item: Object });
const emit = defineEmits(["añadido", "cancelado"]);
const router = useRouter();
const errorMessages = ref({});
const estadosDeMexico = [
    "Aguascalientes",
    "Baja California",
    "Baja California Sur",
    "Campeche",
    "Chiapas",
    "Chihuahua",
    "Ciudad de México",
    "Coahuila",
    "Colima",
    "Durango",
    "Estado de México",
    "Guanajuato",
    "Guerrero",
    "Hidalgo",
    "Jalisco",
    "Michoacán",
    "Morelos",
    "Nayarit",
    "Nuevo León",
    "Oaxaca",
    "Puebla",
    "Querétaro",
    "Quintana Roo",
    "San Luis Potosí",
    "Sinaloa",
    "Sonora",
    "Tabasco",
    "Tamaulipas",
    "Tlaxcala",
    "Veracruz",
    "Yucatán",
    "Zacatecas",
];

const form = ref({
    id: "",
    nombre: "",
    destinatario: "",
    direccion: "",
    telefono: "",
    referencias: "",
    estado: "",
    codigo_postal: "",
});

watch(
    () => props.item,
    (newItem) => {
        if (newItem) {
            form.value = { ...newItem };
        }
    },
    { immediate: true }
);

const closeDialog = () => {
    emit("cancelado")
}

const submitDireccion = async () => {
    try {
        const { data } = await axios.post("/guardarDireccion", form.value); 
            emit("añadido");
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errorMessages.value = error.response.data.errors;
        }
    }
};
</script>
