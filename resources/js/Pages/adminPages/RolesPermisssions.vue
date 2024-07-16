<template>
    <v-layout class="overflow-visible">
        <v-bottom-navigation active>
            <v-btn
                @click="openAddRoleDialog"
                icon="mdi-add"
                v-text="'Nuevo Rol'"
                size="auto"
            ></v-btn>
            <v-btn
                style="margin-left: 30px"
                icon="mdi-add"
                v-text="'Crear Usuario'"
                size="auto"
                @click="openCreateUserDialog = true"
            ></v-btn>
        </v-bottom-navigation>
    </v-layout>
    <v-container fluid>
        <v-row>
            <v-col cols="4">
                <v-card>
                    <v-card-title>
                        Usuarios y sus roles asignados
                    </v-card-title>
                    <v-card-text>
                        <v-expansion-panels variant="inset">
                            <v-expansion-panel
                                v-for="user in users"
                                :key="user.id"
                            >
                                <v-expansion-panel-title>
                                    {{ user.name }}
                                </v-expansion-panel-title>
                                <v-expansion-panel-text>
                                    <v-list dense>
                                        <v-list-item
                                            v-for="role in user.roles"
                                            :key="role.id"
                                        >
                                            <v-list-item-content>
                                                <v-card-text>
                                                    <v-list-item-title> Email:  {{ user.email }}</v-list-item-title>

                                                    <v-list-item-title>Rol: {{ role.name }}</v-list-item-title>
                                                </v-card-text>
                                               
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-card-actions>
                                            <v-row>
                                                <v-col cols="4" class="text-left">
                                                    <v-icon icon="mdi-pencil"  color="primary"></v-icon>
                                                </v-col>
                                                <v-col cols="4"></v-col>
                                                <v-col cols="4" class="text-right">
                                                    <v-icon @click="deleteUserWithRoles(user.id)" icon="mdi-delete" color="danger"></v-icon>

                                                </v-col>

                                            </v-row>
                                                    
                                                </v-card-actions>
                                    </v-list>
                                </v-expansion-panel-text>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col cols="4">
                <v-card color="grey-lighten-3">
                    <v-card-title class="headline">Roles</v-card-title>
                    <v-card-subtitle>
                        Podrás ver los roles existentes junto a los permisos otorgados
                    </v-card-subtitle>

                    <v-card-text v-if="noRoles">
                        Parece que no has creado ningún Rol aún
                    </v-card-text>

                    <v-expansion-panels variant="inset">
                        <v-expansion-panel v-for="role in roles" :key="role.id">
                            <v-expansion-panel-title color="grey-lighten-3">
                                <v-row no-gutters>
                                    <v-col cols="6">{{ role.name }}</v-col>
                                </v-row>
                            </v-expansion-panel-title>
                            <v-expansion-panel-text>
                                <v-card elevation="0">
                                    <v-card-text>
                                        <v-list>
                                            <v-list-item
                                                v-for="permission in role.permissions"
                                                :key="permission.id"
                                            >
                                                {{ permission.name }}
                                            </v-list-item>
                                        </v-list>
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-row>
                                            <v-col cols="8"></v-col>
                                            <v-col cols="2" class="text-right">
                                                <v-icon
                                                    size="x-large"
                                                    color="red"
                                                    @click="deleteRole(role.id)"
                                                >
                                                    mdi-delete
                                                </v-icon>
                                            </v-col>
                                            <v-col cols="2" class="text-center">
                                                <v-icon
                                                    size="x-large"
                                                    color="primary"
                                                >
                                                    mdi-pencil
                                                </v-icon>
                                            </v-col>
                                        </v-row>
                                    </v-card-actions>
                                </v-card>
                            </v-expansion-panel-text>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-card>
            </v-col>

            <v-col cols="4">
                <v-card color="grey-lighten-3">
                    <v-card-title class="headline">Todos los Permisos</v-card-title>
                    <v-expansion-panels variant="inset">
                        <v-expansion-panel>
                            <v-expansion-panel-title>
                                Lista de Permisos
                            </v-expansion-panel-title>
                            <v-expansion-panel-text>
                                <v-list dense>
                                    <v-list-item
                                        v-for="permission in permissions"
                                        :key="permission.id"
                                    >
                                        {{ permission.name }}
                                    </v-list-item>
                                </v-list>
                            </v-expansion-panel-text>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-card>
            </v-col>
        </v-row>
    </v-container>

    <v-dialog v-model="addRoleDialog">
        <AddRole @añadido="fetchRoles"></AddRole>
    </v-dialog>
    <v-dialog v-model="openCreateUserDialog">
        <Usuarios @añadido="fetchUsersRoles" :roles="roles"></Usuarios>
    </v-dialog>
    <v-dialog v-model="openEditUserDialogVisible">
        <EditRole @añadido="fetchUsersRoles" :user="userToEdit" :roles="roles"></EditRole>
    </v-dialog>
</template>

<script setup>
import { ElMessage, ElMessageBox } from "element-plus";
import axios from "axios";
import { ref, onMounted } from "vue";
import AddRole from "../../Components/AddRole.vue";
import { useRouter } from "vue-router";
import Usuarios from "../adminPages/Usuarios.vue";
import EditRole from "../../Components/EditRole.vue";
const router = useRouter();
const token = document.querySelector("meta[name='csrf-token']").getAttribute("content");

const roles = ref([]);
const openCreateUserDialog = ref(false);
const permissions = ref([]);
const addRoleDialog = ref(false);
const noRoles = ref(false);
const users = ref([]);
const userToEdit = ref({});
const openEditUserDialogVisible = ref(false);

const fetchRoles = async () => {
    addRoleDialog.value = false;
    const { data } = await axios.get("/roles");
    roles.value = data.data;
    noRoles.value = roles.value.length === 0;
};

const fetchUsersRoles = async () => {
    const { data } = await axios.get("/getUsersRoles");
    users.value = data.data;
    openEditUserDialogVisible.value = false
    openCreateUserDialog.value = false


};

const fetchPermissions = async () => {
    const { data } = await axios.get("/permissions");
    permissions.value = data.data;
};

const deleteRole = async (id) => {
    try {
        await axios.delete(`/roles/${id}`);
        roles.value = roles.value.filter((role) => role.id !== id);
    } catch (error) {
        console.error("Error deleting role:");
    }
};

const openAddRoleDialog = () => {
    addRoleDialog.value = true;
};

const openEditUserDialog = (user) => {
    userToEdit.value = user;
    openEditUserDialogVisible.value = true;
};

const deleteUserWithRoles = async (user) => {
    try {
        const {data} = await axios.post(`/deleteUser/${user}`,
      {
        headers: { "X-CSRF-TOKEN": token }
      } )
      await fetchUsersRoles()
    } catch (error) {
        
    }
}

onMounted(() => {
    fetchRoles();
    fetchPermissions();
    fetchUsersRoles();
});

</script>