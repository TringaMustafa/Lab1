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


<div class="min-h-screen bg-gradient-to-b from-black via-slate-900 to-zinc-900 text-white pt-28">
    <div class="max-w-6xl mx-auto px-4 py-10">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
          <h1 class="text-3xl md:text-4xl font-bold">
            Menuja jonë
            <span class="text-gold">online</span>
          </h1>
          <p class="text-gray-300 mt-2 text-sm md:text-base max-w-xl">
            Shfleto menunë e restorantit. Për të bërë porosi, së pari hyr ose regjistrohu në llogarinë tënde.
          </p>
        </div>

        <p
          v-if="!isLogged"
          class="text-xs md:text-sm px-4 py-2 rounded-full bg-rose-500/10 text-rose-300 border border-rose-500/40"
        >
          ⚠ Nuk mund të porosisësh pa u futur në llogari.
        </p>
      </div>

      <!-- Categories -->
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

      <!-- Menu items -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="item in filteredMenu"
          :key="item.name"
          class="bg-white/5 border border-white/10 rounded-2xl p-5 flex flex-col justify-between hover:border-gold/60 transition"
        >
          <div>
            <h3 class="text-lg font-semibold">{{ item.name }}</h3>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-wide">
              {{ item.category }}
            </p>
            <p class="text-xs text-gray-400 mt-2">
              {{ item.description }}
            </p>
          </div>

          <div class="flex items-center justify-between mt-4">
            <p class="text-xl font-bold text-gold">
              €{{ item.price.toFixed(2) }}
            </p>

            <button
              @click="handleOrder(item)"
              class="px-4 py-2 rounded-xl text-sm font-semibold bg-gold text-black hover:bg-yellow-400 transition"
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
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../store/auth'

const router = useRouter()
const auth = useAuthStore()

const isLogged = computed(() => auth.isLogged())

const categories = ['Të gjitha', 'Pizza', 'Burger', 'Pasta', 'Saladë', 'Pije']

const activeCategory = ref('Të gjitha')

const menu = ref([
  {
    name: 'Margherita',
    category: 'Pizza',
    price: 6.5,
    description: 'Domate, mocarela, borzilok i freskët.'
  },
  {
    name: 'Chicken Burger',
    category: 'Burger',
    price: 7.0,
    description: 'Burger pule, sallatë, sos shtëpie.'
  },
  {
    name: 'Carbonara',
    category: 'Pasta',
    price: 8.0,
    description: 'Pasta me pancetë, vezë dhe parmixhan.'
  },
  {
    name: 'Caesar Salad',
    category: 'Saladë',
    price: 5.5,
    description: 'Sallatë me mish pule, croutons dhe salcë Caesar.'
  },
  {
    name: 'Cola',
    category: 'Pije',
    price: 2.0,
    description: 'Pije freskuese.'
  }
])

const filteredMenu = computed(() => {
  if (activeCategory.value === 'Të gjitha') return menu.value
  return menu.value.filter(m => m.category === activeCategory.value)
})

function handleOrder(item) {
  if (!isLogged.value) {
    router.push({ path: '/login', query: { redirect: '/menu' } })
    return
  }

  // këtu më vonë lidhe me backend/cart
  console.log('Order item', item)
  alert(`(DEMO) Do të porosisje: ${item.name}`)
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
