<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

const status = ref('verifying')
const message = ref('Verifying your email address...')

onMounted(async () => {
  // Extract params
  const { id, hash } = route.params
  const query = route.query

  // Construct URL
  const verifyApiUrl = `/api/v1/auth/email/verify/${id}/${hash}?expires=${query.expires}&signature=${query.signature}`

  try {
    // Send request (Token not required anymore)
    await axios.get(verifyApiUrl)

    status.value = 'success'
    message.value = 'Email verified successfully! Redirecting...'

    // If the user happens to be logged in locally, update their state
    const user = JSON.parse(localStorage.getItem('user'))
    if (user && user.id == id) {
      user.email_verified_at = new Date().toISOString()
      localStorage.setItem('user', JSON.stringify(user))
    }

    setTimeout(() => router.push('/login'), 2000)

  } catch (err) {
    status.value = 'error'
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
      <router-link to="/login" class="btn">Go to Login</router-link>
    </div>
  </div>
</template>

<style scoped>
/* Reuse previous styles */
.verify-layout { display: flex; justify-content: center; align-items: center; height: 80vh; text-align: center; }
.card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); max-width: 400px; width: 100%; }
.status-icon { font-size: 3rem; margin-bottom: 20px; }
.btn { display: inline-block; margin-top: 20px; text-decoration: none; background: #007bff; color: white; padding: 10px 20px; border-radius: 6px; }
</style>