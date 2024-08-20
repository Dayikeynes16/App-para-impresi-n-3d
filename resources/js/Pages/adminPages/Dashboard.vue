<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <v-card rounded="lg" title="Pedidos Pendientes.">
                   
                    <v-card-subtitle>
                         Total por finalizar: <strong>  {{ formatCurrency(totalPayment) }} </strong>
                    </v-card-subtitle>

                    <v-card-text>
                        <template v-slot>
                            <v-text-field
                                v-model="search"
                                label="Buscar"
                                prepend-inner-icon="mdi-magnify"
                                variant="outlined"
                                hide-details
                                single-line
                            ></v-text-field>                            
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
                            <template v-slot:item.total="{ item }">
                                {{ formatCurrency(item.total) }}
                            </template>

                        </v-data-table-server>
                    </template>
                        
                    </v-card-text>
                    <v-card-actions>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import axios from "@/axios.js";
import dayjs from "dayjs";
import { useRouter } from 'vue-router';
import formatCurrency from '../../composables/formatNumberToCurrency';
const router = useRouter();

import { ref, onMounted } from "vue";

const headers = ref([
    { title: "# Orden", key: "id" },
    { title: "Fecha", key: "created_at" },
    { title: "Cliente", key: "usuario.name" },
    { title: "Correo", key: "usuario.email" },
    { title: "Estatus", key: "status" },
    { title: "Total", key: "total" },
    { title: "Acciones", key: "detalles" },
]);
const search = ref('');
const loading = ref(false)
const pedidos = ref([]);
const totalItems = ref(0)
const perPage = ref(10)
const totalPayment = ref(0);



const loadItems = async ({ page, itemsPerPage, sortBy }) => {
    loading.value = true;
    await traerPendientes({
        page,
        itemsPerPage,
        search: search.value 
    });
    loading.value = false;
};

const traerPendientes = async (params = {}) => {
    try {
        const { data } = await axios.get("/getCarritosPendientes", { params });
        pedidos.value = data.data;
        totalItems.value = data.total;
    } catch (error) {
        console.error("Error al cargar los pedidos:", error);
    }
};

onMounted(() => {
    getTotal();
    loadItems({ page: 1, itemsPerPage: perPage.value }); 
});

const getTotal = async () => {
    axios.get("/totalCarritosPendientes")
    .then((data) => {
        totalPayment.value = data.data.data

    })
    
};


</script>
