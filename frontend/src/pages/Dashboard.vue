<template>
  <div class="min-h-screen bg-gradient-to-b from-black via-slate-900 to-zinc-900 text-white p-6">
    <div v-if="pageError" class="mb-4 rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-rose-300">
      {{ pageError }}
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
        <div class="text-sm text-gray-300">Total Orders</div>
        <div class="text-2xl font-bold mt-1 text-gold">{{ stats.totalOrders }}</div>
      </div>

      <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
        <div class="text-sm text-gray-300">Today Reservations</div>
        <div class="text-2xl font-bold mt-1 text-gold">{{ stats.todayReservations }}</div>
      </div>

      <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
        <div class="text-sm text-gray-300">Tables</div>
        <div class="text-2xl font-bold mt-1 text-gold">{{ stats.totalTables }}</div>
      </div>

      <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
        <div class="text-sm text-gray-300">Menu Items</div>
        <div class="text-2xl font-bold mt-1 text-gold">{{ stats.totalMenus }}</div>
      </div>
    </div>

    <!-- Latest -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
      <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
        <div class="font-semibold mb-4 text-white">Latest Orders</div>

        <div v-if="loadingOrders" class="text-sm text-gray-400">Loading...</div>

        <table v-else class="w-full text-sm text-white">
          <thead class="text-left text-gray-400">
            <tr>
              <th class="py-2">#</th>
              <th class="py-2">Customer</th>
              <th class="py-2">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="o in latestOrders" :key="o.id" class="border-t border-white/10">
              <td class="py-2">{{ o.id }}</td>
              <td class="py-2">{{ o.user?.name ?? "-" }}</td>
              <td class="py-2">{{ o.status ?? "-" }}</td>
            </tr>
            <tr v-if="!latestOrders.length">
              <td colspan="3" class="py-3 text-gray-400">No orders yet</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
        <div class="font-semibold mb-4 text-white">Latest Reservations</div>

        <div v-if="loadingReservations" class="text-sm text-gray-400">Loading...</div>

        <table v-else class="w-full text-sm text-white">
          <thead class="text-left text-gray-400">
            <tr>
              <th class="py-2">#</th>
              <th class="py-2">Name</th>
              <th class="py-2">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in latestReservations" :key="r.id" class="border-t border-white/10">
              <td class="py-2">{{ r.id }}</td>
              <td class="py-2">{{ r.name ?? "-" }}</td>
              <td class="py-2">{{ r.date ?? "-" }}</td>
            </tr>
            <tr v-if="!latestReservations.length">
              <td colspan="3" class="py-3 text-gray-400">No reservations yet</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from "vue"
import api from "../api/axios"

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
const pageError = ref("")

async function loadStats() {
  const res = await api.get("/dashboard/stats")
  Object.assign(stats, res.data)
}

async function loadLatestOrders() {
  loadingOrders.value = true
  try {
    const res = await api.get("/orders")
    const rows = res.data.data ?? res.data ?? []
    latestOrders.value = rows.slice(0, 5)
  } finally {
    loadingOrders.value = false
  }
}

async function loadLatestReservations() {
  loadingReservations.value = true
  try {
    const res = await api.get("/reservations")
    const rows = res.data.data ?? res.data ?? []
    latestReservations.value = rows.slice(0, 5)
  } finally {
    loadingReservations.value = false
  }
}

onMounted(async () => {
  pageError.value = ""

  const results = await Promise.allSettled([
    loadStats(),
    loadLatestOrders(),
    loadLatestReservations(),
  ])

  const failed = results.find(r => r.status === "rejected")
  if (failed) {
    pageError.value = "Dashboard data could not be loaded completely."
  }
})
</script>

<style scoped>
.text-gold {
  color: #d4af37;
}
</style>