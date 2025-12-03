import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
// We will create this Dashboard later, for now we can reuse Login or make a placeholder
import Dashboard from '../views/Dashboard.vue'

const routes = [
    { path: '/login', name: 'Login', component: Login },
    { path: '/register', name: 'Register', component: Register },
    { path: '/', name: 'Dashboard', component: Dashboard },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router