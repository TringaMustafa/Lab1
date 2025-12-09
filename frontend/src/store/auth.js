import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../api/axios'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'))
  const token = ref(localStorage.getItem('token') || null)

  async function login(email, password) {
    const res = await api.post('/auth/login', { email, password })
    if (res.data.token) {
      token.value = res.data.token
      localStorage.setItem('token', token.value)
    }
    user.value = res.data.user
    localStorage.setItem('user', JSON.stringify(user.value))
    return res
  }

  async function logout() {
    try { await api.post('/auth/logout') } catch {}
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  function isLogged() { return !!user.value }

  return { user, token, login, logout, isLogged }
})
