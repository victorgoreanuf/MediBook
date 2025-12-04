import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './router'

// ⚠️ THIS LINE IS MISSING!
// You must import this file so the interceptors start running.
import './axios'

const app = createApp(App)

app.use(router)
app.mount('#app')