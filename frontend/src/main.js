import { createApp } from "vue"
import { createPinia } from "pinia"
import router from "./router"
import App from "./App.vue"
import "./assets/main.css"

import { useAuthStore } from "./store/auth"

const app = createApp(App)

const pinia = createPinia()
app.use(pinia)
app.use(router)

// ✅ verifiko token-in para se UI me vendos Login/Logout
const auth = useAuthStore()
auth.init().finally(() => {
  app.mount("#app")
})
