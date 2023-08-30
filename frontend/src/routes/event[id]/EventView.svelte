<script lang="ts">
    import type {FullEvent} from "./fullEvent";
    import {backend} from "../../api";
    import type {UserSpec} from "../../api";
    import Header from "./Tags.svelte";
    import Shifts from "./Shifts.svelte";
    import {createEventDispatcher} from "svelte";
    import Chat from "./Chat.svelte";

    export let event: FullEvent
    export let user: UserSpec
    export let pauseReload: boolean = false

    let dispatch = createEventDispatcher()

    let datetimeformat = new Intl.DateTimeFormat('de-DE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: "numeric",
        minute: "numeric"
    });

    function parseDate(date: string): Date {
        return new Date(date.replace(" ", "T"));
    }
</script>

<div class="absolute top-0 -z-50 h-1/2 w-full">
    <div class="relative w-full h-full" style="mask-image: linear-gradient(to bottom, rgba(0,0,0,1), rgba(0,0,0,0));">
        <img src="https://images.unsplash.com/{event.hdr_unsplash_id}" alt=""
             class=" absolute top-0 left-0 w-full h-full object-cover object-center">
        <div class="absolute top-0 left-0 w-full h-full bg-white dark:bg-black opacity-30 dark:opacity-50"></div>
    </div>
</div>

<div class="h-[20vh] lg:h-[25vh]"></div>
<div class="card mx-5">
    <div class="relative h-0">
        <img src="{backend}v1/profile/avatar/generate.php?id={event.organizer_id}" alt="Avatar"
             class="w-24 h-24 rounded transform -translate-y-3/4 absolute top-1/2 lg:left-10">
    </div>

    <div class="flex justify-between lg:px-5 px-0 sm:flex-row flex-col">
        <div class="flex-1 lg:mx-5 mx-0">
            <Header {event}/>
            <div class="h-3"/>
            {event.description}
            <div class="py-5">
                <hr class="w-10 mx-auto opacity-50"/>
            </div>

            <h2 class="text-2xl font-bold mb-1">Aufbau</h2>
            <div class="flex justify-between sm:flex-row flex-col">
                <div class="flex-1">
                    <div class="text-md text-gray-500 dark:text-gray-300">Startet</div>
                    <div class="text-lg">{datetimeformat.format(parseDate(event.construction_from))}</div>
                </div>
                <div class="flex-1">
                    <div class="text-md text-gray-500 dark:text-gray-300">Endet</div>
                    <div class="text-lg">{datetimeformat.format(parseDate(event.construction_to))}</div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mt-5 mb-1">Veranstaltung</h2>
            <div class="flex justify-between sm:flex-row flex-col">
                <div class="flex-1">
                    <div class="text-md text-gray-500 dark:text-gray-300">Von</div>
                    <div class="text-lg">{datetimeformat.format(parseDate(event.from_time))}</div>
                </div>
                <div class="flex-1">
                    <div class="text-md text-gray-500 dark:text-gray-300">Bis</div>
                    <div class="text-lg">{datetimeformat.format(parseDate(event.to_time))}</div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mt-5 mb-1">Abbau</h2>
            <div class="flex justify-between sm:flex-row flex-col">
                <div class="flex-1">
                    <div class="text-md text-gray-500 dark:text-gray-300">Startet</div>
                    <div class="text-lg">{datetimeformat.format(parseDate(event.dismantling_from))}</div>
                </div>
                <div class="flex-1">
                    <div class="text-md text-gray-500 dark:text-gray-300">Endet</div>
                    <div class="text-lg">{datetimeformat.format(parseDate(event.dismantling_to))}</div>
                </div>
            </div>

            {#if event.presets.length !== 0}
                <div class="py-5">
                    <hr class="w-10 mx-auto opacity-50"/>
                </div>

                <h2 class="text-2xl font-bold mt-5 mb-1">Angeforderte Technik</h2>
                <ul class="list-disc list-inside">
                    {#each event.presets as preset}
                        <li>{preset.tech}</li>
                    {/each}
                </ul>
            {/if}
        </div>
        <div class="flex-1 flex flex-col justify-between">
            <Shifts {event} {user} bind:pauseReload on:update={() => dispatch("update")}/>
        </div>
    </div>

    <div class="lg:px-10 mt-5">
        <Chat {event} bind:pauseReload on:update={() => dispatch("update")}/>
    </div>
</div>
