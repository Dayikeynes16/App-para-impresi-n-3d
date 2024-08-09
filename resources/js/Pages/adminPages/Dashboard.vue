<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <v-card rounded="lg" title="Pedidos Pendientes">
                    <v-card-text>
                        <v-data-table-server
                            v-model:items-per-page="perPage"
                            :headers="headers"
                            :items="pedidos"
                            :items-length="totalItems"
                            :loading="loading"
                            :search="search"
                            item-value="name"
                            @update:options="loadItems"
                        >
                            <template v-slot:item.created_at="{ item }">
                                {{ dayjs(item.created_at).format('DD/MM/YYYY') }}
                            </template>
                            <template v-slot:item.detalles="{ item }">
                                <v-btn @click="router.push({ name: 'PedidoDetalle', params: { id: item.id } })" color="primary" variant="tonal" prepend-icon="mdi-information-outline">Detalles</v-btn>
                            </template>
                        </v-data-table-server>
                        
                    </v-card-text>
                    <v-card-actions>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import CantidadComponent from '../../Components/CantidadComponent.vue';
import axios from "@/axios.js";
import dayjs from "dayjs";
import { useRouter } from 'vue-router';
const router = useRouter();

import { ref, onMounted } from "vue";

const headers = ref([
    { title: "Pedido", key: "id" },
    { title: "Fecha", key: "created_at" },
    { title: "Correo", key: "usuario.email" },
    { title: "Estatus", key: "status" },
    { title: "Acciones", key: "detalles" }
]);
const paginate = ref({});
const loading = ref(false)
const pedidos = ref([]);
const totalItems = ref(0)
const perPage = ref(10)

const loadItems = async ({ page, itemsPerPage, sortBy }) => {
    loading.value = true
    await traerPendientes({
        page,
        itemsPerPage
    })
    loading.value = false
}

const traerPendientes = async (params = {}) => {
    axios.get("/getCarritosPendientes", { params })
    .then(({ data }) => {
        pedidos.value = data.data;
        totalItems.value = data.total
    });
};

</script>
