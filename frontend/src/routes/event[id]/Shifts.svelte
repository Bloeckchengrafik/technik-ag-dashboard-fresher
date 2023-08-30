<script lang="ts">
    import type {FullEvent, Shift as ShiftSpec} from "./fullEvent";
    import Shift from "./Shift.svelte";
    import type {UserSpec} from "../../api";
    import {createEventDispatcher} from "svelte";
    import {apiPost} from "../../api";
    import Modal from "../../lib/Modal.svelte";
    import TextInput from "../../lib/Forms/TextInput.svelte";
    import DateTimeInput from "../../lib/Forms/DateTimeInput.svelte";
    import SubmitBtn from "../../lib/Forms/SubmitBtn.svelte";
    import {number} from "zod";

    export let event: FullEvent;
    export let user: UserSpec;

    export let pauseReload: boolean = false;

    const dispatch = createEventDispatcher();
    let showEditModal = false;
    $: pauseReload = showEditModal;

    let shiftToEdit: ShiftSpec | null = null;

    let currentShiftName: string = "";
    let currentShiftStart: string = "";
    let currentShiftEnd: string = "";
    let currentShiftMaxParticipants: string = "";

    function initShiftData() {
        if (shiftToEdit == null) {
            currentShiftName = "";
            currentShiftStart = "";
            currentShiftEnd = "";
            currentShiftMaxParticipants = "";
        } else {
            currentShiftName = shiftToEdit.name;
            currentShiftStart = shiftToEdit.from_time;
            currentShiftEnd = shiftToEdit.to_time;
            currentShiftMaxParticipants = shiftToEdit.needed.toString();
        }
    }
</script>

<div class="flex gap-2 h-full lg:mx-5 sm:pt-5 flex-col">
    <h2 class="text-2xl font-bold sm:text-3xl">
        Schichten
    </h2>
    {#each event.shifts as shift}
        <Shift
                {shift}
                {user}
                on:register={async () => {
                    await apiPost("v1/event/shift/join.php?id=" + shift.id, {})
                    dispatch("update");
                }}
                on:unregister={async () => {
                    await apiPost("v1/event/shift/leave.php?id=" + shift.id, {})
                    dispatch("update");
                }}
                on:rm={async () => {
                    await apiPost("v1/event/shift/remove.php?id=" + shift.id, {})
                    dispatch("update");
                }}
                on:edit={() => {
                    shiftToEdit = shift;
                    initShiftData();
                    showEditModal = true;
                }}
        />
    {/each}

    {#if event.shifts.length === 0}
        <div class="flex flex-col items-center justify-center h-full">
            <p class="text-xl text-gray-500 dark:text-neutral-200">
                Es sind noch keine Schichten vorhanden.
            </p>
            <p class="text-xl text-gray-500 dark:text-neutral-200">
                Füge eine Schicht hinzu, um die Anmeldung zu öffnen.
            </p>
        </div>
    {:else }
        <div class="h-full"></div>
    {/if}

    <div class="flex justify-end mt-3 gap-2">
        {#if user.permission.includes("deactivateEvent")}
            {#if event.disabled}
                <button class="p-1 px-2 bg-primary text-white rounded hover:bg-primary_highlight flex gap-1 items-center"
                        on:click={async () => {
                        await apiPost("v1/event/activate.php?id="+event.id, {})
                        dispatch("update");
                    }}>
                    Aktivieren
                </button>
            {:else}
                <button class="p-1 px-2 bg-primary text-white rounded hover:bg-primary_highlight flex gap-1 items-center"
                        on:click={async () => {
                        await apiPost("v1/event/deactivate.php?id="+event.id, {})
                        dispatch("update");
                    }}>
                    Deaktivieren
                </button>
            {/if}
        {/if}
        {#if user.permission.includes("editShifts")}
            <button class="p-1 px-2 bg-primary text-white rounded hover:bg-primary_highlight flex gap-1 items-center"
                    on:click={() => {
                        showEditModal = true;
                        shiftToEdit = null;
                    }}>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="20" height="20">
                    <rect width="256" height="256" fill="none"/>
                    <line x1="40" y1="128" x2="216" y2="128" fill="none" stroke="currentColor" stroke-linecap="round"
                          stroke-linejoin="round" stroke-width="12"/>
                    <line x1="128" y1="40" x2="128" y2="216" fill="none" stroke="currentColor" stroke-linecap="round"
                          stroke-linejoin="round" stroke-width="12"/>
                </svg>
                Schicht hinzufügen
            </button>
        {/if}
    </div>

    {#if showEditModal}
        <Modal on:close={() => showEditModal = false}>
            <h1 class="text-2xl font-bold sm:text-3xl mb-2">
                {#if shiftToEdit === null}Neue Schicht{:else}Schicht Bearbeiten{/if}
            </h1>
            <p class="text-gray-500 dark:text-neutral-200 mb-2 min-w-[300px]">
                {#if shiftToEdit === null}Füge eine neue Schicht hinzu.
                {:else}Bearbeite die Schicht "{shiftToEdit.name}".
                {/if}
            </p>

            <TextInput
                    type="text"
                    name="Name"
                    id="name"
                    bind:value={currentShiftName}
            ></TextInput>
            <DateTimeInput
                    name="Start"
                    id="start"
                    bind:value={currentShiftStart}
            ></DateTimeInput>
            <DateTimeInput
                    name="Ende"
                    id="end"
                    bind:value={currentShiftEnd}
            ></DateTimeInput>
            <TextInput
                    type="number"
                    name="Benötigte Helfer"
                    id="maxParticipants"
                    bind:value={currentShiftMaxParticipants}
            ></TextInput>

            <SubmitBtn
                    disabled={currentShiftName === "" || currentShiftStart === "" || currentShiftEnd === "" || currentShiftMaxParticipants === ""}
                    on:click={async () => {
                        // close modal
                        showEditModal = false;

                        if (shiftToEdit == null) {
                            await apiPost("v1/event/shift/add.php", {
                                event_id: event.id,
                                name: currentShiftName,
                                from_time: currentShiftStart,
                                to_time: currentShiftEnd,
                                needed: parseInt(currentShiftMaxParticipants)
                            })
                        } else {
                            await apiPost("v1/event/shift/update.php", {
                                shift_id: shiftToEdit.id,
                                name: currentShiftName,
                                from_time: currentShiftStart,
                                to_time: currentShiftEnd,
                                needed: parseInt(currentShiftMaxParticipants)
                            })
                        }

                        dispatch("update");
                    }}
                    name="Speichern"
            ></SubmitBtn>
        </Modal>
    {/if}
</div>
