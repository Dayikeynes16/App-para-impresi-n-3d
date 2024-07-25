<template>
    <v-container>
        <v-row>

                    <v-col v-for="imagen in imagenes" :key="imagen.id" cols="4">
                      
                        <el-image
                                :src="imagen.urls[0]"
                                style="height: 300px; width: 100%; "
                                :preview-src-list="imagen.urls"
                                :z-index="1000"
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
                            <v-card-subtitle
                                
                            >{{ imagen.description }}</v-card-subtitle>

                            <v-card-actions>
                                <v-row>
                                    <v-col cols="6">
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

                    <v-col cols="12">
                        <v-pagination
                            v-model="page"
                            :length="paginastotales"
                        ></v-pagination>
                    </v-col>

        </v-row>
    </v-container>
</template>

<script setup>
import axios from "@/axios.js";
import { ref, watch, onMounted } from "vue";
import { useCartStore } from "@/stores/carrito";
const page = ref(1);
const paginastotales = ref(1);
const imagenes = ref([]);
const cantidad = ref(2);

const cartStore = useCartStore();

const modelos = async () => {
    const { data } = await axios.get(`/modelos?page=${page.value}`);
    imagenes.value = data.data.map((imagen) => {
        return {
            ...imagen,
            urls: imagen.imagenes.map((imagen) => imagen.url),
            cantidad: 1,
        };
    });
    page.value = data.current_page;
    paginastotales.value = data.last_page;
};

const añadirCarrito = async (id, cantidad) => {
    await cartStore.addToCart(id, cantidad);
};

watch(
    () => page.value,
    async () => {
        await modelos();
    }
);

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
    modelos();
});
</script>
