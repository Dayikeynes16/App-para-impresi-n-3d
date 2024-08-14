<template>
    <v-container>
      <v-row justify="center">
        <v-col cols="12" md="8">
          <v-card class="mt-5">
            <v-card-title>Recuperar Contraseña</v-card-title>
            <v-card-text>
              <v-form @submit.prevent="cambiarContrasenia">
                <input type="hidden" v-model="token" />
  
                <v-text-field
                  v-model="password"
                  label="Contraseña"
                  type="password"
                  :rules="[rules.required]"
                  required
                  style="margin-bottom: 20px;"
                ></v-text-field>
  
                <v-text-field
                  v-model="passwordConfirmation"
                  label="Confirmar Contraseña"
                  type="password"
                  :rules="[rules.required, rules.matchPassword]"
                  required
                  style="margin-bottom: 20px;"
                ></v-text-field>
  
                <v-btn color="primary" type="submit">
                  Cambiar Contraseña
                </v-btn>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useRoute } from 'vue-router';
  import axios from '@/axios';
  
  const route = useRoute();
  const token = ref('');
  const password = ref('');
  const passwordConfirmation = ref('');
  
  const rules = {
    required: value => !!value || 'Campo requerido.',
    matchPassword: value => value === password.value || 'Las contraseñas no coinciden.',
  };
  
  const cambiarContrasenia = async () => {
    try {
      const response = await axios.post('/actualizar-contrasenia', {
        token: token.value,
        password: password.value,
        password_confirmation: passwordConfirmation.value,
      });
      
      console.log(response.data);
    } catch (error) {
      console.error('Error al cambiar la contraseña:', error);
    }
  };
  
  onMounted(() => {
    // Obtener el token desde la URL
    token.value = route.params.token;
  });
  </script>
  