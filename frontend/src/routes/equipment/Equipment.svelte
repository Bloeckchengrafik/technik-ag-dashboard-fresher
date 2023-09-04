<script lang="ts">
    import {apiGet} from "../../api";
    import type {UserSpec} from "../../api";
    import type {SortedEquipmentSpec} from "../../api";
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import EquipmentView from "./EquipmentView.svelte";
    import EquipmentEditModal from "./EquipmentEditModal.svelte";

    let tab = 0;

    let jsonPromise: Promise<SortedEquipmentSpec>;
    $: jsonPromise = apiGet('v1/equipment/query.php?tab=' + tab).then(res => res.json());

    export let user: UserSpec;

    let showNew = false;
</script>

<AuthGuard requiredPermission="equipmentView" bind:user/>

<div class="min-h-full max-w-7xl pt-2 px-1 mx-auto mb-2">
    <h1 class="text-2xl font-semibold">
        Unser Equipment
        <button class="float-right" on:click={() => {
            showNew = true;
        }}>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 256 256">
                <path d="M224,128a8,8,0,0,1-8,8H136v80a8,8,0,0,1-16,0V136H40a8,8,0,0,1,0-16h80V40a8,8,0,0,1,16,0v80h80A8,8,0,0,1,224,128Z"></path>
            </svg>
        </button>
    </h1>
    <br/>

    <!-- Tab Layout -->
    <div class="flex flex-row">
        <button on:click={() => tab = 0} class="border-b-2 border-b-neutral-500 flex-1 px-2 transition-all"
                class:border-b-primary={tab === 0}>Kategorien
        </button>
        <button on:click={() => tab = 1} class="border-b-2 border-b-neutral-500 flex-1 px-2 transition-all"
                class:border-b-primary={tab === 1}>Bezeichnung
        </button>
        <button on:click={() => tab = 2} class="border-b-2 border-b-neutral-500 flex-1 px-2 transition-all"
                class:border-b-primary={tab === 2}>Lager
        </button>
        <button on:click={() => tab = 3} class="border-b-2 border-b-neutral-500 flex-1 px-2 transition-all"
                class:border-b-primary={tab === 3}>Hersteller
        </button>
    </div>
    <br/>
    {#await jsonPromise}
        <p>Loading...</p>
    {:then json}
        {#if user}
            <EquipmentView {json} {user} on:update={async () => {
                // noinspection JSValidateTypes
                jsonPromise = Promise.resolve(await apiGet('v1/equipment/query.php?tab=' + tab).then(res => res.json()));
            }}/>
        {/if}
    {/await}
</div>
{#if showNew}
    <EquipmentEditModal {user} on:closeme={() => showNew = false} on:update={async () => {
        // noinspection JSValidateTypes
        jsonPromise = Promise.resolve(await apiGet('v1/equipment/query.php?tab=' + tab).then(res => res.json()));
    }}/>
{/if}