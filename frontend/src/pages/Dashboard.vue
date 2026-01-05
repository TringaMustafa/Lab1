<template>
  <div class="space-y-6">

    <!-- KPI GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="k in kpis" :key="k.label"
        class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-2xl transition">
        <p class="text-gray-500 text-sm">{{ k.label }}</p>
        <h2 class="text-3xl font-bold mt-2">{{ k.value }}</h2>
        <p class="text-xs text-gray-400 mt-1">{{ k.sub }}</p>
      </div>
    </div>

    <!-- TWO COLUMN GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <!-- RECENT ORDERS -->
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <h3 class="text-xl font-semibold mb-4">Recent Orders</h3>

        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-gray-500 border-b">
              <th class="py-2">#</th>
              <th>Table</th>
              <th>Items</th>
              <th>Status</th>
              <th>Total</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="o in orders" :key="o.id"
              class="border-b hover:bg-gray-50 transition">
              <td class="py-2">{{ o.id }}</td>
              <td>{{ o.table }}</td>
              <td>{{ o.items }}</td>
              <td>
                <span
                  :class="statusClass(o.status)"
                  class="px-3 py-1 rounded-full text-xs font-medium">
                  {{ o.status }}
                </span>
              </td>
              <td>${{ o.total }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- MINI TABLE OVERVIEW -->
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <h3 class="text-xl font-semibold mb-4">Table Overview</h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
          <div
            v-for="t in tables"
            :key="t.name"
            class="p-4 rounded-xl border shadow hover:shadow-lg transition cursor-pointer">
            
            <p class="font-semibold text-lg">{{ t.name }}</p>
            <p class="text-gray-500 text-sm">{{ t.seats }} seats</p>

            <span
              :class="statusClass(t.status)"
              class="px-3 py-1 inline-block rounded-full text-xs mt-2 font-medium">
              {{ t.status }}
            </span>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      kpis: [
        { label: "Total Orders Today", value: 54, sub: "All channels" },
        { label: "Revenue Today", value: "$682.20", sub: "Including service" },
        { label: "Active Tables", value: 11, sub: "Occupied / Reserved" },
        { label: "Average Ticket", value: "$12.63", sub: "Per order" },
      ],

      orders: [
        { id: "#2041", table: "T3", items: "Pizza x2", status: "Pending", total: 21.5 },
        { id: "#2042", table: "T8", items: "Burger", status: "In Progress", total: 9.5 },
        { id: "#2043", table: "T1", items: "Steak", status: "Completed", total: 16.0 },
      ],

      tables: [
        { name: "T1", seats: 2, status: "FREE" },
        { name: "T2", seats: 4, status: "OCCUPIED" },
        { name: "T3", seats: 4, status: "RESERVED" },
        { name: "T4", seats: 6, status: "FREE" },
        { name: "T5", seats: 2, status: "OCCUPIED" },
      ],
    };
  },

  methods: {
    statusClass(status) {
      return {
        "bg-green-200 text-green-800": status === "FREE",
        "bg-red-200 text-red-800": status === "OCCUPIED",
        "bg-blue-200 text-blue-800": status === "RESERVED",
        "bg-yellow-200 text-yellow-800": status === "Pending",
        "bg-purple-200 text-purple-800": status === "In Progress",
        "bg-emerald-200 text-emerald-800": status === "Completed",
      };
    },
  },
};
</script>
