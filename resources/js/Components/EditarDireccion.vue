<template>
    <v-row>
        <v-col cols="3"></v-col>
        <v-col cols="6">
            <v-card>
                <v-card-title> Actualiza los datos pertinentes </v-card-title>
                <v-card-text>
                    <v-form>
                        <v-text-field
                            v-model="form.nombre"
                            label="Nombre"
                            :error-messages="errorMessages.nombre"
                        >
                        </v-text-field>

                        <v-text-field
                            v-model="form.direccion"
                            label="Direccion"
                            :error-messages="errorMessages.direccion"
                        >
                        </v-text-field>
                        <v-text-field
                            v-model="form.telefono"
                            label="Telefono"
                            :error-messages="errorMessages.telefono"
                        >
                        </v-text-field>
                        <v-text-field v-model="form.destinatario"
                        label="Destinatario"
                        :error-messages="errorMessages.destinatario">
                        </v-text-field>
                        <v-select
                            v-model="form.estado"
                            label="Estados"
                            :error-messages="errorMessages.estado"
                            :items="estadosDeMexico"
                        >
                        </v-select>
                        <v-text-field
                            v-model="form.codigo_postal"
                            label=" Codigo Postal"
                            :error-messages="errorMessages.codigo_postal"
                        >
                        </v-text-field>

                        <v-text-field
                            v-model="form.referencias"
                            label="Referencias de su domiclio"
                            :error-messages="errorMessages.referencias"
                        ></v-text-field>
                    </v-form>
                </v-card-text>

                <v-card-actions>
                    <v-btn @click="update">save</v-btn>
                </v-card-actions>
            </v-card>
        </v-col>
        <v-col cols="3"></v-col>
    </v-row>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
const route = useRoute();
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
const props = defineProps({
    direccion: Object,
});
const emit = defineEmits(["actualizado"]);
const form = ref({});

onMounted(async () => {
    const { data } = await axios.get(`/direccion/${props.direccion.id}`);
    form.value = data.data;
});

const update = async () => {
    try {
        const { data } = await axios.post(
            `/updateDireccion/${props.direccion.id}`,
            form.value
        );
        emit("actualizado");
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errorMessages.value = error.response.data.errors;
        }
    }
};
</script>
