<script lang="ts">
    import {link} from "svelte-spa-router";

    export let events: {
        id: number,
        title: string,
        description: string,
        from_time: string,
        sumShiftNeeded: number,
        sumUserShift: number
    }[];

    let datetimeFormatter = new Intl.DateTimeFormat('de-DE', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric'
    });
    let sorted = events.sort((a, b) => b.id - a.id);

    let firstEventInPast = sorted.find(event => new Date(event.from_time.replace(" ", "T")).getTime() < new Date().getTime());
</script>

{#if firstEventInPast === undefined}
    <div class="text-center text-gray-700 dark:text-gray-300 mt-4">
        <span class="text-xl font-bold">Keine vergangenen Events</span>
    </div>
{/if}

{#if firstEventInPast !== undefined && firstEventInPast.id !== sorted[0].id}
    <div class="text-center text-gray-700 dark:text-gray-300 mt-4">
        <span class="text-xl font-bold">Zukünftige Events</span>
    </div>
{/if}

{#if firstEventInPast !== undefined && firstEventInPast.id === sorted[0].id}
    <div class="text-center text-gray-700 dark:text-gray-300 mt-4">
        <span class="text-xl">Es gibt keine aktuellen Events</span>
    </div>
{/if}

{#each sorted as event}

    {#if firstEventInPast.id === event.id}
        <div class="text-center text-gray-700 dark:text-gray-300 mt-4">
            <span class="text-xl font-bold">Vergangene Events</span>
        </div>
    {/if}

    <div class="bg-light_fill dark:bg-dark_fill rounded shadow-lg p-5 mt-4 flex flex-row justify-between items-center">
        <div class="flex flex-col gap-1">
            <span class="text-xl font-bold">{event.title}&nbsp; {#if event.sumShiftNeeded > 0}<span class="text-sm font-normal">({event.sumUserShift}/{event.sumShiftNeeded} Techniker eingetragen)</span>{/if}</span>
            <span class="text-sm text-gray-500 dark:text-gray-300">Startet am {datetimeFormatter.format(new Date(event.from_time.replace(" ", "T")))}</span>
        </div>
        <button class="mt-2 bg-primary hover:bg-primary_highlight text-white py-1 px-4 rounded">
            <a href="/event/{event.id}" use:link class=" flex flex-row gap-2 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#fff" viewBox="0 0 256 256">
                    <path d="M221.66,133.66l-72,72a8,8,0,0,1-11.32-11.32L196.69,136H40a8,8,0,0,1,0-16H196.69L138.34,61.66a8,8,0,0,1,11.32-11.32l72,72A8,8,0,0,1,221.66,133.66Z"></path>
                </svg>
            </a>
        </button>
    </div>
{/each}