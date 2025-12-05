<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const availableHours = ref([]) // e.g. ["09:00", "10:00"]
const bio = ref('')
const loading = ref(false)
const success = ref(false)

// All possible slots (Simplified list for MVP)
const allSlots = [
  "08:00", "09:00", "10:00", "11:00", "12:00",
  "13:00", "14:00", "15:00", "16:00", "17:00"
]

onMounted(() => {
  const user = JSON.parse(localStorage.getItem('user'))
  if (user) {
    availableHours.value = user.available_hours || []
    bio.value = user.bio || ''
  }
})

const toggleSlot = (slot) => {
  if (availableHours.value.includes(slot)) {
    availableHours.value = availableHours.value.filter(h => h !== slot)
  } else {
    availableHours.value.push(slot)
    availableHours.value.sort()
  }
}

const saveSettings = async () => {
  loading.value = true
  success.value = false
  try {
    const response = await axios.put('/api/v1/doctor/profile', {
      available_hours: availableHours.value,
      bio: bio.value
    })

    // Update local storage
    localStorage.setItem('user', JSON.stringify(response.data.data))
    success.value = true
  } catch (err) {
    alert("Failed to save settings.")
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="settings-container">
    <h2>⚙️ Availability Settings</h2>

    <div class="section">
      <label>Working Hours</label>
      <div class="slots-grid">
        <button
            v-for="slot in allSlots"
            :key="slot"
            class="slot-btn"
            :class="{ active: availableHours.includes(slot) }"
            @click="toggleSlot(slot)"
        >
          {{ slot }}
        </button>
      </div>
    </div>

    <div class="section">
      <label>Biography</label>
      <textarea v-model="bio" rows="4" placeholder="Tell patients about yourself..."></textarea>
    </div>

    <button @click="saveSettings" :disabled="loading" class="btn-save">
      {{ loading ? 'Saving...' : 'Save Changes' }}
    </button>

    <p v-if="success" class="success-msg">✅ Saved successfully!</p>
  </div>
</template>

<style scoped>
.settings-container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 600px; margin: 0 auto; }
.section { margin-bottom: 25px; }
.section label { display: block; font-weight: bold; margin-bottom: 10px; }
.slots-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 10px; }
.slot-btn { padding: 10px; border: 1px solid #ddd; background: #f8f9fa; border-radius: 6px; cursor: pointer; transition: all 0.2s; }
.slot-btn.active { background: #007bff; color: white; border-color: #007bff; }
textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
.btn-save { background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-size: 1rem; }
.success-msg { color: #28a745; margin-top: 10px; font-weight: bold; }
</style>