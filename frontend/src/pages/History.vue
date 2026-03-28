<template>
  <div class="min-h-screen bg-gradient-to-b from-black via-slate-900 to-zinc-900 text-white">
    <!-- NAVBAR -->
    <header class="absolute top-0 left-0 w-full z-50 bg-transparent text-white">
      <div class="max-w-6xl mx-auto flex flex-col md:flex-row md:justify-between md:items-center gap-4 py-4 md:py-6 px-4 md:px-0">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-2xl bg-gold flex items-center justify-center text-black text-xl">
            🍽️
          </div>
          <span class="text-2xl font-bold tracking-wide text-gold">TableFlow</span>
        </div>

        <nav class="w-full md:w-auto flex flex-wrap items-center justify-start md:justify-end gap-3 md:gap-6 text-sm md:text-base">
          <RouterLink to="/" class="hover:text-gold transition">Home</RouterLink>
          <RouterLink to="/menu" class="hover:text-gold transition">Menu</RouterLink>
          <RouterLink to="/tables" class="hover:text-gold transition">Tables</RouterLink>

          <RouterLink
            v-if="isLogged"
            to="/history"
            class="hover:text-gold transition"
          >
            History
          </RouterLink>

          <RouterLink
            v-if="auth.user?.role === 'admin'"
            to="/dashboard"
            class="px-3 py-2 rounded-lg border border-gold text-gold hover:bg-gold hover:text-black transition"
            title="Dashboard"
          >
            Dashboard
          </RouterLink>

          <RouterLink
            v-if="isLogged"
            to="/cart"
            class="relative px-3 py-2 rounded-lg border border-white/20 hover:bg-white/10 transition"
            title="Cart"
          >
            🛒
            <span
              v-if="cart.count"
              class="absolute -top-2 -right-2 text-xs px-2 py-0.5 rounded-full bg-red-500 text-white"
            >
              {{ cart.count }}
            </span>
          </RouterLink>

          <RouterLink
            v-if="!isLogged"
            to="/login"
            class="px-4 py-2 rounded-lg border border-gold text-gold hover:bg-gold hover:text-black transition"
          >
            Login
          </RouterLink>

          <button
            v-else
            @click="handleLogout"
            class="px-4 py-2 rounded-lg border border-red-400 text-red-400 hover:bg-red-400 hover:text-black transition"
          >
            Logout
          </button>
        </nav>
      </div>
    </header>

    <div class="max-w-6xl mx-auto px-4 pt-36 md:pt-32 pb-10">
      <h1 class="text-3xl md:text-4xl font-bold mb-6">History</h1>

      <p v-if="pageError" class="text-rose-300 mb-4">{{ pageError }}</p>

      <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Orders -->
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 md:p-5">
          <div class="flex items-center justify-between gap-3 mb-4">
            <h2 class="text-xl font-semibold">My Orders</h2>
            <span v-if="loadingOrders" class="text-sm text-gray-400">Loading...</span>
          </div>

          <div v-if="!loadingOrders && !orders.length" class="text-sm text-gray-400">
            You do not have any orders yet.
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="order in orders"
              :key="order.id"
              class="rounded-xl border border-white/10 bg-white/5 p-4"
            >
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
                <div>
                  <div class="font-semibold">Order #{{ order.id }}</div>
                  <div class="text-xs text-gray-400">{{ formatDateTime(order.created_at) }}</div>
                </div>

                <div class="px-3 py-1 rounded-full text-xs border border-white/10 bg-white/10 w-fit">
                  {{ order.status ?? "pending" }}
                </div>
              </div>

              <div class="text-sm text-gray-300 mb-2">
                Total: {{ formatMoney(order.total) }} €
              </div>

              <div v-if="order.items?.length" class="space-y-2">
                <div
                  v-for="item in order.items"
                  :key="item.id"
                  class="flex items-center justify-between gap-3 text-sm border-t border-white/10 pt-2"
                >
                  <span>{{ item.menu?.name ?? "Item" }} x {{ item.quantity }}</span>
                  <span>{{ formatMoney(item.price) }} €</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Reservations -->
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 md:p-5">
          <div class="flex items-center justify-between gap-3 mb-4">
            <h2 class="text-xl font-semibold">My Reservations</h2>
            <span v-if="loadingReservations" class="text-sm text-gray-400">Loading...</span>
          </div>

          <div v-if="!loadingReservations && !reservations.length" class="text-sm text-gray-400">
            You do not have any reservations yet.
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="r in reservations"
              :key="r.id"
              class="rounded-xl border border-white/10 bg-white/5 p-4"
            >
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
                <div>
                  <div class="font-semibold">
                    {{ r.table?.name ?? ("Table #" + r.table_id) }}
                  </div>
                  <div class="text-xs text-gray-400">
                    {{ r.date }} | {{ formatTime(r.time) }} - {{ formatTime(r.end_time) }}
                  </div>
                </div>

                <div class="px-3 py-1 rounded-full text-xs border border-white/10 bg-white/10 w-fit">
                  {{ r.status }}
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-300">
                <div>Name: {{ r.name }}</div>
                <div>Phone: {{ r.phone }}</div>
                <div>Guests: {{ r.guests }}</div>
                <div>
                  Released early:
                  {{ r.actual_end_time ? formatTime(r.actual_end_time) : "-" }}
                </div>
              </div>

              <button
                v-if="canEdit(r)"
                @click="openEdit(r)"
                class="mt-4 rounded-xl bg-gold px-4 py-2 text-black font-semibold hover:bg-yellow-400 transition"
              >
                Edit Reservation
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Reservation Modal -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/60 flex items-center justify-center p-4 z-50"
      >
        <div class="bg-zinc-950 border border-white/10 rounded-2xl w-full max-w-lg p-4 md:p-5 max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-lg">Edit My Reservation</h2>
            <button @click="closeModal" class="text-gray-400 hover:text-white">✕</button>
          </div>

          <p v-if="formError" class="text-sm text-rose-300 mb-3">{{ formError }}</p>

          <div class="space-y-3">
            <input
              v-model.trim="form.name"
              class="w-full border border-white/10 bg-white/5 text-white rounded-xl px-3 py-2"
              placeholder="Name"
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <input
                v-model.number="form.table_id"
                type="number"
                min="1"
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

            <button
              @click="submitReservationEdit"
              :disabled="saving"
              class="w-full py-2 rounded-xl bg-gold text-black font-semibold disabled:opacity-60"
            >
              {{ saving ? "Saving..." : "Save Changes" }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue"
import { useRouter } from "vue-router"
import api from "../api/axios"
import { useAuthStore } from "../store/auth"
import { useCartStore } from "../store/cart"

const router = useRouter()
const auth = useAuthStore()
const cart = useCartStore()

const isLogged = computed(() => auth.isLogged())

const orders = ref([])
const reservations = ref([])

const loadingOrders = ref(true)
const loadingReservations = ref(true)
const pageError = ref("")

const showModal = ref(false)
const saving = ref(false)
const formError = ref("")

const form = ref({
  id: null,
  table_id: "",
  name: "",
  phone: "",
  date: "",
  time: "",
  guests: 1,
})

function formatTime(value) {
  if (!value) return "-"
  return String(value).slice(0, 5)
}

function formatDateTime(value) {
  if (!value) return "-"
  return String(value).replace("T", " ").slice(0, 16)
}

function formatMoney(value) {
  const num = Number(value ?? 0)
  return num.toFixed(2)
}

function canEdit(r) {
  if (r.status === "cancelled") return false
  const dateTime = new Date(`${r.date}T${String(r.time).slice(0, 5)}`)
  return dateTime > new Date()
}

function openEdit(r) {
  formError.value = ""
  form.value = {
    id: r.id,
    table_id: r.table_id,
    name: r.name ?? "",
    phone: r.phone ?? "",
    date: r.date ?? "",
    time: formatTime(r.time),
    guests: Number(r.guests ?? 1),
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  formError.value = ""
}

async function loadOrders() {
  loadingOrders.value = true

  try {
    const res = await api.get("/my-orders")
    orders.value = res.data.data ?? []
  } catch (e) {
    pageError.value = e?.response?.data?.message || "Could not load history."
  } finally {
    loadingOrders.value = false
  }
}

async function loadReservations() {
  loadingReservations.value = true

  try {
    const res = await api.get("/my-reservations")
    reservations.value = res.data.data ?? []
  } catch (e) {
    pageError.value = e?.response?.data?.message || "Could not load history."
  } finally {
    loadingReservations.value = false
  }
}

async function submitReservationEdit() {
  formError.value = ""
  saving.value = true

  try {
    await api.put(`/my-reservations/${form.value.id}`, {
      table_id: Number(form.value.table_id),
      name: form.value.name,
      phone: form.value.phone,
      date: form.value.date,
      time: form.value.time,
      guests: Number(form.value.guests),
    })

    showModal.value = false
    await loadReservations()
  } catch (e) {
    formError.value = e?.response?.data?.message || "Could not update reservation."
  } finally {
    saving.value = false
  }
}

async function handleLogout() {
  await auth.logout()
  router.push("/login")
}

onMounted(async () => {
  pageError.value = ""
  await Promise.all([loadOrders(), loadReservations()])
})
</script>

<style scoped>
.bg-gold {
  background-color: #d4af37;
}
.text-gold {
  color: #d4af37;
}
</style>