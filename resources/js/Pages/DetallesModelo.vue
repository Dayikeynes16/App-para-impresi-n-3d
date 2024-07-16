<template>
    <v-container>
        <v-card>
            <el-upload class="upload-demo" drag :http-request="addImages" ref='loadform' :data="{ 'material': 1 }"
                accept=".stl" :headers="{
                    'X-CSRF-TOKEN': token
                }" :auto-upload="true">
                <el-icon class="el-icon--upload"><upload-filled /></el-icon>
                <div class="el-upload__text">
                    Arrastra tu archivo o <em>haz click para subir</em>
                </div>
                <template #tip>
                    <div class="el-upload__tip">
                        Archivos STL de menos de 30 MB
                    </div>
                </template>
            </el-upload>
        </v-card>
    </v-container>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { onMounted, ref } from 'vue'
const product = ref({})
import axios from 'axios';
import { UploadFilled } from '@element-plus/icons-vue'
const token = document.querySelector("meta[name='csrf-token']").getAttribute('value')


const route = useRoute()

const addImages = async ()=>{

}

onMounted(async () => {
    const { data } = await axios.get(`/productos/${route.params.id}`)
    product.value = data.data
})
</script>