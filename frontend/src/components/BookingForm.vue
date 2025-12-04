<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  doctor: { type: Object, required: true }
})

const emit = defineEmits(['close', 'success'])

// State
const selectedDate = ref(new Date().toISOString().slice(0, 10)) // Today YYYY-MM-DD
const slots = ref([])
const selectedSlot = ref(null)
const loadingSlots = ref(false)
const submitting = ref(false)
const error = ref('')

// Computed End Time (for display/submission)
const endTime = computed(() => {
  if (!selectedSlot.value) return ''
  const [h, m] = selectedSlot.value.split(':')
  const endH = parseInt(h) + 1
  return `${endH.toString().padStart(2, '0')}:${m}`
})

// Fetch Slots when Date Changes
const fetchSlots = async () => {
  if (!selectedDate.value) return

  loadingSlots.value = true
  selectedSlot.value = null // Reset selection
  error.value = ''

  try {
    // Use the Public UUID from the prop
    const response = await axios.get(`/api/v1/doctors/${props.doctor.id}/availability`, {
      params: { date: selectedDate.value }
    })
    slots.value = response.data.data
  } catch (err) {
    console.error(err)
    error.value = "Could not load schedule."
  } finally {
    loadingSlots.value = false
  }
}

// Watch date changes to auto-fetch
watch(selectedDate, fetchSlots, { immediate: true })

const submitBooking = async () => {
  if (!selectedSlot.value) return

  submitting.value = true
  error.value = ''

  // Construct strict YYYY-MM-DD HH:mm:ss strings
  const start = `${selectedDate.value} ${selectedSlot.value}:00`
  const end = `${selectedDate.value} ${endTime.value}:00`

  try {
    await axios.post('/api/v1/appointments', {
      doctor_id: props.doctor.id,
      start_time: start,
      end_time: end
    })
    emit('success')
  } catch (err) {
    if (err.response?.status === 422) {
      error.value = err.response.data.message
    } else {
      error.value = "Booking failed. Slot might have been taken."
    }
  } finally {
    submitting.value = false
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

      <div class="booking-body">
        <!-- 1. Pick Date -->
        <div class="form-group">
          <label>Select Date</label>
          <input
              v-model="selectedDate"
              type="date"
              :min="new Date().toISOString().slice(0, 10)"
              class="date-input"
          />
        </div>

        <!-- 2. Pick Slot -->
        <div class="form-group">
          <label>Available Slots</label>

          <div v-if="loadingSlots" class="loading-text">Checking schedule...</div>

          <div v-else-if="slots.length === 0" class="empty-text">
            No slots configured for this day.
          </div>

          <div v-else class="slots-grid">
            <button
                v-for="slot in slots"
                :key="slot.time"
                :disabled="slot.is_booked"
                :class="{
                                'slot-btn': true,
                                'selected': selectedSlot === slot.time,
                                'booked': slot.is_booked
                            }"
                @click="selectedSlot = slot.time"
            >
              {{ slot.time }}
            </button>
          </div>
        </div>

        <!-- Error Display -->
        <div v-if="error" class="error-msg">{{ error }}</div>

        <!-- Actions -->
        <div class="actions">
          <button @click="$emit('close')" class="btn-cancel">Cancel</button>
          <button
              @click="submitBooking"
              :disabled="submitting || !selectedSlot"
              class="btn-confirm"
          >
            {{ submitting ? 'Booking...' : (selectedSlot ? `Book ${selectedSlot}` : 'Select a Slot') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.modal-backdrop {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;
}
.modal-card {
  background: white; padding: 25px; border-radius: 12px; width: 90%; max-width: 450px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;
}
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.btn-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; }

.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
.date-input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box; }

.slots-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 10px; }
.slot-btn {
  padding: 10px; border: 1px solid #ddd; background: white; border-radius: 6px; cursor: pointer;
  font-weight: 500; transition: all 0.2s;
}
.slot-btn:hover:not(:disabled) { border-color: #007bff; color: #007bff; }
.slot-btn.selected { background: #007bff; color: white; border-color: #007bff; }
.slot-btn.booked { background: #f3f4f6; color: #ccc; cursor: not-allowed; text-decoration: line-through; border-color: #eee; }

.loading-text, .empty-text { color: #666; font-style: italic; text-align: center; padding: 10px; }
.error-msg { background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 6px; margin-bottom: 20px; text-align: center; }

.actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
.btn-cancel { padding: 12px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; }
.btn-confirm { padding: 12px 20px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
.btn-confirm:disabled { background: #93c5fd; cursor: not-allowed; }
</style>