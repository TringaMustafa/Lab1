<template>
  <div class="min-h-screen bg-gray-50">
    <div class="flex">
      <!-- Sidebar -->
      <aside class="w-64 min-h-screen bg-white border-r">
        <div class="p-5 font-bold text-lg">Restaurant Admin</div>

        <nav class="px-3 space-y-1">
          <RouterLink to="/dashboard" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
            Dashboard
          </RouterLink>
          <RouterLink to="/menus" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
            Menus
          </RouterLink>
          <RouterLink to="/orders" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
            Orders
          </RouterLink>
          <RouterLink to="/tables" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
            Tables
          </RouterLink>
          <RouterLink to="/reservations" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
            Reservations
          </RouterLink>
        </nav>
      </aside>

      <!-- Content -->
      <main class="flex-1">
        <header class="h-16 bg-white border-b flex items-center justify-between px-6">
          <div class="font-semibold">Dashboard</div>

          <button
            @click="handleLogout"
            class="px-4 py-2 rounded-lg border border-red-400 text-red-500 hover:bg-red-400 hover:text-white transition"
          >
            Logout
          </button>
        </header>

        <div class="p-6">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from "vue-router"
import { useAuthStore } from "../store/auth"

const router = useRouter()
const auth = useAuthStore()

async function handleLogout() {
  await auth.logout()
  router.push("/login")
}
</script>
