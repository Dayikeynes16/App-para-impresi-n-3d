<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="//unpkg.com/element-plus/dist/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vuetify@3.6.14/dist/vuetify.min.css">
    <link href="https://cdn.jsdelivr.net/npm/vuetify@3.6.14/dist/vuetify.min.css">
</head>
<body>

<div id="app">
    <v-layout>
        <v-container align=center>
        <v-card-title  class="mt-10">Recuperar Contraseña</v-card-title>
        <v-card-subtitle class="mb-10">Ingresa una nueva contraseña</v-card-subtitle>
            <v-card variant="outlined" max-width="500px">

            
              <v-card-text>
               
                    <v-text-field :error-messages="errorMessage.password" v-model="form.password" label="Contraseña nueva" variant="outlined">
                    </v-text-field>
                    <v-text-field v-model="form.password_confirmation" label="Confirmar contraseña" variant="outlined">
                    </v-text-field>
                    <v-btn block variant="tonal" @click="changePassword()">Guardar Contraseña</v-btn>

              </v-card-text>
            </v-card>
        </v-container>
        <v-dialog v-model="dialog">
            <v-card >
                <v-card-text>
                
                    La contraseña se ha guardado correctamente
                </v-card-text>
            </v-card>
        </v-dialog>
       
    </v-layout>
</div>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify@3.6.14/dist/vuetify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios@1.6.7/dist/axios.min.js"></script>
<script src="//unpkg.com/element-plus"></script>
<script>

const { createApp, ref, onMounted, useRouter } = Vue
const { createVuetify } = Vuetify
const token = document.querySelector("meta[name='csrf-token']").getAttribute('content');
const vuetify = createVuetify();
const app = createApp({
    setup(){
        onMounted(() => {
            console.log(token);
        })   
        const form = ref({
            email: "{{$email}}",
            password: null,
            token: "{{$token}}",
            password_confirmation: null
        })
        const errorMessage = ref([])
        const dialog = ref(true)

        const changePassword = async () => {
            await axios.post('/actualizar-contrasenia', form.value, { headers: { "X-CSRF-TOKEN": token } })
            .then(({data})=>{
                if(data.data === 0){
                    alert('El token ha expirado')
                }else{
                    dialog.value = true
                }
            })
            .catch((error) => {
            errorMessage.value = error.response.data.errors;
            })

        }
        
        return {
            form,
            changePassword,
            errorMessage,
            dialog
        }
    }
})
app.use(vuetify).mount('#app')

</script>

    
</body>
</html>
