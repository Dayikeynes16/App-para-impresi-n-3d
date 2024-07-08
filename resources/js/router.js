import * as VueRouter from "vue-router";
import Index from "./Pages/Index.vue";
import Ejemplo from "./Pages/ejemplo3.vue";
import Register from "./Pages/Register.vue";
import Cotizar from "./Pages/Cotizacion.vue";
import Login from "./Pages/authentication/Login.vue";
import component from "element-plus/es/components/tree-select/src/tree-select-option.mjs";
import Catalogo from "./Pages/Catalogo.vue";
import EditarCatalogo from "./Pages/EditarCatalogo.vue";
import Cuenta from "./Pages/Cuenta.vue";
import Direcciones from "./Components/Direcciones.vue";
import editarModelo from "./Pages/editarModelo.vue";
import DetallesModelo from "./Pages/DetallesModelo.vue";
import GuardarProducto from "./Pages/GuardarProducto.vue";
import ProcesarCarrito from "./Pages/ProcesarCarrito.vue";
import CarritoFinal from "./Pages/CarritoFinal.vue";
import recuperarContraseña from "./Pages/recuperarContraseña.vue";
import Dashboard from "./Pages/adminPages/Dashboard.vue";
import pedidoDetalles from "./Pages/adminPages/pedidoDetalles.vue";
import EditarDireccion from "./Components/EditarDireccion.vue";
import VentaExitosa from "./Pages/VentaExitosa.vue";
import PedidosPagados from "./Pages/adminPages/PedidosPagados.vue";
import Test from "./Pages/test.vue";

import Main from "./Pages/layout/Main.vue";

const routes = [
    {
        name: "logear",
        path: "/logear",
        component: Login,
    },
    
    {
        name: "register",
        path: "/registrar",
        component: Register,
    },

    {
        name: "Home",
        path: "/",
        component: Main,
        children: [
            {
                name: "catalogo",
                path: "/catalogo",
                component: Catalogo,
            },
            {
                name: "test",
                path: "/test",
                component: Test,
            },
            {
                name: "cotizar",
                path: "/cotizar",
                component: Ejemplo,
            },
            {
                name: "editarcatalogo",
                path: "/editcatalogo",
                component: EditarCatalogo,
            },
            {
                name: "Cuenta",
                path: "/Cuenta",
                component: Cuenta,
            },
            {
                name: "Direcciones",
                path: "/direcciones",
                component: Direcciones,
            },
            {
                name: "editarModelo",
                path: "/modelos/:id/editar",
                component: editarModelo,
            },
            {
                name: "DetallesModelo",
                path: "/DetallesModelos/:id/detalles",
                component: DetallesModelo,
            },
            {
                name: "GuardarProducto",
                path: "/Guardarproducto",
                component: GuardarProducto,
            },
            {
                name: "ProcesarCarrito",
                path: "/ProcesarCarrito",
                component: ProcesarCarrito,
            },
            {
                name: "CarritoFinal",
                path: "/CarritoFinal",
                component: CarritoFinal,
            },
            {
                name: "recuperarContraseña",
                path: "/recuperarContraseña",
                component: recuperarContraseña,
            },
            {
                name: "Dashboard",
                path: "/Dashboard",
                component: Dashboard,
            },
            {
                name: "PedidoDetalle",
                path: "/PedidoDetalle/:id/detalles",
                component: pedidoDetalles,
            },
            {
                name: "editarDireccion",
                path: "/editarDireccion/:id/editar",
                component: EditarDireccion,
            },
            {
                name: "Exito",
                path: "/success",
                component: VentaExitosa,
            },
            {
                name: "PedidosPagados",
                path: "/pedidosPagados",
                component: PedidosPagados,
            }
        ],
    },

];

const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory(),
    routes,
});
export default router;
