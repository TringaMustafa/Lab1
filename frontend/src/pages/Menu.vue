<template>
  <header class="absolute top-0 left-0 w-full z-50 bg-transparent text-white">
    <div class="max-w-6xl mx-auto flex flex-col md:flex-row md:justify-between md:items-center gap-4 py-4 md:py-6 px-4 md:px-0">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-2xl bg-gold flex items-center justify-center text-black text-xl">
          🍽️
        </div>
        <span class="text-2xl font-bold tracking-wide text-gold">TableFlow</span>
      </div>

      <nav class="w-full md:w-auto flex flex-wrap items-center justify-start md:justify-end gap-3 md:gap-6 text-sm md:text-base">
        <RouterLink to="/" class="hover:text-gold transition">Home</RouterLink>
        <RouterLink to="/menu" class="hover:text-gold transition">Menu</RouterLink>
        <RouterLink to="/tables" class="hover:text-gold transition">Tables</RouterLink>

        <RouterLink
          v-if="auth.user?.role === 'admin'"
          to="/dashboard"
          class="px-3 py-2 rounded-lg border border-gold text-gold hover:bg-gold hover:text-black transition"
          title="Dashboard"
        >
          Dashboard
        </RouterLink>

        <RouterLink
          v-if="isLogged"
          to="/history"
          class="hover:text-gold transition"
        >
          History
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
          v-if="!isLogged"
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

  <div class="min-h-screen bg-gradient-to-b from-black via-slate-900 to-zinc-900 text-white pt-36 md:pt-28">
    <div class="max-w-6xl mx-auto px-4 py-8 md:py-10">
      <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
          <h1 class="text-3xl md:text-4xl font-bold">
            Menuja jonë <span class="text-gold">online</span>
          </h1>
          <p class="text-gray-300 mt-2 text-sm md:text-base max-w-xl">
            Shfleto menunë e restorantit. Për të bërë porosi, së pari hyr ose regjistrohu në llogarinë tënde.
          </p>
        </div>

        <p
          v-if="!isLogged"
          class="text-sm md:text-base font-semibold px-4 md:px-5 py-3 rounded-xl bg-red-500/15 text-red-400 border border-red-500/50 shadow-sm max-w-full md:max-w-md"
        >
          Për të porositur, ju lutem
          <RouterLink to="/login?redirect=/menu" class="text-gold underline">kyçu</RouterLink>.
        </p>
      </div>

      <div class="flex gap-3 overflow-x-auto pb-2 mb-6">
        <button
          v-for="c in categories"
          :key="c"
          @click="activeCategory = c"
          class="px-4 py-2 rounded-full border text-sm font-medium whitespace-nowrap transition"
          :class="
            activeCategory === c
              ? 'bg-gold text-black border-gold'
              : 'border-gray-600 text-gray-200 hover:bg-white/5'
          "
        >
          {{ c }}
        </button>
      </div>

      <div v-if="loading" class="text-sm text-gray-300">Loading...</div>
      <div v-else-if="error" class="text-sm text-rose-300">{{ error }}</div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
        <div
          v-for="item in filteredMenu"
          :key="item.id"
          class="bg-white/5 border border-white/10 rounded-2xl p-4 md:p-5 flex flex-col justify-between hover:border-gold/60 transition"
        >
          <div>
            <h3 class="text-lg font-semibold">{{ item.name }}</h3>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-wide">
              {{ item.category }}
            </p>
            <p class="text-xs text-gray-400 mt-2">
              {{ item.description || "No description." }}
            </p>
            <p v-if="item.is_available === false" class="text-xs text-rose-300 mt-2">
              Not available
            </p>
          </div>

          <div class="flex items-center justify-between gap-3 mt-4">
            <p class="text-lg md:text-xl font-bold text-gold">
              €{{ Number(item.price ?? 0).toFixed(2) }}
            </p>

            <button
              @click="handleOrder(item)"
              :disabled="!isLogged || item.is_available === false"
              class="px-4 py-2 rounded-xl text-sm font-semibold transition"
              :class="!isLogged || item.is_available === false
                ? 'bg-gray-500/40 text-gray-300 cursor-not-allowed'
                : 'bg-gold text-black hover:bg-yellow-400 cursor-pointer'"
            >
              Porosit
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue"
import { useRouter } from "vue-router"
import api from "../api/axios"
import { useAuthStore } from "../store/auth"
import { useCartStore } from "../store/cart"

const router = useRouter()
const auth = useAuthStore()
const cart = useCartStore()

const isLogged = computed(() => auth.isLogged())

const loading = ref(false)
const error = ref("")
const menu = ref([])
const activeCategory = ref("Të gjitha")

const categories = computed(() => {
  const set = new Set(["Të gjitha"])
  menu.value.forEach(item => {
    if (item.category) set.add(item.category)
  })
  return Array.from(set)
})

const filteredMenu = computed(() => {
  if (activeCategory.value === "Të gjitha") return menu.value
  return menu.value.filter(m => m.category === activeCategory.value)
})

async function loadMenu() {
  loading.value = true
  error.value = ""
  try {
    const res = await api.get("/public-menus", {
      params: { available: 1 }
    })
    menu.value = res.data.data ?? res.data
  } catch (e) {
    error.value = e?.response?.data?.message || "Gabim gjatë ngarkimit të menusë."
  } finally {
    loading.value = false
  }
}

function handleOrder(item) {
  if (!isLogged.value) {
    router.push({ path: "/login", query: { redirect: "/menu" } })
    return
  }

  if (item.is_available === false) return

  cart.addItem(item)
  alert(`U shtua në cart: ${item.name} ✅`)
}

async function handleLogout() {
  await auth.logout()
  router.push("/login")
}

onMounted(loadMenu)
</script>

<style scoped>
.text-gold { color: #d4af37; }
.bg-gold { background-color: #d4af37; }
</style>