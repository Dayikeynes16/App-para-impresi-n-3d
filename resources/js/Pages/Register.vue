<template>
    <v-card
        class="mx-auto pa-12 pb-8"
        elevation="8"
        max-width="448"
        rounded="lg"
    >
        <v-card-title> Registrate </v-card-title>
        <v-form @submit.prevent="registrar()">
            <div class="text-subtitle-1 text-medium-emphasis">Nombre</div>

            <v-text-field
                required
                :error-messages="errorMessages.name"
                v-model="form.name"
                density="compact"
                placeholder="Ingresa tu nombre"
                variant="outlined"
            ></v-text-field>
            <div class="text-subtitle-1 text-medium-emphasis">Correo</div>

            <v-text-field
                :error-messages="errorMessages.email"
                v-model="form.email"
                density="compact"
                placeholder="Correo electronico"
                prepend-inner-icon="mdi-email-outline"
                variant="outlined"
            ></v-text-field>

            <div
                class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between"
            >
                Contraseña
            </div>

            <v-text-field
                :error-messages="errorMessages.password"
                v-model="form.password"
                :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                :type="visible ? 'text' : 'password'"
                density="compact"
                placeholder="Ingresa una contraseña"
                prepend-inner-icon="mdi-lock-outline"
                variant="outlined"
                @click:append-inner="toggleVisibility"
            ></v-text-field>
            <div
                class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between"
            >
                Confirma tu contraseña
            </div>

            <v-text-field
                :error-messages="errorMessages.password_confirmation"
                v-model="form.password_confirmation"
                :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                :type="visible ? 'text' : 'password'"
                density="compact"
                placeholder="Re-ingresa tu contraseña"
                prepend-inner-icon="mdi-lock-outline"
                variant="outlined"
                @click:append-inner="toggleVisibility"
            ></v-text-field>

            <v-btn
                type="submit"
                class="mb-8"
                color="blue"
                size="large"
                variant="tonal"
                block
            >
                Registrarse
            </v-btn>
        </v-form>

        <v-card-text class="text-center">
            <a
                class="text-blue text-decoration-none"
                @click="router.push({ name: 'logear' })"
                rel="noopener noreferrer"
                target="_blank"
            >
                ¿Ya tienes una cuenta? Ingresa
                <v-icon icon="mdi-chevron-right"></v-icon>
            </a>
        </v-card-text>
    </v-card>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useLoginStore } from "../stores/login";
import axios from "axios";

const loginStore = useLoginStore();
const router = useRouter();

const errorMessages = ref({});

const visible = ref(false);

const form = ref({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
});

const registrar = async () => {
    try {
        const { data } = await axios.post("/usuarios", form.value);
        router.push({ name: "logear" });
    } catch (error) {
        if (error.response.status === 422) {
            errorMessages.value = error.response.data.errors;
        }
    }
};

const toggleVisibility = () => {
    visible.value = !visible.value;
};

</script>

<style>
#body-content {
    margin: 40px;
}
#registrar {
    justify-content: center;
    display: flex;
    margin: 10px;
}
</style>