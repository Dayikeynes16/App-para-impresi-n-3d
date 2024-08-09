import * as VueRouter from "vue-router";
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
import recuperarContrasena from "./Pages/recuperarContraseña.vue";
import Dashboard from "./Pages/adminPages/Dashboard.vue";
import pedidoDetalles from "./Pages/adminPages/pedidoDetalles.vue";
import EditarDireccion from "./Components/EditarDireccion.vue";
import VentaExitosa from "./Pages/VentaExitosa.vue";
import PedidosPagados from "./Pages/adminPages/PedidosPagados.vue";
import Usuarios from "./Pages/adminPages/Usuarios.vue";
import RolesPermisssions from "./Pages/adminPages/RolesPermisssions.vue";
import Main from "./Pages/layout/Main.vue";
import HistorialCliente from "./Pages/clientePages/HistorialCliente.vue";
import Users from "./Pages/adminPages/Users.vue";
import CostoProduccion from "./Pages/adminPages/CostoProduccion.vue";
import cotizacionRapida from "./Pages/clientePages/cotizacionRapida.vue";
import { useLoginStore } from "./stores/login";

const routes = [
    {
        name: "logear",
        path: "/logear",
        component: Login,
    },
    {
        name: "recuperarContrasena",
        path: "/recuperarContrasena",
        component: recuperarContrasena,
    },
    {
        name: "register",
        path: "/registrar",
        component: Register,
    },
    {
        name: "cotizacion-rapida",
        path: "/cotizacion-rapida",
        component: cotizacionRapida,
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
                meta: {
                    permission: "catalogo",
                },
            },
            {
                name: "cotizar",
                path: "/cotizar",
                component: Ejemplo,
                meta: {
                    permission: "cotizar",
                },
            },
            {
                name: "editarcatalogo",
                path: "/editcatalogo",
                component: EditarCatalogo,
                meta: {
                    permission: "catalogo.editar",
                },
            },
            {
                name: "Cuenta",
                path: "/Cuenta",
                component: Cuenta,
                meta: {
                    permission: "usuario",
                },
            },
            {
                name: "Direcciones",
                path: "/direcciones",
                component: Direcciones,
                meta: {
                    permission: "catalogo",
                },
            },
            {
                name: "editarModelo",
                path: "/modelos/:id/editar",
                component: editarModelo,
                meta: {
                    permission: "catalogo.editar",
                },
            },
            {
                name: "DetallesModelo",
                path: "/DetallesModelos/:id/detalles",
                component: DetallesModelo,
                meta: {
                    permission: "catalogo",
                },
            },
            {
                name: "GuardarProducto",
                path: "/Guardarproducto",
                component: GuardarProducto,
                meta: {
                    permission: "catalogo.editar",
                },
            },
            {
                name: "ProcesarCarrito",
                path: "/ProcesarCarrito",
                component: ProcesarCarrito,
                meta: {
                    permission: "usuario",
                },
            },
            {
                name: "CarritoFinal",
                path: "/CarritoFinal",
                component: CarritoFinal,
                meta: {
                    permission: "catalogo",
                },
            },
            {
                name: "Dashboard",
                path: "/Dashboard",
                component: Dashboard,
                meta: {
                    permission: "dashboard",
                },
            },
            {
                name: "PedidoDetalle",
                path: "/PedidoDetalle/:id/detalles",
                component: pedidoDetalles,
                meta: {
                    permission: "dashboard",
                },
            },
            {
                name: "editarDireccion",
                path: "/editarDireccion/:id/editar",
                component: EditarDireccion,
                meta: {
                    permission: "catalogo",
                },
            },
            {
                name: "Exito",
                path: "/success",
                component: VentaExitosa,
                meta: {
                    permission: "catalogo",
                },
            },
            {
                name: "PedidosPagados",
                path: "/pedidosPagados",
                component: PedidosPagados,
                meta: {
                    permission: "admin.historial",
                },
            },
            {
                name: "CrearUsuarios",
                path: "/crearUsuarios",
                component: Usuarios,
                meta: {
                    permission: "usuarios",
                },
            },
            {
                name: "RolesPermissions",
                path: "/RolesPermissions",
                component: RolesPermisssions,
                meta: {
                    permission: "roles.permisos",
                },
            },
            {
                name: "ClienteHistorial",
                path: "/cliente-historial",
                component: HistorialCliente,
                meta: {
                    permission: "user.historial",
                },
            },
            {
                name: "crearUsuario",
                path: "crearUsuario",
                component: Users,
                meta: {
                    permission: "usuarios",
                },
            },
            {
                name: "costoProduccion",
                path: "costo-produccion",
                component: CostoProduccion,
                meta: {
                    permission: "costo.produccion",
                },
            },
        ],
    },
];

const router = VueRouter.createRouter({
    history: VueRouter.createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const loginStore = useLoginStore();
    await loginStore.setUser();

    if (
        [
            "logear",
            "register",
            "recuperarContrasena",
            "cotizacion-rapida",
        ].includes(to.name)
    ) {
        next();
    } else {
        if (!localStorage.user) {
            next({ name: "logear" });

            return;
        }

        if ("logear" !== to.name) {
            if (loginStore.permissions.length > 0) {
                if (!loginStore.permissions.includes(to.meta.permission)) {
                    next({ name: "catalogo" });
                    return;
                }

                next();
                return;
            } else {
                next({ name: "logear" });
                return;
            }
        }
        console.error("Falla en el router.");
    }
});

export default router;
