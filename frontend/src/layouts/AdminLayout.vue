<template>
  <div class="min-h-screen bg-black text-white flex flex-col lg:flex-row">
    <!-- Sidebar -->
    <aside class="w-full lg:w-72 bg-zinc-950 border-b lg:border-b-0 lg:border-r border-white/10 p-4 md:p-6">
      <h1 class="text-2xl md:text-3xl font-bold text-gold mb-6 lg:mb-10">Restaurant Admin</h1>

      <nav class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-1 gap-3">
        <RouterLink to="/dashboard" class="block px-4 py-3 rounded-xl text-center lg:text-left text-gray-300 hover:bg-white/10 hover:text-white transition">
          Dashboard
        </RouterLink>

        <RouterLink to="/menus" class="block px-4 py-3 rounded-xl text-center lg:text-left text-gray-300 hover:bg-white/10 hover:text-white transition">
          Menus
        </RouterLink>

        <RouterLink to="/orders" class="block px-4 py-3 rounded-xl text-center lg:text-left text-gray-300 hover:bg-white/10 hover:text-white transition">
          Orders
        </RouterLink>

        <RouterLink to="/admin-tables" class="block px-4 py-3 rounded-xl text-center lg:text-left text-gray-300 hover:bg-white/10 hover:text-white transition">
          Tables
        </RouterLink>

        <RouterLink to="/reservations" class="block px-4 py-3 rounded-xl text-center lg:text-left text-gray-300 hover:bg-white/10 hover:text-white transition">
          Reservations
        </RouterLink>
      </nav>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Header -->
      <header class="min-h-20 md:h-24 bg-zinc-950 border-b border-white/10 px-4 md:px-8 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h2 class="text-2xl md:text-3xl font-bold text-white">Dashboard</h2>

        <button
          @click="logout"
          class="w-full sm:w-auto px-5 py-3 rounded-xl border border-red-400 text-red-400 hover:bg-red-400 hover:text-black transition"
        >
          Logout
        </button>
      </header>

      <!-- Content -->
      <main class="flex-1 bg-gradient-to-b from-black via-slate-900 to-zinc-900 p-4 md:p-8 min-w-0">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from "vue-router"
import { useAuthStore } from "../store/auth"

const router = useRouter()
const auth = useAuthStore()

async function logout() {
  await auth.logout()
  router.push("/login")
}
</script>

<style scoped>
.text-gold {
  color: #d4af37;
}
.router-link-active {
  background: rgba(255,255,255,0.08);
  color: white;
}
</style>