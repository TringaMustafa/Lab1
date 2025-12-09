<template>
  <div>
    <h1 class="text-4xl font-bold mb-8 text-gray-800">Orders</h1>

    <!-- Filter Tabs -->
    <div class="flex gap-4 mb-8 border-b border-gray-300">
      <button 
        v-for="status in ['All', 'Pending', 'In Progress', 'Served', 'Completed']"
        :key="status"
        @click="filterStatus = status"
        class="px-6 py-3 font-semibold border-b-2 transition"
        :class="filterStatus === status 
          ? 'border-blue-600 text-blue-600' 
          : 'border-transparent text-gray-600 hover:text-gray-800'"
      >
        {{ status }}
      </button>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
      <div 
        v-for="order in filteredOrders" 
        :key="order.id"
        class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition"
      >
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-xl font-bold text-gray-800">Order #{{ order.id }}</h3>
            <p class="text-sm text-gray-600">Table {{ order.table }} • {{ order.time }}</p>
          </div>
          <span 
            class="px-4 py-2 rounded-full font-semibold"
            :class="getStatusClass(order.status)"
          >
            {{ order.status }}
          </span>
        </div>

        <!-- Order Items -->
        <div class="border-t border-b py-4 mb-4">
          <div v-for="item in order.items" :key="item.id" class="flex justify-between text-gray-700 mb-2">
            <span>{{ item.qty }}x {{ item.name }}</span>
            <span>${{ item.subtotal }}</span>
          </div>
        </div>

        <!-- Order Footer -->
        <div class="flex items-center justify-between">
          <div class="text-lg font-bold text-gray-800">
            Total: <span class="text-blue-600">${{ order.total }}</span>
          </div>
          <div class="flex gap-3">
            <button v-if="order.status === 'Pending'" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition">
              Start Cooking
            </button>
            <button v-if="order.status === 'In Progress'" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition">
              Mark Served
            </button>
            <button class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg font-semibold transition">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const filterStatus = ref('All')

const orders = ref([
  { 
    id: 1001, 
    table: 5, 
    status: 'Pending', 
    time: '2 min ago',
    items: [
      { id: 1, name: 'Margherita Pizza', qty: 1, subtotal: 14.99 },
      { id: 2, name: 'Caesar Salad', qty: 2, subtotal: 17.00 }
    ],
    total: 31.99
  },
  { 
    id: 1002, 
    table: 8, 
    status: 'In Progress', 
    time: '15 min ago',
    items: [
      { id: 3, name: 'Grilled Salmon', qty: 1, subtotal: 22.99 }
    ],
    total: 22.99
  },
  { 
    id: 1003, 
    table: 3, 
    status: 'Served', 
    time: '25 min ago',
    items: [
      { id: 4, name: 'Chocolate Cake', qty: 2, subtotal: 15.98 }
    ],
    total: 15.98
  },
  { 
    id: 1004, 
    table: 12, 
    status: 'Completed', 
    time: '45 min ago',
    items: [
      { id: 5, name: 'Coca Cola', qty: 3, subtotal: 8.97 }
    ],
    total: 8.97
  },
])

const filteredOrders = computed(() => {
  return filterStatus.value === 'All'
    ? orders.value
    : orders.value.filter(order => order.status === filterStatus.value)
})

function getStatusClass(status) {
  const classes = {
    'Pending': 'bg-yellow-200 text-yellow-800',
    'In Progress': 'bg-blue-200 text-blue-800',
    'Served': 'bg-green-200 text-green-800',
    'Completed': 'bg-purple-200 text-purple-800'
  }
  return classes[status] || 'bg-gray-200 text-gray-800'
}
</script>