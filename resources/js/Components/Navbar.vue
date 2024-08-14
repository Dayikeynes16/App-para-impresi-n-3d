<template>
    <v-app-bar elevation="0" color="primary">
        <v-app-bar-nav-icon color="white" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>

        <template v-slot:append>
            <v-menu>
                <template v-slot:activator="{ props }">
                    <v-btn
                    color="white"
                    prepend-icon="mdi-account-circle"
                    v-bind="props">
                    {{authUser.name?.split(' ')[0] }}
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

            <v-menu v-if="!cliente">
                <template v-slot:activator="{ props }">
                    <div style="margin-right: 10px; margin-top: 10px">
                        <v-badge color="warning" :content="notificaciones.count">
                            <v-btn v-bind="props" color="white" icon="mdi-bell"></v-btn>
                        </v-badge>
                    </div >
                </template>
                <v-list>
                    <v-list-item prepend-icon="mdi-bell-alert" @click="router.push({name: 'PedidoDetalle', params: {id : notificacion.id}})" class="cursor-pointer" v-for="notificacion in notificaciones.notifications">
                        <v-list-item-text  > Tienes un nuevo pedido</v-list-item-text>
                        <v-list-item-subtitle>{{ formatRelativeTime(notificacion.fecha) }}</v-list-item-subtitle>
                    </v-list-item>
                    <v-list-item v-if="notificaciones.count === 0" prepend-icon="mdi-bell-alert"  class="cursor-pointer">
                        <v-list-item-text  > No tienes notificaciones nuevas</v-list-item-text>
                    </v-list-item>
                </v-list>

            </v-menu>
                <div v-else="cliente" style="margin-right: 10px; margin-top: 10px">
                    <v-badge color="danger" :content="cartStore.items.length" >
                        <v-btn color="white" @click="handleCartClick" icon="mdi-cart"></v-btn>
                    </v-badge>
                </div>
        </template>
    </v-app-bar>

    <v-navigation-drawer color="grey-lighten-2" v-model="drawer" temporary>
        <v-list-item
            prepend-icon="mdi-account-box"

            @click="router.push({ name: 'Cuenta' })"
            :title="authUser.name ?? 'user'"
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
import { useLoginStore } from "../stores/login";
import { useCartStore } from "../stores/carrito";
import { UseNotificationStore } from "../stores/notificaciones";
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import 'dayjs/locale/es';
dayjs.extend(relativeTime);
dayjs.locale('es');

const loginStore = useLoginStore();
const router = useRouter();
const cartStore = useCartStore();
const notificaciones = UseNotificationStore();

const dialog = ref(false);
const drawer = ref(null);

const authUser = ref({});


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
        nombre: "Historial pedidos ",
        permiso: "admin.historial",
        ruta: { name: "PedidosPagados" },
    },
    {
        nombre: "Catálogo",
        permiso: "catalogo",
        ruta: { name: "catalogo" },
    },
    {
        nombre: "Editar Catalogo",
        permiso: "catalogo.editar",
        ruta: { name: "editarcatalogo" },
    },
    {
        nombre: "Roles-Permisos",
        permiso: "roles.permisos",
        ruta: { name: "RolesPermissions" },
    },
    {
        nombre: "Historial",
        permiso: "user.historial",
        ruta: { name: "ClienteHistorial"}
    },
    {
        nombre: "Usuarios",
        permiso: "usuarios",
        ruta: { name: "crearUsuario" }
    },
    {
        nombre: "Costos de impresión",
        permiso: "costo.produccion",
        ruta: { name: "costoProduccion" }
    }
]);

const cerrarSesion = async () => {
    axios.post("/cerrarSesion")
    .then(() => {
        localStorage.removeItem('user');
        router.push({ name: "logear" });
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

const formatRelativeTime = (date) => {
    return dayjs(date).fromNow();
}


onMounted(async () => {
    await loginStore.setUser();
    authUser.value = loginStore.getUserData;

    setTimeout(() => {
        if (loginStore.roles.includes('usuario')) {
            cliente.value = true;
            admin.value = false;
        } else {
            cliente.value = false;
            admin.value = true;
        }
    }, 500);
    notificaciones.getNotifications()
});


</script>
