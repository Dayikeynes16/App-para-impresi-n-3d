<template>
    <v-container>
        <v-row>
            <v-col cols="6">
                <v-card elevation="8">
                    <v-card-item>
                        <v-card-title>
                            <v-avatar
                                ><v-icon icon="mdi-account-circle"></v-icon
                            ></v-avatar>
                            Información de la cuenta
                        </v-card-title>
                        <v-card-text>
                            <v-list lines="two">
                                <v-list-item
                                    :title="user.name"
                                    subtitle="Nombre del usuario"
                                ></v-list-item>
                                <v-list-item
                                    :title="user.email"
                                    subtitle="Correo Electrónico"
                                ></v-list-item>
                                <v-list-item
                                    :title="dayjs(user.created_at).format('DD/MM/YYYY')"
                                    subtitle="Fecha de registro"
                                ></v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card-item>
                </v-card>
            </v-col>
            <v-col cols="6">
                <v-card elevation="8" v-if="!isAdmin">
                    
                    <v-card-title v-text="'Tus direcciones:'"> </v-card-title>
                    <v-card-text>
                        <listaDirecciones></listaDirecciones>
                    </v-card-text>
                   
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref, onBeforeMount } from "vue";
import axios from "@/axios.js";
import listaDirecciones from "../../Components/DirectionsList.vue";
import dayjs from "dayjs";

const isAdmin = ref(false);
const user = ref({

});

const getUser = async () => {
    axios.get("/get_user")
    .then(({data}) => {
        user.value = data.data
        if(user.value.roles[0].name === 'admin'){
            isAdmin.value = true
        }
    });
};

onBeforeMount(() => {
    getUser()
})
</script>
