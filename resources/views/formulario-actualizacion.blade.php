<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
        <v-dialog v-model="dialog" max-width="500px">
            <v-card >
                <v-card-text align=center>
                    <v-row>
                        <v-col cols="12" class="mt-6 mb-6">
                        <i class="fa-solid fa-7x fa-circle-check " style="color: #63E6BE; width: 300px"></i>
                        </v-col>
                        <v-col cols="12" class="mt-6 mb-6">
                            <v-card-title>La contraseña se ha guardado correctamente</v-card-title>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-btn >Login</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="token_expired" max-width="500px">
            <v-card >
                <v-card-text align=center>
                    <v-row>
                        <v-col cols="12" class="mt-6 mb-6">
                        <i class="fa-solid fa-triangle-exclamation fa-7x"  style="color: #FFAF00;"></i>                        </v-col>
                        <v-col cols="12" class="mt-6 mb-6">
                            <v-card-title>El token ha expirado</v-card-title>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-btn variant="outlined" color="primary" href="/logear">Login</v-btn>
                </v-card-actions>
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
const vuetify = createVuetify();
const app = createApp({
    setup(){
        const form = ref({
            email: "{{$email}}",
            password: null,
            token: "{{$token}}",
            password_confirmation: null
        })
        const errorMessage = ref([])
        const dialog = ref(false)
        const token_expired = ref(false);

        const changePassword = async () => {
            await axios.post('/actualizar-contrasenia', form.value, { headers: { "X-CSRF-TOKEN": "{{csrf_token()}}" } })
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
            dialog,
            token_expired
        }
    }
})
app.use(vuetify).mount('#app')

</script>

    
</body>
</html>
