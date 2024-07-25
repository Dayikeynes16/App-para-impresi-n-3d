<template>
    <v-container>
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

                                <v-expansion-panel-text>
                                    <v-list>
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
                                    <v-list>
                                        <v-list-item v-for="archivo in item.producto_carrito_archivos">
                                            <v-row>
                                                <v-col>
                                                    <div class="text-left">
                                                        {{ archivo.nombre }}
                                                    </div>
                                                </v-col>
                                                <v-col cols="6">
                                                    <div class="text-right">
                                                        <v-icon icon="mdi-download" @click="
                                                            downloadArchivo(
                                                                archivo.id,
                                                                archivo.nombre)
                                                        "></v-icon>
                                                    </div>
                                                </v-col>
                                            </v-row>
                                        </v-list-item>
                                    </v-list>
                                </v-expansion-panel-text>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-card-text>
                </v-card>

            </v-col>
            <v-col cols="4">
                <v-card>
                    <v-card-title>
                        Detalles
                    </v-card-title>

                    <v-card-subtitle>
                        Verifica la dirección
                    </v-card-subtitle>
                    <v-card-text v-if="sucursal">
                        Pedido Programado para su Recolección en sucursal
                    </v-card-text>
                    <v-card-text v-else>
                        <v-subheader>Método de Entrega</v-subheader>
                        <p>
                            <strong>Envío a Domicilio</strong><br>
                            <strong>dirección:</strong> {{ direccion.direccion }}<br>
                            <strong>Destinatario:</strong> {{ direccion.destinatario }}<br>
                            <strong>Estado:</strong> {{ direccion.estado }}<br>
                            <strong>Telefono:</strong> {{ direccion.telefono }}<br>
                            <strong>Codigo Postal:</strong> {{ direccion.codigo_postal }}<br>


                        </p>

                    </v-card-text>
                    <v-card-actions>
                        <v-btn @click="terminarTarea(carrito.id)" prepend-icon="mdi-check-outline">Finalizado</v-btn>

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

const token = document.querySelector("meta[name='csrf-token']").getAttribute('value');
const router = useRouter();
const route = useRoute();



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
        console.error("Error fetching data:", error);
    }
});


const downloadArchivo = async (id, nombre) => {
    try {
        const response = await axios.get(`/DownloadArchivo/${id}`, {
            responseType: "blob",
        });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", `${nombre}.stl`);
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
        link.setAttribute("download", `${nombre}.stl`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        console.error("Error downloading the file:", error);
    }
};

const terminarTarea = async (id) => {
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

</script>
