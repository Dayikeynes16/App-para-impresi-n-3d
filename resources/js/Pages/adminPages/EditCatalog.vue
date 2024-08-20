<template>
    <v-container class="mt-0 pt-0 mb-10 pb-10">
        <v-card-title align="center">Editar Catalogo</v-card-title>
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
                <v-row align="center" class="mt-10">
                        <v-col cols="2" class="text-center">
                            <v-select
                                size=""
                                label="Elementos por pagina:"
                                v-model="paginate.perPage"
                                :items="[6, 12, 18, 50]"
                                density="compact"
                                variant="outlined"
                                hide-details="false"
                                @update:model-value="modelos({perPage: paginate.perPage})"
                            ></v-select>
                        </v-col>
                        <v-col cols="2"></v-col>
                        <v-col cols="4" class="text-center">
                            <v-pagination
                                variant="outlined"
                                v-model="paginate.page"
                                :length="paginate.pageCount"
                                :size="25"
                                @update:model-value="modelos({page: paginate.page, perPage: paginate.perPage})"
                            ></v-pagination>
                        </v-col>
                        <v-col cols="4" class="text-center">
                            <div variant="outlined">
                               <v-card-subtitle>
                                Mostrando: {{ paginate.from }} a {{ paginate.to }} de {{ paginate.totalItems }} elementos.
                               </v-card-subtitle>
                            </div>
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
const imagenes = ref([]);
const page = ref(1);
import CardModelo from "../../Components/Product.vue";
import router from "../../router";

const paginate = ref({
    perPage: null,
    from: null,
    to: null,
    total: null,
    current_page: null
});
const modelos = async (filters = {}) => {
    axios.get('/modelos',{params: filters})
    .then(({data}) => {
        imagenes.value = data.data.map((imagen) => {
            return {
                ...imagen,
                urls: imagen.imagenes.map((imagen) => imagen.url),
                cantidad: 1,
            };
        });
        paginate.value.perPage = data.per_page;
        paginate.value.page = data.current_page
        paginate.value.pageCount = data.last_page
        paginate.value.from = data.from ?? 0,
        paginate.value.to = data.to ?? 0,
        paginate.value.totalItems = data.total
        
    })
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
