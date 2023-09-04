import {get, writable} from "svelte/store";

export let backend = import.meta.env.VITE_BACKEND

let apiTokenStr: string | null = null

if (localStorage.getItem('apiToken')) {
    apiTokenStr = localStorage.getItem('apiToken')
}

export let apiToken = writable<string | null>(apiTokenStr)

apiToken.subscribe(value => {
    localStorage.setItem('apiToken', value ? value : '')
})

export function apiGet(endpoint: string, requestInit: RequestInit = {}) {
    let apiTokenStuff = {}
    console.debug('%c🌐 GET', 'color: #00aaff', `${backend}${endpoint}`)
    if (get(apiToken)) {
        apiTokenStuff = {
            headers: {
                Authorization: `Bearer ${get(apiToken)}`
            }
        }
    }
    return fetch(`${backend}${endpoint}`, {
        ...requestInit,
        ...apiTokenStuff
    });
}

export function apiPost(endpoint: string, body: any, requestInit: RequestInit = {
    headers: {
        'Content-Type': 'application/json'
    }
}) {
    console.debug('%c🌐 POST', 'color: #00aaff', `${backend}${endpoint}`, body ? body : '<no body>')
    let apiTokenStuff = {}
    if (get(apiToken)) {
        apiTokenStuff = {
            headers: {
                Authorization: `Bearer ${get(apiToken)}`
            }
        }
    }
    return fetch(`${backend}${endpoint}`, {
        ...requestInit,
        ...apiTokenStuff,
        method: 'POST',
        body: JSON.stringify(body)
    });
}

export async function refreshToken() {
    if (!get(apiToken)) return
    let data = await apiGet("v1/login/jwtrefresh.php").then(r => r.json())
    apiToken.set(data.jwt)
}

export function logout() {
    apiToken.set("")
    window.location.reload()
}

export type Permission =
    "login" |
    "showAsUser" |
    "showAsTechnician" |
    "showAsManager" |
    "showAsAdmin" |
    "denyBooking" |
    "viewAllEvents" |
    "editAllEvents" |
    "editShifts" |
    "joinShifts" |
    "receiveAutomailer" |
    "deactivateEvent" |
    "userAdministration" |
    "equipmentView" |
    "equipmentDelete" |
    "equipmentChange" |
    "viewPresets" |
    "editPresets";

export type UserSpec = {
    id: number
    firstname: string
    lastname: string
    email: string
    groups: string[]
    permission: Permission[]
}

export type EquipmentSpec = {
    id: number
    count: number
    name: string
    category_id: number
    category_name: string
    location_id: number
    location_name: string
    manufacturer_id: number
    manufacturer_name: string
}

export type SortedEquipmentSpec = {
    [key: string]: EquipmentSpec[]
}
