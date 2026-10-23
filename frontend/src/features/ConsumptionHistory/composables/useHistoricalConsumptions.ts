import { ref } from 'vue'
import type { HistoricalConsumption, ProblemDetails } from '@/types/interfacesConsumptions'

export function useHistoricalConsumptions() {
    const urlBase = "http://localhost:8000/api"
    const consumptions = ref<HistoricalConsumption[]>([])
    const loading = ref(false)
    const error = ref<ProblemDetails | null>(null)

    async function loadConsumptions(): Promise<void> {
        loading.value = true
        error.value = null

        try {
            const response = await fetch(`${urlBase}/consumptions`)

            if (!response.ok) {
                // Réponse d'erreur au format RFC 7807
                error.value = await response.json()
                return
            }

            consumptions.value = await response.json()
        }
        catch {

            error.value = {
                type: 'about:blank',
                title: 'Network Error',
                status: 0,
                detail: "Impossible de contacter l'API."
            }
        }
        finally {
            loading.value = false
        }
    }

    return {
        consumptions,
        loading,
        error,
        loadConsumptions
    }
}