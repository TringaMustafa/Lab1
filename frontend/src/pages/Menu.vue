<template>
  <div>
    <h1 class="text-4xl font-bold mb-8 text-gray-800">Menu</h1>

    <!-- Categories Filter -->
    <div class="flex gap-4 mb-8 overflow-x-auto pb-4">
      <button 
        v-for="cat in categories" 
        :key="cat"
        @click="selectedCategory = cat"
        class="px-6 py-2 rounded-full font-semibold whitespace-nowrap transition"
        :class="selectedCategory === cat 
          ? 'bg-blue-600 text-white' 
          : 'bg-white text-gray-700 border border-gray-300 hover:border-blue-600'"
      >
        {{ cat }}
      </button>
    </div>

    <!-- Menu Items Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="item in filteredItems" 
        :key="item.id"
        class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden"
      >
        <div class="h-48 bg-gradient-to-br from-blue-300 to-blue-500 flex items-center justify-center text-4xl">
          {{ item.emoji }}
        </div>
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-2">{{ item.name }}</h3>
          <p class="text-gray-600 text-sm mb-4">{{ item.description }}</p>
          <div class="flex items-center justify-between">
            <span class="text-2xl font-bold text-blue-600">${{ item.price }}</span>
            <button 
              @click="addToCart(item)"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition"
            >
              ➕ Add
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Cart Summary (bottom sticky) -->
    <div v-if="cart.length > 0" class="fixed bottom-0 left-0 right-0 bg-white border-t-2 border-blue-600 p-6 shadow-lg">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="text-lg font-semibold">
          Cart: {{ cart.length }} items - <span class="text-blue-600">${{ cartTotal }}</span>
        </div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
          Proceed to Order
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const categories = ['All', 'Appetizers', 'Main Course', 'Desserts', 'Drinks']
const selectedCategory = ref('All')
const cart = ref([])

const items = ref([
  { id: 1, name: 'Margherita Pizza', category: 'Main Course', price: 14.99, emoji: '🍕', description: 'Classic cheese and tomato' },
  { id: 2, name: 'Caesar Salad', category: 'Appetizers', price: 8.50, emoji: '🥗', description: 'Fresh romaine with croutons' },
  { id: 3, name: 'Grilled Salmon', category: 'Main Course', price: 22.99, emoji: '🐟', description: 'With seasonal vegetables' },
  { id: 4, name: 'Chocolate Cake', category: 'Desserts', price: 7.99, emoji: '🍰', description: 'Rich and delicious' },
  { id: 5, name: 'Coca Cola', category: 'Drinks', price: 2.99, emoji: '🥤', description: 'Cold beverage' },
  { id: 6, name: 'Bruschetta', category: 'Appetizers', price: 6.99, emoji: '🍞', description: 'Toasted bread with toppings' },
])

const filteredItems = computed(() => {
  return selectedCategory.value === 'All' 
    ? items.value 
    : items.value.filter(item => item.category === selectedCategory.value)
})

const cartTotal = computed(() => {
  return cart.value.reduce((sum, item) => sum + item.price, 0).toFixed(2)
})

function addToCart(item) {
  cart.value.push(item)
}
</script>