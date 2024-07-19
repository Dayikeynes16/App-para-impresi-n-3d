<template>
    <v-app-bar elevation="0" color="blue-grey-darken-1">
        <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
        <v-app-bar-title>Applicaciones Creativas</v-app-bar-title>

        <template v-slot:append>
            <div v-if="cliente" style="margin-right: 10px; margin-top: 10px">
                <el-badge
                    :value="2"
                    :max="99"
                    class="item"
                >
                    <v-btn @click="handleCartClick" icon="mdi-cart"></v-btn>
                </el-badge>
            </div>

            <v-dialog v-model="dialog" width="auto">
                <v-card width="100%" prepend-icon="mdi-cart" title="Carrito">
                    <v-card-text v-if="cartStore.visible">
                        <carrito></carrito>
                    </v-card-text>
                    <v-card-text v-else> No has añadido nada aún </v-card-text>
                </v-card>
            </v-dialog>
        </template>
    </v-app-bar>

    <v-navigation-drawer color="grey-lighten-2" v-model="drawer" temporary>
        <v-list-item
            prepend-icon="mdi-account-circle"
            @click="router.push({ name: 'Cuenta' })"
            :title="user.data?.name"
        ></v-list-item>
        <v-divider class="my-0"></v-divider>

        <template v-for="item in menu" :key="item.nombre">
            <div v-if="loginStore.getPermissions.includes(item.permiso)">
                <v-list-item :to="item.ruta" :title="item.nombre"></v-list-item>
                <v-divider class="my-0"></v-divider>
            </div>
        </template>

        <v-list-item @click="cerrarSesion" title="Cerrar sesión"></v-list-item>
        <v-divider class="my-0"></v-divider>
    </v-navigation-drawer>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { useLoginStore } from "@/stores/login";
import { useCartStore } from "../stores/carrito";

const dialog = ref(false);
const drawer = ref(null);

const user = ref({});

const router = useRouter();
const cartStore = useCartStore();

const loginStore = useLoginStore();

const lastRoute = ref(null);
const admin = ref(false);
const cliente = ref(false);
const menu = ref([
    {
        nombre: "Cotizar",
        permiso: "cotizar",
        ruta: { name: "cotizar" },
    },
    {
        nombre: "Dashboard",
        permiso: "dashboard",
        ruta: { name: "Dashboard" },
    },
    {
        nombre: "Historial",
        permiso: "historial",
        ruta: { name: "PedidosPagados" },
    },
    {
        nombre: "Catalogo",
        permiso: "catalogo",
        ruta: { name: "catalogo" },
    },
    {
        nombre: "Editar Catalogo",
        permiso: "catalogo.editar",
        ruta: { name: "editarcatalogo" },
    },
    {
        nombre: "Roles Permissos",
        permiso: "roles.permisos",
        ruta: { name: "RolesPermissions" },
    },
    {
        nombre: "Historial",
        permiso: "admin.historial",
        ruta: { name: "PedidosPagados" },
    },
]);

const cerrarSesion = async () => {
    try {
        await axios.post("/cerrarSesion");
        router.push({ name: "logear" });
    } catch (error) {
        console.error("Error cerrando sesión:", error);
    }
};

const handleCartClick = () => {
    if (router.currentRoute.value.name === "CarritoFinal") {
        router.push({ path: lastRoute.value });
    } else {
        lastRoute.value = router.currentRoute.value.fullPath;
        router.push({ name: "CarritoFinal" });
    }
};

onMounted(() => {
    setTimeout(() => {
        user.value = loginStore.getUserData;
        if (user.value.data.roles[0].name === 'usuario') {
            cliente.value = true;
        } else {
            admin.value = true;
        }
    }, 500);

    cartStore.fetchCart();
});
</script>
