import { createRouter, createWebHistory } from "vue-router"
import { useAuthStore } from "../store/auth"

// Lazy loading / code splitting
const Home = () => import("../pages/Home.vue")
const Login = () => import("../pages/Login.vue")
const Register = () => import("../pages/Register.vue")
const Menu = () => import("../pages/Menu.vue")
const Tables = () => import("../pages/Tables.vue")
const Cart = () => import("../pages/Cart.vue")
const Checkout = () => import("../pages/Checkout.vue")
const Success = () => import("../pages/Success.vue")
const Invoice = () => import("../pages/Invoice.vue")
const AdminMenu = () => import("../pages/AdminMenu.vue")
const AdminReservations = () => import("../pages/AdminReservations.vue")
const AdminTables = () => import("../pages/AdminTables.vue")
const Dashboard = () => import("../pages/Dashboard.vue")
const Orders = () => import("../pages/Orders.vue")
const History = () => import("../pages/History.vue")

const routes = [
  // PUBLIC
  { path: "/", component: Home },
  { path: "/login", component: Login },
  { path: "/register", component: Register },
  { path: "/menu", component: Menu },
  { path: "/tables", component: Tables },
  { path: "/cart", component: Cart, meta: { requiresAuth: true } },
  { path: "/checkout", component: Checkout, meta: { requiresAuth: true } },
  { path: "/success", component: Success, meta: { requiresAuth: true } },
  { path: "/invoice", component: Invoice, meta: { requiresAuth: true } },
  { path: "/history", name: "history", component: History, meta: { requiresAuth: true } },

  // ADMIN (protected)
  {
    path: "/dashboard",
    component: Dashboard,
    meta: { requiresAuth: true, adminOnly: true, layout: "admin" },
  },
  {
    path: "/orders",
    component: Orders,
    meta: { requiresAuth: true, adminOnly: true, layout: "admin" },
  },
  {
    path: "/menus",
    component: AdminMenu,
    meta: { requiresAuth: true, adminOnly: true, layout: "admin" },
  },
  {
    path: "/reservations",
    component: AdminReservations,
    meta: { requiresAuth: true, adminOnly: true, layout: "admin" },
  },
  {
    path: "/admin-tables",
    component: AdminTables,
    meta: { requiresAuth: true, adminOnly: true, layout: "admin" },
  },

  { path: "/:pathMatch(.*)*", redirect: "/" },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()
  const token = localStorage.getItem("token")

  // Nëse ka token, por user nuk është në store (pas refresh), merre /me
  if (token && !auth.user) {
    try {
      await auth.me()
    } catch (e) {
      await auth.logout()
    }
  }

  // Route që kërkon login
  if (to.meta.requiresAuth && !auth.isLogged()) {
    return next("/login")
  }

  // Route vetëm për admin
  if (to.meta.adminOnly && auth.user?.role !== "admin") {
    return next("/")
  }

  // Nëse je logged in, mos e lejo me shku te login/register
  if ((to.path === "/login" || to.path === "/register") && auth.isLogged()) {
    return next(auth.user?.role === "admin" ? "/dashboard" : "/")
  }

  next()
})

export default router