<template>
  <div id="three-container"></div>
</template>

<script setup>
import * as THREE from 'three';
import { STLLoader } from 'three/examples/jsm/loaders/STLLoader.js';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import { onMounted, defineProps } from 'vue';
import axios from '../axios';

const props = defineProps({
  stl: {
    type: Object,
    required: true
  }
});

onMounted(async () => {
  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000); // Relación de aspecto 1:1 para un recuadro cuadrado
  camera.position.set(2, 2, 2);

  const renderer = new THREE.WebGLRenderer({ antialias: true });
  renderer.setSize(500, 500); 
  document.getElementById('three-container').appendChild(renderer.domElement);

  const controls = new OrbitControls(camera, renderer.domElement);
  controls.target.set(0, 0, 0);
  controls.update();
  controls.enableDamping = true;

  const light1 = new THREE.DirectionalLight(0xffffff, 1);
  light1.position.set(5, 10, 7.5).normalize();
  scene.add(light1);

  const light2 = new THREE.AmbientLight(0x404040); // Luz ambiental
  scene.add(light2);

  const loader = new STLLoader();

  try {
    const response = await axios.get(`/stlViewer/${props.stl.id}`);
    if (response.status !== 200) {
      throw new Error('Error al obtener la URL del archivo STL');
    }

    const fileUrl = response.data.file_url;

    loader.load(fileUrl, (geometry) => {
      const material = new THREE.MeshPhongMaterial({ color: 0xff0000 });
      const mesh = new THREE.Mesh(geometry, material);
      mesh.position.set(0, 0, 0);
      mesh.scale.set(0.1, 0.1, 0.1); // Ajuste de escala
      scene.add(mesh);
    }, undefined, (error) => {
      console.error('Error al cargar el archivo STL:', error);
    });

  } catch (error) {
    console.error('Error al realizar la petición o cargar el STL:', error);
  }

  function animate() {
    requestAnimationFrame(animate);
    controls.update();
    renderer.render(scene, camera);
  }

  animate();

  window.addEventListener('resize', () => {
    camera.aspect = 1;
    camera.updateProjectionMatrix();
    renderer.setSize(500, 500); // Mantener el tamaño fijo en 500px x 500px
  });
});
</script>

<style>
#three-container {
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
  overflow: hidden;
}
</style>
