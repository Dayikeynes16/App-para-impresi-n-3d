<template>
    <v-container>
        
        <div>
            <v-stepper
                color="grey-lighten-4"
                v-model="step"
                hide-actions
                :items="['Productos', 'Dirección', 'Pago']"
            >
                <template v-slot:item.1>
                    <v-card>
                        <v-container>
                            <v-divider></v-divider>

                            <v-data-table
                                :items="cartStore.items"
                                :headers="headers"
                                show-expand
                                no-data-text="Su carrito está vacío. Por favor, añada productos. "
                            >
                                <template  v-slot:item.precio="{item}">
                                    {{formatCurrency(item.precio)}}
                                </template>

                                <template v-slot:item.total="{item}">
                                    <td>{{ formatCurrency(item.total) }}</td>
                                </template>

                                <template v-slot:expanded-row="{ item }">
                                    <tr v-for="file in item.files" :key="file.name">
                                        <td class="pl-10">{{ file.nombre }}</td>
                                        <td>{{ formatCurrency(file.precio) }}</td>

                                        <td>
                                            <v-row>
                                                <v-col cols="12">
                                                    <v-row>
                                                            <v-btn @click="restarArchivo(file)"
                                                                variant="text"
                                                                density="comfortable"
                                                                color="primary"
                                                                icon="mdi-minus">
                                                            </v-btn> 
                                                        
                                                           <p  class="mx-3">{{ file.cantidad }}</p> 
                                                     
                                                            <v-btn @click="sumarArchivo(file)"
                                                                variant="text"
                                                                density="comfortable"
                                                                color="primary"
                                                                circle
                                                                icon="mdi-plus">
                                                        </v-btn>
                                                    
                                                    </v-row>      
                                                </v-col>
                                            </v-row>
                                        </td>

                                        <td>{{ formatCurrency(file.precio * file.cantidad) }}</td>
                                    </tr>
                                </template>


                                <template v-slot:item.cantidad="{ item }">
                                    <v-row v-if="item.files.length === 0">
                                        <v-btn
                                            variant="text"
                                            density="comfortable"
                                            color="primary"
                                            @click="restarCantidad(item)"
                                            icon="mdi-minus"
                                        ></v-btn>
                                        <p class="mx-3">{{ item.cantidad }}</p>
                                        <v-btn
                                            variant="text"
                                            density="comfortable"
                                            color="primary"
                                            @click="sumarCantidad(item)"
                                            icon="mdi-plus"
                                        ></v-btn>
                                    </v-row>
                                    <v-row v-if="item.files.length > 0">
                                        <p class="mx-3 text-center">{{ item.cantidad }}</p>
                                    </v-row>
                                </template>
                                <template v-slot:item.eliminar="{ item }">
                                    <v-icon
                                        color="red"
                                        icon="mdi-delete"
                                        @click="borrar(item)"
                                    ></v-icon>
                                </template>
                                <template v-slot:body.append>
                                    <tr class="bg-grey-lighten-3">
                                        <td  colspan="3">Total:</td>
                                        <td>
                                            {{ formatCurrency(cartStore.total) }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </template>
                            </v-data-table>
                            <v-card-actions>
                                <v-row>
                                    <v-col cols="12" class="d-flex justify-end">
                                        <v-icon
                                            @click="pasos(2)"
                                            icon="mdi-arrow-right"
                                        ></v-icon>
                                    </v-col>
                                </v-row>
                            </v-card-actions>
                        </v-container>
                    </v-card>
                </template>

                <template v-slot:item.2>
                    <ElegirDireccion @direccion="direccion = $event" @escogida="direccionSeleccionada" @pasos="pasos"></ElegirDireccion>
                </template>
                <template v-slot:item.3>
                    <v-row>
                        <v-col cols="7">
                            <v-row class="ps-15">
                            <v-col cols="12">
                                <h5>Detalles del envío</h5>
                            </v-col>
                            <v-col cols="12">
                                <v-row class="text-left">
                                    
                                    <v-row v-if="!direccion.es_recoleccion">
                                        <v-col cols="6">
                                        Tipo de envío: 
                                    </v-col>
                                    <v-col cols="6">
                                       <strong>Envío a domiclio</strong>
                                       
                                    </v-col>
                                        <v-col cols="6">
                                            Recibe: 
                                        </v-col>
                                        <v-col cols="6">
                                            <strong>{{ direccion.direccion.destinatario }}</strong>
                                        </v-col>
                                        <v-col cols="6">
                                            Dirección de envío:
                                        </v-col>
                                        <v-col cols="6">
                                            <strong>{{ direccion.direccion.direccion }}</strong>
                                        </v-col>
                                        <v-col cols="6">
                                            Referencias
                                        </v-col>
                                        <v-col cols="6" >
                                            <strong>{{ direccion.direccion.referencias}}</strong>
                                        </v-col>
                                    </v-row>

                                    
                                    <v-row v-else>
                                        <v-col cols="6">
                                            Tipo de envío: 
                                        </v-col>
                                        <v-col cols="6">
                                            <strong>Recolección en sucursal</strong>
                                        </v-col>
                                        <v-col class="ma-3">
                                            <strong>Se te enviará un correo con la información para su recolección</strong>
                                        </v-col>
                                        
                                    </v-row>
                                    
                                </v-row>
                            </v-col>
                            </v-row>
                        </v-col>
                        <v-col cols="5">
                           <v-row>
                            <v-col cols="12">
                                <h5 class="ps-15">Detalles del pago</h5>
                            </v-col>
                                    <v-col cols="12">
                                        <v-row class="text-center">
                                            <v-col cols="6">
                                                Total del pedido: 
                                            </v-col>
                                            <v-col cols="6">
                                                {{ formatCurrency(cartStore.total) }}
                                            </v-col>
                                            <v-col cols="6">
                                                <p>Costo de envío:</p>
                                            </v-col>
                                            <v-col cols="6" v-if="direccion.es_recoleccion">
                                                {{ formatCurrency(0) }}
                                            </v-col>
                                            <v-col cols="6" v-else>
                                                {{ formatCurrency(costoEnvio) }}
                                            </v-col>
                                        </v-row>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-card variant="tonal" color="primary">
                                            <v-card-text class="text-center">
                                                <p>Total a pagar</p>
                                                <h2 v-if="direccion.es_recoleccion">{{ formatCurrency(cartStore.total) }}</h2>
                                                <h2 v-else>{{ formatCurrency(parseFloat(cartStore.total)+parseFloat(costoEnvio)) }}</h2>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>
                                <v-col cols="12" class="text-right" >
                                    <v-btn
                                    block
                                        variant="outlined"
                                        color="primary"
                                        @click="payment()"
                                        prepend-icon="mdi-credit-card-fast-outline"
                                    >
                                        Pagar
                                    </v-btn>
                                </v-col>
                           </v-row>
                        </v-col>
                    </v-row>
                    <v-card-actions>
                                    <v-btn icon @click="pasos(2)">
                                        <v-icon>mdi-arrow-left</v-icon>
                                    </v-btn>
                                    <v-spacer></v-spacer>
                                </v-card-actions>
                </template>
            </v-stepper>
        </div>
    </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useCartStore } from "../../stores/carrito";
import { ElMessage, ElMessageBox } from "element-plus";
import ElegirDireccion from "../../Components/ChooseDirection.vue";
import axios from "@/axios.js";
import formatCurrency from '../../composables/formatNumberToCurrency'
import { useLoginStore } from "@/stores/login";
import { useRoute } from 'vue-router';

const route = useRoute();
const user = ref({});
const loginStore = useLoginStore();
const direccion = ref({});



const direccionElegida = ref();
const cartStore = useCartStore();
const step = ref(1);
const costoEnvio = ref(0);
let stripe = null;
const total = ref(0);
const domicilio = ref(false);
const headers = ref([
    { title: "Producto", key: "nombre" },
    { title: "Precio", key: "precio" },
    { title: "Piezas", key: "cantidad" },
    { title: "Total", key: "total" },
    { title: "Eliminar", key: "eliminar" },
    { title: "", key: "data-table-expand" },
]);

const borrar = async (item) => {
    const url =  "/carrito/borrar";
    open(item.id, "product", async (id, type) => {
        axios
            .post(url, { id })
            .then(({ data }) => {
                cartStore.fetchCart();
            })
            .catch((error) => {
                console.error("Error al borrar el elemento.");
            });
    });
};

const restarCantidad = async (item) => {
    if (item.cantidad > 1) {
        item.cantidad--;
        axios
            .post("/actualizarProductoCarrito", {
            id: item.id,
            cantidad: item.cantidad,
        });
        cartStore.fetchCart()
    }
};

const sumarCantidad = async (item) => {
    item.cantidad++;
    axios
        .post(
        "/actualizarProductoCarrito",
        { id: item.id, cantidad: item.cantidad }
    );
};


const restarArchivo = async (item) => {
    if (item.cantidad > 1) {
        item.cantidad--;
        await axios
            .post(`/carrito/update-file/${item.id}`,{cantidad: item.cantidad})
            .then(()=>{
                cartStore.fetchCart()    
            })
    }
};

const obtenerCostoEnvio = () => {
    let number = 0
    axios.get('/precio-envio')
    .then(({data}) => {
        number = parseFloat(data.data.precio)
        costoEnvio.value = number
    })
}

const sumarArchivo = async (item) => {
    item.cantidad++;
    axios
        .post(`/carrito/update-file/${item.id}`,{cantidad: item.cantidad})
        .then((response) => {
            cartStore.fetchCart();
        })
        .catch((response) => {
            console.log(response)
        });
};

const pasos = (value) => {
    if (value >= 1 && value <= 3) {
        step.value = value;
    }
    if(step.value === 3){
        
    }
};

const open = (id, type, callback) => {
    ElMessageBox.confirm("¿Desea eliminar el producto del carrito?", "Alerta", {
        confirmButtonText: "OK",
        cancelButtonText: "Cancelar",
        type: "warning",
    })
        .then(() => {
            callback(id, type);
            ElMessage({
                type: "success",
                message: "Producto Eliminado",
            });
        })
        .catch(() => {
            ElMessage({
                type: "info",
                message: "Cancelado",
            });
        });
};



const payment = async () => {
    try {
        const { data } = await axios.post(
            "/checkout",
            { total: (cartStore.total + (direccion.value.es_recoleccion ? 0 : costoEnvio.value)).toFixed(2) , id: cartStore.id}
        );
        await stripe.redirectToCheckout({ sessionId: data.id });
    } catch (error) {
        console.error("Error during payment:", error);
    }
};

onMounted(() => {
    cartStore.fetchCart();

    stripe = Stripe(
        "pk_test_51PXiT1Ctt7GPf4Lb8cBVnDt1p6fvT5Bqkvq7LRE8J1y21b48ekwmvyMRcD7XbzcRFA31G6J7YxRgr8XxKEvomNx500mUHyxI1A"
    );

    setTimeout(() => {
        user.value = loginStore.getUserData;
    }, 500);

    obtenerCostoEnvio()

    if(undefined !== route.params.step){
        step.value = route.params.step
    }
});
</script>
