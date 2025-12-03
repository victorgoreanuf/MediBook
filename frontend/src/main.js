import { createApp } from 'vue'
import './style.css' // Optional: Keep default styles
import App from './App.vue'
import router from './router' // Use the router configuration

const app = createApp(App)

app.use(router) // Inject the router into the app
app.mount('#app')