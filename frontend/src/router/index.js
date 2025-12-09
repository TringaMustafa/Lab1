import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../store/auth'
import Login from '../pages/Login.vue'
import Dashboard from '../pages/Dashboard.vue'
import Menu from '../pages/Menu.vue'
import Tables from '../pages/Tables.vue'
import Orders from '../pages/Orders.vue'

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/login', component: Login },
  { path: '/dashboard', component: Dashboard },
  { path: '/menu', component: Menu, meta: { requiresAuth: true } },
  { path: '/tables', component: Tables, meta: { requiresAuth: true } },
  { path: '/orders', component: Orders, meta: { requiresAuth: true } }
]

const router = createRouter({ history: createWebHistory(), routes })

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isLogged()) {
    return next({ path: '/login', query: { redirect: to.fullPath } })
  }
  next()
})

export default router
