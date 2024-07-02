<template>
    <v-container >
        <v-card >
            <v-card-text class="success-card" >
                <v-row>
                    <v-col cols="4"></v-col>
                    <v-col cols="4" class="text-center">
                        <v-icon color="green" size="large" icon="mdi-check-bold"></v-icon>
                    </v-col>
                    <v-col cols="4"></v-col>
                </v-row>
                <v-row>
                    <v-col cols="4"></v-col>
                    <v-col cols="4" class="text-center">
                        <v-text class="success-text">Su Compra ha sido exitosa</v-text>
                    </v-col>
                    <v-col cols="4"></v-col>
                </v-row>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script setup>
import axios from 'axios';
import { onMounted } from 'vue';

const finalizar = async () => {
    try {
        await axios.post('/ConfirmarVenta');

    } catch (error) {
        
    }
};

onMounted(() => {
    finalizar();
});
</script>

<style scoped>
.success-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f0f8f5;
}

.success-card {
    max-width: 400px;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    background-color: #ffffff;
}

.success-text {
    font-size: 18px;
    font-weight: bold;
    color: green;
    margin-top: 10px;
}
</style>
