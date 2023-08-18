import {get, writable} from "svelte/store";

let backend = import.meta.env.VITE_BACKEND
let apiToken = writable<string | null>('apiToken' in localStorage ? localStorage.apiToken : null)
apiToken.subscribe(value => {
    localStorage.apiToken = value
})

export function apiGet(endpoint: string, requestInit: RequestInit = {}) {
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
        ...apiTokenStuff
    });
}
