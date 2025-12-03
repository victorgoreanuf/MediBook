<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const user = ref(null)

onMounted(() => {
  // Check for authentication
  const userData = localStorage.getItem('user')
  const token = localStorage.getItem('token')

  if (!token || !userData) {
    router.push('/login')
  } else {
    user.value = JSON.parse(userData)
  }
})

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/login')
}
</script>

<template>
  <div class="dashboard" v-if="user">
    <h1>Welcome, {{ user.name }}!</h1>
    <p>You are now logged in.</p>

    <div class="actions">
      <!-- We will add the "Book Appointment" button here in the next step -->
      <button @click="logout" class="btn-secondary">Logout</button>
    </div>
  </div>
</template>

<style scoped>
.dashboard { text-align: center; padding: 50px; }
.actions { margin-top: 20px; }
.btn-secondary { background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
.btn-secondary:hover { background: #5a6268; }
</style>