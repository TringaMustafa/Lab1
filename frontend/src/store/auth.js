import { defineStore } from "pinia"
import { ref } from "vue"
import api from "../api/axios"

export const useAuthStore = defineStore("auth", () => {
  const user = ref(JSON.parse(localStorage.getItem("user") || "null"))

  const savedToken = localStorage.getItem("token")
  const token = ref(
    savedToken && savedToken !== "null" && savedToken !== "undefined" ? savedToken : null
  )

  function isLogged() {
    return !!token.value && token.value !== "null" && token.value !== "undefined"
  }

  async function login(email, password) {
    const res = await api.post("/login", { email, password })

    token.value = res.data.token
    user.value = res.data.user

    localStorage.setItem("token", token.value)
    localStorage.setItem("user", JSON.stringify(user.value))

    return res
  }

 async function register(name, email, password) {
  const res = await api.post("/register", { name, email, password })

  token.value = res.data.token
  user.value = res.data.user

  localStorage.setItem("token", token.value)
  localStorage.setItem("user", JSON.stringify(user.value))

  return res
}

  async function me() {
    const res = await api.get("/me")
    user.value = res.data
    localStorage.setItem("user", JSON.stringify(user.value))
    return res
  }

  async function logout() {
    try { await api.post("/logout") } catch (e) {}

    token.value = null
    user.value = null
    localStorage.removeItem("token")
    localStorage.removeItem("user")
  }

  // ✅ KJO E ZGJIDH "Logout del menjehere"
 async function init() {
  if (!token.value) return

  try {
    await me()
  } catch (e) {
    // token invalid → pastro
    token.value = null
    user.value = null
    localStorage.removeItem("token")
    localStorage.removeItem("user")
  }
}


  function isAdmin() {
    return user.value?.role === "admin"
  }

  return { user, token, login, register, me, logout, init, isLogged, isAdmin }
})
