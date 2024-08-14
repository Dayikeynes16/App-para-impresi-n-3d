<template>
  <v-container class="h-100" fluid>
    <v-row justify="center" align="center" class="h-100" >
      <v-col cols="12" >
        <v-row justify="center" class="pt-10">
          <v-col sm="7" cols="10" md="5" lg="4" xl="3">
            <img class="" width="100%" src="../../../images/logoApps.png">
          </v-col>
        </v-row>
      </v-col>
      <v-col sm="7" cols="10" md="6" class="mb-16" lg="4" xl="3">
        <v-card  class="pa-12 pb-8" elevation="8"  rounded="lg">
          <div class="text-subtitle-1 text-medium-emphasis">Correo</div>
          <v-form @submit.prevent="login">
            <v-text-field
              :error-messages="errorMessages.email"
              v-model="form.email"
              density="compact"
              placeholder="Correo electrónico"
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
              placeholder="Ingresa tu contraseña"
              prepend-inner-icon="mdi-lock-outline"
              variant="outlined"
              @click:append-inner="visible = !visible"
            ></v-text-field>
    
            <v-btn type="submit" class="my-8" size="large" variant="tonal" block>
              Ingresar
            </v-btn>
          </v-form>
    
          <v-card-text class="text-center">
            <a
              class="text-blue text-decoration-none cursor-pointer"
              @click="router.push({ name: 'register' })"
              rel="noopener noreferrer"
              target="_blank"
            >
              ¿No tienes una cuenta? registrate <v-icon icon="mdi-chevron-right"></v-icon>
            </a>
            <br>
            <a
              class="text-blue text-decoration-none cursor-pointer"
              @click="router.push({ name: 'recuperarContrasena' })"
              rel="noopener noreferrer"
              target="_blank"
            >
              ¿Olvidaste tu contraseña?<v-icon icon="mdi-chevron-right"></v-icon>
            </a>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from "@/axios.js";
import router from '@/router'
import { useLoginStore } from '../../stores/login';

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

    axios.post('/login', form.value)
    .then(({data}) => {
      loginStore.setUser()
      localStorage.setItem('user', data.data)

      let roles = data.data.roles.map(elememt => elememt.name)

      if(roles.includes('usuario')){
        router.push({name:'cotizar'})
      } else {
        router.push({name: 'Dashboard'})
      }
    })
    .catch((error) => {
      switch (error.response.status) {
        case 422:
          errorMessages.value = error.response.data.errors;  
          break

        case 400:
          console.log(true, 'error', error.response.data.data.message)
          break
        default:
          console.error(error.response);
          break
      }
    })
};


onMounted(() => {
})



</script>
