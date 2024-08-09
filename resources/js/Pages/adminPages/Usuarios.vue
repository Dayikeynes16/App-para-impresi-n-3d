<template>
    <v-row>
        <v-col :cols="12">
            <v-card 
                elevation="4"
                title="Cree un usuario"
                subtitle="añada los permisos">
                <v-card-text>
                    <v-form @submit.prevent="registrar">
                        <v-row>
                            <v-col cols="12">
                                <div class="text-subtitle-1 text-medium-emphasis">Nombre</div>
        
                                <v-text-field
                                    required
                                    :error-messages="errorMessages.name"
                                    v-model="form.name"
                                    density="compact"
                                    placeholder="Ingresa tu nombre"
                                    variant="outlined"
                                ></v-text-field>
        
                                <div class="text-subtitle-1 text-medium-emphasis">Correo</div>
        
                                <v-text-field
                                    :error-messages="errorMessages.email"
                                    v-model="form.email"
                                    density="compact"
                                    placeholder="Correo electrónico"
                                    prepend-inner-icon="mdi-email-outline"
                                    variant="outlined"
                                ></v-text-field>

                                <div class="text-subtitle-1 text-medium-emphasis">Telefono</div>
        
                                <v-text-field
                                    :error-messages="errorMessages.email"
                                    v-model="form.email"
                                    density="compact"
                                    placeholder="Telefono"
                                    prepend-inner-icon="mdi-phone-outline"
                                    variant="outlined"
                                ></v-text-field>
        
                                <div
                                    class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between"
                                >
                                    Contraseña
                                </div>
        
                                <v-text-field
                                    :error-messages="errorMessages.password"
                                    v-model="form.password"
                                    :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                                    :type="visible ? 'text' : 'password'"
                                    density="compact"
                                    placeholder="Ingresa una contraseña"
                                    prepend-inner-icon="mdi-lock-outline"
                                    variant="outlined"
                                    @click:append-inner="visible = !visible"
                                ></v-text-field>
        
                                <div
                                    class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between"
                                >
                                    Confirma tu contraseña
                                </div>
        
                                <v-text-field
                                    :error-messages="errorMessages.password_confirmation"
                                    v-model="form.password_confirmation"
                                    :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                                    :type="visible ? 'text' : 'password'"
                                    density="compact"
                                    placeholder="Re-ingresa tu contraseña"
                                    prepend-inner-icon="mdi-lock-outline"
                                    variant="outlined"
                                    @click:append-inner="visible = !visible"
                                ></v-text-field>
                                
                                <v-select 
                                    label="Escoja el rol" 
                                    :items="roles" 
                                    item-title="name" 
                                    item-value="name"
                                    v-model="form.role"
                                >
                                </v-select>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="6">
                                <v-btn
                                type="submit"
                                class="mb-8"
                                color="danger"
                                size="large"
                                variant="tonal"
                                block
                                @click="cancelar()"
                                >
                                Cancelar
                                </v-btn>
                                </v-col>
                            <v-col cols="6">
                                <v-btn
                                    type="submit"
                                    class="mb-8"
                                    color="blue"
                                    size="large"
                                    variant="tonal"
                                    block
                                >
                                    Crear
                                </v-btn>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="6">
            <v-card elevation="">  </v-card>
        </v-col>
    </v-row>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { defineProps } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const emit = defineEmits(["añadido", "cancelado"]);

const props = defineProps({
  roles: {
    type: Array,
    required: true
  }
});

const roles = ref([]);
const errorMessages = ref({});
const visible = ref(false);
const router = useRouter();

const form = ref({
    name: null,
    email: null,
    telefono: null,
    password: null,
    password_confirmation: null,
    role: null,
});

const registrar = async () => {
    try {
        const { data } = await axios.post("/crearUsuariosRoles", form.value);
        emit('añadido')
     
    } catch (error) {
        if (error.response.status === 422) {
            errorMessages.value = error.response.data.errors;
        }
    }
};
const cancelar = () => {
    emit("cancelado")
}
const getRoles = () => {
    axios.get("/roles")
    .then(({data}) => {
        roles.value = data.data;
    })
}


onMounted(() => {
    getRoles()
});
</script>