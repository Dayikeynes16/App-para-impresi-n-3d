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
import { useRouter } from 'vue-router';
const router = useRouter();

const loginStore = useLoginStore();


onMounted(() => {
    // loginStore.isAutenticated()


    // if (!loginStore.is_auth) {
    //     router.push({name: 'logear'})}
   
})




</script>
