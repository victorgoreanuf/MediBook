<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const form = ref({ email: '', password: '' })
const error = ref('')
const loading = ref(false)

const handleLogin = async () => {
  loading.value = true
  error.value = ''

  try {
    // 1. Call the API
    const response = await axios.post('/api/v1/auth/login', form.value)

    // 2. Store the Token (Local Storage for MVP)
    // In a strictly secure app, you might use HttpOnly cookies, but LS is fine for this stage.
    localStorage.setItem('token', response.data.data.token)
    localStorage.setItem('user', JSON.stringify(response.data.data.user))

    // 3. Redirect to Dashboard
    router.push('/')

  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-container">
    <div class="card">
      <h2>Login to MediBook</h2>
      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label>Email</label>
          <input v-model="form.email" type="email" required placeholder="doctor@example.com" />
        </div>

        <div class="form-group">
          <label>Password</label>
          <input v-model="form.password" type="password" required />
        </div>

        <p v-if="error" class="error">{{ error }}</p>

        <button :disabled="loading" type="submit" class="btn-primary">
          {{ loading ? 'Logging in...' : 'Login' }}
        </button>
      </form>
      <p class="link-text">
        No account? <router-link to="/register">Register here</router-link>
      </p>
    </div>
  </div>
</template>

<style scoped>
.auth-container { display: flex; justify-content: center; align-items: center; height: 80vh; }
.card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
.form-group { margin-bottom: 1rem; text-align: left; }
.form-group label { display: block; margin-bottom: 0.5rem; color: #666; }
.form-group input { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
.btn-primary { width: 100%; padding: 0.75rem; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
.btn-primary:disabled { background: #ccc; }
.error { color: red; font-size: 0.9rem; margin-bottom: 1rem; }
.link-text { margin-top: 1rem; text-align: center; font-size: 0.9rem; }
</style>