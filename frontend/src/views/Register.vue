<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const error = ref('')
const loading = ref(false)

const handleRegister = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await axios.post('/api/v1/auth/register', form.value)

    localStorage.setItem('token', response.data.data.token)
    localStorage.setItem('user', JSON.stringify(response.data.data.user))

    router.push('/')

  } catch (err) {
    error.value = err.response?.data?.message || 'Registration failed.'
    // If validation errors exist, show the first one
    if (err.response?.data?.errors) {
      error.value = Object.values(err.response.data.errors).flat()[0]
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-container">
    <div class="card">
      <h2>Create Account</h2>
      <form @submit.prevent="handleRegister">
        <div class="form-group">
          <label>Full Name</label>
          <input v-model="form.name" type="text" required />
        </div>

        <div class="form-group">
          <label>Email</label>
          <input v-model="form.email" type="email" required />
        </div>

        <div class="form-group">
          <label>Password</label>
          <input v-model="form.password" type="password" required />
        </div>

        <div class="form-group">
          <label>Confirm Password</label>
          <input v-model="form.password_confirmation" type="password" required />
        </div>

        <p v-if="error" class="error">{{ error }}</p>

        <button :disabled="loading" type="submit" class="btn-primary">
          {{ loading ? 'Creating Account...' : 'Register' }}
        </button>
      </form>
      <p class="link-text">
        Already have an account? <router-link to="/login">Login here</router-link>
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
.btn-primary { width: 100%; padding: 0.75rem; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
.btn-primary:disabled { background: #ccc; }
.error { color: red; font-size: 0.9rem; margin-bottom: 1rem; }
.link-text { margin-top: 1rem; text-align: center; font-size: 0.9rem; }
</style>