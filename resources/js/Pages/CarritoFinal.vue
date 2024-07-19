<template>
    <v-container>
        <div v-if="carritovacio">
            <v-card>
                <v-card-title>Carrito vacío</v-card-title>
                <v-card-text>
                    Su carrito está vacío. Por favor, añada productos.
                </v-card-text>
            </v-card>
        </div>
        <div>
            <v-stepper
                color="grey-lighten-4"
                v-model="step"
                hide-actions
                :items="['Productos', 'Direccion', 'Pago']"
            >
                <template v-slot:item.1>
                    <v-card>
                        <v-container>
                            <v-divider></v-divider>

                            <v-data-table
                                :items="cartStore.items"
                                :headers="headers"
                            >
                                <template v-slot:item.cantidad="{ item }">
                                    <v-row>
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
                                </template>
                                <template v-slot:item.eliminar="{ item }">
                                    <v-icon
                                        color="red"
                                        icon="mdi-delete"
                                        @click="borrar(item)"
                                    ></v-icon>
                                </template>
                                <template v-slot:body.append>
                                    <tr>
                                        <td colspan="3">Total:</td>
                                        <td>
                                            ${{
                                                Intl.NumberFormat("es-MX", {
                                                    type: "currency",
                                                    currency: "MXN",
                                                    minimumFractionDigits: 2,
                                                }).format(cartStore.total)
                                            }}
                                        </td>
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
                    <ElegirDireccion @pasos="pasos"></ElegirDireccion>
                </template>
                <template v-slot:item.3>
                    <v-card>
                        <v-card-title class="headline"
                            >Detalles del Pedido</v-card-title
                        >
                        <v-card-text>
                            <v-row>
                                <v-col cols="12">
                                    <v-subheader>Total del Pedido</v-subheader>
                                    <v-row>
                                        <v-col cols="6">
                                            <span>Total:</span>
                                        </v-col>
                                        <v-col cols="6" class="text-right">
                                            <strong>
                                                {{
                                                    Intl.NumberFormat("es-MX", {
                                                        style: "currency",
                                                        currency: "MXN",
                                                        minimumFractionDigits: 2,
                                                    }).format(total)
                                                }}
                                            </strong>
                                        </v-col>
                                    </v-row>
                                    <v-row v-if="domicilio === true">
                                        <v-col cols="6">Costo de Envío</v-col>
                                        <v-col cols="6" class="text-right">
                                            <strong>
                                                {{
                                                    Intl.NumberFormat("es-MX", {
                                                        style: "currency",
                                                        currency: "MXN",
                                                        minimumFractionDigits: 2,
                                                    }).format(250)
                                                }}
                                            </strong>
                                        </v-col>
                                    </v-row>
                                </v-col>
                                <v-col cols="12">
                                    <v-subheader
                                        >Información de Contacto</v-subheader
                                    >
                                    <p>
                                        <strong>Nombre:</strong>
                                        {{ customer.name }}<br />
                                        <strong>Correo:</strong>
                                        {{ customer.email }}<br />
                                        <strong>Teléfono:</strong>
                                        {{ customer.phone }}
                                    </p>
                                </v-col>
                                <v-col cols="12">
                                    <v-subheader>Método de Entrega</v-subheader>
                                    <p v-if="cartStore.domicilio">
                                        <strong>Envío a Domicilio</strong><br />
                                        <strong>Calle:</strong>
                                        {{ cartStore.direccion.nombre }}<br />
                                        <strong>Ciudad:</strong>
                                        {{ cartStore.direccion.estado }}<br />
                                        <strong>Estado:</strong>
                                        {{ cartStore.direccion.referencias
                                        }}<br />
                                    </p>
                                    <p v-else>
                                        <strong>Recolección en Sucursal</strong
                                        ><br />
                                        Se te enviará un correo con las
                                        indicaciones al finalizar tu compra.
                                    </p>
                                </v-col>
                            </v-row>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn icon @click="pasos(2)">
                                <v-icon>mdi-arrow-left</v-icon>
                            </v-btn>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="primary"
                                @click="payment()"
                                prepend-icon="mdi-credit-card-fast-outline"
                            >
                                Pagar
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </template>
            </v-stepper>
        </div>
    </v-container>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
import { useCartStore } from "../stores/carrito";
// import axios from "axios";
import { useRouter } from "vue-router";
import { ElMessage, ElMessageBox } from "element-plus";
import ElegirDireccion from "../Components/ElegirDireccion.vue";
const cartStore = useCartStore();
import axios from "@/axios.js";

onMounted(() => {
    cartStore.fetchCart();

    // updateCart();
    // fetchProductosCarrito()
    // carritoVacio()
    // stripe = Stripe(
    //     "pk_test_51PXiT1Ctt7GPf4Lb8cBVnDt1p6fvT5Bqkvq7LRE8J1y21b48ekwmvyMRcD7XbzcRFA31G6J7YxRgr8XxKEvomNx500mUHyxI1A"
    // );
});

const headers = ref([
    { title: "Producto", key: "nombre" },
    { title: "Precio", key: "precio" },
    { title: "Piezas", key: "cantidad" },
    { title: "Total", key: "total" },
    { title: "Eliminar", key: "eliminar" },
]);

const borrar = async (item) => {
    const url = item.is_file ? "/borrarArchivo" : "/borrarProducto"; 
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
        await axios.post(
            "/actualizarProductoCarrito",
            { id: item.id, cantidad: item.cantidad },
        );
        updateCart();
    }
};

const sumarCantidad = async (item) => {
    item.cantidad++;
    await axios.post(
        "/actualizarProductoCarrito",
        { id: item.id, cantidad: item.cantidad },
        { headers: { "X-CSRF-TOKEN": token } }
    );

    fetchProductosCarrito();
};

const pasos = (value) => {
    if (value >= 1 && value <= 3) {
        step.value = value;

        if (step.value === 3 || step.value === 2) {
            domicilio.value = cartStore.domicilio;
            cartStore.fetchCart();
        }
    }
};



// -----------------------------------------------


const carritovacio = ref(true);
const step = ref(1);
let stripe = null;
const router = useRouter();

const productos_carrito = ref([]);
const files = ref([]);
const total = ref(0);
const domicilio = ref(false);




const updateCart = async () => {
    await cartStore.fetchCart();
    fetchProductosCarrito();
    carritoVacio();
};





const sumarCantidadFile = async (file) => {
    file.cantidad++;
    await axios.post(
        "/actualizarFileCarrito",
        { id: file.id, cantidad: file.cantidad },
        { headers: { "X-CSRF-TOKEN": token } }
    );
    updateCart();
};

const restarCantidadFile = async (file) => {
    if (file.cantidad > 1) {
        file.cantidad--;
        await axios.post(
            "/actualizarFileCarrito",
            { id: file.id, cantidad: file.cantidad },
            { headers: { "X-CSRF-TOKEN": token } }
        );
        updateCart();
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

const carritoVacio = () => {
    if (cartStore.items.length === 0 && cartStore.files.length === 0) {
        carritovacio.value = true;
    } else {
        carritovacio.value = false;
    }
};

const payment = async () => {
    try {
        const envio = domicilio.value ? 250 : 0;
        const { data } = await axios.post(
            "/checkout",
            { total: total.value + envio },
            { headers: { "X-CSRF-TOKEN": token } }
        );
        await stripe.redirectToCheckout({ sessionId: data.id });
    } catch (error) {
        console.error("Error during payment:", error);
    }
};

watch([productos_carrito, files], { immediate: true });
</script>
