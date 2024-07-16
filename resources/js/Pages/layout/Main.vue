<template>
    <Navbar></Navbar>
    <v-main>
        <RouterView></RouterView>
    </v-main>

</template>
<script setup>
import Navbar from '@/Components/Navbar.vue';
import { useLoginStore } from '@/stores/login';
import { onMounted } from 'vue';

const loginStore = useLoginStore();


onMounted(() => {
    loginStore.setUser()
})




</script>
