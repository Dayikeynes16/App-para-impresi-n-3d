<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card title="Pedidos Finalizados">
          <v-card-text>
            <v-expansion-panels>
              <v-expansion-panel v-for="item in Pedidos" :key="item.id">
                <v-expansion-panel-title>
                  Pedido #{{ item.id }} - Total: {{ formatCurrency(item.total) }} - Estado: {{ item.status }}
                </v-expansion-panel-title>
                <v-expansion-panel-text>
                  <v-list>
                    <v-list-item>
                      <v-list-item-content>
                        <v-list-item-title>Fecha de Creación: {{ dayjs(item.created_at).format('MM/DD/YYYY') }}</v-list-item-title>
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
                            <v-list-item-subtitle>Precio Unitario: {{ formatCurrency(producto.total / producto.cantidad) }}</v-list-item-subtitle>
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
              :items="[ 10, 25, 50]"
              density="compact"
              variant="outlined"
              @update:model-value="obtenerHistorial({ perPage: paginate.perPage })"
            ></v-select>
          </v-col>
          <v-col cols="4" class="text-center">
            <v-pagination
              variant="outlined"
              v-model="paginate.page"
              :length="paginate.pageCount"
              @update:model-value="obtenerHistorial({ page: paginate.page })"
            ></v-pagination>
          </v-col>
          <v-col cols="4" class="text-center">
            <div>
              <v-card-subtitle>
                Mostrando: {{ paginate.from }} a {{ paginate.to }} de {{ paginate.totalItems }} elementos.
              </v-card-subtitle>
            </div>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>
  
<script setup>
import axios from '@/axios';
import { ref, onMounted } from 'vue';
import formatCurrency from '../../composables/formatNumberToCurrency';
import dayjs from 'dayjs';

const paginate = ref({
    perPage: 10, // Número de elementos por página
    page: 1, // Página actual
    totalItems: 0, // Total de elementos
    pageCount: 0, // Total de páginas
    from: 0,
    to: 0
});
const Pedidos = ref([]);

const obtenerHistorial = (params = {}) => {
    const queryParams = {
        page: params.page || paginate.value.page,
        perPage: params.perPage || paginate.value.perPage
    };

    axios.get('/carrito/userHistorial', { params: queryParams })
    .then(({ data }) => {
        Pedidos.value = data.data;
        paginate.value.totalItems = data.total;
        paginate.value.pageCount = data.last_page;
        paginate.value.page = data.current_page;
        paginate.value.from = data.from;
        paginate.value.to = data.to;
    });
};

onMounted(() => {
    obtenerHistorial();
});
</script>