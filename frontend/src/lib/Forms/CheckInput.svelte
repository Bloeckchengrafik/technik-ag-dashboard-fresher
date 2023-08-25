<script lang="ts">
    import {createEventDispatcher} from "svelte";

    export let name: string;
    export let id: string;
    export let value: boolean | null = null;

    const dispatch = createEventDispatcher();
</script>

<div class="relative mb-1 w-full">
    <input
            class="checkbox-input absolute top-0 left-0 w-full h-full opacity-0 z-10"
            id={id}
            bind:checked={value}
            data-te-input-state-
            type="checkbox"
            on:change={() => dispatch('change', {value: value})}
    />
    <label for={id} class="checkbox-label relative flex items-center cursor-pointer">
        {#if value}
            <div class="checkbox-icon flex items-center justify-center w-4 h-4 mr-2 border border-dark dark:border-amber-50 rounded">
                <svg class="fill-current w-3 h-3 text-teal-500 pointer-events-none" viewBox="0 0 20 20">
                    <path d="M0 11l2-2 5 5L18 3l2 2L7 18z"/>
                </svg>
            </div>
        {:else }
            <div class="checkbox-icon flex items-center justify-center w-4 h-4 mr-2 border border-gray-400 rounded">
                <svg class="fill-current hidden w-3 h-3 text-teal-500 pointer-events-none" viewBox="0 0 20 20">
                    <path d="M0 11l2-2 5 5L18 3l2 2L7 18z"/>
                </svg>
            </div>
        {/if}
        <span class="select-none">{name} </span>
    </label>
</div>
