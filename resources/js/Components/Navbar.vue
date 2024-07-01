<template>
  <v-app-bar elevation="0" color="blue-grey-darken-1">
    <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
    <v-app-bar-title>Applicaciones Creativas</v-app-bar-title>

    <template  v-slot:append>
      <div v-if="!admin "  style="margin-right: 10px; margin-top: 10px;">
        <el-badge :value="cartStore.items.length + cartStore.files.length" :max="99" class="item">
          <v-btn  @click="handleCartClick" icon="mdi-cart"></v-btn>
        </el-badge>
      </div>

      <v-dialog v-model="dialog" width="auto">
        <v-card width="100%" prepend-icon="mdi-cart" title="Carrito">
          <v-card-text v-if="cartStore.visible">
            <carrito></carrito>
          </v-card-text>
          <v-card-text v-else>
            No has añadido nada aún
            
          </v-card-text>
        </v-card>
      </v-dialog>
    </template>
  </v-app-bar>

  <v-navigation-drawer color="grey-lighten-2" v-model="drawer" temporary>
    <v-list-item prepend-icon="mdi-account-circle" @click='router.push({ name: "Cuenta" })' :title="user.name"></v-list-item>
    <v-divider class="my-0"></v-divider>

    <v-list-item v-if="admin" @click='router.push({ name: "Dashboard" })' title="Dashboard"></v-list-item>
    <v-divider class="my-0" v-if="admin"></v-divider>
    
    <v-list-item v-if="!admin" @click='router.push({ name: "catalogo" })' title="Catalogo"></v-list-item>
    <v-divider v-if="!admin" class="my-0"></v-divider>

    <v-list-item v-if="!admin" @click='router.push({ name: "cotizar" })' title="Cotizar"></v-list-item>
    <v-divider class="my-0"></v-divider>
  
    <v-list-item v-if="admin" @click='router.push({ name: "editarcatalogo" })' title="Editar Catalogo"></v-list-item>
    <v-divider class="my-0" v-if="admin"></v-divider>

    <v-list-item @click="cerrarSesion" title="Cerrar sesión"></v-list-item>
    <v-divider class="my-0"></v-divider>
  </v-navigation-drawer>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useLoginStore } from '../stores/login';
import { useCartStore } from '../stores/carrito';
import Carrito from './Carrito.vue';

const dialog = ref(false);
const drawer = ref(null);
const user = ref({ name: null, correo: null });
const router = useRouter();
const cartStore = useCartStore();
const loginStore = useLoginStore();
const lastRoute = ref(null);
const admin = ref(false);
const cliente = ref(false);

const get_user = async () => {
  try {
    const { data } = await axios.get('/get_user');
    user.value.name = data.name;
    user.value.correo = data.email;
    loginStore.setAutenticado(true);
    if (user.value.correo === 'javierMay@appscreativas.com') {
      admin.value = true;
      cliente.value = true;
    } else {
      cliente.value = true;
    }
  } catch (error) {
    if (error.response && error.response.status === 401) {
      router.push({ name: 'logear' });
    }
  }
};

const cerrarSesion = async () => {
  try {
    await axios.post('/cerrarSesion');
    loginStore.setAutenticado(false);
    router.push({ name: 'logear' });
  } catch (error) {
    console.error('Error cerrando sesión:', error);
  }
};

const handleCartClick = () => {
  if (router.currentRoute.value.name === 'CarritoFinal') {
    router.push({ path: lastRoute.value });
  } else {
    lastRoute.value = router.currentRoute.value.fullPath;
    router.push({ name: 'CarritoFinal' });
  }
};

onMounted(() => {
  get_user();
  cartStore.fetchCart();
});
</script>
