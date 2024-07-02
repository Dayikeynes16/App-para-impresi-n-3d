<template>
    <v-container>
       <v-row>
        <v-col cols="4"></v-col>
        <v-col cols="4">
            <v-card color="grey-lighten-3" class="py -6" title="Recuperar Contraseña">
            <v-card-text>
                <v-form @submit.prevent="recuperacion">
                    <v-text-field
                        v-model="email"
                        label="Ingresa tu correo"
                        type="email"
                        required
                    ></v-text-field>
                    <v-btn type="submit">Enviar Email de Recuperación</v-btn>
                </v-form>
                <v-alert v-if="successMessage" type="success" dismissible>{{ successMessage }}</v-alert>
                <v-alert v-if="errorMessage" type="error" dismissible>{{ errorMessage }}</v-alert>
            </v-card-text>
        </v-card>

        </v-col>
        <v-col cols="4"></v-col>
       </v-row>
        
    </v-container>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";

const token = document.querySelector("meta[name='csrf-token']").getAttribute("value");
const email = ref("");
const successMessage = ref("");
const errorMessage = ref("");

const recuperacion = async () => {
    successMessage.value = "";
    errorMessage.value = "";

    try {
        const { data } = await axios.post(
            "/enviar-recuperar-contrasenia",
            { email: email.value },
            {
                headers: {
                    "X-CSRF-TOKEN": token,
                },
            }
        );
        successMessage.value = data.message;
    } catch (error) {
        if (error.response && error.response.data && error.response.data.message) {
            errorMessage.value = error.response.data.message;
        } else {
            errorMessage.value = "Ocurrió un error al enviar el correo de recuperación.";
        }
    }
};
</script>
