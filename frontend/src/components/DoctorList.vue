<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// Events
const emit = defineEmits(['select'])

// State
const doctors = ref([])
const loading = ref(true)
const error = ref('')

// Fetch Data
onMounted(async () => {
  try {
    const response = await axios.get('/api/v1/doctors')
    doctors.value = response.data.data
  } catch (err) {
    error.value = 'Failed to load doctors.'
    console.error(err)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="doctor-list-container">
    <div v-if="loading" class="loading">Loading Specialists...</div>
    <div v-else-if="error" class="error">{{ error }}</div>

    <div v-else class="doctor-grid">
      <div v-for="doctor in doctors" :key="doctor.id" class="doctor-card">
        <div class="card-header">
          <h3>{{ doctor.name }}</h3>
          <span class="badge">{{ doctor.specialization || 'General' }}</span>
        </div>

        <p class="bio">{{ doctor.bio || 'No biography available.' }}</p>

        <!-- Helper to show hours if available -->
        <div class="hours" v-if="doctor.available_hours">
          <small>🕒 Available: {{ doctor.available_hours.length }} slots</small>
        </div>

        <button @click="$emit('select', doctor)" class="btn-book">
          Book Appointment
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.doctor-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.doctor-card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
  transition: transform 0.2s;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.doctor-card:hover { transform: translateY(-5px); }

.card-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px; }
.card-header h3 { margin: 0; font-size: 1.1rem; color: #333; }
.badge { background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }

.bio { color: #666; font-size: 0.9rem; line-height: 1.4; margin-bottom: 15px; flex-grow: 1; }
.hours { margin-bottom: 15px; color: #555; }

.btn-book {
  width: 100%;
  padding: 10px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}
.btn-book:hover { background: #0056b3; }

.loading, .error { text-align: center; padding: 20px; font-size: 1.2rem; color: #666; }
.error { color: #dc3545; }
</style>