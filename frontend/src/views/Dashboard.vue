<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

// Component Imports
import DoctorList from '../components/DoctorList.vue'
import BookingForm from '../components/BookingForm.vue'
import MyBookings from '../components/MyBookings.vue'
import DoctorDashboard from './DoctorDashboard.vue'
import DoctorSettings from './DoctorSettings.vue'

// Routing & State
const router = useRouter()
const user = ref(null)
const loading = ref(true)

// Patient State
const selectedDoctor = ref(null)
const showSuccessMessage = ref(false)
const myBookingsRef = ref(null)

// Doctor State
const showSettings = ref(false)

// Verification State
const verificationSent = ref(false)
const verificationLoading = ref(false)

// --- Initialization ---
onMounted(() => {
  const userData = localStorage.getItem('user')
  const token = localStorage.getItem('token')

  if (!token || !userData) {
    router.push('/login')
  } else {
    user.value = JSON.parse(userData)
  }
  loading.value = false
})

// --- Computed Properties ---
const isDoctor = computed(() => {
  return user.value?.is_doctor || false
})

const isVerified = computed(() => {
  return user.value && user.value.email_verified_at !== null
})

// --- Global Actions ---
const logout = async () => {
  try { await axios.post('/api/v1/auth/logout') } catch (e) {}
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/login')
}

// --- Verification Logic ---
const resendVerification = async () => {
  verificationLoading.value = true
  try {
    await axios.post('/api/v1/auth/email/verification-notification')
    verificationSent.value = true
  } catch (err) {
    alert("Failed to resend email. It might already be verified.")
  } finally {
    verificationLoading.value = false
  }
}

// --- Patient Actions ---
const openBooking = (doctor) => {
  if (!isVerified.value) {
    alert("Please verify your email address to book appointments.")
    return
  }
  selectedDoctor.value = doctor
}

const closeBooking = () => { selectedDoctor.value = null }

const handleBookingSuccess = () => {
  selectedDoctor.value = null
  showSuccessMessage.value = true

  // Auto-hide success message after 5 seconds
  setTimeout(() => showSuccessMessage.value = false, 5000)

  // Refresh the side list
  if (myBookingsRef.value) {
    myBookingsRef.value.refresh()
  }
}
</script>

<template>
  <div v-if="user" class="dashboard-layout">
    <!-- HEADER -->
    <header class="header">
      <div class="welcome">
        <h1>Hello, {{ user.name }} 👋</h1>
        <p v-if="isDoctor" class="role-badge doctor">👨‍⚕️ Doctor Account</p>
        <p v-else class="role-badge patient">👤 Patient Account</p>
      </div>

      <div class="controls">
        <!-- Doctor Specific Controls -->
        <button
            v-if="isDoctor"
            @click="showSettings = !showSettings"
            class="btn-settings"
        >
          {{ showSettings ? 'View Schedule' : 'Settings' }}
        </button>

        <button @click="logout" class="btn-logout">Logout</button>
      </div>
    </header>

    <!-- VERIFICATION WARNING (Global) -->
    <div v-if="!isVerified" class="alert-warning">
      <div class="warning-content">
        <span class="icon">⚠️</span>
        <span><strong>Your email is not verified.</strong> You must verify it to use platform features.</span>
      </div>
      <button
          @click="resendVerification"
          :disabled="verificationLoading || verificationSent"
          class="btn-resend"
      >
        {{ verificationSent ? 'Email Sent!' : (verificationLoading ? 'Sending...' : 'Resend Verification Link') }}
      </button>
    </div>

    <!-- SUCCESS ALERT (Global) -->
    <div v-if="showSuccessMessage" class="alert-success">
      ✅ Appointment booked successfully!
    </div>

    <!-- DOCTOR VIEW -->
    <main v-if="isDoctor" class="doctor-section">
      <DoctorSettings v-if="showSettings" />
      <DoctorDashboard v-else />
    </main>

    <!-- PATIENT VIEW -->
    <main v-else class="content-grid">
      <section class="main-section">
        <h2>Find a Specialist</h2>
        <DoctorList @select="openBooking" />
      </section>

      <aside class="sidebar">
        <MyBookings ref="myBookingsRef" />
      </aside>
    </main>

    <!-- MODAL (Patient Only) -->
    <BookingForm
        v-if="selectedDoctor"
        :doctor="selectedDoctor"
        @close="closeBooking"
        @success="handleBookingSuccess"
    />
  </div>
</template>

<style scoped>
/* Layout */
.dashboard-layout { max-width: 1200px; margin: 0 auto; padding: 20px; font-family: 'Segoe UI', sans-serif; }

/* Header */
.header {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;
}
.welcome h1 { margin: 0 0 5px 0; color: #2c3e50; font-size: 1.8rem; }
.role-badge { font-size: 0.9rem; font-weight: 600; margin: 0; }
.role-badge.doctor { color: #007bff; }
.role-badge.patient { color: #6c757d; }

.controls { display: flex; gap: 10px; }

/* Buttons */
.btn-logout { background: white; border: 1px solid #ddd; padding: 8px 16px; border-radius: 6px; cursor: pointer; transition: all 0.2s; color: #dc3545; font-weight: 500; }
.btn-logout:hover { background: #fff5f5; border-color: #dc3545; }

.btn-settings { background: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 500; }
.btn-settings:hover { background: #0056b3; }

/* Alerts */
.alert-warning {
  background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ffeeba;
  display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;
}
.warning-content { display: flex; align-items: center; gap: 10px; }
.btn-resend { background: #856404; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 0.85rem; }
.btn-resend:disabled { opacity: 0.6; cursor: not-allowed; }

.alert-success { background: #d1e7dd; color: #0f5132; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #badbcc; text-align: center; font-weight: bold; }

/* Patient Grid */
.content-grid { display: grid; grid-template-columns: 1fr 350px; gap: 30px; }
@media (max-width: 900px) { .content-grid { grid-template-columns: 1fr; } }

.main-section h2 { color: #2c3e50; margin-bottom: 15px; font-size: 1.4rem; }

/* Doctor Section */
.doctor-section { margin-top: 20px; }
</style>