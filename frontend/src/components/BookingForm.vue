<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
  doctor: { type: Object, required: true }
})

const emit = defineEmits(['close', 'success'])

const startTime = ref('')
const loading = ref(false)
const error = ref('')

// ROBUST END TIME CALCULATION
// 1. Create a Date object from the user's input
// 2. Add 1 Hour
// 3. Manually format it to "YYYY-MM-DD HH:mm:ss" (Local Time) to avoid UTC shifts
const endTime = computed(() => {
  if (!startTime.value) return ''

  const date = new Date(startTime.value)
  date.setHours(date.getHours() + 1)

  const y = date.getFullYear()
  const m = String(date.getMonth() + 1).padStart(2, '0')
  const d = String(date.getDate()).padStart(2, '0')
  const h = String(date.getHours()).padStart(2, '0')
  const min = String(date.getMinutes()).padStart(2, '0')

  return `${y}-${m}-${d} ${h}:${min}:00`
})

const submitBooking = async () => {
  loading.value = true
  error.value = ''

  console.log('Selected Doctor:', props.doctor) // Debugging

  // Format Start Time: "2025-10-10T10:00" -> "2025-10-10 10:00:00"
  const formattedStart = startTime.value.replace('T', ' ') + ':00'
  const formattedEnd = endTime.value // Computed property is already formatted

  const payload = {
    doctor_id: props.doctor.id,
    start_time: formattedStart,
    end_time: formattedEnd
  }

  console.log('Sending Payload:', payload) // Debugging

  try {
    await axios.post('/api/v1/appointments', payload)
    emit('success')

  } catch (err) {
    console.error('Booking Error:', err.response)

    if (err.response?.status === 422) {
      // Handle Laravel Validation Errors
      const errors = err.response.data.errors
      if (errors) {
        // Combine all error messages into a readable list
        error.value = Object.values(errors).flat().join('\n')
      } else {
        error.value = err.response.data.message
      }
    } else {
      error.value = 'Booking failed. Please try a different slot.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="modal-backdrop" @click.self="$emit('close')">
    <div class="modal-card">
      <div class="modal-header">
        <h2>Book with {{ doctor.name }}</h2>
        <button @click="$emit('close')" class="btn-close">×</button>
      </div>

      <form @submit.prevent="submitBooking">
        <div class="form-group">
          <label>Select Start Time</label>
          <!-- Native DateTime Picker -->
          <input
              v-model="startTime"
              type="datetime-local"
              required
              :min="new Date().toISOString().slice(0, 16)"
          />
          <small>Appointments are 1 hour long.</small>
        </div>

        <!-- Error Display -->
        <div v-if="error" class="error-msg">
          <span style="white-space: pre-line">{{ error }}</span>
        </div>

        <div class="actions">
          <button type="button" @click="$emit('close')" class="btn-cancel">Cancel</button>
          <button type="submit" :disabled="loading" class="btn-confirm">
            {{ loading ? 'Booking...' : 'Confirm Booking' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.modal-backdrop {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex; justify-content: center; align-items: center;
  z-index: 1000;
}
.modal-card {
  background: white; padding: 25px; border-radius: 12px; width: 90%; max-width: 500px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.modal-header h2 { margin: 0; font-size: 1.4rem; }
.btn-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; }

.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
.form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; }
.form-group small { color: #666; margin-top: 5px; display: block; }

.error-msg { background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }

.actions { display: flex; gap: 10px; justify-content: flex-end; }
.btn-cancel { padding: 10px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; }
.btn-confirm { padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
.btn-confirm:disabled { background: #93c5fd; }
</style>