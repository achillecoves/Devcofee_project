<script setup>
import RecordButton from './RecordButton.vue'

const props = defineProps({
  consumption: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['delete'])
</script>

<template>
    <div class="consumption-display">
        <div class="consumption-display__info">
            <p class="consumption-display__name">{{ consumption.developerName }}</p>
            <p class="consumption-display__drink">
                {{ consumption.drinkName }} ({{ consumption.caffeineMg }} mg)
            </p>
            <p class="consumption-display__date">
                {{ new Date(consumption.consumedAt.replace(' ', 'T')).toLocaleString() }}
            </p>
        </div>
        <RecordButton button-value="Supprimer" @click="$emit('delete', consumption)" />
    </div>
</template>

<style scoped>
.consumption-display {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 12px 16px;
  background: #fff;
  border: 1px solid #e3e3e3;
  border-radius: 8px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  transition: box-shadow 0.2s ease;
}

.consumption-display:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.consumption-display__info {
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0; /* permet au texte de se couper au lieu de déborder */
}

.consumption-display__info p {
  margin: 0;
}

.consumption-display__name {
  font-weight: 600;
  font-size: 1rem;
  color: #222;
}

.consumption-display__drink {
  font-size: 0.95rem;
  color: #6f4e37; /* marron café */
}

.consumption-display__date {
  font-size: 0.8rem;
  color: #888;
}

/* Sur petit écran : le bouton passe sous les infos */
@media (max-width: 480px) {
  .consumption-display {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>