<script setup lang="ts">
const { buttonValue = 'Valider' } = defineProps<{
  buttonValue: string 
}>()

</script>

<template>
    <button type="submit" class="submitButton">{{buttonValue}}</button>
</template>

<style scoped>
    .submitButton{
        background-color: var(--color-danger);
        color:var(--color-surface);
        display:flex;
        justify-content: center;
        align-items: center;
        padding: 5px 10px;
        border-radius: 10px;
        border: none;
        font-size:16px;
    }
</style>