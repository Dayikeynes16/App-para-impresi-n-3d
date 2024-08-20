<template>
       <v-row justify="center" align="center" class="h-100">
            <v-col cols="10" >
                <v-row justify="center" >
                    <v-col sm="7" cols="10" md="5" lg="4" xl="3">
                        <img class="" width="100%" src="../../images/logoApps.png">
                    </v-col>
                </v-row>
            </v-col>
            <v-col sm="7" cols="10" md="6" class="mb-16" lg="4" xl="3">
                <v-card  elevation="8" class="px-12 py-8" rounded="lg" title="Recuperar Contraseña" subtitle=""
                >
                <v-card-text>
                    <v-form @submit.prevent="recuperacion">
                        <v-text-field
                            v-model="email"
                            label="Ingresa tu correo"
                            type="email"
                            required
                            density="compact"
                            variant="outlined"
                        ></v-text-field>
                        <v-btn text="Enviar Email" type="submit" class="my-5" size="large" variant="tonal" block></v-btn>
                    </v-form>
                    <v-alert class="mt-5" v-if="successMessage" type="success" dismissible>{{ successMessage }}</v-alert>
                    <v-alert class="mt-5" v-if="errorMessage" type="error" dismissible>{{ errorMessage }}</v-alert>
                </v-card-text>
                <v-card-actions>
                    <a
                        class="text-blue text-decoration-none"
                        @click="router.push({ name: 'logear' })"
                        rel="noopener noreferrer"
                        target="_blank">Login
                            <v-icon icon="mdi-chevron-right"></v-icon>
                    </a>
                </v-card-actions>
                </v-card>
            </v-col>
       </v-row>
</template>

<script setup>
import { ref } from "vue";
import axios from "@/axios.js";
import { useRouter } from 'vue-router';

const router = useRouter();
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
