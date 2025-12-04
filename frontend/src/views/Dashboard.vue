<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import DoctorList from '../components/DoctorList.vue'
import BookingForm from '../components/BookingForm.vue'

const router = useRouter()
const user = ref(null)
const selectedDoctor = ref(null)
const showSuccessMessage = ref(false)

onMounted(() => {
  const userData = localStorage.getItem('user')
  const token = localStorage.getItem('token')

  if (!token || !userData) {
    router.push('/login')
  } else {
    user.value = JSON.parse(userData)
  }
})

const logout = async () => {
  try { await axios.post('/api/v1/auth/logout') } catch (e) {}
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/login')
}

const openBooking = (doctor) => {
  selectedDoctor.value = doctor
}

const closeBooking = () => {
  selectedDoctor.value = null
}

const handleBookingSuccess = () => {
  selectedDoctor.value = null
  showSuccessMessage.value = true
  setTimeout(() => showSuccessMessage.value = false, 5000) // Hide after 5s
}
</script>

<template>
  <div class="dashboard-layout" v-if="user">
    <header class="header">
      <div class="welcome">
        <h1>Hello, {{ user.name }} 👋</h1>
        <p>Find a specialist and book your appointment.</p>
      </div>
      <button @click="logout" class="btn-logout">Logout</button>
    </header>

    <!-- Success Alert -->
    <div v-if="showSuccessMessage" class="alert-success">
      ✅ Appointment booked successfully! Check your email for confirmation.
    </div>

    <main>
      <DoctorList @select="openBooking" />
    </main>

    <!-- Modal -->
    <BookingForm
        v-if="selectedDoctor"
        :doctor="selectedDoctor"
        @close="closeBooking"
        @success="handleBookingSuccess"
    />
  </div>
</template>

<style scoped>
.dashboard-layout { max-width: 1200px; margin: 0 auto; padding: 20px; }

.header {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;
}
.welcome h1 { margin: 0 0 5px 0; color: #2c3e50; }
.welcome p { margin: 0; color: #666; }

.btn-logout { background: white; border: 1px solid #ddd; padding: 8px 16px; border-radius: 6px; cursor: pointer; transition: all 0.2s; }
.btn-logout:hover { background: #f8f9fa; border-color: #ccc; }

.alert-success {
  background: #d1e7dd; color: #0f5132; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #badbcc;
}
</style>