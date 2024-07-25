<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <v-card rounded="lg" title="Pedidos Pendientes">
                    <v-card-text>
                        <PedidosPendientesList></PedidosPendientesList>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import PedidosPendientesList from "../../Components/PedidosPendientesList.vue";





</script>
