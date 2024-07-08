<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-stepper v-model="step" :items="items" hide-actions>
          <template  v-slot:item.1>
                <v-card>
                  <v-card-title>
                    Ingresa los datos del modelo
                  </v-card-title>
                  <v-form @submit.prevent="savemodel">
                    <v-text-field v-model="form.name" label="Nombre del modelo" required
                      :error-messages="errorMessages.name">
                    </v-text-field>
    
                    <v-textarea required v-model="form.description" label="Añade una descripción"
                      :error-messages="errorMessages.description">
                    </v-textarea>
    
                    <v-text-field v-model="form.price" label="Ingresa el precio" :error-messages="errorMessages.price">
                    </v-text-field>
                    <v-card-actions >
                      <v-btn type="submit" class="" color="blue" prepend-icon="mdi-arrow-right" variant="tonal" >
                        Siguiente 
                      </v-btn>
                    </v-card-actions>
                  </v-form>
                </v-card>
          </template>
          <template v-slot:item.2>
            <v-row>
              <v-col cols="6">

             
              <v-card-title>Archivos del producto</v-card-title>
              <v-card-text>
                  <el-upload class="upload-demo" drag :http-request="guardarSTL" ref='loadform'
                    accept=".stl" :headers="{
                      'X-CSRF-TOKEN': token
                    }" :auto-upload="true">
                    <el-icon class="el-icon--upload"><upload-filled /></el-icon>
                    <div class="el-upload__text">
                      Arrastra tu archivo o <em>haz click para subir</em>
                    </div>
                    <template #tip>
                      <div class="el-upload__tip">
                        Archivos de imagen de menos de 30 MB
                      </div>
                    </template>
                  </el-upload>
                </v-card-text>
              </v-col>
              <v-col cols="6">
                <v-card>
                  <v-card-title>Archivos</v-card-title>
                  <v-list v-for="archivo in archivos">
                          <v-list-item class="text-right">
                              <v-row>

                              <v-col cols="6">
                                  {{ archivo.nombre }}
                              </v-col>

                              <v-col cls="6">
                                  <v-icon
                                      icon="mdi-Delete"
                                      @click="
                                          eliminarArchivo(archivo.id)
                                      "
                                  >
                                  </v-icon>
                              </v-col>
                          </v-row>

                          </v-list-item>
                  </v-list>
              </v-card>


              </v-col>


           

            </v-row>
            <v-row>
              <v-col cols="12">
                <v-card-actions >
                  <v-col cols="10"></v-col>
                  <v-col cols="2">
                    <v-btn @click="siguientePaso()">Siguiente</v-btn>
                  </v-col>
                </v-card-actions>
              </v-col>
            </v-row>
        
             
         
   
          </template>
          <template v-slot:item.3>
            <v-row>
              <v-col cols="6">
                <v-container>
                  <v-card-title>
                  Imagenes del producto
                  </v-card-title>
                  <v-card-text>
                    <el-upload class="upload-demo" drag :http-request="guardarImagen" ref='loadform'
                      accept=".jpeg,.jpg,.png,.gif" :headers="{
                        'X-CSRF-TOKEN': token
                      }" :auto-upload="true">
                      <el-icon class="el-icon--upload"><upload-filled /></el-icon>
                      <div class="el-upload__text">
                        Arrastra las imagenes del producto o <em>haz click para subir</em>
                      </div>
                      <template #tip>
                        <div class="el-upload__tip">
                          Archivos de imagen de menos de 30 MB
                        </div>
                      </template>
                    </el-upload>
                  </v-card-text>
         
                </v-container>

              </v-col>
              <v-col cols="6">
                <div v-if="visible">
                  <v-row>
                    <v-col cols="3" v-for="imagen in Producto.imagenes" >
                      <v-card>
                   <v-card-text>
                    <v-img :src="imagen.url"></v-img>
                   </v-card-text>
                   <v-card-actions>
                    <v-btn @click="eliminarImagen(imagen.id)"><v-icon  icon="mdi-Delete"></v-icon></v-btn>
                    
                   </v-card-actions>
                </v-card>
                
        
                    </v-col>
        
                  </v-row>
                  
                </div>

              </v-col>
            </v-row>
            <v-card-actions>
              <v-btn @click="router.push({name: 'editarcatalogo'})">Finalizar</v-btn>
            </v-card-actions>

            
          </template>

        </v-stepper>

      </v-col>
      <v-col cols="8">
        
      </v-col>

    </v-row>


  </v-container>
</template>

<script setup>
import { UploadFilled } from '@element-plus/icons-vue'
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
const router = useRouter();

const token = document.querySelector("meta[name='csrf-token']").getAttribute('value');
const errorMessages = ref({});
const items = ref(['Información', 'Archivos', 'Fotos']);
const step = ref(1);
const archivos = ref([])


const visible = ref(false)

const form = ref({
  name: null,
  description: null,
  price: null
});

const siguientePaso = () =>{
  step.value = step.value + 1
}


const Producto = ref({
  producto_id: null,
  imagenes: []
});

const savemodel = async () => {
  try {
    const { data } = await axios.post('/savemodel', form.value, {
      headers: {
        'X-CSRF-TOKEN': token
      }
    });

    if (data.code === 200) {
      console.log(data.data);
      Producto.value.producto_id = data.data.id;
      step.value = 2;
    }
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errorMessages.value = error.response.data.errors;
    }
  }
};

const guardarImagen = async (file) => {

  const formData = new FormData();
  formData.append('image', file.file);
  formData.append('producto_id', Producto.value.producto_id);

  try {
    const { data } = await axios.post('/guardarImagen', formData, {
      headers: {
        'X-CSRF-TOKEN': token,
        'Content-Type': 'multipart/form-data'
      }
    });

    Producto.value.imagenes.push(data.data)

    visible.value= true

  } catch (error) {
    console.error(error);
  }
};

const guardarSTL = async (file) => {
    const formData = new FormData();
    formData.append("file", file.file);
    formData.append("producto_id", Producto.value.producto_id);

    try {
        const { data } = await axios.post("/guardarSTLproducto", formData, {
            headers: {
                "X-CSRF-TOKEN": token,
                "Content-Type": "multipart/form-data",
            },
        });
        if (data.data === 200) {
            await traerArchivos(Producto.value.producto_id);
        }
    } catch (error) {
        console.error(error);
    }
};

const traerArchivos = async (id) => {
    try {
        const { data } = await axios.get("/traerArchivos", { params: { id } });
        archivos.value = data;
    } catch (error) {
        console.error("Error fetching data:", error);
    }
};

const eliminarArchivo = async (id) => {
    try {
        await axios.post(
            "/eliminarArchivo",
            { id },
            { headers: { "X-CSRF-TOKEN": token } }
        );
        archivos.value = archivos.value.filter((file) => file.id !== id);
    } catch (error) {
        alert("Error al eliminar el archivo");
    }
};

const eliminarImagen = async(id) =>{
  try {
    await axios.post('/eliminarImagen',{id},{headers:{
      'X-CSRF-TOKEN': token}
  })
  Producto.value.imagenes = Producto.value.imagenes.filter(imagen => imagen.id !== id)
  } catch (error) {
    alert('error al eliminar el archivo')
  }
}
onMounted(()=>{
  traerArchivos(Producto.producto_id)

})
</script>