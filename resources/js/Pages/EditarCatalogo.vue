<template>
    <!-- <v-layout class="overflow-visible" style="height: 56px">
        <v-bottom-navigation v-model="value" active>
            <v-btn
            icon="mdi-add"
            v-text="'Añadir  Productos'"
            size="auto"
            @click="router.push({ name: 'GuardarProducto' })"
            ></v-btn>
        </v-bottom-navigation>
    </v-layout> -->
    <v-container>
        <v-row>
            <v-col cols="12">
                <v-row>
                    <v-col v-for="imagen in imagenes" cols="4">
                        <CardModelo
                            :item="imagen"
                            @eliminado="eliminar(imagen.id)"
                        ></CardModelo>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12">
                        <v-pagination
                            v-model="page"
                            :length="paginastotales"
                        ></v-pagination>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
        <v-fab
            icon="mdi-plus"
            variant="outlined"
            absolute
            location="bottom end"
            app
            size="70"
            @click="router.push({ name: 'GuardarProducto' })"
        ></v-fab>
    </v-container>
</template>

<script setup>
import axios from "@/axios.js";
import { onMounted, ref, watch } from "vue";
import { useRouter } from "vue-router";
const visible = ref(false);
const imagenes = ref([]);
const img = {};
const page = ref(1);
const paginastotales = ref(1);
import CardModelo from "./CardModelo.vue";
import GuardarProducto from "./GuardarProducto.vue";
import router from "../router";

const modelos = async () => {
    const { data } = await axios.get(`/modelos?page=${page.value}`);
    imagenes.value = data.data;
    page.value = data.current_page;
    paginastotales.value = data.last_page;
};

watch(
    () => page.value,
    async () => {
        await modelos();
    }
);

onMounted(() => {
    modelos();
});

const eliminar = (id) => {
    imagenes.value = imagenes.value.filter((imagen) => imagen.id != id);
};
</script>
