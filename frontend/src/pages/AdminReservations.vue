<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">Reservations</h1>

      <button @click="openCreate" class="px-4 py-2 rounded-xl bg-black text-white">
        + Add Reservation
      </button>
    </div>

    <div class="flex flex-col md:flex-row gap-3 mb-4">
      <select v-model="status" @change="load" class="border rounded-xl px-3 py-2 w-full md:w-64">
        <option value="all">All statuses</option>
        <option value="pending">pending</option>
        <option value="confirmed">confirmed</option>
        <option value="cancelled">cancelled</option>
      </select>

      <button @click="load" class="border rounded-xl px-4 py-2">Filter</button>
    </div>

    <div v-if="loading" class="text-sm text-gray-500">Loading...</div>

    <div v-else class="bg-white border rounded-2xl overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
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
          <tr v-for="r in items" :key="r.id" class="border-t">
            <td class="p-3">{{ r.id }}</td>
            <td class="p-3">{{ r.name }}</td>
            <td class="p-3">{{ r.phone }}</td>
            <td class="p-3">{{ r.table?.name ?? r.table_id }}</td>
            <td class="p-3">{{ r.date }} {{ r.time }}</td>
            <td class="p-3">{{ r.guests }}</td>
            <td class="p-3">
              <span
                class="px-2 py-1 rounded-full text-xs"
                :class="badgeClass(r.status)"
              >
                {{ r.status }}
              </span>
            </td>

            <td class="p-3 text-right flex justify-end gap-2">
              <button @click="openEdit(r)" class="px-3 py-1 rounded-lg border">Edit</button>
              <button
                @click="remove(r.id)"
                class="px-3 py-1 rounded-lg border border-rose-300 text-rose-600"
              >
                Delete
              </button>
            </td>
          </tr>

          <tr v-if="!items.length">
            <td class="p-4 text-gray-500" colspan="8">No data</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MODAL -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl w-full max-w-lg p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-lg">{{ form.id ? "Edit Reservation" : "Create Reservation" }}</h2>
          <button @click="closeModal" class="text-gray-500">✕</button>
        </div>

        <p v-if="error" class="text-sm text-rose-600 mb-3">{{ error }}</p>

        <div class="space-y-3">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <input v-model.number="form.user_id" type="number" class="w-full border rounded-xl px-3 py-2" placeholder="User ID" />
            <input v-model.number="form.table_id" type="number" class="w-full border rounded-xl px-3 py-2" placeholder="Table ID" />
          </div>

          <input v-model.trim="form.name" class="w-full border rounded-xl px-3 py-2" placeholder="Client name" />
          <input v-model.trim="form.phone" class="w-full border rounded-xl px-3 py-2" placeholder="Phone" />

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <input v-model="form.date" type="date" class="w-full border rounded-xl px-3 py-2" />
            <input v-model="form.time" type="time" class="w-full border rounded-xl px-3 py-2" />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <input v-model.number="form.guests" type="number" min="1" class="w-full border rounded-xl px-3 py-2" placeholder="Guests" />
            <select v-model="form.status" class="w-full border rounded-xl px-3 py-2">
              <option value="pending">pending</option>
              <option value="confirmed">confirmed</option>
              <option value="cancelled">cancelled</option>
            </select>
          </div>

          <button
            @click="submit"
            :disabled="saving"
            class="w-full py-2 rounded-xl bg-black text-white disabled:opacity-60"
          >
            {{ saving ? "Saving..." : "Save" }}
          </button>
        </div>

        <p class="text-xs text-gray-500 mt-3">
          Tip: User ID/Table ID i merr nga DB. (Më vonë mund t’i bëjmë dropdown me tables/users.)
        </p>
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

const items = ref([])
const status = ref("all")

const showModal = ref(false)
const form = ref({
  id: null,
  user_id: 1,
  table_id: 1,
  name: "",
  phone: "",
  date: "",
  time: "",
  guests: 1,
  status: "pending",
})

function badgeClass(s) {
  if (s === "confirmed") return "bg-green-100 text-green-700"
  if (s === "cancelled") return "bg-rose-100 text-rose-700"
  return "bg-amber-100 text-amber-700"
}

function openCreate() {
  error.value = ""
  form.value = { id: null, user_id: 1, table_id: 1, name: "", phone: "", date: "", time: "", guests: 1, status: "pending" }
  showModal.value = true
}

function openEdit(r) {
  error.value = ""
  form.value = {
    id: r.id,
    user_id: r.user_id,
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
  try {
    const params = {}
    if (status.value && status.value !== "all") params.status = status.value

    const res = await api.get("/reservations", { params })
    items.value = res.data.data ?? res.data
  } catch (e) {
    error.value = e?.response?.data?.message || "Error loading reservations."
  } finally {
    loading.value = false
  }
}

async function submit() {
  error.value = ""

  if (!form.value.user_id) return (error.value = "User ID required.")
  if (!form.value.table_id) return (error.value = "Table ID required.")
  if (!form.value.name || form.value.name.length < 2) return (error.value = "Name required (min 2 chars).")
  if (!form.value.phone || form.value.phone.length < 6) return (error.value = "Phone required.")
  if (!form.value.date) return (error.value = "Date required.")
  if (!form.value.time) return (error.value = "Time required.")
  if (!form.value.guests || form.value.guests < 1) return (error.value = "Guests must be >= 1.")

  saving.value = true
  try {
    const payload = {
      user_id: Number(form.value.user_id),
      table_id: Number(form.value.table_id),
      name: form.value.name,
      phone: form.value.phone,
      date: form.value.date,
      time: form.value.time,
      guests: Number(form.value.guests),
      status: form.value.status,
    }

    if (form.value.id) await api.put(`/reservations/${form.value.id}`, payload)
    else await api.post("/reservations", payload)

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
