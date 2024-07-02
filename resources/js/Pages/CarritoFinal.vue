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
        <div v-else>
            <v-stepper
            olor="grey-lighten-4"
                v-model="step"
                hide-actions
                :items="['Productos', 'Direccion', 'Pago']"
            >
                <template v-slot:item.1>
                    <v-card >
                        <v-container>
                            <v-divider></v-divider>
                            <v-table
                                hover="true"
                                class="table table-borderless"
                            >
                                <thead>
                                    <tr>
                                        <th>
                                            <p>Producto</p>
                                        </th>
                                        <th>
                                            <p>Piezas</p>
                                        </th>
                                        <th>
                                            <p>Total</p>
                                        </th>
                                        <th class="text-center">
                                            <p>Eliminar</p>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="item in productos_carrito"
                                        :key="item.id"
                                    >
                                        <td>
                                            <p>{{ item.nombre }}</p>
                                        </td>
                                        <td>
                                            <v-row>
                                                <v-icon
                                                    @click="
                                                        restarCantidad(item)
                                                    "
                                                    icon="mdi-minus"
                                                ></v-icon>
                                                <p>{{ item.cantidad }}</p>
                                                <v-icon
                                                    @click="sumarCantidad(item)"
                                                    icon="mdi-plus"
                                                ></v-icon>
                                            </v-row>
                                        </td>
                                        <td>
                                            $ {{ item.precio * item.cantidad }}
                                        </td>
                                        <td class="text-center">
                                            <v-icon
                                                color="red"
                                                icon="mdi-delete"
                                                @click="borrarProducto(item.id)"
                                            ></v-icon>
                                        </td>
                                    </tr>
                                    <tr v-for="file in files" :key="file.id">
                                        <td>
                                            <p>{{ file.nombre }}</p>
                                        </td>
                                        <td>
                                            <v-row>
                                                <v-icon
                                                    @click="
                                                        restarCantidadFile(file)
                                                    "
                                                    icon="mdi-minus"
                                                ></v-icon>
                                                <p>{{ file.cantidad }}</p>
                                                <v-icon
                                                    @click="
                                                        sumarCantidadFile(file)
                                                    "
                                                    icon="mdi-plus"
                                                ></v-icon>
                                            </v-row>
                                        </td>
                                        <td>$ {{ file.precio }}</td>
                                        <td class="text-center">
                                            <v-icon
                                                color="red"
                                                icon="mdi-delete"
                                                @click="borrarFile(file.id)"
                                            ></v-icon>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>
                            <v-divider></v-divider>
                            <v-card-actions>
                                <v-row>
                                    <v-col cols="2">
                                        <v-text>Total:</v-text>
                                    </v-col>
                                    <v-col cols="2">
                                        $
                                        {{
                                            Intl.NumberFormat("es-MX", {
                                                type: "currency",
                                                currency: "MXN",
                                                minimumFractionDigits: 2,
                                            }).format(total)
                                        }}
                                    </v-col>
                                    <v-col cols="8" class="d-flex justify-end">
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
        <v-card-title class="headline">Detalles del Pedido</v-card-title>
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
                </v-col>
                <v-col cols="12">
                    <v-subheader>Información de Contacto</v-subheader>
                    <p>
                        <strong>Nombre:</strong> {{ customer.name }}<br>
                        <strong>Correo:</strong> {{ customer.email }}<br>
                        <strong>Teléfono:</strong> {{ customer.phone }}
                    </p>
                </v-col>
                <v-col cols="12">
                    <v-subheader>Dirección de Envío</v-subheader>
                    <p>
                        <strong>Calle:</strong> {{ shipping.address }}<br>
                        <strong>Ciudad:</strong> {{ shipping.city }}<br>
                        <strong>Estado:</strong> {{ shipping.state }}<br>
                        <strong>Código Postal:</strong> {{ shipping.zip }}
                    </p>
                </v-col>
            </v-row>
        </v-card-text>
        <v-card-actions>
            <v-btn icon @click="pasos(2)">
                <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="payment()" prepend-icon="mdi-credit-card-fast-outline">
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
import axios from "axios";
import { useRouter } from "vue-router";
import { ElMessage, ElMessageBox } from "element-plus";
import ElegirDireccion from "../Components/ElegirDireccion.vue";


const customer = ref({
    name: "Carlos Cabeza de Vaca",
    email: "carlos@gmail.com",
    phone: "9931790341"
});

const shipping = ref({
    address: "Calle Falsa 123",
    city: "Villahermosa",
    state: "Tabasco",
    zip: "86000"
});


const carritovacio = ref(false);
const step = ref(1);
let stripe = null;
const router = useRouter();
const cartStore = useCartStore();
const token = document
    .querySelector("meta[name='csrf-token']")
    .getAttribute("value");

const productos_carrito = ref([]);
const files = ref([]);
const total = ref(0);

const pasos = (value) => {
    step.value = value;
};

const fetchProductosCarrito = () => {
    productos_carrito.value = cartStore.items.map((item) => ({
        id: item.id,
        nombre: item.producto.name,
        cantidad: item.cantidad,
        precio: item.producto.price,
    }));

    files.value = cartStore.files.map((file) => ({
        id: file.id,
        nombre: file.nombre,
        cantidad: file.cantidad,
        precio: file.precio,
    }));
};

const updateCart = async () => {
    await cartStore.fetchCart();
    fetchProductosCarrito();
    totalCarrito();
    carritoVacio()
};

const restarCantidad = async (item) => {
    if (item.cantidad > 1) {
        item.cantidad--;
        await axios.post(
            "/actualizarProductoCarrito",
            { id: item.id, cantidad: item.cantidad },
            { headers: { "X-CSRF-TOKEN": token } }
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
    updateCart();
};

const borrarProducto = async (id) => {
    open(id, "product", async (id, type) => {
        try {
            await axios.post(
                "/borrarProducto",
                { id },
                { headers: { "X-CSRF-TOKEN": token } }
            );
            updateCart();
        } catch (error) {
            console.error("Error deleting product:", error);
        }
    });
};

const borrarFile = async (id) => {
    open(id, "file", async (id, type) => {
        try {
            await axios.post(
                "/deletefile",
                { id },
                { headers: { "X-CSRF-TOKEN": token } }
            );
            updateCart();
          
        } catch (error) {
            console.error("Error deleting file:", error);
        }
    });
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
        updateCart()
        
    }
};

const open = (id, type, callback) => {
    ElMessageBox.confirm("¿Desea Eliminar el producto del carrito?", "Alerta", {
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



const totalCarrito = () => {
    total.value = 0;
    for (let i = 0; i < productos_carrito.value.length; i++) {
        total.value +=
            productos_carrito.value[i].precio *
            productos_carrito.value[i].cantidad;
    }
    for (let i = 0; i < files.value.length; i++) {
        total.value += files.value[i].precio;
    }
    console.log(total.value);
};

const carritoVacio = () =>{
    if (cartStore.items.length === 0 && cartStore.files.length === 0){
        carritovacio.value = true
    }else{
        carritovacio.value=false
    }
}


const payment = async () => {
    try {
        const { data } = await axios.post(
            "/checkout",
            { total: total.value },
            { headers: { "X-CSRF-TOKEN": token } }
        );
        await stripe.redirectToCheckout({ sessionId: data.id });
    } catch (error) {
        console.error("Error during payment:", error);
    }
};

watch([productos_carrito, files], totalCarrito, { immediate: true });


onMounted(() => {
    updateCart();
    carritoVacio()
    stripe = Stripe(
        "pk_live_51PXiT1Ctt7GPf4Lbd8Bx3koTzqCepRUoBdGfhQl67tXuU7QwAoG3TnAP8OmB1FjGB9g58Syl6oveq2gNj2IUkcgU00k6g3ujk9"
    );
});
</script>
