<template>
  <!-- Stats -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white border rounded-2xl p-5">
      <div class="text-sm text-gray-500">Total Orders</div>
      <div class="text-2xl font-bold mt-1">{{ stats.totalOrders }}</div>
    </div>

    <div class="bg-white border rounded-2xl p-5">
      <div class="text-sm text-gray-500">Today Reservations</div>
      <div class="text-2xl font-bold mt-1">{{ stats.todayReservations }}</div>
    </div>

    <div class="bg-white border rounded-2xl p-5">
      <div class="text-sm text-gray-500">Tables</div>
      <div class="text-2xl font-bold mt-1">{{ stats.totalTables }}</div>
    </div>

    <div class="bg-white border rounded-2xl p-5">
      <div class="text-sm text-gray-500">Menu Items</div>
      <div class="text-2xl font-bold mt-1">{{ stats.totalMenus }}</div>
    </div>
  </div>

  <!-- Latest -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <div class="bg-white border rounded-2xl p-5">
      <div class="font-semibold mb-4">Latest Orders</div>

      <div v-if="loadingOrders" class="text-sm text-gray-500">Loading...</div>

      <table v-else class="w-full text-sm">
        <thead class="text-left text-gray-500">
          <tr>
            <th class="py-2">#</th>
            <th class="py-2">Customer</th>
            <th class="py-2">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="o in latestOrders" :key="o.id" class="border-t">
            <td class="py-2">{{ o.id }}</td>
            <td class="py-2">{{ o.customer_name ?? "-" }}</td>
            <td class="py-2">{{ o.status ?? "-" }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="bg-white border rounded-2xl p-5">
      <div class="font-semibold mb-4">Latest Reservations</div>

      <div v-if="loadingReservations" class="text-sm text-gray-500">Loading...</div>

      <table v-else class="w-full text-sm">
        <thead class="text-left text-gray-500">
          <tr>
            <th class="py-2">#</th>
            <th class="py-2">Name</th>
            <th class="py-2">Date</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in latestReservations" :key="r.id" class="border-t">
            <td class="py-2">{{ r.id }}</td>
            <td class="py-2">{{ r.name ?? "-" }}</td>
            <td class="py-2">{{ r.date ?? r.reservation_date ?? "-" }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from "vue"
import api from "../api/axios"

// NOTE: AdminLayout hiqet këtu sepse po e vendos App.vue (layout = 'admin')

const stats = reactive({
  totalOrders: 0,
  todayReservations: 0,
  totalTables: 0,
  totalMenus: 0,
})

const latestOrders = ref([])
const latestReservations = ref([])

const loadingOrders = ref(true)
const loadingReservations = ref(true)

async function loadStats() {
  const res = await api.get("/dashboard/stats")
  Object.assign(stats, res.data)
}

async function loadLatestOrders() {
  loadingOrders.value = true
  const res = await api.get("/orders?limit=5")
  latestOrders.value = res.data.data ?? res.data
  loadingOrders.value = false
}

async function loadLatestReservations() {
  loadingReservations.value = true
  const res = await api.get("/reservations?limit=5")
  latestReservations.value = res.data.data ?? res.data
  loadingReservations.value = false
}

onMounted(async () => {
  await Promise.allSettled([loadStats(), loadLatestOrders(), loadLatestReservations()])
})
</script>
