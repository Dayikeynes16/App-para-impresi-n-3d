import { defineStore } from "pinia";
import axios from "axios";

export const useCartStore = defineStore("cart", {
    state: () => ({
        id: null,
        items: [],
        files: [],
        total: 0,
    }),
    actions: {
        async fetchCart() {
            axios
                .get("/carrito")
                .then(({ data }) => {
                    this.items = data.data.articulos;
                    this.id = data.data.id;
                    this.totalCarrito(data.data.articulos);
                })
                .catch((error) => {
                    console.error("Error fetching cart:", error);
                });
        },

        totalCarrito (data)  {
          this.total = 0;
          data.forEach(element => {
            this.total += element.total
          });
        
      },


        async addToCart(id, cantidad) {
            try {
                await axios.post("/carrito/agregar", {producto_id: id, cantidad });
                await this.fetchCart();
            } catch (error) {
                console.error("Error adding to cart:", error);
            }
        },
    },
    getters: {
        itemCount: (state) => state.items.length,
    },
});
