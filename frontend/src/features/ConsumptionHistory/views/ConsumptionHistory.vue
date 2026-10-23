<script setup lang="ts">
import { onMounted } from 'vue'
import { useHistoricalConsumptions } from '../composables/useHistoricalConsumptions'
import ConsumptionDisplay from '@/_shared/components/ConsumptionDisplay.vue'

const { consumptions, loadConsumptions, deleteConsumption } = useHistoricalConsumptions()

onMounted(() => {
  loadConsumptions()
})
</script>

<template>  
    <ul>
        <li v-for="consumption in consumptions" :key="consumption.id">
            <ConsumptionDisplay :consumption="consumption" @delete="deleteConsumption(consumption.id)" />
        </li>
    </ul>
</template>

<style scoped>
.history {
  max-width: 640px;
  margin: 0 auto;
  padding: 24px 16px;
}

.history__list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.history__item {
  margin: 0;
}

@media (max-width: 480px) {
  .history {
    padding: 16px 12px;
  }
}
</style>