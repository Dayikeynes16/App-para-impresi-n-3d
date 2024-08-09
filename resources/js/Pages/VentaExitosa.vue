<template>
    <v-container >
        <v-card >
            <v-card-title class="mt-10">
                <v-row class="text-center">
                    <v-col cols="12">
                        <v-icon color="success" size="85" icon="mdi-check-decagram"></v-icon>
                    </v-col>
                    <v-col cols="12">
                        <h2>¡Pago exitoso!</h2>
                    </v-col>
                </v-row>
            </v-card-title>
            <v-divider :thickness="2"></v-divider>
            <v-card-text>
                <v-row class="text-center mb-10">
                    <v-col cols="6"><h3>ID de compra:</h3></v-col>
                    <v-col cols="6"><strong><h3>#{{ orden.id }}</h3></strong></v-col>
                    <v-col cols="6"><h3>Total:</h3></v-col>
                    <v-col cols="6"><h3>{{ formatCurrency(orden.total) }}</h3></v-col>
                    <v-col cols="6"><h3>Estatus:</h3></v-col>
                    <v-col cols="6"><strong><h3>Pagado</h3></strong></v-col>
                    <v-col cols="12">
                        <v-card-subtitle>
                            <h4>Se te enviará un correo con información pertinente</h4>
                        </v-card-subtitle>
                    </v-col>
                    <v-col cols="12"><v-btn variant="outlined" color="success" @click="router.push({name: 'cotizar'})">Regresar al inicio</v-btn></v-col>
                   
                </v-row>
            </v-card-text >
        </v-card>
    </v-container>
</template>

<script setup>
import axios from '@/axios'
import { onMounted, ref } from 'vue';
import { useCartStore } from '../stores/carrito';
import formatCurrency from '../composables/formatNumberToCurrency';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const cartStore = useCartStore();
const direccion = ref({})
const orden = ref({})

const traerCarrito = () => {
    const cartId = route.params.cart_id;
    axios.get(`/carrito/ventaConfirmada/${cartId}`)
    .then(({data}) => {
        orden.value = data.data;
        direccion.value = data.direccion;
    })
    .catch(error => {
        console.error("Error al obtener los detalles del carrito:", error);
    });
}

onMounted(() => {
    traerCarrito();
});
</script>