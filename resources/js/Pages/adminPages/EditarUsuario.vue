<template>
        <v-container>
            <v-row>
                <v-col cols="12">
                    <v-card class="pa-3">
                        <v-card-title>Actualiza los campos necesarios</v-card-title>
                        <v-card-text>
                            <v-text-field v-model="user.name" variant="outlined" label="Nombre"></v-text-field>
                            <v-text-field v-model="user.email" variant="outlined" label="Correo"></v-text-field>
                            <v-text-field v-model="user.password" variant="outlined" label="Contraseña"></v-text-field>
                            <v-select v-model="user.role" :items="roles" variant="outlined"></v-select>
                       </v-card-text>
                    <v-card-actions>
                        <v-btn variant="outlined" color="danger" @click="$emit('cerrar')" append-icon="mdi-close">cerrar</v-btn>
                        <v-spacer></v-spacer>
                        <v-btn variant="outlined" @click="update" append-icon="mdi-check" color="success">Guardar</v-btn>
                    </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
</template>

<script setup>
import { ref,onMounted } from 'vue';
import axios from '@/axios';
const emits = defineEmits(["cerrar"]);
const roles = ref([])
const user = ref({
    name: null,
    email: null,
    password: null,
    role: null
});

const props = defineProps({
    id: {
        type: Number,
        required: true
    }
})

const getUser = async () => {
    axios.get(`/get-user/${props.id}`)
    .then(({data}) => {
        user.value = data.data
        user.value.role = data.rol
    })
}

const getRoles = async () => {
    axios.get('/roles')
    .then(({data}) => {
        roles.value = data.data.map(item => item.name)
    })
}

const update = async () => {
    axios.post(`/update-user/${user.value.id}`, user.value)
    .then(() => {
        emits("cerrar")
    })
}


onMounted(() => {
    getUser()
    getRoles()
})

</script>