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
 
        axios.get('/traerCarrito')
          .then(({data}) => {
            this.items = data.data.productos;
            this.files = data.data.orden.files
            this.id = data.data.id;


            // this.direccion.nombre = data.direccion.nombre
            // this.direccion.direccion = data.direccion.nombre
            // this.direccion.nombre = data.direccion.direccion
            // this.direccion.referencias = data.direccion.referencias
            // this.direccion.telefono = data.direccion.telefono
            // this.direccion.destinatario = data.direccion.destinatario
            // this.direccion.codigo_postal = data.direccion.codigo_postal

              // if (data.data.recoleccion === false){
        //   this.domicilio = false
        // } else{
        //   this.domicilio = true
        // }

            // for (file in data.data.orden.files) {
            //   this.files.push(file);
            //   console.log('probando si puedo asignar los archivos al carrito', this.files);
            // } 
        // else {
        //   this.files = [];
        // }

          }) 
          .catch((error) => {
            console.error('Error fetching cart:', error);

          });

        


      
        
      
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
