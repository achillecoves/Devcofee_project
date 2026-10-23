import { ref } from 'vue';
import { Drink } from '@/types/interfacesDrinks';
export function useDrinks() {
    const urlBase = "http://127.0.0.1:5500/api/"
    const drinks = ref<Drink[] | null>(null);
    const error = ref<string | null>(null);

    async function getAllDrinks() {

        try {
            const url = `${urlBase}drinks`

            const response = await fetch(url)

            if (!response.ok) {
                throw new Error(`${response.status}`)
            }

            const jsonDrinks: Drink[] = await response.json()

            drinks.value = jsonDrinks
        }
        catch (err) {
            error.value = err instanceof Error ? err.message : "Erreur inconnue"
        }
    }

    return {
        drinks,
        getAllDrinks,
        error
    }
}
