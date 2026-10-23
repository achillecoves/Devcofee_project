import { ref } from 'vue'
import type { HistoricalConsumption, ProblemDetails } from '@/types/interfacesConsumptions'

export function useHistoricalConsumptions() {
    const urlBase = "http://localhost:5500/api"
    const consumptions = ref<HistoricalConsumption[]>([])
    const loading = ref(false)
    const error = ref<ProblemDetails | null>(null)

    async function loadConsumptions(): Promise<void> {
        loading.value = true
        error.value = null

        try {
            const response = await fetch(`${urlBase}/consumptions`)

            if (!response.ok) {
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

    async function deleteConsumption(consumptionId: number): Promise<boolean> {
        error.value = null

        try {
            const response = await fetch(`${urlBase}/consumptions/${consumptionId}`, {
                method: 'DELETE'
            })

            if (!response.ok) {
                const text = await response.text()
                error.value = text
                    ? JSON.parse(text)
                    : {
                        type: 'about:blank',
                        title: response.statusText || 'Erreur',
                        status: response.status,
                        detail: 'La suppression a échoué.'
                    }
                return false
            }

            removeConsumption(consumptionId)
            return true
        }
        catch {
            error.value = {
                type: 'about:blank',
                title: 'Network Error',
                status: 0,
                detail: "Impossible de contacter l'API."
            }
            return false
        }
    }

    function removeConsumption(consumptionId: number): void {
        consumptions.value = consumptions.value.filter(c => c.id !== consumptionId)
    }

    return {
        consumptions,
        loading,
        error,
        loadConsumptions,
        deleteConsumption,
        removeConsumption
    }
}