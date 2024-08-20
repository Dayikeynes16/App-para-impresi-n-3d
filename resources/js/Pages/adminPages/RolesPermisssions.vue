<template>
    <v-container >
        <v-row>
            <v-col cols="6">
                <v-card color="">
                    <v-card-title class="headline">Roles</v-card-title>
                    <v-card-text v-if="noRoles">
                        Parece que no has creado ningún rol.
                    </v-card-text>

                    <v-expansion-panels variant="inset">
                        <v-expansion-panel v-for="role in roles" :key="role.id">
                            <v-expansion-panel-title color="">
                                <v-row no-gutters>
                                    <v-col cols="6">{{ role.name }}</v-col>
                                </v-row>
                            </v-expansion-panel-title>
                            <v-expansion-panel-text>
                                <v-card elevation="0">
                                    <v-card-text>
                                        <v-list lines="one">
                                            <v-list-item
                                                prepend-icon="mdi-lock-open-check-outline"
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
                                                    @click="openEditRoleDialog(role)"
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

            <v-col cols="6">
                <v-card >
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

    <v-dialog v-model="addRoleDialog" max-width="400">
        <AddRole  @añadido="fetchRoles()" @cancelado="addRoleDialog = false"></AddRole>
    </v-dialog>
  
    <v-dialog v-model="openEditRoleDialogVisible" max-width="400">
        <EditRole @añadido="fetchRoles()" :userPermission="userPermission" :role="rolToEdit" :permisos="permissions" @cancelado="openEditRoleDialog()"></EditRole>
    </v-dialog>
    <v-fab
        icon="mdi-plus"
        variant="tonal"
        absolute
        location="bottom end"
        app
        @click="openAddRoleDialog"
        size="70"
    ></v-fab>
</template>

<script setup>
import { ElMessage, ElMessageBox } from "element-plus";
import axios from "axios";
import { ref, onMounted } from "vue";
import AddRole from "../../Components/AddRole.vue";
import { useRouter } from "vue-router";
import EditRole from "../../Components/EditRole.vue";

const roles = ref([]);
const permissions = ref([]);
const addRoleDialog = ref(false);
const noRoles = ref(false);
const openEditRoleDialogVisible = ref(false);
const rolToEdit = ref({})

const userPermission = ref([])

const fetchRoles = async () => {
    axios.get("/roles")
    .then(({data}) => {
        roles.value = data.data;
        noRoles.value = roles.value.length === 0;
        openEditRoleDialogVisible.value = false
        addRoleDialog.value = false

    })
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

const openEditRoleDialog = (role) => {
    if(openEditRoleDialogVisible.value === false){
        openEditRoleDialogVisible.value = true
        rolToEdit.value = role
        userPermission.value = role.permissions.map(permission => permission.id)
    }else {
        openEditRoleDialogVisible.value = false
    }
};



onMounted(() => {
    fetchRoles();
    fetchPermissions();
});

</script>