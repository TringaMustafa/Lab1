<template>
  <div class="min-h-screen bg-gradient-to-br from-black via-slate-900 to-zinc-900 text-white">

    <!-- NAVBAR (identik me Tables.vue) -->
    <header class="max-w-6xl mx-auto flex justify-between items-center py-6 px-4 md:px-0">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-2xl bg-gold flex items-center justify-center text-black text-xl">
          🍽️
        </div>
        <span class="text-2xl font-bold tracking-wide text-gold">TableFlow</span>
      </div>

      <nav class="flex items-center gap-6 text-sm md:text-base">
        <RouterLink to="/" class="hover:text-gold transition">Home</RouterLink>
        <RouterLink to="/menu" class="hover:text-gold transition">Menu</RouterLink>
        <RouterLink to="/tables" class="hover:text-gold transition">Tables</RouterLink>

<RouterLink
  v-if="isLogged"
  to="/cart"
  class="relative ml-2 px-3 py-2 rounded-lg border border-white/20 hover:bg-white/10 transition"
  title="Cart"
>
  🛒
  <span
    v-if="cart.count"
    class="absolute -top-2 -right-2 text-xs px-2 py-0.5 rounded-full bg-red-500 text-white"
  >
    {{ cart.count }}
  </span>
</RouterLink>


 <!-- NËSE NUK ËSHTË LOGGED -->
<RouterLink
  v-if="!auth.isLogged()"
  to="/login"
  class="ml-4 px-4 py-2 rounded-lg border border-gold text-gold hover:bg-gold hover:text-black transition"
>
  Login
</RouterLink>

<!-- NËSE ËSHTË LOGGED -->
<button
  v-else
  @click="handleLogout"
  class="ml-4 px-4 py-2 rounded-lg border border-red-400 text-red-400 hover:bg-red-400 hover:text-black transition"
>
  Logout
</button>

      </nav>
    </header>


    <!-- HERO SECTION -->
    <main class="max-w-6xl mx-auto px-4 md:px-0 pt-20 pb-16 flex flex-col md:flex-row items-center gap-12">

      <!-- TEXT SIDE -->
      <section class="flex-1 space-y-5">
        <p class="uppercase text-xs tracking-[0.35em] text-gold/80">
          Premium Restaurant Experience
        </p>

        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight">
          Menaxho porositë dhe
          <span class="text-gold">rezervimet</span>
          në një vend.
        </h1>

        <p class="text-gray-300 text-base md:text-lg max-w-xl">
          Lejo klientët të shohin menunë, disponueshmërinë e tavolinave dhe të bëjnë
          porosi online – ndërsa ti kontrollon gjithçka nga paneli i adminit.
        </p>

        <div class="flex flex-wrap gap-4 pt-2">
          <RouterLink
            to="/menu"
            class="px-7 py-3 rounded-xl bg-gold text-black font-semibold text-sm md:text-base shadow-lg hover:bg-yellow-400 transition"
          >
            Shiko Menunë
          </RouterLink>

          <RouterLink
            to="/tables"
            class="px-7 py-3 rounded-xl border border-gold text-gold font-semibold text-sm md:text-base hover:bg-gold/10 transition"
          >
            Shiko Tavolinat
          </RouterLink>
        </div>

        <p class="text-xs text-gray-400 pt-1">
          Për të porositur ose rezervuar, së pari krijo llogari ose hyr si përdorues.
        </p>
      </section>


      <!-- ILLUSTRATION SIDE -->
      <section class="flex-1 flex justify-center">
        <div class="relative w-full max-w-md">

          <!-- Karta kryesore -->
          <div class="rounded-3xl bg-white/5 border border-white/10 backdrop-blur-md p-6 shadow-2xl">
            <p class="text-sm text-gray-300 mb-4">Live restaurant overview</p>

            <div class="space-y-4">

              <!-- Online Orders -->
              <div class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-2xl bg-gold/90 flex items-center justify-center text-black">
                    🧾
                  </div>
                  <div>
                    <p class="text-sm font-semibold">Online Orders</p>
                    <p class="text-xs text-gray-400">See incoming orders in real-time.</p>
                  </div>
                </div>
                <span class="text-xs px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300">
                  Only with login
                </span>
              </div>

              <!-- Table Map -->
              <div class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-2xl bg-gold/90 flex items-center justify-center text-black">
                    🪑
                  </div>
                  <div>
                    <p class="text-sm font-semibold">Table Map</p>
                    <p class="text-xs text-gray-400">Check free & reserved tables.</p>
                  </div>
                </div>
                <span class="text-xs px-3 py-1 rounded-full bg-blue-500/20 text-blue-300">
                  Reserve after login
                </span>
              </div>

              <!-- Admin Dashboard -->
              <div class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-2xl bg-gold/90 flex items-center justify-center text-black">
                    🔑
                  </div>
                  <div>
                    <p class="text-sm font-semibold">Admin Dashboard</p>
                    <p class="text-xs text-gray-400">Manage menu, tables & staff.</p>
                  </div>
                </div>
                <span class="text-xs px-3 py-1 rounded-full bg-rose-500/20 text-rose-300">
                  Admin only
                </span>
              </div>

            </div>
          </div>

          <!-- Rrethat dekorativë -->
          <div class="absolute -top-6 -right-4 w-16 h-16 rounded-full bg-gold/30 blur-xl"></div>
          <div class="absolute -bottom-10 -left-6 w-24 h-24 rounded-full bg-gold/10 blur-2xl"></div>

        </div>
      </section>

    </main>
  </div>
</template>


<script setup>
import { computed } from "vue"
import { useAuthStore } from "../store/auth"
import { useRouter } from "vue-router"
import { useCartStore } from "../store/cart"

const auth = useAuthStore()
const router = useRouter()
const cart = useCartStore()

const isLogged = computed(() => auth.isLogged())

async function handleLogout() {
  await auth.logout()
  router.push("/login")
}
</script>



<style scoped>
.text-gold {
  color: #d4af37;
}
.bg-gold {
  background-color: #d4af37;
}
</style>
