<template>
  <!-- Nëse rruga ka meta.layout = 'admin' → përdor AdminLayout -->
  <AdminLayout v-if="isAdminLayout">
    <router-view />
  </AdminLayout>

  <!-- Përndryshe, vetëm router-view pa sidebar (Home, Menu, Tables, Login...) -->
  <router-view v-else />
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import AdminLayout from './layouts/AdminLayout.vue'

const route = useRoute()

const isAdminLayout = computed(() => route.meta.layout === 'admin')
</script>
