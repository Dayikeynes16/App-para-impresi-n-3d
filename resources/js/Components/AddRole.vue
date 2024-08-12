<template>
  <v-row>
    <v-col cols="12">
      <v-card>
        <v-card-title>Rellene los campos necesarios</v-card-title>
        <v-card-text>
          <v-row>
              <v-col cosl="12">
                <v-text-field
                  v-model="name.name"
                  label="Nombre del rol"
                  :error-messages="errorMessages.name"
                  required
                ></v-text-field>

              </v-col>
          </v-row>
          <v-row style="height: 250px;" class="overflow-y-auto">
              <v-col cols="12" >
                <v-checkbox
                  hide-details
                  v-for="permiso in permisos"
                  :key="permiso.id"
                  :label="permiso.name"
                  :value="permiso.id"
                  v-model="permisosSeleccionados"
                ></v-checkbox>
                <v-alert v-if="errorMessages.permission" type="error" dense>{{ errorMessages.permission }}</v-alert>
              </v-col>
          </v-row>
  
           
      
        </v-card-text>
        <v-card-actions>
          <v-row>
            <v-col cols="6">
              <v-btn color="danger" variant="tonal" block @click="cancelar()" prepend-icon="mdi-close">Cancelar</v-btn>
            </v-col>
            <v-col cols="6">
              <v-btn variant="tonal" block prepend-icon="mdi-check" @click="guardarRol">Guardar</v-btn>
            </v-col>
          </v-row>
        </v-card-actions>
      </v-card>
    </v-col>

  </v-row>
</template>

<script setup>
import axios from "@/axios.js";
import { ref, onMounted } from "vue";
import { VRow } from "vuetify/lib/components/index.mjs";
import { ElMessage } from 'element-plus'

const emit = defineEmits(["añadido", "cancelado"]);
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
    if (permisosSeleccionados.value.length > 0) {
      const response = await axios.post(
        "/roles",
        {
          name: name.value.name,
          permission: permisosSeleccionados.value
        });
        emit("añadido");
    }else{
      ElMessage.error(
        'Selecciona al menos 1 permiso'
      )
    }
    
  } catch (error) {
    errorMessages.value = error.response.data.errors;
    console.error("Error guardando el rol", error);
  }
};

const cancelar = () => {
  emit("cancelado")
}

onMounted(() => {
  getPermissions();
});
</script>