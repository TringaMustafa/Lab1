<template>
  <!-- NAVBAR TRANSPARENT SI HOME/MENU -->
  <header class="absolute top-0 left-0 w-full z-50 bg-transparent text-white">
    <div class="max-w-6xl mx-auto flex justify-between items-center py-6 px-4 md:px-0">
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

        <!-- CART (vetëm kur logged) -->
        <RouterLink
          v-if="auth.isLogged()"
          to="/cart"
          class="relative ml-1 px-3 py-2 rounded-lg border border-white/20 hover:bg-white/10 transition"
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

        <!-- LOGIN / LOGOUT -->
        <RouterLink
          v-if="!auth.isLogged()"
          to="/login"
          class="ml-2 px-4 py-2 rounded-lg border border-gold text-gold hover:bg-gold hover:text-black transition"
        >
          Login
        </RouterLink>

        <button
          v-else
          @click="handleLogout"
          class="ml-2 px-4 py-2 rounded-lg border border-red-400 text-red-400 hover:bg-red-400 hover:text-black transition"
        >
          Logout
        </button>
      </nav>
    </div>
  </header>

  <!-- PAGE -->
  <div class="min-h-screen bg-gradient-to-b from-black via-slate-900 to-zinc-900 text-white pt-28">
    <div class="max-w-5xl mx-auto px-4 py-10">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">
          Cart <span class="text-gold">🛒</span>
        </h1>

        <button
          v-if="cart.items.length"
          @click="cart.clear()"
          class="px-4 py-2 rounded-lg border border-red-400 text-red-300 hover:bg-red-400 hover:text-black transition"
        >
          Clear
        </button>
      </div>

      <div v-if="!cart.items.length" class="bg-white/5 border border-white/10 rounded-2xl p-6 text-gray-300">
        Cart është bosh.
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="it in cart.items"
          :key="it.id"
          class="bg-white/5 border border-white/10 rounded-2xl p-5 flex items-center justify-between"
        >
          <div>
            <div class="font-semibold">{{ it.name }}</div>
            <div class="text-sm text-gray-300">€{{ Number(it.price ?? 0).toFixed(2) }}</div>
          </div>

          <div class="flex items-center gap-3">
            <button
              @click="cart.dec(it.id)"
              class="w-9 h-9 rounded-lg border border-white/20 hover:bg-white/10 transition"
            >
              -
            </button>

            <div class="min-w-[28px] text-center font-semibold">{{ it.qty }}</div>

            <button
              @click="cart.inc(it.id)"
              class="w-9 h-9 rounded-lg border border-white/20 hover:bg-white/10 transition"
            >
              +
            </button>

            <div class="w-24 text-right font-semibold text-gold">
              €{{ (Number(it.qty ?? 0) * Number(it.price ?? 0)).toFixed(2) }}
            </div>

            <button
              @click="cart.removeItem(it.id)"
              class="px-3 py-2 rounded-lg border border-red-400 text-red-300 hover:bg-red-400 hover:text-black transition"
            >
              Remove
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between mt-6 bg-white/5 border border-white/10 rounded-2xl p-5">
          <div class="text-gray-300">Total</div>
          <div class="text-2xl font-bold text-gold">
            €{{ Number(cart.total ?? 0).toFixed(2) }}
          </div>
        </div>

        <RouterLink
  to="/checkout"
  class="block text-center w-full mt-4 py-3 rounded-xl bg-gold text-black font-semibold hover:bg-yellow-400 transition"
>
  Checkout
</RouterLink>


      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from "vue-router"
import { useCartStore } from "../store/cart"
import { useAuthStore } from "../store/auth"

const router = useRouter()
const cart = useCartStore()
const auth = useAuthStore()

function goCheckout() {
  if (!cart.items.length) return
  router.push("/checkout")
}

async function handleLogout() {
  await auth.logout()
  cart.clear() // opsionale: pastro cart-in pas logout
  router.push("/login")
}
</script>

<style scoped>
.text-gold { color: #d4af37; }
.bg-gold { background-color: #d4af37; }
</style>
