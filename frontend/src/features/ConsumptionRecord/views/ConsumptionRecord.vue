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


</style>