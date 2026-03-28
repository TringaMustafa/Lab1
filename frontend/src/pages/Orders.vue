<template>
  <div class="p-4 md:p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
      <h1 class="text-2xl font-bold">Orders</h1>

      <select v-model="statusFilter" @change="loadOrders" class="w-full sm:w-auto border rounded-xl px-3 py-2">
        <option value="all">All</option>
        <option value="pending">pending</option>
        <option value="confirmed">confirmed</option>
        <option value="completed">completed</option>
        <option value="cancelled">cancelled</option>
      </select>
    </div>

    <div v-if="loading" class="text-sm text-gray-500">Loading...</div>

    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden text-white">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-sm">
          <thead class="bg-white/5 text-left text-gray-300">
            <tr>
              <th class="p-3">#</th>
              <th class="p-3">User</th>
              <th class="p-3">Phone</th>
              <th class="p-3">Total</th>
              <th class="p-3">Status</th>
              <th class="p-3">Payment</th>
              <th class="p-3 text-right">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="o in items" :key="o.id" class="border-t border-white/10">
              <td class="p-3">{{ o.id }}</td>
              <td class="p-3">{{ o.user?.name || '—' }}</td>
              <td class="p-3">{{ o.phone }}</td>
              <td class="p-3">€{{ Number(o.total).toFixed(2) }}</td>
              <td class="p-3">
                <select
                  :value="o.status"
                  @change="updateStatus(o.id, $event.target.value)"
                  class="w-full min-w-[130px] border rounded-lg px-2 py-1 text-black"
                >
                  <option value="pending">pending</option>
                  <option value="confirmed">confirmed</option>
                  <option value="completed">completed</option>
                  <option value="cancelled">cancelled</option>
                </select>
              </td>
              <td class="p-3">{{ o.payment_status }}</td>
              <td class="p-3 text-right">
                <button
                  @click="removeOrder(o.id)"
                  class="px-3 py-1 rounded-lg border border-rose-300 text-rose-300 hover:bg-rose-500/10"
                >
                  Delete
                </button>
              </td>
            </tr>

            <tr v-if="!items.length">
              <td class="p-4 text-gray-500" colspan="7">No orders found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue"
import api from "../api/axios"

const loading = ref(false)
const items = ref([])
const statusFilter = ref("all")

async function loadOrders() {
  loading.value = true
  try {
    const res = await api.get("/orders", {
      params: { status: statusFilter.value }
    })
    items.value = res.data.data ?? res.data
  } finally {
    loading.value = false
  }
}

async function updateStatus(id, status) {
  await api.put(`/orders/${id}`, { status })
  await loadOrders()
}

async function removeOrder(id) {
  if (!confirm("Delete this order?")) return
  await api.delete(`/orders/${id}`)
  await loadOrders()
}

onMounted(loadOrders)
</script>