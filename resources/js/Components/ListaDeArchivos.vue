<template>
  <div>
    hola
    <!-- Aquí puedes agregar la lógica para mostrar los archivos -->
    <div v-if="archivos.length">
      <ul>
        <li v-for="archivo in archivos" :key="archivo.id">{{ archivo.name }}</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import axios from "@/axios.js";
import { ref, onMounted } from 'vue';
import { defineProps } from 'vue';

const props = defineProps({
  producto: {
    type: [String, Number],
    required: true
  }
});

const archivos = ref([]);


const traerArchivos = async (id) => {
  try {
    const { data } = await axios.get('/traerArchivos', { params: { id } });
    archivos.value = data;
  } catch (error) {
    console.error("Error fetching data:", error);
  }
};

onMounted(() => {

  traerArchivos(props.producto);
});

</script>