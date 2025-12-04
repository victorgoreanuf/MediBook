import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Dashboard from '../views/Dashboard.vue'
import VerifyEmail from '../views/VerifyEmail.vue'

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: { guest: true } // 🔒 Mark as "Guest Only"
    },
    {
        path: '/register',
        name: 'Register',
        component: Register,
        meta: { guest: true } // 🔒 Mark as "Guest Only"
    },
    {
        path: '/',
        name: 'Dashboard',
        component: Dashboard,
        meta: { requiresAuth: true } // 🔒 Mark as "Protected"
    },
    {
        // Capture the ID and Hash from the URL
        path: '/verify-email/:id/:hash',
        name: 'VerifyEmail',
        component: VerifyEmail,
        // No 'guest' guard because we NEED to be logged in to verify
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Global Navigation Guard
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')

    if (to.meta.requiresAuth && !token) {
        // Case 1: Trying to access protected page without token -> Go to Login
        next('/login')
    } else if (to.meta.guest && token) {
        // Case 2: Trying to access Login/Register while already logged in -> Go to Dashboard
        next('/')
    } else {
        // Case 3: Valid navigation
        next()
    }
})

export default router