<template>
  <div>
    <h2>Planning Detail</h2>
    
    <div class="mt-3" style="margin-bottom: 15px;">
      <button class="btn btn-secondary" @click="emit('go-back')">Back to History</button>
    </div>

    <div v-if="state === 'loading'" class="alert alert-success">
      Loading...
    </div>

    <div v-if="state === 'error'" class="alert alert-error">
      {{ errorMessage }}
    </div>

    <div v-if="state === 'data' && detail">
      <div style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd;">
        <p><strong>Request Code:</strong> {{ detail.requestCode || detail.request_code }}</p>
        <p><strong>Candidate Token:</strong> {{ detail.candidateToken || detail.candidate_token }}</p>
        <p><strong>Created At:</strong> {{ detail.createdAt || detail.created_at }}</p>
        <p><strong>Status:</strong> {{ detail.status }}</p>
      </div>

      <h3>Slots</h3>
      <table>
        <thead>
          <tr>
            <th>Slot Order</th>
            <th>Slot Name</th>
            <th>Original</th>
            <th>Balanced</th>
            <th>Is Active</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="slot in detail.slots" :key="slot.id">
            <td>{{ slot.slotOrder || slot.slot_order }}</td>
            <td>{{ slot.name || slot.slot_name }}</td>
            <td>{{ slot.originalQuantity || slot.original_quantity || slot.quantity }}</td>
            <td>{{ slot.balancedQuantity || slot.balanced_quantity || slot.balanced }}</td>
            <td>{{ slot.isActive || slot.is_active ? 'Yes' : 'No' }}</td>
          </tr>
        </tbody>
      </table>

      <div style="margin-top: 20px; padding: 15px; background: #e9ecef; border: 1px solid #ccc;">
        <p><strong>Original Total:</strong> {{ originalTotal }}</p>
        <p><strong>Balanced Total:</strong> {{ balancedTotal }}</p>
        <p><strong>Totals Match:</strong> <span :style="{ color: isTotalValid ? 'green' : 'red' }">{{ isTotalValid ? 'Yes' : 'No' }}</span></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue'
import { getDetail } from '../services/api'

const props = defineProps(['planningId'])
const emit = defineEmits(['go-back'])

const state = ref('loading')
const errorMessage = ref('')
const detail = ref(null)

async function fetchDetail() {
  if (!props.planningId) return
  
  state.value = 'loading'
  try {
    const response = await getDetail(props.planningId)
    detail.value = response.data
    state.value = 'data'
  } catch (error) {
    state.value = 'error'
    errorMessage.value = error.response?.data?.message || error.message || 'Failed to fetch details'
  }
}

onMounted(() => {
  fetchDetail()
})

watch(() => props.planningId, () => {
  fetchDetail()
})

const originalTotal = computed(() => {
  if (!detail.value || !detail.value.slots) return 0
  return detail.value.slots.reduce((sum, item) => {
    const val = item.originalQuantity || item.original_quantity || item.quantity || 0
    return sum + Number(val)
  }, 0)
})

const balancedTotal = computed(() => {
  if (!detail.value || !detail.value.slots) return 0
  return detail.value.slots.reduce((sum, item) => {
    const val = item.balancedQuantity || item.balanced_quantity || item.balanced || 0
    return sum + Number(val)
  }, 0)
})

const isTotalValid = computed(() => {
  return originalTotal.value === balancedTotal.value
})
</script>
