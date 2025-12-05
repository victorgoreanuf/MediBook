<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const appointments = ref([])
const loading = ref(true)
const error = ref('')

const formatDate = (dateString) => {
  if (!dateString) return ''

  // 🚨 THE FIX: Strip the timezone offset ('Z' or '+00:00')
  // This turns "2025-12-05T10:00:00+00:00" into "2025-12-05T10:00:00"
  // The browser treats this "naked" string as Local Time, so 10:00 stays 10:00.
  const rawDate = dateString.replace(/(Z|\+\d{2}:\d{2})$/, '')
  const date = new Date(rawDate)

  return date.toLocaleString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: 'numeric'
  })
}

const fetchAppointments = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/v1/appointments')
    appointments.value = response.data.data
  } catch (err) {
    console.error("Failed to fetch bookings", err)
    error.value = "Failed to load appointments."
  } finally {
    loading.value = false
  }
}

const cancelAppointment = async (id) => {
  if (!confirm("Are you sure you want to cancel this appointment?")) return

  try {
    await axios.patch(`/api/v1/appointments/${id}/cancel`)
    await fetchAppointments()
  } catch (err) {
    alert(err.response?.data?.message || "Failed to cancel.")
  }
}

defineExpose({ refresh: fetchAppointments })

onMounted(fetchAppointments)
</script>

<template>
  <div class="bookings-card">
    <h3>📅 My Appointments</h3>

    <div v-if="loading" class="state-msg">Loading history...</div>
    <div v-else-if="error" class="state-msg error">{{ error }}</div>

    <div v-else-if="appointments.length === 0" class="state-msg empty">
      <p>You haven't booked any appointments yet.</p>
    </div>

    <ul v-else class="booking-list">
      <li v-for="apt in appointments" :key="apt.id" class="booking-item">
        <div class="header">
          <span class="doctor-name">Dr. {{ apt.doctor.name }}</span>
          <span class="status-badge" :class="apt.status">{{ apt.status }}</span>
        </div>

        <div class="details">
          <div class="specialization">{{ apt.doctor.specialization }}</div>
          <!-- Now guaranteed to show the database time -->
          <div class="time">{{ formatDate(apt.start_time) }}</div>
        </div>

        <div v-if="apt.status === 'scheduled'" class="actions">
          <button @click="cancelAppointment(apt.id)" class="btn-cancel-sm">
            Cancel
          </button>
        </div>
      </li>
    </ul>
  </div>
</template>

<style scoped>
/* Keeping same styles as before */
.bookings-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); height: fit-content; }
h3 { margin-top: 0; color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; }
.booking-list { list-style: none; padding: 0; margin: 0; }
.booking-item { padding: 15px 0; border-bottom: 1px solid #f0f0f0; }
.booking-item:last-child { border-bottom: none; }
.header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 5px; }
.doctor-name { font-weight: 600; color: #333; }
.status-badge { font-size: 0.75rem; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; font-weight: 700; }
.status-badge.scheduled { background: #e3f2fd; color: #1565c0; }
.status-badge.completed { background: #e8f5e9; color: #2e7d32; }
.status-badge.cancelled { background: #ffebee; color: #c62828; }
.details { margin-bottom: 8px; }
.specialization { font-size: 0.85rem; color: #777; margin-bottom: 2px; }
.time { font-size: 0.95rem; color: #555; }
.actions { text-align: right; }
.btn-cancel-sm { padding: 4px 10px; background: white; border: 1px solid #dc3545; color: #dc3545; border-radius: 4px; cursor: pointer; font-size: 0.8rem; transition: all 0.2s; }
.btn-cancel-sm:hover { background: #dc3545; color: white; }
.state-msg { text-align: center; color: #888; padding: 20px 0; font-style: italic; }
.state-msg.error { color: #dc3545; font-style: normal; }
</style>