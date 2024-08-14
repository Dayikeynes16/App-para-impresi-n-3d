<template>
  <v-container class="mt-0 pt-0">
    <v-row class="mt-0 pt-0">
      <v-col class="mt-0 pt-0">
        <v-card class="mt-0 pt-0">
          <v-card-title>
            Historial de pedidos finalizados.
          </v-card-title>
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
                v-model:page="paginate.page"
                :headers="headers"
                :items="Pedidos"
                :items-length="totalItems"
                :loading="loading"
                :search="search"
                v-model:items-per-page="paginate.perPage"
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
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import axios from 'axios';
import { ref, onMounted } from 'vue';
import dayjs from 'dayjs';
import formatCurrency from '../../composables/formatNumberToCurrency';
import { useRouter } from 'vue-router';

const router = useRouter();
const search = ref('');
const headers = ref([
  { title: "# Orden", key: "id" },
  { title: "Fecha", key: "created_at" },
  { title: "Cliente", key: "usuario.name" },
  { title: "Telefono", key: "usuario.telefono" },
  { title: "Correo", key: "usuario.email" },
  { title: "Total", key: "total" },
  { title: "Acciones", key: "detalles" },
]);

const Pedidos = ref([]);
const totalItems = ref(0);
const paginate = ref({
  perPage: 10,
  page: 1,
});
const loading = ref(false);

const loadItems = async ({ page, itemsPerPage }) => {
  loading.value = true;
  await fetchPedidos({
    page: page || paginate.value.page,  
    itemsPerPage: itemsPerPage || paginate.value.perPage,  
    search: search.value
  });
  loading.value = false;
};

const fetchPedidos = async (params = {}) => {
  try {
    const { data } = await axios.get("/traerPedidosViejos", { params });
    Pedidos.value = data.data;
    totalItems.value = data.total;
    paginate.value.page = data.current_page; 
    paginate.value.perPage = params.itemsPerPage;  
  } catch (error) {
    console.error("Error al cargar los pedidos:", error);
  }
};


onMounted(() => {
  loadItems({ page: 1, itemsPerPage: paginate.value.perPage });
});
</script>
