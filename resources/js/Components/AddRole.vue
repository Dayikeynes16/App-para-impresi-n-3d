<template>
  <v-row>
    <v-col cols="4"></v-col>
    <v-col cols="4">
      <v-card>
        <v-card-text>
          <v-card-title>Rellene los campos necesarios</v-card-title>
          <v-form>
            <v-text-field
              v-model="name.name"
              label="Nombre del rol"
              :error-messages="errorMessages.name"
              required
            ></v-text-field>

            <v-checkbox
              v-for="permiso in permisos"
              :key="permiso.id"
              :label="permiso.name"
              :value="permiso.id"
              v-model="permisosSeleccionados"
            ></v-checkbox>
            <v-alert v-if="errorMessages.permission" type="error" dense>{{ errorMessages.permission }}</v-alert>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn prepend-icon="mdi-check" @click="guardarRol">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
    <v-col cols="4"></v-col>
  </v-row>
</template>

<script setup>
import axios from "@/axios.js";
import { ref, onMounted } from "vue";

const emit = defineEmits(["añadido"]);
const errorMessages = ref({});
const permisos = ref([]);
const permisosSeleccionados = ref([]);
const name = ref({
  name: null
});

const getPermissions = async () => {
  const { data } = await axios.get("/permissions");
  permisos.value = data.data;
};

const guardarRol = async () => {
  try {
    const response = await axios.post(
      "/roles",
      {
        name: name.value.name,
        permission: permisosSeleccionados.value
      }
    );
    emit("añadido");
  } catch (error) {
    errorMessages.value = error.response.data.errors;
    console.error("Error guardando el rol", error);
  }
};

onMounted(() => {
  getPermissions();
});
</script>