<script lang="ts">
    import type { FullEvent } from "./fullEvent";
    import {apiPost, backend} from "../../api";
    import type { UserSpec } from "../../api";
    import Header from "./Tags.svelte";
    import Shifts from "./Shifts.svelte";
    import { createEventDispatcher } from "svelte";
    import Chat from "./Chat.svelte";
    import Swal from "sweetalert2";

    export let event: FullEvent;
    export let user: UserSpec;
    export let pauseReload: boolean = false;
    let userCanEdit = user.permission.includes("editAllEvents") || event.organizer_id === user.id;

    let dispatch = createEventDispatcher();

    let datetimeformat = new Intl.DateTimeFormat("de-DE", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "numeric",
        minute: "numeric",
    });

    function parseDate(date: string): Date {
        return new Date(date.replace(" ", "T"));
    }

    function time_is_null(time: string): boolean {
        return time === "0000-00-00 00:00:00";
    }
</script>

<div class="absolute top-0 -z-50 h-1/2 w-full">
    <div
        class="relative w-full h-full"
        style="mask-image: linear-gradient(to bottom, rgba(0,0,0,1), rgba(0,0,0,0));"
    >
        <img
            src="https://images.unsplash.com/{event.hdr_unsplash_id}"
            alt=""
            class=" absolute top-0 left-0 w-full h-full object-cover object-center"
        />
        <div
            class="absolute top-0 left-0 w-full h-full bg-white dark:bg-black opacity-30 dark:opacity-50"
        />
    </div>
</div>

<div class="h-[20vh] lg:h-[25vh]" />
<div class="card mx-5">
    <div class="relative h-0">
        <img
            src="{backend}v1/profile/avatar/generate.php?id={event.organizer_id}"
            alt="Avatar"
            class="w-24 h-24 rounded transform -translate-y-3/4 absolute top-1/2 lg:left-10"
        />
    </div>

    <div class="flex justify-between lg:px-5 px-0 sm:flex-row flex-col">
        <div class="flex-1 lg:mx-5 mx-0">
            <Header {event} {userCanEdit} />
            <div class="h-3 flex" />
            <p>{event.description} {#if userCanEdit}<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="12" height="12" class="mt-2 cursor-pointer" on:click={async () => {
                let data = await Swal.fire({
                    title: "Neue Beschreibung",
                    input: "text",
                    inputPlaceholder: "Beschreibung",
                    inputValue: event.description,
                    showCancelButton: true,
                    confirmButtonText: "Erstellen",
                    cancelButtonText: "Abbrechen",
                    showLoaderOnConfirm: true,
                });

                if (data.isDenied) return

                event.description = data.value;
                await apiPost(`v1/event/update.php?id=${event.id}`, {
                    description: event.description
                })
            }}><rect width="256" height="256" fill="none"/><path d="M92.69,216H48a8,8,0,0,1-8-8V163.31a8,8,0,0,1,2.34-5.65L165.66,34.34a8,8,0,0,1,11.31,0L221.66,79a8,8,0,0,1,0,11.31L98.34,213.66A8,8,0,0,1,92.69,216Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="136" y1="64" x2="192" y2="120" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="164" y1="92" x2="68" y2="188" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="95.49" y1="215.49" x2="40.51" y2="160.51" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>{/if}
            </p>
            <div class="py-5">
                <hr class="w-10 mx-auto opacity-50" />
            </div>

            {#if !time_is_null(event.construction_from) && !time_is_null(event.construction_to)}
                <h2 class="text-2xl font-bold mb-1">Aufbau</h2>
                <div class="flex justify-between sm:flex-row flex-col">
                    <div class="flex-1">
                        <div class="text-md text-gray-500 dark:text-gray-300">
                            Startet
                        </div>
                        <div class="text-lg">
                            {datetimeformat.format(
                                parseDate(event.construction_from)
                            )}
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-md text-gray-500 dark:text-gray-300">
                            Endet
                        </div>
                        <div class="text-lg">
                            {datetimeformat.format(
                                parseDate(event.construction_to)
                            )}
                        </div>
                    </div>
                </div>
            {/if}

            <h2 class="text-2xl font-bold mt-5 mb-1">Veranstaltung</h2>
            <div class="flex justify-between sm:flex-row flex-col">
                <div class="flex-1">
                    <div class="text-md text-gray-500 dark:text-gray-300">
                        Von
                    </div>
                    <div class="text-lg">
                        {datetimeformat.format(parseDate(event.from_time))}
                    </div>
                </div>
                <div class="flex-1">
                    <div class="text-md text-gray-500 dark:text-gray-300">
                        Bis
                    </div>
                    <div class="text-lg">
                        {datetimeformat.format(parseDate(event.to_time))}
                    </div>
                </div>
            </div>

            {#if !time_is_null(event.dismantling_from) && !time_is_null(event.dismantling_to)}
                <h2 class="text-2xl font-bold mt-5 mb-1">Abbau</h2>
                <div class="flex justify-between sm:flex-row flex-col">
                    <div class="flex-1">
                        <div class="text-md text-gray-500 dark:text-gray-300">
                            Startet
                        </div>
                        <div class="text-lg">
                            {datetimeformat.format(
                                parseDate(event.dismantling_from)
                            )}
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-md text-gray-500 dark:text-gray-300">
                            Endet
                        </div>
                        <div class="text-lg">
                            {datetimeformat.format(
                                parseDate(event.dismantling_to)
                            )}
                        </div>
                    </div>
                </div>
            {/if}

            {#if event.presets.length !== 0}
                <div class="py-5">
                    <hr class="w-10 mx-auto opacity-50" />
                </div>

                <h2 class="text-2xl font-bold mt-5 mb-1">
                    Angeforderte Technik
                </h2>
                <ul class="list-disc list-inside">
                    {#each event.presets as preset}
                        <li>{preset.tech}</li>
                    {/each}
                </ul>
            {/if}
        </div>
        <div class="flex-1 flex flex-col justify-between">
            <Shifts
                {event}
                {user}
                bind:pauseReload
                on:update={() => dispatch("update")}
            />
        </div>
    </div>

    <div class="lg:px-10 mt-5">
        <Chat {event} bind:pauseReload on:update={() => dispatch("update")} />
    </div>
</div>
