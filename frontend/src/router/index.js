import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../store/auth'

import Home from '../pages/Home.vue'
import Login from '../pages/Login.vue'
import Dashboard from '../pages/Dashboard.vue'
import Menu from '../pages/Menu.vue'
import Tables from '../pages/Tables.vue'
import Orders from '../pages/Orders.vue'
import Register from '../pages/Register.vue'

const routes = [
  // PUBLIC
  { path: '/', component: Home },
  { path: '/login', component: Login },
  { path: '/menu', component: Menu },
  { path: '/tables', component: Tables },
  { path : '/register', component: Register},

  // ADMIN
  { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true, layout: 'admin', title: 'Dashboard' } },
  { path: '/orders', component: Orders,   meta: { requiresAuth: true, layout: 'admin', title: 'Orders' } },

  { path: '/:pathMatch(.*)*', redirect: '/' }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isLogged()) {
    return next('/login')
  }
  if (to.meta.adminOnly && auth.user?.role !== 'admin') {
    return next('/')
  }
  next()
})


export default router
