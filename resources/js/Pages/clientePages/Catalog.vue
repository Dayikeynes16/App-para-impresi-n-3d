<template>
    <v-container>
     
            <v-row>
                <v-col v-for="imagen in imagenes" :key="imagen.id" cols="4">
                    
                    <el-image
                            :src="imagen.urls[0]"
                            style="height: 300px; width: 100%; "
                            :preview-src-list="imagen.urls"
                            :z-index="2000"
                            fit="cover"
                            class= "rounded-t"
                        ></el-image>
                    
                    <v-card color="grey-lighten-4" elevation="1">
                        
                        <v-card-title>
                            <v-row>
                                <v-col cols="8"
                                    ><div class="font-weight-regular">
                                        {{ imagen.name }}
                                    </div></v-col
                                >
                                <v-col cols="4">
                                    <div class="font-weight-normal">
                                        {{
                                            Intl.NumberFormat("es-MX", {
                                                style: "currency",
                                                currency: "MXN",
                                                minimumFractionDigits: 2,
                                            }).format(imagen.price)
                                        }}
                                    </div>
                                </v-col>
                            </v-row>
                        </v-card-title>
                        <p class="mx-6" style="color: gray;">{{ imagen.description }}</p>

                        <v-card-actions>
                            <v-row>
                                <v-col cols="8">
                                    <v-row>
                                        <v-col class="text-center" cols="3"
                                            ><v-icon
                                                @click="
                                                    restarProductoCantidad(
                                                        imagen
                                                    )
                                                "
                                                circle
                                                icon="mdi-minus"
                                            ></v-icon
                                        ></v-col>
                                        <v-col
                                            class="text-center"
                                            cols="3"
                                            >{{ imagen.cantidad }}</v-col
                                        >
                                        
                                        <v-col class="text-center" cols="3"
                                            ><v-icon
                                                @click="
                                                    sumarProductoCantidad(
                                                        imagen
                                                    )
                                                "
                                                circle
                                                icon="mdi-plus"
                                            ></v-icon
                                        ></v-col>
                                    
                                    </v-row>
                                
                                </v-col>
                               
                                <v-col class="text-right" cols="4">
                                    <v-icon
                                        @click="
                                            añadirCarrito(
                                                imagen.id,
                                                imagen.cantidad
                                            )
                                        "
                                        icon="mdi-cart"
                                        color="primary"
                                    ></v-icon
                                ></v-col>
                            </v-row>
                        </v-card-actions>
                    </v-card>
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
            

    </v-container>
</template>

<script setup>
import axios from "@/axios.js";
import { ref, watch, onMounted } from "vue";
import { useCartStore } from "@/stores/carrito";
import { ElMessage } from 'element-plus'

const paginastotales = ref(1);
const imagenes = ref([]);
const cantidad = ref(2);
const paginate = ref({
    perPage: null,
    from: null,
    to: null,
    total: null,
    current_page: null
});
const cartStore = useCartStore();


const getProducts = async (filters = {}) => {
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

const añadirCarrito = async (id, cantidad) => {
    await cartStore.addToCart(id, cantidad)
    .then(()=> {
        ElMessage({
            message: 'Producto añadido',
            type: 'success',
        })
    })
};



const restarProductoCantidad = (imagen) => {
    if (imagen.cantidad > 1) {
        imagen.cantidad--;
    } else {
        
    }
};

const sumarProductoCantidad = (imagen) => {
    imagen.cantidad++;
};

onMounted(() => {
    getProducts();
});
</script>
