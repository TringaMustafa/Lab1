<template>

<!-- NAVBAR TRANSPARENT SI HOME -->
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

      <RouterLink
        to="/login"
        class="ml-4 px-4 py-2 rounded-lg border border-gold text-gold hover:bg-gold hover:text-black transition"
      >
        Login
      </RouterLink>
    </nav>

  </div>
</header>





  <!-- BACKGROUND -->
<div class="min-h-screen bg-gradient-to-b from-black via-slate-900 to-zinc-900 text-white pt-28">
    <div class="max-w-6xl mx-auto px-4 py-10">

      <!-- HEADER (zbritur me pt-20) -->
      <div class="pt-20 flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
          <h1 class="text-3xl md:text-4xl font-bold">
            Disponueshmëria e
            <span class="text-gold">tavolinave</span>
          </h1>

          <p class="text-gray-300 mt-2 text-sm md:text-base max-w-xl">
            Shiko cilat tavolina janë të lira, të zëna apo të rezervuara. 
            Për të bërë rezervim, së pari hyr ose krijo llogari.
          </p>
        </div>

        <p
          v-if="!isLogged"
          class="text-xs md:text-sm px-4 py-2 rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/40"
        >
          ⚠ Nuk mund të bësh rezervim pa u futur në llogari.
        </p>
      </div>



      <!-- TABLES GRID -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
        <div
          v-for="t in tables"
          :key="t.id"
          class="rounded-2xl bg-white/5 border border-white/10 p-5 flex flex-col justify-between hover:border-gold/60 transition"
        >
          <div>
            <p class="text-lg font-semibold">Tavolina {{ t.name }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ t.seats }} vende</p>
          </div>

          <div class="mt-4 flex items-center justify-between">
            <span
              class="px-3 py-1 rounded-full text-xs font-semibold"
              :class="statusClass(t.status)"
            >
              {{ t.status }}
            </span>

            <button
              @click="handleReserve(t)"
              class="px-3 py-1 rounded-lg text-xs font-semibold border border-gold text-gold hover:bg-gold hover:text-black transition"
            >
              Rezervo
            </button>
          </div>
        </div>
      </div>


    </div>
  </div>

</template>



<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../store/auth'

const router = useRouter()
const auth = useAuthStore()
const isLogged = computed(() => auth.isLogged())

const tables = ref([
  { id: 1, name: 'T1', seats: 2, status: 'E LIRË' },
  { id: 2, name: 'T2', seats: 4, status: 'E ZËNË' },
  { id: 3, name: 'T3', seats: 4, status: 'E REZERVUAR' },
  { id: 4, name: 'T4', seats: 6, status: 'E LIRË' },
  { id: 5, name: 'T5', seats: 2, status: 'DUKE U PASRTUAR' }
])

function statusClass(status) {
  if (status === 'E LIRË') return 'bg-emerald-500/15 text-emerald-300'
  if (status === 'E ZËNË') return 'bg-rose-500/15 text-rose-300'
  if (status === 'E REZERVUAR') return 'bg-blue-500/15 text-blue-300'
  return 'bg-amber-500/15 text-amber-300'
}

function handleReserve(table) {
  if (!isLogged.value) {
    router.push({ path: '/login', query: { redirect: '/tables' } })
    return
  }

  alert(`(DEMO) Do të rezervosh: Tavolina ${table.name}`)
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
