<template>
  <div id="app" class="min-h-screen bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-blue-700 to-blue-900 text-white sticky top-0 z-50 shadow-lg">
      <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-8">
          <div class="text-2xl font-bold">🍽️ Restaurant</div>
          <div class="flex gap-6">
            <router-link 
              to="/dashboard" 
              class="hover:bg-blue-800 px-4 py-2 rounded-lg transition"
              :class="{ 'bg-blue-600': $route.path === '/dashboard' }"
            >
              📊 Dashboard
            </router-link>
            <router-link 
              to="/menu" 
              class="hover:bg-blue-800 px-4 py-2 rounded-lg transition"
              :class="{ 'bg-blue-600': $route.path === '/menu' }"
            >
              🍴 Menu
            </router-link>
            <router-link 
              to="/tables" 
              class="hover:bg-blue-800 px-4 py-2 rounded-lg transition"
              :class="{ 'bg-blue-600': $route.path === '/tables' }"
            >
              🪑 Tables
            </router-link>
            <router-link 
              to="/orders" 
              class="hover:bg-blue-800 px-4 py-2 rounded-lg transition"
              :class="{ 'bg-blue-600': $route.path === '/orders' }"
            >
              📦 Orders
            </router-link>
          </div>
        </div>
        <button 
          @click="logout"
          class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg transition font-semibold"
        >
          Logout
        </button>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-8">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from './store/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

async function logout() {
  await auth.logout()
  router.push('/login')
}
</script>