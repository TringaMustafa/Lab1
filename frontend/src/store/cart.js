import { defineStore } from "pinia"
import { ref, computed, watch } from "vue"

export const useCartStore = defineStore("cart", () => {
  // ✅ safe parse
  function loadItems() {
    try {
      const raw = localStorage.getItem("cart_items")
      const parsed = raw ? JSON.parse(raw) : []
      return Array.isArray(parsed) ? parsed : []
    } catch (e) {
      return []
    }
  }

  const items = ref(loadItems())

  // ✅ auto-save kur ndryshon cart
  watch(
    items,
    (val) => {
      localStorage.setItem("cart_items", JSON.stringify(val))
    },
    { deep: true }
  )

  function normalizeProduct(product) {
    return {
      id: product?.id,
      name: product?.name ?? "Item",
      price: Number(product?.price ?? 0),
      qty: 1,
    }
  }

  function addItem(product) {
    const p = normalizeProduct(product)
    if (p.id === undefined || p.id === null) return

    const existing = items.value.find((i) => i.id === p.id)
    if (existing) {
      existing.qty = Number(existing.qty ?? 0) + 1
    } else {
      items.value.push(p)
    }
  }

  function removeItem(id) {
    items.value = items.value.filter((i) => i.id !== id)
  }

  function inc(id) {
    const it = items.value.find((i) => i.id === id)
    if (!it) return
    it.qty = Number(it.qty ?? 0) + 1
  }

  function dec(id) {
    const it = items.value.find((i) => i.id === id)
    if (!it) return
    const q = Number(it.qty ?? 0)
    if (q <= 1) return
    it.qty = q - 1
  }

  function clear() {
    items.value = []
  }

  const total = computed(() =>
    items.value.reduce((sum, it) => {
      const price = Number(it?.price ?? 0)
      const qty = Number(it?.qty ?? 0)
      return sum + price * qty
    }, 0)
  )

  const count = computed(() =>
    items.value.reduce((sum, it) => sum + Number(it?.qty ?? 0), 0)
  )

  return { items, count, total, addItem, removeItem, inc, dec, clear }
})
