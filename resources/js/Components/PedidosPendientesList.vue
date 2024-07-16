<template>
    <v-card color="blue-grey-lighten-5" elevation="6">
        <v-card-title class="text-center">
            <v-text >Pedidos Pendientes</v-text>
        </v-card-title>
        <v-card-text v-if="existenPedidos">
            <v-table class="tablaPersonalizada">
                <thead >
                    <tr>
                        <th class="text-left">N. Orden</th>
                        <th class="text-left">Correo</th>
                        <th class="text-left">Estatus</th>
                        <th class="text-center"><v-icon icon="mdi-Card-Search"></v-icon></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="i in pedidos">
                        <td class="text-left">#{{ i.id }}</td>
                        <td class="text-left">{{ i.usuario.email }}</td>
                        <td class="text-left">{{ i.status }}</td>
                        <td class="text-center"><v-icon icon="mdi-dots-horizontal" @click="router.push({name: 'PedidoDetalle', params: {id : i.id}})" ></v-icon></td>
                    </tr>
                </tbody>
            </v-table>
        </v-card-text>
        <v-card-text v-else>
            <v-text>No hay pedidos pendientes</v-text>
        </v-card-text>
    </v-card>
</template>
<script setup>
import axios from "axios";
import { useRouter } from 'vue-router';
const router = useRouter();

import { ref, watch, onMounted } from "vue";

const pedidos = ref([]);
const existenPedidos = ref(false);

const traerPendientes = async () => {
    const { data } = await axios.get("/getCarritosPendientes");
    pedidos.value = data;
    if (pedidos.value.length > 0) {
        existenPedidos.value = true;
    }
};

traerPendientes();
</script>

<style>
.tablaPersonalizada{
    background-color: #CFD8DC !important;
}

</style>