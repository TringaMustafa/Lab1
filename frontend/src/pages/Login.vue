<template>
  <!-- NAVBAR TRANSPARENT -->
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

        <!-- Login button (gjithmonë në login page) -->
        <span class="ml-4 px-4 py-2 rounded-lg border border-gold text-gold">
          Login
        </span>
      </nav>
    </div>
  </header>

  <!-- BACKGROUND -->
  <div class="min-h-screen bg-gradient-to-b from-black via-slate-900 to-zinc-900 text-white">
    <div class="flex justify-center items-start pt-40 w-full px-4">
      <div class="w-full max-w-md bg-white/5 border border-white/10 rounded-3xl p-8 shadow-2xl backdrop-blur">

        <h1 class="text-2xl font-bold mb-2 text-center">
          Mirësevjen në <span class="text-gold">TableFlow</span>
        </h1>

        <p class="text-gray-300 text-sm text-center mb-6">
          Hyr në llogarinë tënde për të vazhduar.
        </p>

        <!-- ERROR -->
        <p v-if="error" class="text-rose-400 text-sm text-center mb-3">
          {{ error }}
        </p>

        <form @submit.prevent="handleLogin" class="space-y-4">
          <div>
            <label class="block text-sm mb-1">Email</label>
            <input
              v-model="email"
              type="email"
              required
              class="w-full px-3 py-2 rounded-lg bg-black/40 border border-gray-600 text-sm text-white focus:border-gold outline-none"
            />
          </div>

          <div>
            <label class="block text-sm mb-1">Fjalëkalimi</label>
            <input
              v-model="password"
              type="password"
              required
              class="w-full px-3 py-2 rounded-lg bg-black/40 border border-gray-600 text-sm text-white focus:border-gold outline-none"
            />
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full mt-2 py-2.5 rounded-xl bg-gold text-black font-semibold text-sm hover:bg-yellow-400 disabled:opacity-60 transition"
          >
            {{ loading ? "Duke u futur..." : "Hyr" }}
          </button>
        </form>

        <p class="text-xs text-gray-400 text-center mt-4">
          Nuk ke llogari?
          <RouterLink to="/register" class="text-gold hover:underline">
            Regjistrohu
          </RouterLink>
        </p>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import { useRouter, useRoute } from "vue-router"
import { useAuthStore } from "../store/auth"

const email = ref("")
const password = ref("")
const error = ref("")
const loading = ref(false)

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

async function handleLogin() {
  error.value = ""
  loading.value = true

  try {
    const res = await auth.login(email.value, password.value)

    // ADMIN → dashboard
    if (res.data.user.role === "admin") {
      return router.push("/dashboard")
    }

    // USER → redirect (p.sh /menu) ose home
    const redirect = route.query.redirect || "/"
    router.push(redirect)

  } catch (e) {
    error.value =
      e?.response?.data?.message ||
      e?.response?.data?.error ||
      "Email ose fjalëkalim i pasaktë!"
  } finally {
    loading.value = false
  }
}
</script>

<style>
.text-gold { color: #d4af37 }
.bg-gold { background-color: #d4af37 }
</style>
