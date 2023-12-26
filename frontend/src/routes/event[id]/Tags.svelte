<script lang="ts">
    import type {FullEvent} from "./fullEvent";
    import Swal from "sweetalert2";
    import {apiPost} from "../../api";

    export let event: FullEvent;
    export let userCanEdit: boolean;
</script>

<div class="mt-10">
    <h2 class="text-xl">{event.organizer.firstname} {event.organizer.lastname}</h2>
    <h1 class="text-4xl font-bold break-words break-normal flex items-center">
        {event.title} {#if userCanEdit}<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="32" height="32" class="ml-2 cursor-pointer" on:click={async () => {
            let data = await Swal.fire({
                title: "Neuer Name",
                input: "text",
                inputPlaceholder: "Name",
                showCancelButton: true,
                confirmButtonText: "Erstellen",
                cancelButtonText: "Abbrechen",
                showLoaderOnConfirm: true,
            });

            if (data.isDenied) return

            event.title = data.value;
            await apiPost(`v1/event/update.php?id=${event.id}`, {
                title: event.title
            })
        }}><rect width="256" height="256" fill="none"/><path d="M92.69,216H48a8,8,0,0,1-8-8V163.31a8,8,0,0,1,2.34-5.65L165.66,34.34a8,8,0,0,1,11.31,0L221.66,79a8,8,0,0,1,0,11.31L98.34,213.66A8,8,0,0,1,92.69,216Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="136" y1="64" x2="192" y2="120" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="164" y1="92" x2="68" y2="188" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="95.49" y1="215.49" x2="40.51" y2="160.51" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>{/if}
    </h1>
    <div class="mt-2 items-center">
        <div class="p-1 mt-1 px-2 bg-[#00000020] dark:bg-[#ffffff30] w-fit rounded items-center gap-1 inline-flex">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="16" height="16">
                <rect width="256" height="256" fill="none"/>
                <circle cx="128" cy="104" r="32" fill="none" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="12"/>
                <path d="M208,104c0,72-80,128-80,128S48,176,48,104a80,80,0,0,1,160,0Z" fill="none"
                      stroke="currentColor"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="12"/>
            </svg>
            {event.room}
        </div>

        <div class="p-1 mt-1 px-2 bg-[#00000020] dark:bg-[#ffffff30] w-fit rounded inline-flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="16" height="16">
                <rect width="256" height="256" fill="none"/>
                <line x1="128" y1="40" x2="128" y2="128" fill="none" stroke="currentColor" stroke-linecap="round"
                      stroke-linejoin="round" stroke-width="12"/>
                <line x1="48" y1="96" x2="128" y2="128" fill="none" stroke="currentColor" stroke-linecap="round"
                      stroke-linejoin="round" stroke-width="12"/>
                <line x1="72" y1="200" x2="128" y2="128" fill="none" stroke="currentColor" stroke-linecap="round"
                      stroke-linejoin="round" stroke-width="12"/>
                <line x1="184" y1="200" x2="128" y2="128" fill="none" stroke="currentColor" stroke-linecap="round"
                      stroke-linejoin="round" stroke-width="12"/>
                <line x1="208" y1="96" x2="128" y2="128" fill="none" stroke="currentColor" stroke-linecap="round"
                      stroke-linejoin="round" stroke-width="12"/>
            </svg>
            {event.type}
        </div>

        {#if event.needs_consultation}
            <div class="p-1 mt-1 px-2 bg-[#00000020] dark:bg-[#ffffff30] w-fit rounded inline-flexflex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="16" height="16">
                    <rect width="256" height="256" fill="none"/>
                    <path d="M192,120a59.91,59.91,0,0,1,48,24" fill="none" stroke="currentColor"
                          stroke-linecap="round"
                          stroke-linejoin="round" stroke-width="12"/>
                    <path d="M16,144a59.91,59.91,0,0,1,48-24" fill="none" stroke="currentColor"
                          stroke-linecap="round"
                          stroke-linejoin="round" stroke-width="12"/>
                    <circle cx="128" cy="144" r="40" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="12"/>
                    <path d="M72,216a65,65,0,0,1,112,0" fill="none" stroke="currentColor" stroke-linecap="round"
                          stroke-linejoin="round" stroke-width="12"/>
                    <path d="M161,80a32,32,0,1,1,31,40" fill="none" stroke="currentColor" stroke-linecap="round"
                          stroke-linejoin="round" stroke-width="12"/>
                    <path d="M64,120A32,32,0,1,1,95,80" fill="none" stroke="currentColor" stroke-linecap="round"
                          stroke-linejoin="round" stroke-width="12"/>
                </svg>
                Benötigt Beratung
            </div>
        {/if}
    </div>
</div>
