<template>
  <div v-if="resultado">
    <v-img class="mx-auto my-6" max-width="150">
      <img style="max-width:100%;" src="../../images/logo horizontal aps creativas2.png" />
    </v-img>

    <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg">
      <div class="text-subtitle-1 text-medium-emphasis">Correo</div>
      <v-form @submit.prevent="login">
        <v-text-field
          :error-messages="errorMessages.email"
          v-model="form.email"
          density="compact"
          placeholder="Email address"
          prepend-inner-icon="mdi-email-outline"
          variant="outlined"
        ></v-text-field>

        <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
          Contraseña
        </div>

        <v-text-field
          :error-messages="errorMessages.password"
          v-model="form.password"
          :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
          :type="visible ? 'text' : 'password'"
          density="compact"
          placeholder="Enter your password"
          prepend-inner-icon="mdi-lock-outline"
          variant="outlined"
          @click:append-inner="visible = !visible"
        ></v-text-field>

        <v-btn type="submit" class="mb-8" color="blue" size="large" variant="tonal" block>
          Ingresar
        </v-btn>
      </v-form>

      <v-card-text class="text-center">
        <a
          class="text-blue text-decoration-none"
          @click="router.push({ name: 'register' })"
          rel="noopener noreferrer"
          target="_blank"
        >
          ¿No tienes una cuenta? registrate <v-icon icon="mdi-chevron-right"></v-icon>
        </a>
        <br>
        <a
          class="text-blue text-decoration-none"
          @click="router.push({ name: 'recuperarContraseña' })"
          rel="noopener noreferrer"
          target="_blank"
        >
          ¿Olvidaste tu contraseña? Recuperala<v-icon icon="mdi-chevron-right"></v-icon>
        </a>
      </v-card-text>
    </v-card>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useLoginStore } from '../stores/login';

const router = useRouter();
const loginStore = useLoginStore();
const errorMessages = ref({});
const visible = ref(false);
const resultado = ref(true);

const form = ref({
  email: '',
  password: ''
});

const login = async () => {
  errorMessages.value = {};
  try {
    const { data } = await axios.post('/login', form.value);
    if (data.code === 200) {
      loginStore.setAutenticado(true);
      if (data.data.email === 'javierMay@appscreativas.com'){
        router.push({ name: 'Dashboard' });
      } else{
        router.push({ name: 'cotizar' });
      }
    } else {
      alert(data.data);
    }
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errorMessages.value = error.response.data.errors;
    }
  }
};




</script>
