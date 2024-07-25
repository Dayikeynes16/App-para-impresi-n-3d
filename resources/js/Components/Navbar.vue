<template>
    <v-app-bar elevation="0" color="primary">
        <v-app-bar-nav-icon color="white" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
        <v-app-bar-title>Applicaciones Creativas</v-app-bar-title>

        <template v-slot:append>
            <v-menu>
                <template v-slot:activator="{ props }">
                    <v-btn
                    color="white"
                    v-bind="props">
                    {{user.data?.name}}
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item prepend-icon="mdi-account" base-color="primary">
                        <v-list-item-title class="cursor-pointer" @click="router.push({ name: 'Cuenta' })">Cuenta</v-list-item-title>
                    </v-list-item>
                    <v-list-item prepend-icon="mdi-logout" base-color="danger">
                        <v-list-item-title class="cursor-pointer"  @click="cerrarSesion()">Cerrar sesión</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>

            <div v-if="cliente" style="margin-right: 10px; margin-top: 10px">
                <v-badge color="danger" :content="cartStore.items.length">
                    <v-btn color="white" @click="handleCartClick" icon="mdi-cart"></v-btn>
                </v-badge>
            </div>

        </template>
    </v-app-bar>

    <v-navigation-drawer color="grey-lighten-2" v-model="drawer" temporary>
        <v-list-item
            prepend-icon="mdi-account-circle"
            @click="router.push({ name: 'Cuenta' })"
            :title="user.data?.name"
        ></v-list-item>
        <v-divider class="my-0"></v-divider>
        <v-list >
            <template v-for="item in menu" :key="item.nombre">
                <div v-if="loginStore.getPermissions.includes(item.permiso)">
                    <v-list-item class="ma-2" rounded="shaped" color="primary" :to="item.ruta" :title="item.nombre"></v-list-item>
                </div>
            </template>
        </v-list>
    </v-navigation-drawer>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "@/axios.js";
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
        nombre: "Historial Pedidos ",
        permiso: "admin.historial",
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
        permiso: "user.historial",
        ruta: { name: "ClienteHistorial"}
    },
    {
        nombre: "Crear Usuarios",
        permiso: "usuarios",
        ruta: { name: "crearUsuario" }
    }
]);

const cerrarSesion = async () => {
    await axios.post("/cerrarSesion")
        .then(() => {
            router.push({ name: "logear" });ß
        })

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

    // cartStore.fetchCart();
});
</script>
