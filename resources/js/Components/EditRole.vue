<template>
    <v-row>
      <v-col cols="4"></v-col>
      <v-col cols="4">
        <v-card title="Actualiza los datos de necesarios">
        <v-card-text>

             <v-text-field v-model="user.name" label="Nombre"> </v-text-field>

            <v-text-field v-model="user.email" label="Correo"> </v-text-field> 
             <v-select
             label="Asigna un rol"
           
            :items="roles"
            item-title="name" 
            item-value="name"
            v-model="rolSeleccionado"
            > </v-select> 

        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn prepend-icon="mdi-check" @click="updateUser()">Guardar</v-btn>
        </v-card-actions>

      </v-card>
      </v-col>
      <v-col cols="4"></v-col>

      
       
    </v-row>
</template>
<script setup>
import axios from "@/axios.js";
import { defineProps, onMounted, ref } from "vue";

const emit = defineEmits(["añadido"]);

const rolSeleccionado = ref('');
const user = ref({});
const roles = ref([]);
const props = defineProps({
    roles: {
        type: Array,
        required: true,
    },
    user: {
        type: Object,
        required: true,
    },
});

const updateUser = async () => {
  try {
    axios.put(`/actualizarRolesUsuario/${user.value.id}`,{user, rol: rolSeleccionado.value})
    emit('añadido')
  } catch (error) {
    console.log(error)
  }
  
}


onMounted(() => {
    roles.value = props.roles;
    user.value = props.user;
});
</script>
