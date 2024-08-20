<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <v-card elevation="3">
                    <v-card-title>Usuarios</v-card-title>
                    <v-data-table :headers="headers" :items="users">
                        <template v-slot:item.roles="{item}">
                            <v-icon
                                icon="mdi-magnify"
                                @click="showRolesDialog(item.roles)">
                            </v-icon>
                        </template>
                        <template v-slot:item.eliminar="{item}">
                            <v-row>
                                <v-col cols="6">
                                    <v-icon icon="mdi-pencil" @click="showEditUserDialog(item)" color="success" ></v-icon>
                                </v-col>
                                <v-col cols="6">
                                    <v-icon @click="open(item.id)" icon="mdi-delete" color="danger"></v-icon>
                                </v-col>
                            </v-row>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
    </v-container>

    <v-dialog v-model="showRoles" max-width="400">
        <v-card>
            <v-card-text class="text-center">
                <v-row v-for="role in roles" >
                    <v-list> 
                            Role: <strong>{{ role.name }}</strong>
                    </v-list>
                </v-row>
            </v-card-text>
        </v-card>
    </v-dialog>

    <v-dialog max-width="600" v-model="showDialogUser">
        <Usuarios @cancelado="showUserDialog" @añadido="getUsers" ></Usuarios>
    </v-dialog>
    <v-dialog v-model="openDialogEditUser" @update:model-value="openDialogEditUser = false" max-width="600">
        <EditarUsuario :id="id" @cerrar="openDialogEditUser = false"></EditarUsuario>
    </v-dialog>


    <v-fab
        icon="mdi-plus"
        variant="tonal"
        absolute
        location="bottom end"
        app
        @click="showUserDialog()"
        size="70"
    ></v-fab>

</template>

<script setup>
import axios from '@/axios';
import { ref,onMounted } from 'vue';
import { ElMessage, ElMessageBox } from "element-plus";
import Usuarios from './AddUser.vue';
import EditarUsuario from './EditUser.vue';

const openDialogEditUser = ref(false);
const showDialogUser = ref(false);
const showRoles = ref(false)
const id = ref(0)
const roles = ref([])
const users = ref([])
const headers = ref([
    { title: "Usuario", key: "name" },
    { title: "Email", key: "email" },
    { title: "Rol", key: "roles" },
    { title: "Acciones", key: "eliminar" }
])

const getUsers = async () => {
    axios.get('/getUsersRoles')
    .then(({data}) => {
        users.value = data.data
    })
    showDialogUser.value = false
}
const open = async (id) => {
    try {
        const confirmed = await ElMessageBox.confirm(
            "Borraras permanentemente este producto, ¿Deseas continuar?",
            "Advertencia",
            {
                confirmButtonText: "Eliminar",
                cancelButtonText: "Cancelar",
                type: "warning",
            }
        );
        await deleteUser(id);
    } catch (error) {

    }
};

const showRolesDialog = (data) => {
    roles.value = data;
    showRoles.value = true;
}

const showEditUserDialog = (item) => {
    id.value = item.id
    openDialogEditUser.value = true
  
}



const showUserDialog = () => {
    if(showDialogUser.value === true){
        showDialogUser.value = false
    } else {
        showDialogUser.value = true

    }
    
}

const deleteUser = async (user) => {

    axios.post(`/deleteUser/${user}`)
    .then(() => {
        getUsers()
    })
    .catch ((error) => {
        console.log(error);
    }) 
}

onMounted(() => {
    getUsers()
})


</script>