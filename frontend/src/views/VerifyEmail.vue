<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

const status = ref('verifying') // verifying, success, error
const message = ref('Verifying your email address...')

onMounted(async () => {
  // 1. Check if user is logged in
  const token = localStorage.getItem('token')

  if (!token) {
    // Strict Security: You cannot verify if you aren't logged in (Auth middleware)
    status.value = 'error'
    message.value = 'Please login to verify your email.'

    // Redirect to login after 3 seconds
    setTimeout(() => router.push('/login'), 3000)
    return
  }

  // 2. Extract params from the Vue Route (Browser URL)
  const { id, hash } = route.params
  const query = route.query // contains ?expires=...&signature=...

  // 3. Construct the API Endpoint URL
  // This points to the route you defined in api.php
  const verifyApiUrl = `/api/v1/auth/email/verify/${id}/${hash}?expires=${query.expires}&signature=${query.signature}`

  try {
    // 4. Send the Request
    // The axios interceptor in 'axios.js' will auto-attach the Bearer Token
    await axios.get(verifyApiUrl)

    status.value = 'success'
    message.value = 'Email verified successfully! Redirecting...'

    // Optional: Update local user state to reflect verification
    const user = JSON.parse(localStorage.getItem('user'))
    if (user) {
      user.email_verified_at = new Date().toISOString()
      localStorage.setItem('user', JSON.stringify(user))
    }

    // 5. Redirect to Dashboard
    setTimeout(() => router.push('/'), 2000)

  } catch (err) {
    status.value = 'error'
    console.error(err)
    message.value = err.response?.data?.message || 'Verification link invalid or expired.'
  }
})
</script>

<template>
  <div class="verify-layout">
    <div class="card">
      <div class="status-icon" :class="status">
        <span v-if="status === 'verifying'">⏳</span>
        <span v-if="status === 'success'">✅</span>
        <span v-if="status === 'error'">❌</span>
      </div>

      <h2>Email Verification</h2>
      <p>{{ message }}</p>

      <router-link v-if="status === 'error'" to="/login" class="btn">
        Go to Login
      </router-link>
    </div>
  </div>
</template>

<style scoped>
.verify-layout { display: flex; justify-content: center; align-items: center; height: 80vh; }
.card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); text-align: center; max-width: 400px; width: 100%; }
.status-icon { font-size: 3rem; margin-bottom: 20px; }
.btn { display: inline-block; margin-top: 20px; text-decoration: none; background: #007bff; color: white; padding: 10px 20px; border-radius: 6px; }
</style>