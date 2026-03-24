<template>
  <!-- NAVBAR TRANSPARENT SI HOME -->
  <header class="absolute top-0 left-0 w-full z-50 bg-transparent text-white">
    <div class="max-w-6xl mx-auto flex justify-between items-center py-6 px-4 md:px-0">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-2xl bg-gold flex items-center justify-center text-black text-xl">
          🍽️
        </div>
        <span class="text-2xl font-bold tracking-wide text-gold">TableFlow</span>
      </div>

      <nav class="flex items-center gap-6 text-sm md:text-base">
        <RouterLink to="/" class="hover:text-gold transition">Home</RouterLink>
        <RouterLink to="/menu" class="hover:text-gold transition">Menu</RouterLink>
        <RouterLink to="/tables" class="hover:text-gold transition">Tables</RouterLink>
       <RouterLink
  v-if="auth.isAdmin()"
  to="/dashboard"
  class="..."
>
  Dashboard
</RouterLink>
        <RouterLink
          v-if="isLogged"
          to="/cart"
          class="relative ml-2 px-3 py-2 rounded-lg border border-white/20 hover:bg-white/10 transition"
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


        <!-- NËSE NUK ËSHTË LOGGED -->
        <RouterLink
          v-if="!auth.isLogged()"
          to="/login"
          class="ml-4 px-4 py-2 rounded-lg border border-gold text-gold hover:bg-gold hover:text-black transition"
        >
          Login
        </RouterLink>

        <!-- NËSE ËSHTË LOGGED -->
        <button
          v-else
          @click="handleLogout"
          class="ml-4 px-4 py-2 rounded-lg border border-red-400 text-red-400 hover:bg-red-400 hover:text-black transition"
        >
          Logout
        </button>
      </nav>
    </div>
  </header>

  <!-- BACKGROUND -->
  <div class="min-h-screen bg-gradient-to-b from-black via-slate-900 to-zinc-900 text-white pt-28">
    <div class="max-w-6xl mx-auto px-4 py-10">
      <!-- HEADER -->
      <div class="pt-20 flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
          <h1 class="text-3xl md:text-4xl font-bold">
            Disponueshmëria e <span class="text-gold">tavolinave</span>
          </h1>

          <p class="text-gray-300 mt-2 text-sm md:text-base max-w-xl">
            Zgjidh datën/orën dhe shiko cilat tavolina janë të lira. Për rezervim duhet të jesh i kyçur.
          </p>
        </div>

        <p
          v-if="!isLogged"
          class="text-xs md:text-sm px-4 py-2 rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/40"
        >
          ⚠ Nuk mund të bësh rezervim pa u futur në llogari.
        </p>
      </div>

      <!-- FILTERS (date/time/guests) -->
      <div class="mb-6 rounded-2xl bg-white/5 border border-white/10 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
          <div>
            <label class="text-xs text-gray-300">Data</label>
            <input
              v-model="filters.date"
              type="date"
              class="mt-1 w-full px-3 py-2 rounded-xl bg-white/10 text-white border border-white/10"
            />
          </div>

          <div>
            <label class="text-xs text-gray-300">Ora</label>
            <input
              v-model="filters.time"
              type="time"
              class="mt-1 w-full px-3 py-2 rounded-xl bg-white/10 text-white border border-white/10"
            />
          </div>

          <div>
            <label class="text-xs text-gray-300">Persona</label>
            <input
              v-model.number="filters.guests"
              type="number"
              min="1"
              class="mt-1 w-full px-3 py-2 rounded-xl bg-white/10 text-white border border-white/10"
            />
          </div>

          <button
            @click="checkAvailability"
            class="px-4 py-2 rounded-xl bg-gold text-black font-semibold hover:opacity-90 transition"
          >
            Check availability
          </button>
        </div>

        <p v-if="info" class="mt-3 text-sm text-gray-300">{{ info }}</p>
      </div>

      <!-- TABLES GRID -->
      <div v-if="loading" class="text-sm text-gray-300">Loading...</div>

      <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
        <div
          v-for="t in tables"
          :key="t.id"
          class="rounded-2xl bg-white/5 border border-white/10 p-5 flex flex-col justify-between hover:border-gold/60 transition"
        >
          <div>
            <p class="text-lg font-semibold">Tavolina {{ t.name }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ t.seats }} vende</p>
          </div>

          <div class="mt-4 flex items-center justify-between">
            <span
              class="px-3 py-1 rounded-full text-xs font-semibold"
              :class="availabilityBadgeClass(t)"
            >
              {{ availabilityText(t) }}
            </span>

            <button
              @click="openReserveModal(t)"
              :disabled="!isLogged || !isAvailable(t.id)"
              class="px-3 py-1 rounded-lg text-xs font-semibold border border-gold text-gold hover:bg-gold hover:text-black transition disabled:opacity-40"
            >
              Rezervo
            </button>
          </div>
        </div>
      </div>

      <!-- MODAL RESERVATION -->
      <div v-if="showModal" class="fixed inset-0 bg-black/60 flex items-center justify-center p-4">
        <div class="w-full max-w-lg rounded-2xl bg-zinc-950 border border-white/10 p-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-lg">
              Rezervo – Tavolina {{ selected?.name }}
            </h2>
            <button @click="closeModal" class="text-gray-300 hover:text-white">✕</button>
          </div>

          <p v-if="error" class="text-sm text-rose-300 mb-3">{{ error }}</p>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="text-xs text-gray-300">Data</label>
              <input v-model="form.date" type="date"
                     class="mt-1 w-full px-3 py-2 rounded-xl bg-white/10 text-white border border-white/10" />
            </div>

            <div>
              <label class="text-xs text-gray-300">Ora</label>
              <input v-model="form.time" type="time"
                     class="mt-1 w-full px-3 py-2 rounded-xl bg-white/10 text-white border border-white/10" />
            </div>

            <div>
              <label class="text-xs text-gray-300">Persona</label>
              <input v-model.number="form.guests" type="number" min="1"
                     class="mt-1 w-full px-3 py-2 rounded-xl bg-white/10 text-white border border-white/10" />
            </div>

            <div>
              <label class="text-xs text-gray-300">Telefon</label>
              <input v-model.trim="form.phone" placeholder="049..."
                     class="mt-1 w-full px-3 py-2 rounded-xl bg-white/10 text-white border border-white/10" />
            </div>
          </div>

          <div class="mt-3">
            <label class="text-xs text-gray-300">Emri</label>
            <input v-model.trim="form.name" placeholder="Emri"
                   class="mt-1 w-full px-3 py-2 rounded-xl bg-white/10 text-white border border-white/10" />
          </div>

          <div class="mt-4 flex gap-3">
            <button
              @click="submitReservation"
              :disabled="saving"
              class="flex-1 px-4 py-2 rounded-xl bg-gold text-black font-semibold disabled:opacity-60"
            >
              {{ saving ? "Saving..." : "Confirm" }}
            </button>

            <button
              @click="closeModal"
              class="px-4 py-2 rounded-xl border border-white/20 text-white/80"
            >
              Cancel
            </button>
          </div>

          <p class="text-xs text-gray-400 mt-3">
            Nëse dikush e rezervon ndërkohë, sistemi kthen 409 dhe ti e sheh mesazhin.
          </p>
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

const loading = ref(false)
const saving = ref(false)
const info = ref("")
const error = ref("")

const tables = ref([])            // vijnë nga DB
const availableIds = ref([])      // ids available për date/time/guests

const filters = ref({
  date: new Date().toISOString().slice(0, 10),
  time: "19:30",
  guests: 2,
})

const showModal = ref(false)
const selected = ref(null)
const form = ref({
  date: "",
  time: "",
  guests: 2,
  name: "",
  phone: "",
})

function isAvailable(id) {
  return availableIds.value.includes(id)
}

function availabilityText(t) {
  // “E ZËNË” nëse s’është available për filtrat aktual
  return isAvailable(t.id) ? "E LIRË" : "E ZËNË"
}

function availabilityBadgeClass(t) {
  return isAvailable(t.id)
    ? "bg-emerald-500/15 text-emerald-300"
    : "bg-rose-500/15 text-rose-300"
}

async function loadTables() {
  const res = await api.get("/tables")
  tables.value = res.data.data ?? res.data
}

async function checkAvailability() {
  info.value = ""
  loading.value = true
  try {
    const res = await api.get("/tables/available", { params: filters.value })
    const data = res.data.data ?? res.data
    availableIds.value = (data || []).map(t => t.id)
    info.value = `Tavolina të lira: ${availableIds.value.length}`
  } catch (e) {
    info.value = e?.response?.data?.message || "Gabim në availability."
    availableIds.value = []
  } finally {
    loading.value = false
  }
}

function openReserveModal(t) {
  if (!isLogged.value) {
    router.push({ path: "/login", query: { redirect: "/tables" } })
    return
  }

  if (!isAvailable(t.id)) {
    info.value = "Kjo tavolinë është e zënë për këtë datë/orë. Ndrysho filtrat."
    return
  }

  error.value = ""
  selected.value = t
  form.value = {
    date: filters.value.date,
    time: filters.value.time,
    guests: filters.value.guests,
    name: auth.user?.name || "",
    phone: "",
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

async function submitReservation() {
  error.value = ""
  if (!selected.value) return
  if (!form.value.date || !form.value.time) return (error.value = "Zgjidh datën dhe orën.")
  if (!form.value.name || form.value.name.length < 2) return (error.value = "Emri është i detyrueshëm.")
  if (!form.value.phone || form.value.phone.length < 6) return (error.value = "Telefoni është i detyrueshëm.")
  if (!form.value.guests || form.value.guests < 1) return (error.value = "Guests duhet >= 1.")

  saving.value = true
  try {
    await api.post("/reservations", {
      table_id: selected.value.id,
      date: form.value.date,
      time: form.value.time,
      guests: Number(form.value.guests),
      name: form.value.name,
      phone: form.value.phone,
    })

    showModal.value = false

    // ✅ rifresko availability që tavolina të dalë “E ZËNË”
    await checkAvailability()

    info.value = `Rezervimi u krijua ✅ (Tavolina ${selected.value.name})`
  } catch (e) {
    // 409 -> already reserved
    error.value = e?.response?.data?.message || "Rezervimi dështoi."
  } finally {
    saving.value = false
  }
}

async function handleLogout() {
  await auth.logout()
  router.push("/login")
}

onMounted(async () => {
  await loadTables()
  await checkAvailability()
})
</script>

<style scoped>
.text-gold {
  color: #d4af37;
}
.bg-gold {
  background-color: #d4af37;
}
</style>
