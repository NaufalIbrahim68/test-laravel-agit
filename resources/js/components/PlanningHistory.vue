<template>
  <div>
    <h2>Planning History</h2>

    <div v-if="state === 'loading'" class="alert alert-success">
      Loading...
    </div>

    <div v-if="state === 'error'" class="alert alert-error">
      {{ errorMessage }}
    </div>

    <div v-if="state === 'empty'" class="alert alert-secondary">
      No planning history found
    </div>

    <div v-if="state === 'data'">
      <table>
        <thead>
          <tr>
            <th>Request Code</th>
            <th>Created At</th>
            <th>Status</th>
            <th>Total</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="item.id">
            <td>{{ item.requestCode || item.request_code }}</td>
            <td>{{ item.createdAt || item.created_at }}</td>
            <td>{{ item.status }}</td>
            <td>{{ item.totalQuantity || item.total_quantity }}</td>
            <td>
              <button class="btn btn-secondary" @click="emit('view-detail', item.id)">View</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="d-flex mt-3">
        <button class="btn btn-secondary" :disabled="page <= 1" @click="fetchData(page - 1)">Previous</button>
        <span style="padding: 8px;">Page {{ page }} of {{ lastPage }} (Total: {{ total }})</span>
        <button class="btn btn-secondary" :disabled="page >= lastPage" @click="fetchData(page + 1)">Next</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { getHistory } from '../services/api'

const props = defineProps(['refreshTrigger'])
const emit = defineEmits(['view-detail', 'go-back'])

const state = ref('loading')
const errorMessage = ref('')
const items = ref([])
const page = ref(1)
const lastPage = ref(1)
const total = ref(0)
const perPage = ref(10)

async function fetchData(targetPage = 1) {
  state.value = 'loading'
  try {
    const response = await getHistory(targetPage, perPage.value)
    items.value = response.data.data
    page.value = response.data.meta.page || response.data.meta.current_page || 1
    lastPage.value = response.data.meta.last_page || 1
    total.value = response.data.meta.total || 0
    state.value = items.value.length > 0 ? 'data' : 'empty'
  } catch (error) {
    state.value = 'error'
    errorMessage.value = error.response?.data?.message || error.message || 'Failed to fetch history'
  }
}

onMounted(() => {
  fetchData(page.value)
})

watch(() => props.refreshTrigger, () => {
  fetchData(1)
})
</script>
