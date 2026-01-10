<template>
  <!-- Nëse route ka meta.layout === 'admin' -->
  <AdminLayout v-if="isAdminLayout">
    <RouterView />
  </AdminLayout>

  <!-- Pages normale -->
  <RouterView v-else />
</template>

<script setup>
import { computed } from "vue"
import { useRoute } from "vue-router"
import AdminLayout from "./layouts/AdminLayout.vue"

const route = useRoute()

// kontrollon layout-in nga router meta
const isAdminLayout = computed(() => route.meta.layout === "admin")
</script>
