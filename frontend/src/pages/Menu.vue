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

        <p v-if="!isLogged" class=" text-sm md:text-base
    font-semibold
    px-5 py-3
    rounded-xl
    bg-red-500/15
    text-red-400
    border border-red-500/50
    shadow-sm">
  Për të porositur, ju lutem
  <RouterLink to="/login?redirect=/menu" class="text-gold underline">kyçu</RouterLink>.
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
  :disabled="!isLogged"
  class="px-4 py-2 rounded-xl text-sm font-semibold transition"
  :class="!isLogged
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
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../store/auth'
import { useCartStore } from '../store/cart'   // ✅ SHTO KETE

const router = useRouter()
const auth = useAuthStore()
const cart = useCartStore()                   // ✅ tani ekziston

const isLogged = computed(() => auth.isLogged())

const categories = ['Të gjitha', 'Pizza', 'Burger', 'Pasta', 'Saladë', 'Pije']
const activeCategory = ref('Të gjitha')

const menu = ref([
  { id: 1, name: 'Margherita', category: 'Pizza', price: 6.5, description: 'Domate, mocarela, borzilok i freskët.' },
  { id: 2, name: 'Chicken Burger', category: 'Burger', price: 7.0, description: 'Burger pule, sallatë, sos shtëpie.' },
  { id: 3, name: 'Carbonara', category: 'Pasta', price: 8.0, description: 'Pasta me pancetë, vezë dhe parmixhan.' },
  { id: 4, name: 'Caesar Salad', category: 'Saladë', price: 5.5, description: 'Sallatë me mish pule, croutons dhe salcë Caesar.' },
  { id: 5, name: 'Cola', category: 'Pije', price: 2.0, description: 'Pije freskuese.' }
])

const filteredMenu = computed(() => {
  if (activeCategory.value === 'Të gjitha') return menu.value
  return menu.value.filter(m => m.category === activeCategory.value)
})

function handleOrder(item) {
  if (!isLogged.value) {
    alert("Nuk mund të porosisësh pa u kyç! Ju lutem bëhuni login.")
    router.push({ path: '/login', query: { redirect: '/menu' } })
    return
  }

  // ✅ shtoje në cart (localStorage)
  cart.addItem(item)
  alert(`U shtua në cart: ${item.name} ✅`)
}
</script>

<style scoped>
.text-gold { color: #d4af37; }
.bg-gold { background-color: #d4af37; }
</style>
