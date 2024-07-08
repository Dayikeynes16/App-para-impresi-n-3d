import { defineStore } from 'pinia';
import axios from 'axios';

export const useCartStore = defineStore('cart', {
  state: () => ({
    id: null,
    items: [],
    files: [],
    visible: false,
    domicilio: false,
    direccion: {
      nombre: null,
      direccion: null,
      referencias: null,
      telefono: null,
      destinatario: null,
      codigo_postal: null
    }
  }),
  actions: {
    async fetchCart() {
      try {
        const { data } = await axios.get('/traerCarrito');
        console.log(data.data)
        this.items = data.data.productos;
        this.id = data.data.id;


        this.direccion.nombre = data.direccion.nombre
        this.direccion.direccion = data.direccion.nombre
        this.direccion.nombre = data.direccion.direccion
        this.direccion.referencias = data.direccion.referencias
        this.direccion.telefono = data.direccion.telefono
        this.direccion.destinatario = data.direccion.destinatario
        this.direccion.codigo_postal = data.direccion.codigo_postal


        if (data.data.recoleccion === false){
          this.domicilio = false
        } else{
          this.domicilio = true
        }

        
   
        if (data.orden && data.orden.length > 0) {
          this.files = data.orden.flatMap(orden => orden.files);
        } else {
          this.files = [];
        }
        
        this.visible = this.items.length > 0 || this.files.length > 0;
      } catch (error) {
        console.error('Error fetching cart:', error);
      }
    },
    async addToCart(id, cantidad) {
      try {
        await axios.post('/añadirCarrito', { id, cantidad });
        await this.fetchCart();
      } catch (error) {
        console.error('Error adding to cart:', error);
      }
    }
  },
  getters: {
    itemCount: (state) => state.items.length
  }
});
