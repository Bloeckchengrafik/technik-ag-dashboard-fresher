<script lang="ts">
    import type {EquipmentSpec, UserSpec} from "../../api";
    import {createEventDispatcher} from "svelte";
    import {apiGet} from "../../api";
    import EquipmentEditModal from "./EquipmentEditModal.svelte";

    export let equipment: EquipmentSpec;
    export let user: UserSpec;

    let dispatch = createEventDispatcher();
    let showEditModal = false;
</script>

<div class="bg-light_fill dark:bg-dark_fill p-4 rounded-lg shadow-lg mt-2">
    <h1 class="text-3xl">{equipment.count}x <b>{equipment.name}</b></h1>
    <div class="mt-2 flex flex-row">
        <p class="grid grid-cols-2 w-fit">
            <span class="pr-2">Kategorie:</span> <b>{equipment.category_name}</b>
            <span class="pr-2">Hersteller:</span> <b>{equipment.manufacturer_name}</b>
            <span class="pr-2">Lager:</span> <b>{equipment.location_name}</b>
        </p>
        <p class="flex sm:flex-row flex-col ml-auto mt-2 sm:mt-0">
            {#if user.permission.includes("equipmentChange")}
                <button
                        on:click={() => showEditModal = true}
                        class="rounded-lg bg-primary hover:bg-primary_highlight h-min text-white px-4 py-2 m-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                         viewBox="0 0 256 256">
                        <path d="M227.31,73.37,182.63,28.68a16,16,0,0,0-22.63,0L36.69,152A15.86,15.86,0,0,0,32,163.31V208a16,16,0,0,0,16,16H92.69A15.86,15.86,0,0,0,104,219.31L227.31,96a16,16,0,0,0,0-22.63ZM51.31,160,136,75.31,152.69,92,68,176.68ZM48,179.31,76.69,208H48Zm48,25.38L79.31,188,164,103.31,180.69,120Zm96-96L147.31,64l24-24L216,84.68Z"></path>
                    </svg>
                </button>
            {/if}
            {#if user.permission.includes("equipmentDelete")}
                <button
                        on:click={async () => {
                            await apiGet("v1/equipment/delete.php?id=" + equipment.id)
                            dispatch("update")
                        }}
                        class="rounded-lg bg-red-500 hover:bg-red-400 h-min text-white px-4 py-2 m-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                         viewBox="0 0 256 256">
                        <path d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM96,40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8v8H96Zm96,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z"></path>
                    </svg>
                </button>
            {/if}
        </p>
    </div>
</div>
{#if showEditModal}
    <EquipmentEditModal
            {equipment}
            {user}
            on:update={() => dispatch("update")}
            on:closeme={() => showEditModal = false}
    />
{/if}