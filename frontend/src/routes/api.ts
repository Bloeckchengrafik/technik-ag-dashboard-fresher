import {get, writable} from "svelte/store";

let backend = import.meta.env.VITE_BACKEND
export let apiToken = writable<string | null>('apiToken' in localStorage ? localStorage.apiToken : null)
apiToken.subscribe(value => {
    localStorage.apiToken = value
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
