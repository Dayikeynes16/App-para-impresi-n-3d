<template>
    <v-container class="text-center">
      <v-card-text v-if="addButtonIsVisible">
        <v-expansion-panels variant="popout">
          <v-expansion-panel v-for="direccion in direcciones" :key="direccion.id">
            <v-expansion-panel-title>
              {{ direccion.nombre }}
            </v-expansion-panel-title>
            <v-expansion-panel-text>
              <v-card-text>
                <h7> Direccion: {{ direccion.direccion }} </h7>
                <v-divider></v-divider>
                <h7>Destinatario: {{ direccion.destinatario }}</h7>
                <v-divider></v-divider>
                <h7>Telefono: {{ direccion.telefono }}</h7>
                <v-divider></v-divider>
                <h7>Referencias: {{ direccion.referencias }}</h7>
              </v-card-text>
              <v-card-actions>
                <v-row>
                  <v-col class="text-left" cols="6">
                    <v-icon
                      color="danger"
                      icon="mdi-delete"
                      @click="open(direccion.id)"
                    ></v-icon>
                  </v-col>
                  <v-col class="text-right" cols="6">
                    <v-icon @click="editDireccion(direccion)" icon="mdi-pencil"></v-icon>
                  </v-col>
                </v-row>
              </v-card-actions>
            </v-expansion-panel-text>
          </v-expansion-panel>
        </v-expansion-panels>
      </v-card-text>
      <v-card-actions v-if="addButtonIsVisible">
        <v-btn
          class="ma-2"
          color="#4D869C"
          @click="openDialog()"
        >añadir<v-icon icon="mdi-plus-box"></v-icon></v-btn>
      </v-card-actions>
      <p v-else>
        Parece ser que no has registrado ninguna dirección aun,
        <a href="javascript:void(0)" @click="openDialog()">haz click para añadir una</a>
      </p>
      <v-dialog v-model="dialog" class="text-center">
        <Direcciones @cancelado="cerrarDialogs" @añadido="getDirecciones" ></Direcciones>
      </v-dialog>
      <v-dialog v-model="editar" class="text-center">
 
          <EditarDireccion :direccion="selectedDireccion" @actualizado="getDirecciones()" @cancelado="cerrarDialogs()"></EditarDireccion>

      </v-dialog>
    </v-container>
  </template>
  
  <script setup>
  import Direcciones from "../Components/Direcciones.vue";
  import { ref } from "vue";
  import axios from "@/axios.js";
  import { useRouter } from 'vue-router';
  import { ElMessage, ElMessageBox } from "element-plus";
  import EditarDireccion from "./EditarDireccion.vue";
  
  const direcciones = ref([]);
  const dialog = ref(false);
  const router = useRouter();
  const editar = ref(false)
  const selectedDireccion = ref(null);  
  const addButtonIsVisible = ref(false);
  const isEditMode = ref(false);
  
  const getDirecciones = async () => {
    dialog.value = false;
    editar.value=false
    try {
      const { data } = await axios.get("/getDirecciones");
      direcciones.value = data;
      addButtonIsVisible.value = direcciones.value.length > 0;
    } catch (error) {
      console.error(error);
    }
  };
  
  getDirecciones();
  
  const eliminarDireccion = async (id) => {
 
      await axios.post("/eliminarDireccion", { id });
      then(() => {
        direcciones.value = direcciones.value.filter((direccion) => direccion.id !== id);
        addButtonIsVisible.value = direcciones.value.length > 0;
      })

  };
  
  const open = (id) => {
    ElMessageBox.confirm(
      "¿Está seguro de que desea eliminar esta dirección permanentemente?",
      "Advertencia",
      {
        confirmButtonText: "OK",
        cancelButtonText: "Cancelar",
        type: "warning",
      }
    )
      .then(() => {
        ElMessage({
          type: "success",
          message: "Dirección eliminada",
        });
        eliminarDireccion(id);
      })
      .catch(() => {
        ElMessage({
          type: "info",
          message: "Eliminación cancelada",
        });
      });
  };

const cerrarDialogs = () => {
  if(dialog.value === true){
    dialog.value = false
  }
  if(editar.value === true){
    editar.value = false
  }
}
  
  const openDialog = () => {
    selectedDireccion.value = null;
    dialog.value = true;
  };
  
  const editDireccion = (direccion) => {
    selectedDireccion.value = direccion
    editar.value = true
  };
  </script>
  