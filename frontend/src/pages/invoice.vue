<template>
  <div class="min-h-screen bg-gray-100 text-gray-900 py-10 px-4">
    <div class="max-w-3xl mx-auto">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Invoice</h1>

        <div class="flex gap-2">
          <button
            @click="printInvoice"
            class="px-4 py-2 rounded-lg bg-black text-white hover:opacity-90"
          >
            Print / Save PDF
          </button>
          <RouterLink to="/" class="px-4 py-2 rounded-lg border bg-white hover:bg-gray-50">
            Home
          </RouterLink>
        </div>
      </div>

      <div v-if="!order" class="bg-white rounded-2xl p-6 shadow">
        Nuk ka porosi për faturë. Bëj një porosi fillimisht.
      </div>

      <div v-else id="invoice" class="bg-white rounded-2xl p-8 shadow">
        <div class="flex items-start justify-between">
          <div>
            <div class="text-xl font-bold">TableFlow Restaurant</div>
            <div class="text-sm text-gray-600">Prishtinë</div>
          </div>

          <div class="text-right">
            <div class="font-semibold">Order: {{ order.id }}</div>
            <div class="text-sm text-gray-600">{{ prettyDate(order.createdAt) }}</div>
          </div>
        </div>

        <hr class="my-6" />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
          <div>
            <div class="font-semibold mb-1">Customer</div>
            <div>{{ order.user?.name }}</div>
            <div class="text-gray-600">{{ order.user?.email }}</div>
          </div>

          <div>
            <div class="font-semibold mb-1">Contact</div>
            <div>{{ order.phone }}</div>
            <div class="text-gray-600">{{ order.address || "-" }}</div>
          </div>
        </div>

        <hr class="my-6" />

        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-gray-600">
              <th class="py-2">Item</th>
              <th class="py-2">Qty</th>
              <th class="py-2">Price</th>
              <th class="py-2 text-right">Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="it in order.items" :key="it.id" class="border-t">
              <td class="py-2">{{ it.name }}</td>
              <td class="py-2">{{ it.qty }}</td>
              <td class="py-2">€{{ Number(it.price).toFixed(2) }}</td>
              <td class="py-2 text-right font-semibold">
                €{{ Number(it.lineTotal).toFixed(2) }}
              </td>
            </tr>
          </tbody>
        </table>

        <div class="flex items-center justify-between mt-6">
          <div class="text-gray-600 text-sm">Faleminderit për porosinë!</div>
          <div class="text-xl font-bold">
            Total: €{{ Number(order.total).toFixed(2) }}
          </div>
        </div>
      </div>

      <p class="text-xs text-gray-500 mt-3">
        (Tip) Te Print, zgjedh “Save as PDF” për ta ruajtur faturën si PDF.
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue"

const order = computed(() => {
  try {
    const raw = localStorage.getItem("last_order")
    return raw ? JSON.parse(raw) : null
  } catch {
    return null
  }
})

function prettyDate(iso) {
  try {
    return new Date(iso).toLocaleString()
  } catch {
    return "-"
  }
}

function printInvoice() {
  window.print()
}
</script>

<style scoped>
@media print {
  body { background: white !important; }
  button, a { display: none !important; }
  #invoice { box-shadow: none !important; }
}
</style>
