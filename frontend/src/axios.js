import axios from 'axios'
import router from './router'

// 1. Request Interceptor (Outgoing)
// This runs before every request is sent.
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('token')

    // If we have a token, attach it to the header
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }

    return config
}, error => {
    return Promise.reject(error)
})

// 2. Response Interceptor (Incoming)
// This runs when we receive a response from the backend.
axios.interceptors.response.use(response => {
    // If the response is good, just pass it through
    return response
}, error => {
    // If the response is 401 (Unauthorized), the token is invalid or expired
    if (error.response && error.response.status === 401) {
        // Clear the storage
        localStorage.removeItem('token')
        localStorage.removeItem('user')

        // Force redirect to login
        router.push('/login')
    }

    return Promise.reject(error)
})

export default axios