<template>
    <v-container class="text-center">
      <v-card-text elevation="0" v-if="addButtonIsVisible">
        <v-row>
          <v-col cols="12">
                <v-expansion-panels >
              <v-expansion-panel v-for="direccion in direcciones" :key="direccion.id">
                <v-expansion-panel-title>
                  {{ direccion.nombre }}
                </v-expansion-panel-title>
                <v-expansion-panel-text align="left">
                    <v-list lines="one">
                      <v-list-item
                        :title="direccion.direccion"
                        subtitle="Dirección"
                      ></v-list-item>
                      <v-list-item
                        :title="direccion.destinatario"
                        subtitle="Destinatario"
                      ></v-list-item>
                      <v-list-item
                        :title="direccion.telefono"
                        subtitle="Teléfono"
                      ></v-list-item>
                      <v-list-item
                        :title="direccion.referencias"
                        subtitle="Referencias"
                      ></v-list-item>
                    </v-list>
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
                  
                </v-expansion-panel-text>
                
              </v-expansion-panel>
            </v-expansion-panels>
          </v-col>
          <v-col>
            <v-btn
              block
              variant="tonal"
              @click="openDialog()"
              >añadir<v-icon icon="mdi-plus-box"></v-icon></v-btn>
            </v-col>
        </v-row>
        
       
      </v-card-text>

      <p v-else>
        Parece ser que no has registrado ninguna dirección aun,
        <a href="javascript:void(0)" @click="openDialog()">haz click para añadir una</a>
      </p>
      
    
    </v-container>

    <v-dialog
     v-model="editar" 
     class="text-center"
     width="500"
    >
      <v-card
        prepend-icon="mdi-google-maps"
        title="Actualiza los campos necesarios"
      >
        <v-card-text>
            <EditarDireccion :direccion="selectedDireccion" @actualizado="getDirecciones()" @cancelado="cerrarDialogs()"></EditarDireccion>
        </v-card-text>
      
      </v-card>
    </v-dialog>
      
    <v-dialog
      v-model="dialog"
      width="500"
    >
      <v-card
        prepend-icon="mdi-google-maps"
        title="Agregar una dirección"
      >
        <v-card-text>
          <Direcciones @cancelado="cerrarDialogs" @añadido="getDirecciones()" ></Direcciones>
        </v-card-text>
      </v-card>
    </v-dialog>
  </template>
  
  <script setup>
  import Direcciones from "./AddDirectons.vue";
  import { ref } from "vue";
  import axios from "@/axios.js";
  import { useRouter } from 'vue-router';
  import { ElMessage, ElMessageBox } from "element-plus";
  import EditarDireccion from "./EditDirections.vue";
  
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
  