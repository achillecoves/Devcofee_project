<script setup lang="ts">
import {ref,onMounted,useTemplateRef} from "vue"
import RecordButton from '@/_shared/components/RecordButton.vue';
import { useDrinks } from '../composables/useDrink';
import { useConsumption } from "../composables/useConsumption";

const {drinks,getAllDrinks,error}=useDrinks();
const {consumption,createConsumption,eror}=useConsumption();
const selectedDrinkId=ref<number|null>(null)
const developerName= ref("");


onMounted(()=>{
    getAllDrinks()
})

function envoyer(){

    if(!selectedDrinkId.value || !developerName.value){
        console.warn("Vous devez renseigner l'ensemble des champs du formulaire")
    }

    const consumptionRequest = {
    drinkId: selectedDrinkId.value,
    developerName: developerName.value,
    consumedAt: new Date().toISOString() // Format standard ISO pour les API
    }

    createConsumption(consumptionRequest)
    
}

</script>

<template>
    <form class="consumption-form" @submit.prevent="envoyer">
    <div class="consumption-form__field">
    <label class="consumption-form__label">Nom</label>
    <input type="text" v-model="developerName" placeholder="Ex:Jean Dupont" class="consumption-form__input"/>
    </div>
    <div class="consumption-form__field">
    <label class="consumption-form__label">Boisson choisie</label>
    <div class="consumption-form__select-wrapper">
    <select v-model="selectedDrinkId" class="consumption-form__select">
        <option :value="null" disabled>Sélectionne une boisson...</option>
        <option v-for="drink in drinks" :key="drink.id" :value="drink.id">{{drink.name}}</option>
    </select>
    </div>
    </div>
    <div class="consumption-form__action">
    <RecordButton button-value="Commander"/> 
    </div>
    </form>
</template>

<style scoped>
.consumption-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
  width: 100%;
  max-width: 380px;
  margin: 2rem auto;
  padding: 24px;
  background-color: var(--color-surface, #ffffff);
  border: 1px solid var(--color-border, #e2e8f0);
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  box-sizing: border-box;
}

/* Groupes de champs */
.consumption-form__field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

/* Libellés */
.consumption-form__label {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--color-text, #1e293b);
}

/* Champ texte */
.consumption-form__input {
  width: 100%;
  padding: 10px 14px;
  font-size: 0.95rem;
  font-family: inherit;
  color: var(--color-text, #0f172a);
  background-color: var(--color-background, #f8fafc);
  border: 1px solid var(--color-border, #cbd5e1);
  border-radius: 8px;
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
}

.consumption-form__input:focus {
  background-color: #ffffff;
  border-color: var(--color-primary, #3b82f6);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

.consumption-form__input::placeholder {
  color: #94a3b8;
}

/* Wrapper pour styliser la flèche du select */
.consumption-form__select-wrapper {
  position: relative;
  width: 100%;
}

/* Liste déroulante */
.consumption-form__select {
  width: 100%;
  padding: 10px 36px 10px 14px;
  font-size: 0.95rem;
  font-family: inherit;
  color: var(--color-text, #0f172a);
  background-color: var(--color-background, #f8fafc);
  border: 1px solid var(--color-border, #cbd5e1);
  border-radius: 8px;
  outline: none;
  box-sizing: border-box;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  cursor: pointer;
  transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
}

.consumption-form__select:focus {
  background-color: #ffffff;
  border-color: var(--color-primary, #3b82f6);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

/* Chevron personnalisé pour le select */
.consumption-form__select-wrapper::after {
  content: "";
  position: absolute;
  top: 50%;
  right: 14px;
  width: 8px;
  height: 5px;
  transform: translateY(-50%);
  background-color: #64748b;
  clip-path: polygon(100% 0%, 0 0%, 50% 100%);
  pointer-events: none;
}

/* Zone d'action et bouton */
.consumption-form__action {
  margin-top: 8px;
}

.consumption-form__action :deep(button) {
  width: 100%;
  height: 42px;
}

</style>