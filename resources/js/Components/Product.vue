<template>
    <v-card color="grey-lighten-3">
        <v-carousel show-arrows="hover" cycle>
            <v-carousel-item v-for="img in item.imagenes" cover :src="img.url">
            </v-carousel-item>
        </v-carousel>

        <v-card-title>
            {{ item.name }}
        </v-card-title>
        <v-card-actions>
            <v-col class="text-left" cols="6">
                <v-icon
                color="primary"
                icon="mdi-pencil"
                @click="router.push({name: 'editarModelo',params: {id: item.id,},})"
                >
                </v-icon>
                
                </v-col>
                
                <v-col class="text-right" cols="6">
                <v-icon color="danger" icon="mdi-delete" @click="open(item.id)"> </v-icon>
            </v-col>
        </v-card-actions>
    </v-card>
</template>
<script setup>
import axios from "@/axios.js";
import { ElMessage, ElMessageBox } from "element-plus";
const props = defineProps({ item: Object });
import router from "../router";
import { useRouter } from "vue-router";

const emit = defineEmits(["eliminado"]);

const eliminarProducto = async (id) => {
    const { data } = await axios.post("/eliminarProducto", { id });
};

const open = async (id) => {
    try {
        const confirmed = await ElMessageBox.confirm(
            "Borraras permanentemente este producto, ¿Deseas continuar?",
            "Advertencia",
            {
                confirmButtonText: "Eliminar",
                cancelButtonText: "Cancelar",
                type: "warning",
            }
        );
        await eliminarProducto(id);
        emit("eliminado");
    } catch (error) {}
};
</script>
