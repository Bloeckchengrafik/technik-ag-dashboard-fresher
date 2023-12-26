<script lang="ts">
    import type { Shift } from "./fullEvent";
    import type { UserSpec } from "../../api";
    import { createEventDispatcher } from "svelte";

    export let shift: Shift;
    export let user: UserSpec;

    const dispatch = createEventDispatcher();

    let datetimeFormatter = new Intl.DateTimeFormat("de-DE", {
        day: "numeric",
        month: "numeric",
        year: "numeric",
        hour: "numeric",
        minute: "numeric",
        hour12: false,
    });

    function intRange(start: number, end: number) {
        let result = [];
        for (let i = start; i <= end; i++) {
            result.push(i);
        }
        return result;
    }

    let positions = intRange(
        0,
        Math.max(0, shift.needed - 1, shift.users.length - 1)
    );
</script>

<div
    class="flex flex-col border-2 border-gray-500 dark:border-neutral-800 border-opacity-25 rounded shadow"
>
    <div class="flex flex-row justify-between items-center px-4 py-2">
        <div class="flex flex-col">
            <div
                class="text-lg font-semibold text-neutral-800 dark:text-neutral-200"
            >
                {shift.name}
                <div
                    class="text-md text-neutral-700 dark:text-neutral-300 text-sm"
                >
                    {datetimeFormatter.format(
                        new Date(shift.from_time.replace(" ", "T"))
                    )}
                    - {datetimeFormatter.format(
                        new Date(shift.to_time.replace(" ", "T"))
                    )}
                    {#if shift.needed < shift.users.length}
                        - &Uuml;berbesetzt ({shift.users.length - shift.needed})
                    {/if}
                </div>
            </div>
            <div class="text-sm">
                {#each positions as position}
                    <span class="text-neutral-600 dark:text-neutral-400"
                        >{#if shift.users[position]}{shift.users[position]
                                .firstname}
                            {shift.users[position]
                                .lastname}{:else}unbesezt{/if}</span
                    >{#if position < shift.needed - 1}<span
                            class="text-neutral-200 dark:text-neutral-400"
                            >,
                        </span>{/if}
                {/each}
            </div>
        </div>
        <div class="flex flex-row gap-2 items-end">
            {#if user.permission.includes("joinShifts")}
                <span>
                    {#if shift.users.find((u) => u.id === user.id)}
                        <button
                            class="p-1 px-2 bg-tertiary text-white rounded hover:bg-tertiary_highlight flex gap-2 items-center"
                            on:click={() => dispatch("unregister", shift.id)}
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="currentColor"
                                viewBox="0 0 256 256"
                                ><path
                                    d="M256,136a8,8,0,0,1-8,8H200a8,8,0,0,1,0-16h48A8,8,0,0,1,256,136Zm-57.87,58.85a8,8,0,0,1-12.26,10.3C165.75,181.19,138.09,168,108,168s-57.75,13.19-77.87,37.15a8,8,0,0,1-12.25-10.3c14.94-17.78,33.52-30.41,54.17-37.17a68,68,0,1,1,71.9,0C164.6,164.44,183.18,177.07,198.13,194.85ZM108,152a52,52,0,1,0-52-52A52.06,52.06,0,0,0,108,152Z"
                                /></svg
                            >
                            Austragen
                        </button>
                    {:else}
                        <button
                            class="p-1 px-2 bg-primary text-white rounded hover:bg-primary_highlight flex gap-2 items-center"
                            on:click={() => dispatch("register", shift.id)}
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 256 256"
                                width="20"
                                height="20"
                                fill="currentColor"
                                ><rect
                                    width="256"
                                    height="256"
                                    fill="none"
                                /><line
                                    x1="200"
                                    y1="136"
                                    x2="248"
                                    y2="136"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="16"
                                /><line
                                    x1="224"
                                    y1="112"
                                    x2="224"
                                    y2="160"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="16"
                                /><circle
                                    cx="108"
                                    cy="100"
                                    r="60"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-miterlimit="10"
                                    stroke-width="16"
                                /><path
                                    d="M24,200c20.55-24.45,49.56-40,84-40s63.45,15.55,84,40"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="16"
                                /></svg
                            >
                            Eintragen
                        </button>
                    {/if}
                </span>
            {/if}

            {#if user.permission.includes("editShifts")}
                <button
                    class="p-1 px-2 bg-primary text-white rounded hover:bg-primary_highlight block h-full"
                    on:click={() => dispatch("edit", shift.id)}
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 256 256"
                        width="23"
                        height="23"
                    >
                        <rect width="256" height="256" fill="none" />
                        <path
                            d="M92.69,216H48a8,8,0,0,1-8-8V163.31a8,8,0,0,1,2.34-5.65L165.66,34.34a8,8,0,0,1,11.31,0L221.66,79a8,8,0,0,1,0,11.31L98.34,213.66A8,8,0,0,1,92.69,216Z"
                            fill="none"
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="16"
                        />
                        <line
                            x1="136"
                            y1="64"
                            x2="192"
                            y2="120"
                            fill="none"
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="16"
                        />
                        <line
                            x1="164"
                            y1="92"
                            x2="68"
                            y2="188"
                            fill="none"
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="16"
                        />
                        <line
                            x1="95.49"
                            y1="215.49"
                            x2="40.51"
                            y2="160.51"
                            fill="none"
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="16"
                        />
                    </svg>
                </button>
                <button
                    class="p-1 px-2 bg-primary text-white rounded hover:bg-primary_highlight block h-full"
                    on:click={() => dispatch("rm", shift.id)}
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        fill="currentColor"
                        viewBox="0 0 256 256"
                        ><path
                            d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM96,40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8v8H96Zm96,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z"
                        /></svg
                    >
                </button>
            {/if}
        </div>
    </div>
</div>
