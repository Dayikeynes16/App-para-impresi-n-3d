<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card title="Pedidos Finalizados">
          <v-card-text>
            <v-expansion-panels>
              <v-expansion-panel v-for="item in Pedidos" :key="item.id">
                <v-expansion-panel-title>
                  Pedido #{{ item.id }}  <v-chip class="ms-5" color="success">{{ item.status }}</v-chip>
                </v-expansion-panel-title>
                <v-expansion-panel-text>
                  <v-list>
                    <v-list-item>
                      <v-list-item-content>
                        <v-list-item-title>
                          <v-row>
                            <v-col cols="10">Fecha de pedido: {{ dayjs(item.created_at).format('MM/DD/YYYY') }}</v-col>
                            <v-col cols="2"><v-icon class="cursor-pointer" @click="router.push({ name: 'PedidoDetalle', params: { id: item.id } })" icon="mdi-file-search-outline"></v-icon></v-col>
                          </v-row>
                         
                        </v-list-item-title>
                        <v-list-item-subtitle>Usuario ID: {{ item.usuario_id }}</v-list-item-subtitle>
                      </v-list-item-content>
                    </v-list-item>
                    <v-list-item-group>
                      <template v-for="(producto, index) in item.productos" :key="index">
                        <v-divider></v-divider>
                        <v-list-item>
                          <v-list-item-content>
                            <v-list-item-title>Producto: {{ producto.producto.name }}</v-list-item-title>
                            <v-list-item-subtitle>Descripción: {{ producto.producto.description }}</v-list-item-subtitle>
                            <v-list-item-subtitle>Cantidad: {{ producto.cantidad }}</v-list-item-subtitle>
                            <v-list-item-subtitle>Precio unitario: {{ formatCurrency(producto.total / producto.cantidad) }}</v-list-item-subtitle>
                            <v-list-item-subtitle>Total: {{ formatCurrency(producto.total) }}</v-list-item-subtitle>
                          </v-list-item-content>
                        </v-list-item>
                      </template>
                    </v-list-item-group>
                  </v-list>
                </v-expansion-panel-text>
              </v-expansion-panel>
            </v-expansion-panels>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12">
        <v-row align="center" class="mt-10">
          <v-col cols="2" class="text-center">
            <v-select
              label="Elementos por pagina:"
              v-model="paginate.perPage"
              :items="[10, 25, 50]"
              density="compact"
              variant="outlined"
              @update:model-value="fetchPedidos"
            ></v-select>
          </v-col>
          <v-col cols="2"></v-col>
          <v-col cols="4" class="text-center">
            <v-pagination
              variant="outlined"
              v-model="paginate.page"
              :length="paginate.pageCount"
              @update:model-value="fetchPedidos"
            ></v-pagination>
          </v-col>
          <v-col cols="4" class="text-center">
            <div variant="outlined">
              <v-card-subtitle>
                Mostrando: {{ paginate.from }} a {{ paginate.to }} de {{ paginate.total }} elementos.
              </v-card-subtitle>
            </div>
          </v-col>
        </v-row>
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

const Pedidos = ref([]);
const paginate = ref({
  perPage: 10, // Número de elementos por página por defecto
  page: 1, // Página actual por defecto
  total: 0, // Total de elementos
  from: 0,
  to: 0,
  pageCount: 0 // Total de páginas
});

const fetchPedidos = async () => {
  try {
    const { data } = await axios.get('/traerPedidosViejos', {
      params: {
        page: paginate.value.page,
        perPage: paginate.value.perPage
      }
    });
    
    Pedidos.value = data.data;
    paginate.value.total = data.total;
    paginate.value.from = data.from;
    paginate.value.to = data.to;
    paginate.value.pageCount = data.last_page;
    paginate.value.page = data.current_page;
  } catch (error) {
    console.error('Error fetching orders:', error);
  }
};

onMounted(() => {
  fetchPedidos();
});
</script>
