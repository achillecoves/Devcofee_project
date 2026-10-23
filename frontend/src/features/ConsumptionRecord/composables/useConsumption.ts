import { ref } from 'vue';
export interface ConsumptionRequest {
    "drinkId": number | null,
    "developerName": string,
    "consumedAt": string
}

export interface ConsumptionResponse {
    "id": number,
    "drinkId": number,
    "developerName": string,
    "consumedAt": string
}

export function useConsumption() {
    const urlBase = "http://127.0.0.1:5500/api/"
    const consumption = ref<ConsumptionResponse | null>(null);
    const eror = ref<string | null>(null);

    async function createConsumption(consumptionRequest: ConsumptionRequest) {

        try {
            const url = `${urlBase}consumption`
            const response = await fetch(url,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ consumptionRequest })

                })

            if (!response.ok) {
                throw new Error(`${response.status}`)
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
