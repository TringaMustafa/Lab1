<template>
  <!-- NAVBAR -->
  <header class="absolute top-0 left-0 w-full z-50 bg-transparent text-white">
    <div class="max-w-6xl mx-auto flex justify-between items-center py-6 px-4 md:px-0">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-2xl bg-gold flex items-center justify-center text-black text-xl">🍽️</div>
        <span class="text-2xl font-bold tracking-wide text-gold">TableFlow</span>
      </div>

      <nav class="flex items-center gap-6 text-sm md:text-base">
        <RouterLink to="/" class="hover:text-gold transition">Home</RouterLink>
        <RouterLink to="/menu" class="hover:text-gold transition">Menu</RouterLink>
        <RouterLink to="/tables" class="hover:text-gold transition">Tables</RouterLink>
        <RouterLink to="/login" class="ml-4 px-4 py-2 rounded-lg border border-gold text-gold hover:bg-gold hover:text-black transition">Login</RouterLink>
      </nav>
    </div>
  </header>

  <!-- BACKGROUND -->
  <div class="min-h-screen bg-gradient-to-b from-black via-slate-900 to-zinc-900 text-white">
    <div class="flex justify-center items-start pt-40 w-full px-4">

      <div class="w-full max-w-md bg-white/5 border border-white/10 rounded-3xl p-8 shadow-2xl backdrop-blur">

        <h1 class="text-2xl font-bold mb-2 text-center">
          Krijo llogari në <span class="text-gold">TableFlow</span>
        </h1>

        <!-- ERROR MESSAGE -->
        <p v-if="error" class="text-rose-400 text-sm text-center mb-3">{{ error }}</p>

        <!-- SUCCESS MESSAGE -->
        <p v-if="success" class="text-emerald-400 text-sm text-center mb-3">{{ success }}</p>

        <form @submit.prevent="handleRegister" class="space-y-4">

          <div>
            <label class="block text-sm mb-1">Emri</label>
            <input v-model="name" required class="w-full px-3 py-2 rounded-lg bg-black/40 border border-gray-600 text-sm text-white" />
          </div>

          <div>
            <label class="block text-sm mb-1">Email</label>
            <input v-model="email" type="email" required
                   class="w-full px-3 py-2 rounded-lg bg-black/40 border border-gray-600 text-sm text-white" />
          </div>

          <!-- ROLE SELECT -->
          <div>
            <label class="block text-sm mb-1">Roli</label>
            <select v-model="role" class="w-full px-3 py-2 rounded-lg bg-black/40 border border-gray-600 text-white">
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <div>
            <label class="block text-sm mb-1">Fjalëkalimi</label>
            <input v-model="password" type="password" required
                   class="w-full px-3 py-2 rounded-lg bg-black/40 border border-gray-600 text-sm text-white" />
          </div>

          <button
            type="submit"
            class="w-full mt-2 py-2.5 rounded-xl bg-gold text-black font-semibold text-sm hover:bg-yellow-400 transition">
            Regjistrohu
          </button>
        </form>

        <p class="text-xs text-gray-400 text-center mt-4">
          Ke llogari? <RouterLink to="/login" class="text-gold hover:underline">Hyr këtu</RouterLink>
        </p>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import api from "../api/axios"

const name = ref("")
const email = ref("")
const password = ref("")
const role = ref("user")

const error = ref("")
const success = ref("")

async function handleRegister() {
  error.value = ""
  success.value = ""

  // Vetëm këta email mund të jenë admin:
  const allowedAdmins = ["admin1@example.com", "admin2@example.com"]

  if (role.value === "admin" && !allowedAdmins.includes(email.value)) {
    error.value = "Ky email nuk lejohet të regjistrohet si admin!"
    return
  }

  try {
    await api.post("/register", {
      name: name.value,
      email: email.value,
      password: password.value,
      role: role.value,
    })

    success.value = "U regjistrove me sukses! Tani hyr në llogari."
    name.value = ""
    email.value = ""
    password.value = ""

  } catch (err) {
    error.value = err.response?.data?.message || "Gabim gjatë regjistrimit."
  }
}
</script>

<style>
.text-gold { color: #d4af37 }
.bg-gold { background-color: #d4af37 }
</style>
