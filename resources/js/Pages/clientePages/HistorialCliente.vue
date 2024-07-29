<template>
    <v-container>
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
                      <v-list-item-subtitle>Usuario ID: {{ item.usuario_id }}</v-list-item-subtitle>
                    </v-list-item-content>
                  </v-list-item>
                  <v-list-item>
             
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
    </v-container>
  </template>
  
<script setup>
import axios from '@/axios';
import { ref, onMounted } from 'vue';
import formatCurrency from '../../composables/formatNumberToCurrency';
import dayjs from 'dayjs';
const Pedidos = ref([])
const obtenerHistorial = () => {
    axios.get('/carrito/userHistorial')
    .then(({data}) =>{
        Pedidos.value = data.data;
    })
}
onMounted(() => {
    obtenerHistorial()
})
</script>