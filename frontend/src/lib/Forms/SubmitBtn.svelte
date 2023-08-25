<script lang="ts">
    import {createEventDispatcher} from "svelte";

    export let name = 'Senden';
    export let disabled = false;
    export let spinner = false;
    export let href = '';

    const dispatch = createEventDispatcher();
</script>

<button
        type="{href ? 'button' : 'submit'}"
        class="inline-block w-full rounded bg-primary px-7 pb-2.5 pt-3 text-sm font-medium uppercase leading-normal
               text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600
               hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600
               focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none
               focus:ring-0 active:bg-primary-700
               active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]
               dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)]
               dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]
               dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]
               dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] relative
               {disabled||spinner? 'opacity-50 cursor-not-allowed' : ''}"
        disabled={disabled||spinner}
        data-te-ripple-init
        data-te-ripple-color="light"
        on:click={() => {
            if (href) {
                window.location.href = href;
            } else {
                dispatch('click');
            }
        }}
>
    {#if spinner}
        <span>&nbsp;</span>
        <!--- Loading spinner -->
        <div class="absolute inset-0 flex items-center justify-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                 xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0
                  12h4zm2 5.291A7.962 7.962 0 014 12H0c0
                  3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    {:else}
        {name}
    {/if}
</button>