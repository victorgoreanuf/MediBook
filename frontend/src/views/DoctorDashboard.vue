<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const appointments = ref([])
const loading = ref(true)

const formatDate = (dateString) => {
  if (!dateString) return ''

  // 🚨 THE FIX: Strip timezone here too
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

onMounted(async () => {
  try {
    const response = await axios.get('/api/v1/doctor/schedule')
    appointments.value = response.data.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="doctor-dash">
    <h2>🩺 My Upcoming Schedule</h2>

    <div v-if="loading" class="loading">Loading schedule...</div>
    <div v-else-if="appointments.length === 0" class="empty">No upcoming appointments.</div>

    <div v-else class="grid">
      <div v-for="apt in appointments" :key="apt.id" class="card">
        <div class="time-badge">
          {{ formatDate(apt.start_time) }}
        </div>
        <div class="patient-info">
          <h3>{{ apt.patient?.name || 'Unknown Patient' }}</h3>
          <p class="email">{{ apt.patient?.email }}</p>
        </div>
        <div class="status" :class="apt.status">{{ apt.status }}</div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.doctor-dash { padding: 20px; }
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
.card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 4px solid #007bff; }
.time-badge { font-weight: bold; font-size: 1.1rem; margin-bottom: 10px; color: #333; }
.patient-info h3 { margin: 0; font-size: 1rem; }
.email { color: #666; margin: 5px 0 0 0; font-size: 0.9rem; }
.status { display: inline-block; margin-top: 10px; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; }
.status.scheduled { background: #e3f2fd; color: #1976d2; }
.status.cancelled { background: #ffebee; color: #c62828; }
.loading, .empty { text-align: center; color: #666; font-style: italic; margin-top: 20px; }
</style>