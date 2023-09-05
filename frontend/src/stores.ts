import {writable} from "svelte/store";
import type {Writable} from "svelte/store";

export let currentTab: Writable<
    "home" |
    "dash" |
    "equipment" |
    "presets" |
    "booking" |
    "settings" |
    "login" |
    "register" |
    "status" |
    "quiz"
> = writable('home');