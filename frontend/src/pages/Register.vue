<template>
  <div class="min-h-screen bg-gradient-to-b from-black via-slate-900 to-zinc-900 text-white">
    <div class="flex justify-center items-start pt-40 w-full px-4">
      <div class="w-full max-w-md bg-white/5 border border-white/10 rounded-3xl p-8 shadow-2xl backdrop-blur">

        <h1 class="text-2xl font-bold mb-2 text-center">
          Regjistrohu në <span class="text-gold">TableFlow</span>
        </h1>

        <p v-if="error" class="text-rose-400 text-sm text-center mb-3">{{ error }}</p>
        <p v-if="success" class="text-emerald-400 text-sm text-center mb-3">{{ success }}</p>

        <form @submit.prevent="handleRegister" class="space-y-4">
          <div>
            <label class="block text-sm mb-1">Emri</label>
            <input v-model="name" required
              class="w-full px-3 py-2 rounded-lg bg-black/40 border border-gray-600 text-sm text-white" />
          </div>

          <div>
            <label class="block text-sm mb-1">Email</label>
            <input v-model="email" type="email" required
              class="w-full px-3 py-2 rounded-lg bg-black/40 border border-gray-600 text-sm text-white" />
          </div>

          <div>
            <label class="block text-sm mb-1">Fjalëkalimi</label>
            <input v-model="password" type="password" required
              class="w-full px-3 py-2 rounded-lg bg-black/40 border border-gray-600 text-sm text-white" />
          </div>

          <button type="submit"
            class="w-full mt-2 py-2.5 rounded-xl bg-gold text-black font-semibold text-sm hover:bg-yellow-400 transition">
            Regjistrohu
          </button>
        </form>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import { useAuthStore } from "../store/auth"

const name = ref("")
const email = ref("")
const password = ref("")
const error = ref("")
const success = ref("")

const router = useRouter()
const auth = useAuthStore()

async function handleRegister() {
  error.value = ""
  success.value = ""

  try {
    await auth.register(name.value, email.value, password.value)
    success.value = "U regjistrove me sukses! Tani kyçu."
    setTimeout(() => router.push("/login"), 800)
  } catch (err) {
    error.value = err.response?.data?.message || "Gabim gjatë regjistrimit."
  }
}
</script>

<style>
.text-gold { color: #d4af37 }
.bg-gold { background-color: #d4af37 }
</style>
