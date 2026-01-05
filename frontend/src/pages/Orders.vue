<template>
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

    <!-- LEFT SIDE: Categories + Menu -->
    <div class="lg:col-span-3 space-y-6">

      <!-- Categories -->
      <div class="flex gap-3 overflow-x-auto pb-2">
        <button 
          v-for="c in categories"
          :key="c"
          @click="activeCategory = c"
          class="px-4 py-2 rounded-full border text-sm font-medium whitespace-nowrap transition"
          :class="activeCategory === c ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-600 hover:bg-gray-100'"
        >
          {{ c }}
        </button>
      </div>

      <!-- Menu Items -->
      <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
        <div
          v-for="item in filteredMenu"
          :key="item.name"
          @click="addToCart(item)"
          class="bg-white border rounded-2xl shadow hover:shadow-xl cursor-pointer transition p-4 flex flex-col"
        >
          <img 
            :src="item.image"
            class="h-32 w-full object-cover rounded-xl mb-3"
          >
          <h3 class="font-semibold text-lg">{{ item.name }}</h3>
          <p class="text-sm text-gray-500">{{ item.category }}</p>
          <p class="text-xl font-bold text-blue-600 mt-auto">${{ item.price }}</p>
        </div>
      </div>

    </div>

    <!-- RIGHT SIDE: Cart -->
    <div class="bg-white rounded-2xl shadow p-6 h-fit">

      <h2 class="text-xl font-semibold mb-4">Your Order</h2>

      <div v-if="cart.length === 0" class="text-gray-400 text-sm">
        No items yet.
      </div>

      <div v-for="(c, i) in cart" :key="i" class="flex justify-between items-center border-b py-3">
        <div>
          <p class="font-medium">{{ c.name }}</p>
          <p class="text-sm text-gray-500">${{ c.price }}</p>
        </div>

        <button
          @click="removeFromCart(i)"
          class="text-red-500 hover:text-red-700"
        >
          ✕
        </button>
      </div>

      <div v-if="cart.length > 0" class="mt-4">
        <p class="text-lg font-bold">Total: ${{ total }}</p>
        <button class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-xl shadow">
          Place Order
        </button>
      </div>

    </div>

  </div>
</template>

<script>
export default {
  data() {
    return {
      activeCategory: "All",

      categories: [
        "All", "Pizza", "Drinks", "Burger", "Salad", "Dessert"
      ],

      menu: [
        { name: "Margherita Pizza", category: "Pizza", price: 8.0, image: "https://picsum.photos/200" },
        { name: "Pepsi", category: "Drinks", price: 2.0, image: "https://picsum.photos/201" },
        { name: "Chicken Burger", category: "Burger", price: 6.5, image: "https://picsum.photos/202" },
        { name: "Caesar Salad", category: "Salad", price: 5.5, image: "https://picsum.photos/203" },
        { name: "Chocolate Cake", category: "Dessert", price: 4.0, image: "https://picsum.photos/204" },
      ],

      cart: []
    };
  },

  computed: {
    filteredMenu() {
      if (this.activeCategory === "All") return this.menu;
      return this.menu.filter(m => m.category === this.activeCategory);
    },

    total() {
      return this.cart.reduce((sum, item) => sum + item.price, 0).toFixed(2);
    }
  },

  methods: {
    addToCart(item) {
      this.cart.push(item);
    },
    removeFromCart(i) {
      this.cart.splice(i, 1);
    }
  }
};
</script>
