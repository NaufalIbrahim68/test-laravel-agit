<template>
  <div class="app-container">
    <div class="tabs">
      <button :class="{ active: activeTab === 'form' }" @click="setTab('form')">Create Planning</button>
      <button :class="{ active: activeTab === 'history' }" @click="setTab('history')">History</button>
    </div>

    <div class="tab-content">
      <PlanningForm v-if="activeTab === 'form'" @planning-created="handlePlanningCreated" />
      <PlanningHistory v-if="activeTab === 'history'" :refresh-trigger="refreshCounter" @view-detail="handleViewDetail" @go-back="setTab('form')" />
      <PlanningDetail v-if="activeTab === 'detail'" :planning-id="selectedPlanningId" @go-back="setTab('history')" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import PlanningForm from './components/PlanningForm.vue'
import PlanningHistory from './components/PlanningHistory.vue'
import PlanningDetail from './components/PlanningDetail.vue'

const activeTab = ref('form')
const refreshCounter = ref(0)
const selectedPlanningId = ref(null)

function goBack() {
  window.history.back()
}

function setTab(tab) {
  activeTab.value = tab
}

function handlePlanningCreated() {
  refreshCounter.value++
  setTab('history')
}

function handleViewDetail(id) {
  selectedPlanningId.value = id
  setTab('detail')
}
</script>

<style>
body {
  font-family: sans-serif;
  margin: 0;
  padding: 20px;
  background-color: #f5f5f5;
}
.app-container {
  max-width: 100%;
  margin: 0 auto;
  background: white;
  padding: 20px;
  border-radius: 4px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.tabs {
  margin-bottom: 20px;
  border-bottom: 1px solid #ccc;
  padding-bottom: 10px;
}
.tabs button {
  padding: 10px 20px;
  margin-right: 10px;
  cursor: pointer;
  border: 1px solid #ccc;
  background: #eee;
}
.tabs button.active {
  background: #007bff;
  color: white;
  border-color: #007bff;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}
th, td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: left;
}
th {
  background-color: #f9f9f9;
}
.btn {
  padding: 8px 16px;
  cursor: pointer;
  background: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
}
.btn-danger {
  background: #dc3545;
}
.btn-secondary {
  background: #6c757d;
}
.form-group {
  margin-bottom: 15px;
}
.form-group label {
  display: block;
  margin-bottom: 5px;
}
.form-group input {
  width: 100%;
  padding: 8px;
  box-sizing: border-box;
}
.mt-3 {
  margin-top: 15px;
}
.alert {
  padding: 10px;
  margin-bottom: 15px;
  border-radius: 4px;
}
.alert-success {
  background-color: #d4edda;
  color: #155724;
}
.alert-error {
  background-color: #f8d7da;
  color: #721c24;
}
.d-flex {
  display: flex;
  gap: 10px;
}
</style>
