import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../api/axios'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'))
  const token = ref(localStorage.getItem('token') || null)

  async function login(email, password) {
    const res = await api.post('/login', { email, password })

    token.value = res.data.token
    user.value = res.data.user

    localStorage.setItem('token', token.value)
    localStorage.setItem('user', JSON.stringify(user.value))

    return res
  }

  async function register(name, email, password, role) {
    return await api.post('/register', { name, email, password, role })
  }

  function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  function isLogged() {
    return !!token.value
  }

  return { user, token, login, register, logout, isLogged }
})
