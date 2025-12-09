<template>
  <div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-4xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600 mt-2">{{ currentTime }}</p>
      </div>
      <button @click="refreshData" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
        🔄 Refresh
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Active Orders -->
      <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-blue-100 text-sm font-semibold">Active Orders</p>
            <p class="text-4xl font-bold mt-2">{{ activeOrders }}</p>
          </div>
          <div class="text-5xl opacity-30">📦</div>
        </div>
        <div class="text-xs text-blue-100 flex items-center gap-1">
          <span class="text-green-300">↑</span> 12% vs yesterday
        </div>
      </div>

      <!-- Occupied Tables -->
      <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-green-100 text-sm font-semibold">Occupied Tables</p>
            <p class="text-4xl font-bold mt-2">{{ occupiedTables }}/{{ totalTables }}</p>
          </div>
          <div class="text-5xl opacity-30">🪑</div>
        </div>
        <div class="w-full bg-green-700 rounded-full h-2 mt-4">
          <div class="bg-green-300 h-2 rounded-full" :style="{ width: (occupiedTables/totalTables)*100 + '%' }"></div>
        </div>
      </div>

      <!-- Today's Revenue -->
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-purple-100 text-sm font-semibold">Today's Revenue</p>
            <p class="text-4xl font-bold mt-2">${{ todayRevenue }}</p>
          </div>
          <div class="text-5xl opacity-30">💰</div>
        </div>
        <div class="text-xs text-purple-100">
          <span class="text-green-300">↑</span> $450 vs yesterday
        </div>
      </div>

      <!-- Avg Prep Time -->
      <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-orange-100 text-sm font-semibold">Avg Prep Time</p>
            <p class="text-4xl font-bold mt-2">{{ avgPrepTime }} min</p>
          </div>
          <div class="text-5xl opacity-30">⏱️</div>
        </div>
        <div class="text-xs text-orange-100">Kitchen efficiency</div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-2xl font-bold text-gray-800 mb-4">Quick Actions</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <router-link 
          to="/menu"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-4 rounded-lg font-semibold transition flex items-center justify-center gap-2"
        >
          ➕ New Order
        </router-link>
        <router-link 
          to="/tables"
          class="bg-green-600 hover:bg-green-700 text-white px-6 py-4 rounded-lg font-semibold transition flex items-center justify-center gap-2"
        >
          🪑 Manage Tables
        </router-link>
        <router-link 
          to="/orders"
          class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-4 rounded-lg font-semibold transition flex items-center justify-center gap-2"
        >
          📋 View Orders
        </router-link>
        <button 
          @click="printBill"
          class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-4 rounded-lg font-semibold transition flex items-center justify-center gap-2"
        >
          🖨️ Print Bill
        </button>
      </div>
    </div>

    <!-- Order Status Kanban -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Order Status Board</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Pending -->
        <div class="bg-yellow-50 border-2 border-yellow-300 rounded-lg p-4">
          <h3 class="font-bold text-yellow-800 mb-4 flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
            Pending ({{ kanbanOrders.pending.length }})
          </h3>
          <div class="space-y-3">
            <div 
              v-for="order in kanbanOrders.pending" 
              :key="order.id"
              class="bg-white p-3 rounded border border-yellow-200 hover:shadow-md transition cursor-pointer"
              @click="selectOrder(order)"
            >
              <p class="font-semibold text-sm">#{{ order.id }}</p>
              <p class="text-xs text-gray-600">Table {{ order.table }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ order.items.length }} items</p>
            </div>
          </div>
        </div>

        <!-- In Progress -->
        <div class="bg-blue-50 border-2 border-blue-300 rounded-lg p-4">
          <h3 class="font-bold text-blue-800 mb-4 flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-blue-400 animate-pulse"></span>
            In Progress ({{ kanbanOrders.inProgress.length }})
          </h3>
          <div class="space-y-3">
            <div 
              v-for="order in kanbanOrders.inProgress" 
              :key="order.id"
              class="bg-white p-3 rounded border border-blue-200 hover:shadow-md transition cursor-pointer"
              @click="selectOrder(order)"
            >
              <p class="font-semibold text-sm">#{{ order.id }}</p>
              <p class="text-xs text-gray-600">Table {{ order.table }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ order.prepTime }} min left</p>
            </div>
          </div>
        </div>

        <!-- Served -->
        <div class="bg-green-50 border-2 border-green-300 rounded-lg p-4">
          <h3 class="font-bold text-green-800 mb-4 flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-green-400"></span>
            Served ({{ kanbanOrders.served.length }})
          </h3>
          <div class="space-y-3">
            <div 
              v-for="order in kanbanOrders.served" 
              :key="order.id"
              class="bg-white p-3 rounded border border-green-200 hover:shadow-md transition cursor-pointer"
              @click="selectOrder(order)"
            >
              <p class="font-semibold text-sm">#{{ order.id }}</p>
              <p class="text-xs text-gray-600">Table {{ order.table }}</p>
              <p class="text-xs text-green-600 font-semibold">${{ order.total }}</p>
            </div>
          </div>
        </div>

        <!-- Completed -->
        <div class="bg-purple-50 border-2 border-purple-300 rounded-lg p-4">
          <h3 class="font-bold text-purple-800 mb-4 flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-purple-400"></span>
            Completed ({{ kanbanOrders.completed.length }})
          </h3>
          <div class="space-y-3">
            <div 
              v-for="order in kanbanOrders.completed" 
              :key="order.id"
              class="bg-white p-3 rounded border border-purple-200 hover:shadow-md transition cursor-pointer opacity-75"
              @click="selectOrder(order)"
            >
              <p class="font-semibold text-sm">#{{ order.id }}</p>
              <p class="text-xs text-gray-600">Table {{ order.table }}</p>
              <p class="text-xs text-gray-500">Completed</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-2xl font-bold text-gray-800 mb-4">Recent Activity</h2>
      <div class="space-y-4 max-h-64 overflow-y-auto">
        <div v-for="activity in recentActivity" :key="activity.id" class="flex items-start gap-4 pb-4 border-b last:border-b-0">
          <div class="text-2xl">{{ activity.emoji }}</div>
          <div class="flex-1">
            <p class="font-semibold text-gray-800">{{ activity.message }}</p>
            <p class="text-xs text-gray-500">{{ activity.time }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Detail Modal -->
    <div v-if="selectedOrder" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl w-full max-h-96 overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-3xl font-bold">Order #{{ selectedOrder.id }}</h2>
          <button @click="selectedOrder = null" class="text-gray-500 hover:text-gray-700 text-2xl">✕</button>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
          <div>
            <p class="text-gray-600">Table</p>
            <p class="text-2xl font-bold">{{ selectedOrder.table }}</p>
          </div>
          <div>
            <p class="text-gray-600">Status</p>
            <span class="text-lg font-bold px-3 py-1 rounded-full" :class="getStatusClass(selectedOrder.status)">
              {{ selectedOrder.status }}
            </span>
          </div>
        </div>

        <!-- Items -->
        <div class="border-t border-b py-4 mb-6">
          <h3 class="font-bold text-gray-800 mb-3">Order Items</h3>
          <div v-for="item in selectedOrder.items" :key="item.id" class="flex justify-between text-gray-700 mb-2">
            <span>{{ item.qty }}x {{ item.name }}</span>
            <span>${{ item.subtotal }}</span>
          </div>
        </div>

        <!-- Total & Actions -->
        <div class="flex items-center justify-between mb-6">
          <p class="text-2xl font-bold">Total: <span class="text-blue-600">${{ selectedOrder.total }}</span></p>
        </div>

        <div class="flex gap-3">
          <button v-if="selectedOrder.status === 'Pending'" @click="updateOrderStatus(selectedOrder.id, 'In Progress')" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition">
            Start Cooking
          </button>
          <button v-if="selectedOrder.status === 'In Progress'" @click="updateOrderStatus(selectedOrder.id, 'Served')" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold transition">
            Mark Served
          </button>
          <button v-if="selectedOrder.status === 'Served'" @click="updateOrderStatus(selectedOrder.id, 'Completed')" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg font-semibold transition">
            Mark Completed
          </button>
          <button @click="selectedOrder = null" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white py-2 rounded-lg font-semibold transition">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const currentTime = ref('')
const selectedOrder = ref(null)

// Stats
const activeOrders = ref(12)
const occupiedTables = ref(8)
const totalTables = ref(15)
const todayRevenue = ref('2,450')
const avgPrepTime = ref(28)

// Mock Orders Data
const allOrders = ref([
  { id: 1001, table: 5, status: 'Pending', prepTime: 15, items: [{ id: 1, name: 'Pizza', qty: 1, subtotal: 14.99 }, { id: 2, name: 'Salad', qty: 1, subtotal: 8.50 }], total: 23.49, time: '2 min ago' },
  { id: 1002, table: 8, status: 'In Progress', prepTime: 10, items: [{ id: 3, name: 'Salmon', qty: 1, subtotal: 22.99 }], total: 22.99, time: '10 min ago' },
  { id: 1003, table: 3, status: 'Served', prepTime: 0, items: [{ id: 4, name: 'Cake', qty: 2, subtotal: 15.98 }], total: 15.98, time: '20 min ago' },
  { id: 1004, table: 12, status: 'Pending', prepTime: 20, items: [{ id: 5, name: 'Burger', qty: 1, subtotal: 12.99 }], total: 12.99, time: '1 min ago' },
  { id: 1005, table: 7, status: 'In Progress', prepTime: 8, items: [{ id: 6, name: 'Pasta', qty: 1, subtotal: 16.99 }], total: 16.99, time: '5 min ago' },
  { id: 1006, table: 2, status: 'Completed', prepTime: 0, items: [{ id: 7, name: 'Steak', qty: 1, subtotal: 28.99 }], total: 28.99, time: '30 min ago' },
])

// Kanban Orders
const kanbanOrders = computed(() => ({
  pending: allOrders.value.filter(o => o.status === 'Pending'),
  inProgress: allOrders.value.filter(o => o.status === 'In Progress'),
  served: allOrders.value.filter(o => o.status === 'Served'),
  completed: allOrders.value.filter(o => o.status === 'Completed'),
}))

// Recent Activity
const recentActivity = ref([
  { id: 1, emoji: '📦', message: 'Order #1006 completed', time: '2 min ago' },
  { id: 2, emoji: '👨‍🍳', message: 'Order #1002 moved to In Progress', time: '5 min ago' },
  { id: 3, emoji: '🪑', message: 'Table 5 occupied', time: '8 min ago' },
  { id: 4, emoji: '💰', message: 'Payment received - Order #1005', time: '12 min ago' },
  { id: 5, emoji: '📝', message: 'New order #1001 received', time: '15 min ago' },
])

// Functions
onMounted(() => {
  updateTime()
  setInterval(updateTime, 60000)
})

function updateTime() {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString()
}

function selectOrder(order) {
  selectedOrder.value = { ...order }
}

function updateOrderStatus(orderId, newStatus) {
  const order = allOrders.value.find(o => o.id === orderId)
  if (order) {
    order.status = newStatus
    recentActivity.value.unshift({
      id: Date.now(),
      emoji: '📋',
      message: `Order #${orderId} moved to ${newStatus}`,
      time: 'just now'
    })
    selectedOrder.value = { ...order }
  }
}

function getStatusClass(status) {
  const classes = {
    'Pending': 'bg-yellow-200 text-yellow-800',
    'In Progress': 'bg-blue-200 text-blue-800',
    'Served': 'bg-green-200 text-green-800',
    'Completed': 'bg-purple-200 text-purple-800'
  }
  return classes[status] || 'bg-gray-200 text-gray-800'
}

function refreshData() {
  console.log('Refreshing dashboard data...')
}

function printBill() {
  alert('Print functionality - coming soon!')
}
</script>