import { createRouter, createWebHistory } from "vue-router"
import { useAuthStore } from "../store/auth"

import Home from "../pages/Home.vue"
import Login from "../pages/Login.vue"
import Register from "../pages/Register.vue"
import Menu from "../pages/Menu.vue"
import Tables from "../pages/Tables.vue"
import Cart from "../pages/Cart.vue"
import Checkout from "../pages/Checkout.vue"
import Success from "../pages/Success.vue"
import Invoice from "../pages/Invoice.vue"


import Dashboard from "../pages/Dashboard.vue"
import Orders from "../pages/Orders.vue"

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
