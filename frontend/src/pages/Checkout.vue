<template>
  <div class="min-h-screen bg-gradient-to-b from-black via-slate-900 to-zinc-900 text-white pt-28">
    <div class="max-w-5xl mx-auto px-4 py-10">

      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Checkout</h1>
        <RouterLink to="/cart" class="text-gold hover:underline">← Kthehu te Cart</RouterLink>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- LEFT -->
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
          <h2 class="font-semibold mb-4">Të dhënat e porosisë</h2>

          <p v-if="error" class="text-rose-300 text-sm mb-3">{{ error }}</p>
          <p v-if="success" class="text-emerald-300 text-sm mb-3">{{ success }}</p>

          <div class="space-y-4">

            <!-- FULL NAME -->
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

            <!-- PAYMENT METHOD -->
            <div>
              <label class="block text-sm mb-2">Mënyra e pagesës</label>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                <button
                  type="button"
                  @click="selectPayment('cash')"
                  :class="payBtnClass('cash')"
                >
                  Cash
                </button>

                <button
                  type="button"
                  @click="selectPayment('visa')"
                  :class="payBtnClass('visa')"
                >
                  Visa
                </button>

                <button
                  type="button"
                  @click="selectPayment('paypal')"
                  :class="payBtnClass('paypal')"
                >
                  PayPal
                </button>

              </div>
            </div>

            <!-- CARD FIELDS (vetëm kur Visa/PayPal) -->
            <div v-if="paymentMethod === 'visa' || paymentMethod === 'paypal'" class="space-y-4">
              <div>
                <label class="block text-sm mb-1">Card Holder</label>
                <input
                  v-model.trim="cardHolder"
                  type="text"
                  class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/20 text-white outline-none focus:border-gold"
                  placeholder="p.sh. Natyra Kyçyku"
                />
              </div>

              <div>
                <label class="block text-sm mb-1">Card Number</label>
                <input
                  :value="cardNumber"
                  @input="cardNumber = formatCardNumber($event.target.value)"
                  inputmode="numeric"
                  class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/20 text-white outline-none focus:border-gold"
                  placeholder="4444 3333 2222 1111"
                />
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm mb-1">Expiry (MM/YY)</label>
                  <input
                    :value="cardExpiry"
                    @input="cardExpiry = formatExpiry($event.target.value)"
                    inputmode="numeric"
                    class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/20 text-white outline-none focus:border-gold"
                    placeholder="05/27"
                  />
                </div>

                <div>
                  <label class="block text-sm mb-1">CVC</label>
                  <input
                    :value="cardCvc"
                    @input="cardCvc = onlyDigits($event.target.value).slice(0,4)"
                    inputmode="numeric"
                    class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/20 text-white outline-none focus:border-gold"
                    placeholder="123"
                  />
                </div>
              </div>

              <p class="text-xs text-gray-300">
                * Demo UI: mos i ruaj kartelat/CVC në databazë.
              </p>
            </div>

            <!-- ADDRESS -->
            <div>
              <label class="block text-sm mb-1">Adresa (opsionale)</label>
              <input
                v-model.trim="address"
                type="text"
                class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/20 text-white outline-none focus:border-gold"
                placeholder="Rruga, Qyteti"
              />
            </div>

            <!-- PLACE ORDER -->
            <button
              @click="placeOrder"
              :disabled="placing || !cart.items.length"
              class="w-full mt-2 py-3 rounded-xl bg-gold text-black font-semibold hover:bg-yellow-400 disabled:opacity-60 transition"
            >
              {{ placing ? "Duke e dërguar..." : "Place Order" }}
            </button>
          </div>
        </div>

        <!-- RIGHT -->
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

            <RouterLink
              v-if="lastOrderId"
              to="/invoice"
              class="block text-center mt-4 py-2 rounded-xl border border-white/20 hover:bg-white/10 transition"
            >
              Shiko Faturën
            </RouterLink>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { computed, ref } from "vue"
import { useRouter } from "vue-router"
import api from "../api/axios"
import { useCartStore } from "../store/cart"
import { useAuthStore } from "../store/auth"

const router = useRouter()
const cart = useCartStore()
const auth = useAuthStore()

const fullName = ref(auth.user?.name ?? "")
const address = ref("")
const error = ref("")
const success = ref("")
const placing = ref(false)

const paymentMethod = ref("cash")
const countryCode = ref("+383")
const phoneRaw = ref("")
const lastOrderId = ref(null)

const cardHolder = ref("")
const cardNumber = ref("")
const cardExpiry = ref("")
const cardCvc = ref("")

const phoneDisplay = computed({
  get() {
    const d = phoneRaw.value.replace(/\D/g, "").slice(0, 9)
    const a = d.slice(0, 3)
    const b = d.slice(3, 6)
    const c = d.slice(6, 9)
    return [a, b, c].filter(Boolean).join(" ")
  },
  set(val) {
    phoneRaw.value = String(val || "").replace(/\D/g, "").slice(0, 9)
  },
})

function payBtnClass(val) {
  const base = "px-4 py-2 rounded-xl border text-sm font-semibold transition"
  return paymentMethod.value === val
    ? `${base} bg-gold text-black border-gold`
    : `${base} border-white/20 text-white hover:bg-white/10`
}

function selectPayment(m) {
  paymentMethod.value = m
  // kur kthehesh në cash, pastro fushat e kartelës
  if (m === "cash") {
    cardHolder.value = ""
    cardNumber.value = ""
    cardExpiry.value = ""
    cardCvc.value = ""
  }
}

function onlyDigits(str) {
  return String(str || "").replace(/\D/g, "")
}

function formatCardNumber(val) {
  const digits = onlyDigits(val).slice(0, 16)
  return digits.replace(/(\d{4})(?=\d)/g, "$1 ")
}

function formatExpiry(val) {
  const digits = onlyDigits(val).slice(0, 4)
  if (digits.length <= 2) return digits
  return digits.slice(0, 2) + "/" + digits.slice(2, 4)
}

async function placeOrder() {
  error.value = ""
  success.value = ""

  if (!cart.items.length) return (error.value = "Cart është bosh.")
  if (!fullName.value || fullName.value.length < 3) return (error.value = "Shkruaj emrin dhe mbiemrin.")
  if (phoneRaw.value.length < 8) return (error.value = "Shkruaj numrin e telefonit (8-9 shifra).")

  // ✅ validim i kartelës vetëm kur Visa/PayPal
  if (paymentMethod.value === "visa" || paymentMethod.value === "paypal") {
    const digits = onlyDigits(cardNumber.value)

    if (!cardHolder.value) return (error.value = "Shkruaj emrin në kartelë.")
    if (digits.length !== 16) return (error.value = "Numri i kartelës duhet të ketë 16 shifra.")
    if (!/^\d{2}\/\d{2}$/.test(cardExpiry.value)) return (error.value = "Expiry duhet të jetë në formatin MM/YY.")
    if (onlyDigits(cardCvc.value).length < 3) return (error.value = "CVC duhet të ketë së paku 3 shifra.")
  }

  placing.value = true

  try {
    const payload = {
      items: cart.items.map(i => ({
        id: i.id,
        price: Number(i.price ?? 0),
        qty: Number(i.qty ?? 0),
      })),
      phone: `${countryCode.value} ${phoneDisplay.value}`,
      payment_method: paymentMethod.value,
    }

    const res = await api.post("/orders", payload)

    const last = {
      order_id: res.data.order_id,
      fullName: fullName.value,
      phone: payload.phone,
      payment_method: payload.payment_method,
      address: address.value,
      items: cart.items,
      total: Number(cart.total ?? 0),
      createdAt: new Date().toISOString(),
    }

    localStorage.setItem("last_order", JSON.stringify(last))
    lastOrderId.value = res.data.order_id

    cart.clear()
    router.push("/success")
  } catch (e) {
    error.value = e?.response?.data?.message || "Ndodhi një gabim gjatë porosisë. Provo prap."
  } finally {
    placing.value = false
  }
}
</script>

<style scoped>
.text-gold { color: #d4af37; }
.bg-gold { background-color: #d4af37; }
</style>
