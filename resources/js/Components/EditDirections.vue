<template>
   
          <v-form>
            <v-text-field
              v-model="form.nombre"
              label="Nombre"
              :error-messages="errorMessages.nombre"
            ></v-text-field>
  
            <v-text-field
              v-model="form.direccion"
              label="Dirección"
              :error-messages="errorMessages.direccion"
            ></v-text-field>
  
            <v-text-field
              v-model="form.telefono"
              label="Teléfono"
              :error-messages="errorMessages.telefono"
            ></v-text-field>
  
            <v-text-field
              v-model="form.destinatario"
              label="Destinatario"
              :error-messages="errorMessages.destinatario"
            ></v-text-field>
  
            <v-select
              v-model="form.estado"
              label="Estados"
              :error-messages="errorMessages.estado"
              :items="estadosDeMexico"
            ></v-select>
  
            <v-text-field
              v-model="form.codigo_postal"
              label="Codigo Postal"
              :error-messages="errorMessages.codigo_postal"
            ></v-text-field>
  
            <v-text-field
              v-model="form.referencias"
              label="Referencias de su domiclio"
              :error-messages="errorMessages.referencias"
            ></v-text-field>
          </v-form>
        <v-card-actions>
        <v-row>
            <v-col class="text-left" cols="6">
                <v-btn class="text-left" color="danger" @click="closeDialog()" prepend-icon="mdi-close" variant="tonal">cancelar</v-btn>

            </v-col>
            <v-col class="text-right" cols="6">
          <v-btn variant="tonal" @click="update" prepend-icon="mdi-Check-Outline">Guardar</v-btn>
            </v-col>
        </v-row>
        </v-card-actions>
    
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useRoute } from 'vue-router';
  import axios from "@/axios.js";
  
  const dialog = ref(true);
  const route = useRoute();
  const errorMessages = ref({});
  
  const estadosDeMexico = [
    'Aguascalientes', 'Baja California', 'Baja California Sur', 'Campeche', 'Chiapas',
    'Chihuahua', 'Ciudad de México', 'Coahuila', 'Colima', 'Durango', 'Estado de México',
    'Guanajuato', 'Guerrero', 'Hidalgo', 'Jalisco', 'Michoacán', 'Morelos', 'Nayarit',
    'Nuevo León', 'Oaxaca', 'Puebla', 'Querétaro', 'Quintana Roo', 'San Luis Potosí',
    'Sinaloa', 'Sonora', 'Tabasco', 'Tamaulipas', 'Tlaxcala', 'Veracruz', 'Yucatán', 'Zacatecas'
  ];
  
  const props = defineProps({
    direccion: Object,
  });
  const emit = defineEmits(['actualizado']);
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
      emit('actualizado');
    } catch (error) {
      if (error.response && error.response.status === 422) {
        errorMessages.value = error.response.data.errors;
      }
    }
  };
  
  const closeDialog = () => {
    emit('cancelado')

  };
  </script>