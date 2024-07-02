import { defineStore } from 'pinia';
import axios from 'axios';

export const useCartStore = defineStore('cart', {
  state: () => ({
    id: null,
    items: [],
    files: [],
    visible: false,
    domicilio: false
  }),
  actions: {
    async fetchCart() {
      try {
        const { data } = await axios.get('/traerCarrito');
        this.items = data.productos;
        this.id = data.id;
        if (data.recoleccion === false){
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
