import { ref } from 'vue';
import type { ConsumptionRequest, ConsumptionResponse } from '@/types/interfacesConsumptions';
import { useErrorStore } from '@/stores/error';

export function useConsumption() {
    const urlBase = "http://127.0.0.1:5500/api/"
    const consumption = ref<ConsumptionResponse | null>(null);
    const eror = ref<string | null>(null);
    
    const errorStore = useErrorStore();

    async function createConsumption(consumptionRequest: ConsumptionRequest) {

        errorStore.clearError()

        try {
            const url = `${urlBase}consumptions`
            const response = await fetch(url,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(consumptionRequest)

                })

            if (!response.ok) {
                const problem = await response.json()
                errorStore.setError(problem.title, problem.detail, problem.status)
                return
            }

            const jsonConsumption: ConsumptionResponse = await response.json()

            consumption.value = jsonConsumption
        }
        catch (err) {
            eror.value = err instanceof Error ? err.message : "Erreur inconnue"
        }
    }

    return {
        consumption,
        createConsumption,
        eror
    }
}
