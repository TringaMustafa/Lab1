<template>
  <div class="min-h-screen bg-gradient-to-b from-black via-slate-900 to-zinc-900 text-white pt-28">
    <div class="max-w-5xl mx-auto px-4 py-10">

      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Checkout</h1>
        <RouterLink to="/cart" class="text-gold hover:underline">← Kthehu te Cart</RouterLink>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- LEFT: Form -->
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
          <h2 class="font-semibold mb-4">Të dhënat e porosisë</h2>

          <p v-if="error" class="text-rose-300 text-sm mb-3">{{ error }}</p>

          <div class="space-y-4">
            <div>
              <label class="block text-sm mb-1">Emri dhe Mbiemri</label>
              <input
                v-model.trim="fullName"
                type="text"
                class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/20 text-white outline-none focus:border-gold"
                placeholder="p.sh. Natyra Kyçyku"
              />
            </div>

            <!-- PHONE -->
            <div>
              <label class="block text-sm mb-1">Numri i telefonit</label>

              <div class="flex gap-2">
                <select
                  v-model="countryCode"
                  class="w-28 px-3 py-2 rounded-lg bg-black/40 border border-white/20 text-white outline-none focus:border-gold"
                >
                  <option value="+383">+383</option>
                  <option value="+355">+355</option>
                  <option value="+389">+389</option>
                  <option value="+381">+381</option>
                  <option value="+30">+30</option>
                </select>

                <input
                  v-model="phoneDisplay"
                  inputmode="numeric"
                  class="flex-1 px-3 py-2 rounded-lg bg-black/40 border border-white/20 text-white outline-none focus:border-gold"
                  placeholder="044 123 456"
                />
              </div>

              <p class="text-xs text-gray-300 mt-2">
                Format: 3-3-3 (p.sh. 044 123 456). Vetëm numra.
              </p>
            </div>

            <div>
              <label class="block text-sm mb-1">Adresa (opsionale)</label>
              <input
                v-model.trim="address"
                type="text"
                class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/20 text-white outline-none focus:border-gold"
                placeholder="Rruga, Qyteti"
              />
            </div>

            <button
              @click="placeOrder"
              :disabled="placing || !cart.items.length"
              class="w-full mt-2 py-3 rounded-xl bg-gold text-black font-semibold hover:bg-yellow-400 disabled:opacity-60 transition"
            >
              {{ placing ? "Duke e dërguar..." : "Place Order" }}
            </button>
          </div>
        </div>

        <!-- RIGHT: Summary -->
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
          <h2 class="font-semibold mb-4">Përmbledhja</h2>

          <div v-if="!cart.items.length" class="text-gray-300">
            Cart është bosh.
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="it in cart.items"
              :key="it.id"
              class="flex items-center justify-between border-b border-white/10 pb-3"
            >
              <div>
                <div class="font-semibold">{{ it.name }}</div>
                <div class="text-xs text-gray-300">
                  {{ it.qty }} x €{{ Number(it.price ?? 0).toFixed(2) }}
                </div>
              </div>

              <div class="font-semibold text-gold">
                €{{ (Number(it.price ?? 0) * Number(it.qty ?? 0)).toFixed(2) }}
              </div>
            </div>

            <div class="flex items-center justify-between pt-2">
              <div class="text-gray-300">Total</div>
              <div class="text-2xl font-bold text-gold">
                €{{ Number(cart.total ?? 0).toFixed(2) }}
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { computed, ref } from "vue"
import { useRouter } from "vue-router"
import { useCartStore } from "../store/cart"
import { useAuthStore } from "../store/auth"

const router = useRouter()
const cart = useCartStore()
const auth = useAuthStore()

const fullName = ref(auth.user?.name ?? "")
const address = ref("")
const error = ref("")
const placing = ref(false)

// ✅ Country code + phone formatting 3-3-3
const countryCode = ref("+383")
const phoneRaw = ref("") // vetëm numrat, pa hapësira

const phoneDisplay = computed({
  get() {
    const d = phoneRaw.value.replace(/\D/g, "").slice(0, 9)
    // 3-3-3
    const a = d.slice(0, 3)
    const b = d.slice(3, 6)
    const c = d.slice(6, 9)
    return [a, b, c].filter(Boolean).join(" ")
  },
  set(val) {
    phoneRaw.value = String(val || "").replace(/\D/g, "").slice(0, 9)
  },
})

function makeOrderId() {
  return "ORD-" + Date.now()
}

async function placeOrder() {
  error.value = ""

  if (!cart.items.length) {
    error.value = "Cart është bosh."
    return
  }

  if (!fullName.value || fullName.value.length < 3) {
    error.value = "Shkruaj emrin dhe mbiemrin."
    return
  }

  if (phoneRaw.value.length < 8) {
    error.value = "Shkruaj numrin e telefonit (të paktën 8-9 shifra)."
    return
  }

  placing.value = true

  try {
    // ✅ për momentin: e ruajmë porosinë lokalisht (pa backend)
    const order = {
      id: makeOrderId(),
      user: { id: auth.user?.id, name: fullName.value, email: auth.user?.email },
      phone: `${countryCode.value} ${phoneDisplay.value}`,
      address: address.value,
      items: cart.items.map(i => ({
        id: i.id,
        name: i.name,
        price: Number(i.price ?? 0),
        qty: Number(i.qty ?? 0),
        lineTotal: Number(i.price ?? 0) * Number(i.qty ?? 0),
      })),
      total: Number(cart.total ?? 0),
      createdAt: new Date().toISOString(),
    }

    localStorage.setItem("last_order", JSON.stringify(order))

    // pastro cart
    cart.clear()

    // shko te success
    router.push("/success")
  } finally {
    placing.value = false
  }
}
</script>

<style scoped>
.text-gold { color: #d4af37; }
.bg-gold { background-color: #d4af37; }
</style>
