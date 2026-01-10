<template>
  <div class="min-h-screen bg-gradient-to-b from-black via-slate-900 to-zinc-900 text-white pt-28">
    <div class="max-w-3xl mx-auto px-4 py-16 text-center">
      <div class="bg-white/5 border border-white/10 rounded-3xl p-10">
        <div class="text-5xl mb-4">✅</div>

        <h1 class="text-3xl font-bold mb-2">
          Porosia u përfundua me sukses!
        </h1>

        <p class="text-gray-300 mb-6">
          Faleminderit. Mund ta gjenerosh faturën më poshtë.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
          <RouterLink
            to="/invoice"
            class="px-6 py-3 rounded-xl bg-gold text-black font-semibold hover:bg-yellow-400 transition"
          >
            Gjenero faturën
          </RouterLink>

          <RouterLink
            to="/menu"
            class="px-6 py-3 rounded-xl border border-white/20 hover:bg-white/10 transition"
          >
            Kthehu te menu
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.text-gold { color: #d4af37; }
.bg-gold { background-color: #d4af37; }
</style>
