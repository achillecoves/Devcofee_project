import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useErrorStore = defineStore('error', () => {
  const errorTitle = ref('')
  const errorMessage = ref('')
  const errorStatus = ref<number | null>(null)

  function setError(title: string, message: string, status: number | null = null) {
    errorTitle.value = title
    errorMessage.value = message
    errorStatus.value = status
  }

  function clearError() {
    errorTitle.value = ''
    errorMessage.value = ''
    errorStatus.value = null
  }

  return { errorTitle, errorMessage, errorStatus, setError, clearError }
})