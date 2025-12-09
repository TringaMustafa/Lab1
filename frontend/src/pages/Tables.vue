<template>
  <div>
    <h1 class="text-4xl font-bold mb-8 text-gray-800">Table Management</h1>

    <!-- Status Legend -->
    <div class="flex gap-6 mb-8 flex-wrap">
      <div class="flex items-center gap-2">
        <div class="w-6 h-6 bg-green-500 rounded"></div>
        <span class="text-sm font-semibold">Available</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-6 h-6 bg-red-500 rounded"></div>
        <span class="text-sm font-semibold">Occupied</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-6 h-6 bg-yellow-500 rounded"></div>
        <span class="text-sm font-semibold">Reserved</span>
      </div>
    </div>

    <!-- Tables Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div 
        v-for="table in tables" 
        :key="table.id"
        @click="selectTable(table)"
        class="p-6 rounded-lg shadow-md hover:shadow-lg transition cursor-pointer border-2"
        :class="getTableClass(table.status)"
      >
        <div class="text-center">
          <div class="text-4xl mb-2">🪑</div>
          <div class="text-2xl font-bold mb-2">{{ table.name }}</div>
          <div class="text-sm text-gray-600 mb-3">{{ table.seats }} seats</div>
          <span class="text-xs font-semibold px-3 py-1 rounded-full" :class="getStatusBadge(table.status)">
            {{ table.status }}
          </span>
        </div>
      </div>
    </div>

    <!-- Table Detail Modal -->
    <div v-if="selectedTableData" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold mb-4">{{ selectedTableData.name }}</h2>
        <div class="space-y-4 mb-6">
          <div>
            <span class="text-gray-600">Seats:</span>
            <span class="font-semibold ml-2">{{ selectedTableData.seats }}</span>
          </div>
          <div>
            <span class="text-gray-600">Status:</span>
            <span class="font-semibold ml-2">{{ selectedTableData.status }}</span>
          </div>
        </div>
        <div class="flex gap-4">
          <button 
            @click="createOrder"
            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition"
          >
            New Order
          </button>
          <button 
            @click="selectedTableData = null"
            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 rounded-lg font-semibold transition"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const selectedTableData = ref(null)

const tables = ref([
  { id: 1, name: 'Table 1', seats: 2, status: 'Available' },
  { id: 2, name: 'Table 2', seats: 4, status: 'Occupied' },
  { id: 3, name: 'Table 3', seats: 2, status: 'Available' },
  { id: 4, name: 'Table 4', seats: 6, status: 'Reserved' },
  { id: 5, name: 'Table 5', seats: 4, status: 'Occupied' },
  { id: 6, name: 'Table 6', seats: 8, status: 'Available' },
  { id: 7, name: 'Table 7', seats: 2, status: 'Available' },
  { id: 8, name: 'Table 8', seats: 4, status: 'Occupied' },
])

function getTableClass(status) {
  const classes = {
    'Available': 'border-green-500 bg-green-50 hover:bg-green-100',
    'Occupied': 'border-red-500 bg-red-50 hover:bg-red-100',
    'Reserved': 'border-yellow-500 bg-yellow-50 hover:bg-yellow-100'
  }
  return classes[status] || ''
}

function getStatusBadge(status) {
  const classes = {
    'Available': 'bg-green-200 text-green-800',
    'Occupied': 'bg-red-200 text-red-800',
    'Reserved': 'bg-yellow-200 text-yellow-800'
  }
  return classes[status] || ''
}

function selectTable(table) {
  selectedTableData.value = table
}

function createOrder() {
  alert(`Creating order for ${selectedTableData.value.name}`)
  selectedTableData.value = null
}
</script>
