<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">Tables</h1>
      <button @click="openCreate" class="px-4 py-2 rounded-xl bg-black text-white">+ Add Table</button>
    </div>

    <div v-if="loading" class="text-sm text-gray-500">Loading...</div>

<div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden text-white">      <table class="w-full text-sm">
<thead class="bg-white/5 text-left text-gray-300">          <tr>
            <th class="p-3">#</th>
            <th class="p-3">Name</th>
            <th class="p-3">Seats</th>
            <th class="p-3">Status</th>
            <th class="p-3 text-right">Actions</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="t in items" :key="t.id" class="border-t">
            <td class="p-3">{{ t.id }}</td>
            <td class="p-3">{{ t.name }}</td>
            <td class="p-3">{{ t.seats }}</td>
            <td class="p-3">{{ t.status }}</td>
            <td class="p-3 text-right flex justify-end gap-2">
              <button @click="openEdit(t)" class="px-3 py-1 rounded-lg border">Edit</button>
              <button @click="remove(t.id)" class="px-3 py-1 rounded-lg border border-rose-300 text-rose-600">
                Delete
              </button>
            </td>
          </tr>

          <tr v-if="!items.length">
            <td class="p-4 text-gray-500" colspan="5">No data</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MODAL -->
  <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4">
  <div class="bg-zinc-950 border border-white/10 rounded-2xl w-full max-w-lg p-5 text-white">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-lg">{{ form.id ? "Edit Table" : "Create Table" }}</h2>
          <button @click="closeModal" class="text-gray-500">✕</button>
        </div>

        <p v-if="error" class="text-sm text-rose-600 mb-3">{{ error }}</p>

        <div class="space-y-3">
          <input
  v-model.trim="form.name"
  class="w-full border border-white/10 bg-white/5 text-white rounded-xl px-3 py-2"
  placeholder="Name (e.g. T1)"
/>

<input
  v-model.number="form.seats"
  type="number"
  class="w-full border border-white/10 bg-white/5 text-white rounded-xl px-3 py-2"
  placeholder="Seats"
/>

<select
  v-model="form.status"
  class="w-full border border-white/10 bg-white/5 text-white rounded-xl px-3 py-2"
>
  <option value="free" class="text-black">free</option>
  <option value="occupied" class="text-black">occupied</option>
  <option value="reserved" class="text-black">reserved</option>
  <option value="cleaning" class="text-black">cleaning</option>
</select>
          <button @click="submit" :disabled="saving" class="w-full py-2 rounded-xl bg-black text-white disabled:opacity-60">
            {{ saving ? "Saving..." : "Save" }}
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
const items = ref([])

const showModal = ref(false)
const form = ref({ id: null, name: "", seats: 2, status: "free" })

function openCreate() {
  error.value = ""
  form.value = { id: null, name: "", seats: 2, status: "free" }
  showModal.value = true
}
function openEdit(t) {
  error.value = ""
  form.value = { id: t.id, name: t.name, seats: Number(t.seats), status: t.status }
  showModal.value = true
}
function closeModal() { showModal.value = false }

async function load() {
  loading.value = true
  try {
    const res = await api.get("/tables")
    items.value = res.data.data ?? res.data
  } finally {
    loading.value = false
  }
}

async function submit() {
  error.value = ""
  if (!form.value.name) return (error.value = "Name required.")
  if (!form.value.seats || form.value.seats < 1) return (error.value = "Seats must be >= 1.")

  saving.value = true
  try {
    const payload = { name: form.value.name, seats: Number(form.value.seats), status: form.value.status }
    if (form.value.id) await api.put(`/tables/${form.value.id}`, payload)
    else await api.post("/tables", payload)

    showModal.value = false
    await load()
  } catch (e) {
    error.value = e?.response?.data?.message || "Error saving."
  } finally {
    saving.value = false
  }
}

async function remove(id) {
  if (!confirm("Delete this table?")) return
  await api.delete(`/tables/${id}`)
  await load()
}

onMounted(load)
</script>
