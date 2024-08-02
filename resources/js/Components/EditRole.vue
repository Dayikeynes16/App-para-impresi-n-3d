<template>
    <v-row>
    
        <v-card title="Actualiza los datos de necesarios">
        <v-card-text>
        
          <v-text-field
          v-model="role.name"
          ></v-text-field>
          <v-row style="height: 250px;" class="overflow-y-auto">
            <v-col>
              <v-checkbox
                hide-details
                v-for="permiso in permissions"
                :key="permiso.id"
                :label="permiso.name"
                :value="permiso.id"
                v-model="permisosSeleccionados"
              ></v-checkbox>
            </v-col>
          </v-row>

    
        </v-card-text>
        <v-card-actions>
          <v-row>
            <v-col cols="6">
              <v-btn prepend-icon="mdi-close" color="danger" block @click="emit('cancelado')">Cerrar</v-btn>

            </v-col>
            <v-col cols="6">
              <v-btn prepend-icon="mdi-check" block @click="updateRole()">Guardar</v-btn>
            </v-col>
          </v-row>
        </v-card-actions>

      </v-card>
 

      
       
    </v-row>
</template>
<script setup>
import axios from "@/axios.js";
import { defineProps, onMounted, ref } from "vue";
import { ElMessage } from 'element-plus'
const emit = defineEmits(["añadido", "cancelado"]);
const permisosSeleccionados = ref([]);
const rolSeleccionado = ref('');
const user = ref({});
const role = ref({});
const permissions = ref([])
const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    permisos: {
      type: Array,
      required: true
    },
    userPermission: {
      type: Array,
      required: true
    }
});

const updateRole = async () => {
  try {
    if(permisosSeleccionados.value.length > 0){
      axios.put(`/roles/${role.value.id}`,{ permission: permisosSeleccionados.value})
      .then(() => {
        emit('añadido')
      })
    } else {
      ElMessage.error(
        'Selecciona al menos 1 permiso'
      )
    }
  } catch (error) {
    console.log(error)
  }
  
}


onMounted(() => {
    role.value = props.role
    permissions.value = props.permisos
    permisosSeleccionados.value = props.userPermission
 
});
</script>
