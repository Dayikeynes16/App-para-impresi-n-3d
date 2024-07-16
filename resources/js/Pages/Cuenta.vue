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
                            Informacion de la cuenta
                        </v-card-title>
                        <v-card-text>
                            Bienvenido {{ user.name }} <br />
                            Correo: {{ user.correo }}
                        </v-card-text>
                    </v-card-item>
                </v-card>
            </v-col>
            <v-col cols="6">
                <v-card elevation="8">
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
import { ref } from "vue";
import axios from "axios";
import Direcciones from "../Components/Direcciones.vue";
import listaDirecciones from "../Components/listaDirecciones.vue";
import router from "../router";

const user = ref({
    name: null,
    correo: null,
});

const get_user = async () => {
    const { data } = await axios.get("/get_user");
    user.value.name = data.name;
    user.value.correo = data.email;

};

get_user();
</script>
