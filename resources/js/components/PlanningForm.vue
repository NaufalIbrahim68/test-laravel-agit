<template>
  <div>
    <h2>Create Planning</h2>
    
    <div v-if="state === 'loading'" class="alert alert-success">
      Processing...
    </div>

    <div v-if="state === 'error'" class="alert alert-error">
      {{ errorMessage }}
    </div>

    <form v-if="state !== 'success'" @submit.prevent="submitForm">
      <div class="form-group">
        <label>Request Code</label>
        <input type="text" v-model="form.requestCode" required />
      </div>
      <div class="form-group">
        <label>Candidate Token</label>
        <input type="text" v-model="form.candidateToken" required />
      </div>

      <h3>Slots</h3>
      <div v-for="(slot, index) in form.slots" :key="index" class="d-flex form-group">
        <input type="text" v-model="slot.name" placeholder="Slot Name" required />
        <input type="number" v-model="slot.quantity" placeholder="Quantity" required min="1" />
      </div>

      <div class="d-flex mt-3">
        <button type="button" class="btn btn-secondary" @click="addSlot">Add Slot</button>
        <button type="button" class="btn btn-danger" @click="removeSlot" :disabled="form.slots.length <= 1">Remove Last Slot</button>
      </div>

      <div class="mt-3">
        <button type="submit" class="btn" :disabled="state === 'loading'">Submit Planning</button>
      </div>
    </form>

    <div v-if="state === 'success'">
      <div class="alert alert-success">Planning created successfully!</div>
      
      <table>
        <thead>
          <tr>
            <th>Slot Name</th>
            <th>Original</th>
            <th>Balanced</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="slot in resultData" :key="slot.name">
            <td>{{ slot.name }}</td>
            <td>{{ slot.original }}</td>
            <td>{{ slot.balanced }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th>Totals</th>
            <th>{{ originalTotal }}</th>
            <th>{{ balancedTotal }}</th>
          </tr>
        </tfoot>
      </table>

      <button class="btn mt-3" @click="resetForm">Create Another</button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { createPlanning } from '../services/api'

const emit = defineEmits(['planning-created'])

const state = ref('idle')
const errorMessage = ref('')
const resultData = ref([])

const form = reactive({
  requestCode: '',
  candidateToken: 'VEH-TEST1234',
  slots: [
    { name: 'Slot 1', quantity: 10 },
    { name: 'Slot 2', quantity: 20 },
    { name: 'Slot 3', quantity: 30 }
  ]
})

function addSlot() {
  const nextNumber = form.slots.length + 1
  form.slots.push({ name: `Slot ${nextNumber}`, quantity: 10 })
}

function removeSlot() {
  if (form.slots.length > 1) {
    form.slots.pop()
  }
}

async function submitForm() {
  state.value = 'loading'
  errorMessage.value = ''
  try {
    const response = await createPlanning(form)
    if (response.data && response.data.slots) {
      resultData.value = response.data.slots.map(s => ({
        name: s.name,
        original: s.originalQuantity || s.quantity,
        balanced: s.balancedQuantity || s.balanced_quantity || s.quantity
      }))
    }
    state.value = 'success'
    emit('planning-created')
  } catch (error) {
    state.value = 'error'
    errorMessage.value = error.response?.data?.message || error.message || 'An error occurred'
  }
}

function resetForm() {
  state.value = 'idle'
  form.requestCode = ''
  form.slots = [
    { name: 'Slot 1', quantity: 10 },
    { name: 'Slot 2', quantity: 20 },
    { name: 'Slot 3', quantity: 30 }
  ]
}

const originalTotal = computed(() => resultData.value.reduce((sum, item) => sum + Number(item.original), 0))
const balancedTotal = computed(() => resultData.value.reduce((sum, item) => sum + Number(item.balanced), 0))
</script>
