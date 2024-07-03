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
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { defineProps } from 'vue';

// Define la prop para recibir el id del producto
const props = defineProps({
  producto: {
    type: [String, Number],
    required: true
  }
});

// Define la variable archivos
const archivos = ref([]);

// Función para traer los archivos
const traerArchivos = async (id) => {
  try {
    console.log('ID recibido en el componente hijo:', id);
    const { data } = await axios.get('/traerArchivos', { params: { id } });
    archivos.value = data;
    console.log('Archivos:', data);
  } catch (error) {
    console.error("Error fetching data:", error);
  }
};

// Llama a traerArchivos cuando el componente se monta, usando el id del producto
onMounted(() => {
  console.log('ID en props.producto:', props.value
  );
  traerArchivos(props.producto);
});

console.log('Componente hijo montado');
</script>