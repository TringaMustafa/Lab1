<template>
  <div class="p-6 text-white">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">Reservations</h1>
    </div>

    <div class="flex flex-col md:flex-row gap-3 mb-4">
      <select
        v-model="status"
        @change="load"
        class="border border-white/10 bg-white/5 text-white rounded-xl px-3 py-2 w-full md:w-64"
      >
        <option value="all" class="text-black">All statuses</option>
        <option value="pending" class="text-black">pending</option>
        <option value="confirmed" class="text-black">confirmed</option>
        <option value="cancelled" class="text-black">cancelled</option>
      </select>

      <button
        @click="load"
        class="border border-white/10 bg-white/5 text-white rounded-xl px-4 py-2 hover:bg-white/10 transition"
      >
        Filter
      </button>
    </div>

    <p v-if="pageError" class="text-sm text-rose-300 mb-3">{{ pageError }}</p>

    <div v-if="loading" class="text-sm text-gray-400">Loading...</div>

    <div v-else class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-white/5 text-left text-gray-300">
          <tr>
            <th class="p-3">#</th>
            <th class="p-3">Client</th>
            <th class="p-3">Phone</th>
            <th class="p-3">Table</th>
            <th class="p-3">Date/Time</th>
            <th class="p-3">Guests</th>
            <th class="p-3">Status</th>
            <th class="p-3 text-right">Actions</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="r in items" :key="r.id" class="border-t border-white/10">
            <td class="p-3">{{ r.id }}</td>
            <td class="p-3">{{ r.name }}</td>
            <td class="p-3">{{ r.phone }}</td>
            <td class="p-3">{{ r.table?.name ?? r.table_id }}</td>
            <td class="p-3">{{ r.date }} {{ r.time }}</td>
            <td class="p-3">{{ r.guests }}</td>
            <td class="p-3">
              <span class="px-2 py-1 rounded-full text-xs" :class="badgeClass(r.status)">
                {{ r.status }}
              </span>
            </td>

            <td class="p-3 text-right flex justify-end gap-2">
              <button
                @click="openEdit(r)"
                class="px-3 py-1 rounded-lg border border-white/20 text-white hover:bg-white/10 transition"
              >
                Edit
              </button>
              <button
                @click="remove(r.id)"
                class="px-3 py-1 rounded-lg border border-rose-400 text-rose-400 hover:bg-rose-500/10 transition"
              >
                Delete
              </button>
            </td>
          </tr>

          <tr v-if="!items.length">
            <td class="p-4 text-gray-400" colspan="8">No data</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MODAL -->
    <div v-if="showModal" class="fixed inset-0 bg-black/60 flex items-center justify-center p-4">
      <div class="bg-zinc-950 border border-white/10 rounded-2xl w-full max-w-lg p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-lg text-white">Edit Reservation</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-white">✕</button>
        </div>

        <p v-if="error" class="text-sm text-rose-300 mb-3">{{ error }}</p>

        <div class="space-y-3">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <input
              v-model.number="form.table_id"
              type="number"
              class="w-full border border-white/10 bg-white/5 text-white rounded-xl px-3 py-2"
              placeholder="Table ID"
            />
            <input
              v-model.number="form.guests"
              type="number"
              min="1"
              class="w-full border border-white/10 bg-white/5 text-white rounded-xl px-3 py-2"
              placeholder="Guests"
            />
          </div>

          <input
            v-model.trim="form.name"
            class="w-full border border-white/10 bg-white/5 text-white rounded-xl px-3 py-2"
            placeholder="Client name"
          />

          <input
            v-model.trim="form.phone"
            class="w-full border border-white/10 bg-white/5 text-white rounded-xl px-3 py-2"
            placeholder="Phone"
          />

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <input
              v-model="form.date"
              type="date"
              class="w-full border border-white/10 bg-white/5 text-white rounded-xl px-3 py-2"
            />
            <input
              v-model="form.time"
              type="time"
              class="w-full border border-white/10 bg-white/5 text-white rounded-xl px-3 py-2"
            />
          </div>

          <select
            v-model="form.status"
            class="w-full border border-white/10 bg-white/5 text-white rounded-xl px-3 py-2"
          >
            <option value="pending" class="text-black">pending</option>
            <option value="confirmed" class="text-black">confirmed</option>
            <option value="cancelled" class="text-black">cancelled</option>
          </select>

          <button
            @click="submit"
            :disabled="saving"
            class="w-full py-2 rounded-xl bg-gold text-black font-semibold disabled:opacity-60"
          >
            {{ saving ? "Saving..." : "Save Changes" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue"
import api from "../api/axios"

const loading = ref(false)
const saving = ref(false)
const error = ref("")
const pageError = ref("")

const items = ref([])
const status = ref("all")

const showModal = ref(false)
const form = ref({
  id: null,
  table_id: 1,
  name: "",
  phone: "",
  date: "",
  time: "",
  guests: 1,
  status: "pending",
})

function badgeClass(s) {
  if (s === "confirmed") return "bg-green-500/15 text-green-300"
  if (s === "cancelled") return "bg-rose-500/15 text-rose-300"
  return "bg-amber-500/15 text-amber-300"
}

function openEdit(r) {
  error.value = ""
  form.value = {
    id: r.id,
    table_id: r.table_id,
    name: r.name ?? "",
    phone: r.phone ?? "",
    date: r.date ?? "",
    time: r.time ?? "",
    guests: Number(r.guests ?? 1),
    status: r.status ?? "pending",
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

async function load() {
  loading.value = true
  pageError.value = ""

  try {
    const params = {}
    if (status.value && status.value !== "all") params.status = status.value

    const res = await api.get("/reservations", { params })
    items.value = res.data.data ?? res.data
  } catch (e) {
    pageError.value = e?.response?.data?.message || "Error loading reservations."
  } finally {
    loading.value = false
  }
}

async function submit() {
  error.value = ""

  if (!form.value.id) return (error.value = "Reservation ID missing.")
  if (!form.value.table_id) return (error.value = "Table ID required.")
  if (!form.value.name || form.value.name.length < 2) return (error.value = "Name required (min 2 chars).")
  if (!form.value.phone || form.value.phone.length < 6) return (error.value = "Phone required.")
  if (!form.value.date) return (error.value = "Date required.")
  if (!form.value.time) return (error.value = "Time required.")
  if (!form.value.guests || form.value.guests < 1) return (error.value = "Guests must be >= 1.")

  saving.value = true
  try {
    const payload = {
      table_id: Number(form.value.table_id),
      name: form.value.name,
      phone: form.value.phone,
      date: form.value.date,
      time: form.value.time,
      guests: Number(form.value.guests),
      status: form.value.status,
    }

    await api.put(`/reservations/${form.value.id}`, payload)

    showModal.value = false
    await load()
  } catch (e) {
    error.value = e?.response?.data?.message || "Error saving."
  } finally {
    saving.value = false
  }
}

async function remove(id) {
  if (!confirm("Delete this reservation?")) return
  await api.delete(`/reservations/${id}`)
  await load()
}

onMounted(load)
</script>

<style scoped>
.bg-gold {
  background-color: #d4af37;
}
</style>