<template>
  <div class="flex h-screen bg-gray-100">
    <!-- SIDEBAR -->
    <aside class="w-64 bg-gray-900 text-gray-200 flex flex-col">
      <div class="p-4 text-xl font-bold text-white border-b border-gray-700">
        🍽️ TableFlow
      </div>

      <nav class="flex-1 p-4 space-y-2">
        <RouterLink
          v-for="item in menu"
          :key="item.path"
          :to="item.path"
          class="flex items-center gap-2 p-2 rounded-md hover:bg-gray-800 transition"
          :class="{ 'bg-blue-600 text-white': $route.path === item.path }"
        >
          <span>{{ item.icon }}</span>
          <span>{{ item.label }}</span>
        </RouterLink>
      </nav>

      <button
        class="m-4 p-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition"
      >
        Logout
      </button>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 overflow-y-auto">
      <!-- TOPBAR -->
      <header class="bg-white shadow p-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold">
          {{ $route.meta.title || '' }}
        </h1>

        <div class="flex items-center gap-4">
          <input
            type="text"
            placeholder="Search..."
            class="border rounded-lg px-3 py-1"
          />

          <div class="flex items-center gap-2 bg-gray-100 p-2 rounded-full">
            <div class="w-8 h-8 flex items-center justify-center bg-blue-200 text-blue-700 rounded-full">
              R
            </div>
            <div>
              <p class="text-sm font-semibold">Admin</p>
              <p class="text-xs text-gray-500">Manager</p>
            </div>
          </div>
        </div>
      </header>

      <section class="p-6">
        <slot />
      </section>
    </main>
  </div>
</template>

<script>
export default {
  data() {
    return {
      menu: [
        { label: 'Dashboard', path: '/dashboard', icon: '📊' },
        { label: 'Orders', path: '/orders', icon: '🧾' },
        { label: 'Tables', path: '/tables', icon: '🪑' },
        { label: 'Menu', path: '/menu', icon: '🍕' }
      ]
    }
  }
}
</script>
