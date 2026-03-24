<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">Menus</h1>

      <button @click="openCreate" class="px-4 py-2 rounded-xl bg-black text-white">
        + Add Menu
      </button>
    </div>

    <div class="flex flex-col md:flex-row gap-3 mb-4">
      <input
        v-model="q"
        @keyup.enter="load"
        class="border rounded-xl px-3 py-2 w-full md:w-80"
        placeholder="Search by name..."
      />

      <select v-model="category" @change="load" class="border rounded-xl px-3 py-2 w-full md:w-56">
        <option value="all">All categories</option>
        <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
      </select>

      <select v-model="available" @change="load" class="border rounded-xl px-3 py-2 w-full md:w-56">
        <option :value="null">All</option>
        <option :value="1">Available</option>
        <option :value="0">Not available</option>
      </select>

      <button @click="load" class="border rounded-xl px-4 py-2">Filter</button>
    </div>

    <div v-if="loading" class="text-sm text-gray-500">Loading...</div>

    <div v-else class="bg-white border rounded-2xl overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
          <tr>
            <th class="p-3">#</th>
            <th class="p-3">Name</th>
            <th class="p-3">Category</th>
            <th class="p-3">Price</th>
            <th class="p-3">Available</th>
            <th class="p-3 text-right">Actions</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="m in items" :key="m.id" class="border-t">
            <td class="p-3">{{ m.id }}</td>
            <td class="p-3">{{ m.name }}</td>
            <td class="p-3">{{ m.category ?? "-" }}</td>
            <td class="p-3">€{{ Number(m.price ?? 0).toFixed(2) }}</td>
            <td class="p-3">
              <span
                class="px-2 py-1 rounded-full text-xs"
                :class="m.is_available ? 'bg-green-100 text-green-700' : 'bg-rose-100 text-rose-700'"
              >
                {{ m.is_available ? "Yes" : "No" }}
              </span>
            </td>

            <td class="p-3 text-right flex justify-end gap-2">
              <button @click="openEdit(m)" class="px-3 py-1 rounded-lg border">Edit</button>
              <button
                @click="remove(m.id)"
                class="px-3 py-1 rounded-lg border border-rose-300 text-rose-600"
              >
                Delete
              </button>
            </td>
          </tr>

          <tr v-if="!items.length">
            <td class="p-4 text-gray-500" colspan="6">No data</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MODAL -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl w-full max-w-lg p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-lg">{{ form.id ? "Edit Menu" : "Create Menu" }}</h2>
          <button @click="closeModal" class="text-gray-500">✕</button>
        </div>

        <p v-if="error" class="text-sm text-rose-600 mb-3">{{ error }}</p>

        <div class="space-y-3">
          <input v-model.trim="form.name" class="w-full border rounded-xl px-3 py-2" placeholder="Name" />
          <input
            v-model.trim="form.category"
            class="w-full border rounded-xl px-3 py-2"
            placeholder="Category (e.g. Pizza)"
          />
          <input
            v-model.number="form.price"
            type="number"
            step="0.01"
            class="w-full border rounded-xl px-3 py-2"
            placeholder="Price"
          />
          <input
            v-model.trim="form.image"
            class="w-full border rounded-xl px-3 py-2"
            placeholder="Image URL (optional)"
          />
          <textarea
            v-model.trim="form.description"
            class="w-full border rounded-xl px-3 py-2"
            placeholder="Description (optional)"
          ></textarea>

          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" v-model="form.is_available" />
            Available
          </label>

          <button
            @click="submit"
            :disabled="saving"
            class="w-full py-2 rounded-xl bg-black text-white disabled:opacity-60"
          >
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
const q = ref("")
const category = ref("all")
const available = ref(null)

const categories = ref(["Pizza", "Burger", "Pasta", "Saladë", "Pije", "Other"])

const showModal = ref(false)
const form = ref({
  id: null,
  name: "",
  category: "Other",
  price: 0,
  image: "",
  description: "",
  is_available: true,
})

function openCreate() {
  error.value = ""
  form.value = { id: null, name: "", category: "Other", price: 0, image: "", description: "", is_available: true }
  showModal.value = true
}

function openEdit(m) {
  error.value = ""
  form.value = {
    id: m.id,
    name: m.name ?? "",
    category: m.category ?? "Other",
    price: Number(m.price ?? 0),
    image: m.image ?? "",
    description: m.description ?? "",
    is_available: !!m.is_available,
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
    if (q.value) params.q = q.value
    if (category.value && category.value !== "all") params.category = category.value
    if (available.value !== null) params.available = available.value

    const res = await api.get("/menus", { params })
    items.value = res.data.data ?? res.data
  } catch (e) {
    error.value = e?.response?.data?.message || "Error loading menus."
  } finally {
    loading.value = false
  }
}

async function submit() {
  error.value = ""
  if (!form.value.name || form.value.name.length < 2) return (error.value = "Name required (min 2 chars).")
  if (form.value.price === null || form.value.price === undefined) return (error.value = "Price required.")

  saving.value = true
  try {
    const payload = {
      name: form.value.name,
      category: form.value.category || "Other",
      price: Number(form.value.price ?? 0),
      image: form.value.image || null,
      description: form.value.description || null,
      is_available: !!form.value.is_available,
    }

    if (form.value.id) await api.put(`/menus/${form.value.id}`, payload)
    else await api.post("/menus", payload)

    showModal.value = false
    await load()
  } catch (e) {
    error.value = e?.response?.data?.message || "Error saving."
  } finally {
    saving.value = false
  }
}

async function remove(id) {
  if (!confirm("Delete this menu?")) return
  await api.delete(`/menus/${id}`)
  await load()
}

onMounted(load)
</script>
