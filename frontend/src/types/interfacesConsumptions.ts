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

export interface ConsumptionLink {
    href: string
    method: string
}

export interface HistoricalConsumption {
    id: number
    developerName: string
    drinkName: string
    caffeineMg: number
    consumedAt: string
    _links: {
        delete: ConsumptionLink
    }
}

export interface ProblemDetails {
    type: string
    title: string
    status: number
    detail: string
}