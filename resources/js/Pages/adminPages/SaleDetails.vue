<template>
     <v-container class="" align="center" v-if="loading">
        <v-progress-circular indeterminate></v-progress-circular>
    </v-container>
    <v-container v-else>
        <v-row>
           
            <v-col cols="8">
                <v-card elevation="4">
                    <v-card-title> Productos </v-card-title>
                    <v-card-subtitle>
                        Abrir para ver los archivos correspondientes
                    </v-card-subtitle>
                    <v-card-text>
                        <v-expansion-panels v-for="item in productos" :key="item.producto.id">
                            <v-expansion-panel class="mb-1" elevation="0">
                                <v-expansion-panel-title>
                                    <v-row>
                                        <v-col cols="7">
                                            <div class="text-left">
                                                {{ item.producto.name }}
                                            </div>
                                        </v-col>
                                        <v-col cols="4">
                                            Piezas: {{ item.cantidad }}</v-col>
                                    </v-row>
                                </v-expansion-panel-title>

                                <v-expansion-panel-text class="ma-0 pa-0">
                                    <v-list class="ma-0 pa-0">
                                        <v-list-item v-for="file in item.producto.files" :key="file.id">
                                            <v-list-item-content>
                                                <v-row>
                                                    <v-col cols="6">
                                                        <div class="text-left">
                                                            {{ file.nombre }}
                                                        </div>
                                                    </v-col>

                                                    <v-col cols="6">
                                                        <div class="text-right">
                                                            <v-icon icon="mdi-download" @click="
                                                                download(
                                                                    file.id,
                                                                    file.nombre
                                                                )
                                                                "></v-icon>
                                                        </div>
                                                    </v-col>
                                                </v-row>
                                            </v-list-item-content>
                                        </v-list-item>
                                    </v-list>
                                    <v-list class="ma-0 pa-0">
                                        <v-list-item v-for="archivo in item.producto_carrito_archivos" class="ma-0 pa-0">
                                            <v-card-subtitle>
                                                <v-row class="ma-0 pa-0">
                                                
                                                <v-col cosl="7" class="ma-0 pa-0">
                                                    <div class="text-left">
                                                    {{ archivo.nombre }}
                                                    </div>
                                                </v-col >
                                                <v-col cols="3" class="ps-5">
                                                        {{ archivo.cantidad }}
                                                    </v-col>
                                                <v-col cols="2" class="ma-0 pa-0">
                                                    <div class="text-right">
                                                        <v-icon icon="mdi-download" @click="
                                                            downloadArchivo(
                                                                archivo.id,
                                                                archivo.nombre)
                                                        "></v-icon>
                                                    </div>
                                                </v-col>
                                            </v-row>
                                            </v-card-subtitle>
                                            
                                        </v-list-item>
                                    </v-list>
                                </v-expansion-panel-text>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-card-text>
                </v-card>

            </v-col>
            <v-col cols="4">
                <v-card class="pa-3">
                    <v-card-title>
                        Detalles
                    </v-card-title>

                    
                    <v-card-text v-if="sucursal" class="mt-1">
                        
                        Pedido programado para su recolección en sucursal.
                        <v-divider></v-divider>
                        
                        <v-row>
                            <v-col cols="6"> Recibe:</v-col>
                            <v-col cols="6">{{ carrito.usuario.name }}</v-col>
                            <v-col cols="6">Telefono: </v-col>
                            <v-col cols="6">{{ carrito.usuario.telefono}}</v-col>
                            <v-col cols="6">Email: </v-col>
                            <v-col cols="6">{{ carrito.usuario.email }}</v-col>
                            <v-col cols="6">Estatus: </v-col>
                            <v-col cols="6">{{ carrito.status }}</v-col>
                            <v-col cols="6">Total: </v-col>
                            <v-col cols="6">{{ formatCurrency(carrito.total) }}</v-col>
                        </v-row>
                    </v-card-text>
                    <v-card-text v-else>
                        <v-row align="center">
                            <v-col cols="6">
                                <strong>Método de entrega</strong>
                            </v-col>
                            <v-col cols="6">
                                <v-chip color="primary">Envío a domicilio</v-chip> 
                            </v-col>
                            <v-divider></v-divider>
                            <v-list lines="two">
                                <v-list-item :subtitle="`${direccion.direccion}, ${direccion.estado}, ${direccion.codigo_postal}` " title="Dirección"></v-list-item>
                                <v-list-item :subtitle="direccion.destinatario" title="Recibe"></v-list-item>
                                <v-list-item :subtitle="direccion.telefono" title="Teléfono"></v-list-item>
                                <v-list-item  title="Total">
                                    <v-list-item-subtitle>{{ formatCurrency(carrito.total) }}</v-list-item-subtitle>
                                </v-list-item>
                            </v-list>
                        </v-row>
                        

                    </v-card-text>
                    <v-card-actions>
                        <v-btn block v-if="carrito.status === 'Pago Confirmado'" variant="outlined" @click="PedidoListo(carrito.id)" prepend-icon="mdi-check-outline">Pedido Listo</v-btn> <br>
                        <v-btn block v-if="['Listo para recolectar', 'Listo Para Enviar'].includes(carrito.status)" variant="outlined" @click="terminarTarea(carrito.id)" prepend-icon="mdi-check-outline">Finalizar</v-btn>
                    </v-card-actions>
                </v-card>

            </v-col>
        </v-row>

    </v-container>
</template>

<script setup>
import { onMounted, ref } from "vue";
import axios from "axios";
import { useRouter, useRoute } from "vue-router";
import formatCurrency from "../../composables/formatNumberToCurrency";

const token = document.querySelector("meta[name='csrf-token']").getAttribute('value');
const router = useRouter();
const route = useRoute();


const loading =ref(false);
const files = ref([]);
const productos = ref([]);
const carrito = ref({});
const sucursal = ref(false);
const direccion = ref({})

onMounted(async () => {
    try {
        const { data } = await axios.get(`/carrito/${route.params.id}`);
        files.value = data.files;
        productos.value = data.productos;
        carrito.value = data.carrito;

        if (data.carrito.recoleccion) {
            sucursal.value = true
        } else {
            sucursal.value = false
            direccion.value = data.direccion
        }
    } catch (error) {
        router.push({name: 'cotizar'})
    }
});


const downloadArchivo = async (id, nombre) => {
    try {
        const response = await axios.get(`/DownloadArchivo/${id}`, {
            responseType: "blob",
        });
        console.log('holi');
        console.log(response);
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", `${nombre}`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        console.error("Error downloading the file:", error);
    }
};


const download = async (id, nombre) => {
    try {
        const response = await axios.get(`/DownloadFile/${id}`, {
            responseType: "blob",
        });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", `${nombre}`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        console.error("Error downloading the file:", error);
    }
};

const PedidoListo = async (id) => {
    try {
        const { data } = await axios.post(`/listoParaEnvio/${id}`, {
            headers: { "X-CSRF-TOKEN": token },
        });
        if (data.data === 'exito') {
            router.push({ name: 'Dashboard' });
        }
    } catch (error) {
        console.error("Error finalizando la tarea:", error);
    }
};

const terminarTarea = async (id) => {
    loading.value = true;
    axios.post(`/listoParaEnvio/${id}`, {status: 'Finalizado'})
    .then(() => {
        loading.value = false;
        router.push({name: 'Dashboard'});
    })
}


</script>
