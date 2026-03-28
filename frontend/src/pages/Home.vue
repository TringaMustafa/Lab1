<template>
  <div class="min-h-screen bg-gradient-to-br from-black via-slate-900 to-zinc-900 text-white">
    <!-- NAVBAR -->
    <header class="max-w-6xl mx-auto px-4 lg:px-0">
      <div class="flex flex-wrap items-center justify-between gap-4 py-4 md:py-6">
        <div class="flex items-center gap-3 shrink-0">
          <div class="w-9 h-9 rounded-2xl bg-gold flex items-center justify-center text-black text-xl">
            🍽️
          </div>
          <span class="text-2xl font-bold tracking-wide text-gold">TableFlow</span>
        </div>

        <nav class="flex flex-wrap items-center justify-end gap-3 md:gap-5 text-sm md:text-base">
          <RouterLink to="/" class="hover:text-gold transition">Home</RouterLink>
          <RouterLink to="/menu" class="hover:text-gold transition">Menu</RouterLink>
          <RouterLink to="/tables" class="hover:text-gold transition">Tables</RouterLink>

          <RouterLink
            v-if="isLogged"
            to="/history"
            class="hover:text-gold transition"
          >
            History
          </RouterLink>

          <RouterLink
            v-if="auth.isAdmin()"
            to="/dashboard"
            class="px-3 py-2 rounded-lg border border-gold text-gold hover:bg-gold hover:text-black transition"
          >
            Dashboard
          </RouterLink>

          <RouterLink
            v-if="isLogged"
            to="/cart"
            class="relative px-3 py-2 rounded-lg border border-white/20 hover:bg-white/10 transition"
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

          <RouterLink
            v-if="!auth.isLogged()"
            to="/login"
            class="px-4 py-2 rounded-lg border border-gold text-gold hover:bg-gold hover:text-black transition"
          >
            Login
          </RouterLink>

          <button
            v-else
            @click="handleLogout"
            class="px-4 py-2 rounded-lg border border-red-400 text-red-400 hover:bg-red-400 hover:text-black transition"
          >
            Logout
          </button>
        </nav>
      </div>
    </header>

    <!-- HERO -->
    <main class="max-w-6xl mx-auto px-4 lg:px-0 pt-10 md:pt-14 lg:pt-20 pb-16">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 items-center min-h-[70vh]">
        <!-- LEFT -->
        <section class="max-w-2xl">
          <p class="uppercase text-[11px] md:text-xs tracking-[0.35em] text-gold/80 mb-5">
            Premium Restaurant Experience
          </p>

          <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight">
            Menaxho porositë dhe
            <span class="text-gold">rezervimet</span>
            në një vend.
          </h1>

          <p class="text-gray-300 text-base md:text-lg max-w-xl mt-5 leading-7">
            Lejo klientët të shohin menunë, tavolinat dhe të bëjnë porosi online,
            ndërsa ti kontrollon gjithçka lehtësisht nga paneli i adminit.
          </p>

          <div class="flex flex-col sm:flex-row gap-4 mt-8">
            <RouterLink
              to="/menu"
              class="px-7 py-3 rounded-xl bg-gold text-black font-semibold hover:bg-yellow-400 transition text-center"
            >
              Shiko Menunë
            </RouterLink>

            <RouterLink
              to="/tables"
              class="px-7 py-3 rounded-xl border border-gold text-gold hover:bg-gold/10 transition text-center"
            >
              Shiko Tavolinat
            </RouterLink>
          </div>
        </section>

        <!-- RIGHT -->
        <section class="w-full flex justify-center lg:justify-end">
          <div class="relative w-full max-w-md">
            <div class="rounded-3xl bg-white/5 border border-white/10 backdrop-blur-md p-5 md:p-6 shadow-2xl">
              <p class="text-sm text-gray-300 mb-4">Live restaurant overview</p>

              <div class="space-y-3">
                <div class="rounded-2xl bg-white/5 px-4 py-3 flex items-center gap-3">
                  <div class="w-8 h-8 rounded-xl bg-gold/90 flex items-center justify-center text-black shrink-0">
                    🧾
                  </div>
                  <div>
                    <p class="text-sm font-semibold">Orders</p>
                  </div>
                </div>

                <div class="rounded-2xl bg-white/5 px-4 py-3 flex items-center gap-3">
                  <div class="w-8 h-8 rounded-xl bg-gold/90 flex items-center justify-center text-black shrink-0">
                    🪑
                  </div>
                  <div>
                    <p class="text-sm font-semibold">Tables</p>
                  </div>
                </div>

                <div class="rounded-2xl bg-white/5 px-4 py-3 flex items-center gap-3">
                  <div class="w-8 h-8 rounded-xl bg-gold/90 flex items-center justify-center text-black shrink-0">
                    🔑
                  </div>
                  <div>
                    <p class="text-sm font-semibold">Admin</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="absolute -top-5 -right-3 w-16 h-16 rounded-full bg-gold/20 blur-xl"></div>
            <div class="absolute -bottom-8 -left-4 w-24 h-24 rounded-full bg-gold/10 blur-2xl"></div>
          </div>
        </section>
      </div>
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
.text-gold { color: #d4af37; }
.bg-gold { background-color: #d4af37; }
</style>