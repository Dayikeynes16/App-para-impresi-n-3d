<template>
            <v-data-table
            :headers="headers"
            :items="pedidos"
            >
            <template v-slot:item.created_at="{item}">
                {{ dayjs(item.created_at).format('DD/MM/YYYY') }}
            </template>
            <template v-slot:item.detalles="{item}">
                <v-btn @click="router.push({name: 'PedidoDetalle', params: {id : item.id}})" color="primary" variant="tonal" prepend-icon="mdi-information-outline">Detalles</v-btn>
            </template>
            </v-data-table>
</template>
<script setup>
import axios from "@/axios.js";
import dayjs from "dayjs";
import { useRouter } from 'vue-router';
const router = useRouter();

import { ref, watch, onMounted } from "vue";

const headers = ref([
    {title: "Pedido", key: "id"},
    {title: "Fecha", key: "created_at"},
    {title: "Correo", key: "usuario.email"},
    {title: "Estatus", key: "status"},
    {title: "Más Información", key: "detalles"}
])

const pedidos = ref([]);
const existenPedidos = ref(false);

const traerPendientes = async () => {
    const { data } = await axios.get("/getCarritosPendientes");
    pedidos.value = data;
    if (pedidos.value.length > 0) {
        existenPedidos.value = true;
    }
};

onMounted(() => {
    traerPendientes();
})

</script>

<style>
.tablaPersonalizada{
    background-color: #CFD8DC !important;
}

</style>