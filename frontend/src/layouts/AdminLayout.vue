<template>
  <div class="min-h-screen bg-black text-white flex">
    <!-- Sidebar -->
    <aside class="w-72 bg-zinc-950 border-r border-white/10 p-6">
      <h1 class="text-3xl font-bold text-gold mb-10">Restaurant Admin</h1>

      <nav class="space-y-3">
        <RouterLink to="/dashboard" class="block px-4 py-3 rounded-xl text-gray-300 hover:bg-white/10 hover:text-white transition">
          Dashboard
        </RouterLink>

        <RouterLink to="/menus" class="block px-4 py-3 rounded-xl text-gray-300 hover:bg-white/10 hover:text-white transition">
          Menus
        </RouterLink>

        <RouterLink to="/orders" class="block px-4 py-3 rounded-xl text-gray-300 hover:bg-white/10 hover:text-white transition">
          Orders
        </RouterLink>

        <RouterLink to="/admin-tables" class="block px-4 py-3 rounded-xl text-gray-300 hover:bg-white/10 hover:text-white transition">
          Tables
        </RouterLink>

        <RouterLink to="/reservations" class="block px-4 py-3 rounded-xl text-gray-300 hover:bg-white/10 hover:text-white transition">
          Reservations
        </RouterLink>
      </nav>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="h-24 bg-zinc-950 border-b border-white/10 px-8 flex items-center justify-between">
        <h2 class="text-3xl font-bold text-white">Dashboard</h2>

        <button
          @click="logout"
          class="px-5 py-3 rounded-xl border border-red-400 text-red-400 hover:bg-red-400 hover:text-black transition"
        >
          Logout
        </button>
      </header>

      <!-- Content -->
      <main class="flex-1 bg-gradient-to-b from-black via-slate-900 to-zinc-900 p-8">
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