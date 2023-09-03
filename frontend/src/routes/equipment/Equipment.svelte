<script lang="ts">
    import {apiGet} from "../../api";
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import EquipmentView from "./EquipmentView.svelte";

    let tab = 0;

    $: jsonPromise = apiGet('v1/equipment/query.php?tab=' + tab).then(res => res.json());
</script>

<AuthGuard requiredPermission="equipmentView" />

<div class="min-h-full max-w-7xl pt-2 px-1 mx-auto mb-2">
    <h1 class="text-2xl font-semibold">Unser Equipment</h1>
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
                class:border-b-primary={tab === 3}>Firma
        </button>
    </div>
    <br/>
    {#await jsonPromise}
        <p>Loading...</p>
    {:then json}
        <EquipmentView {json}/>
    {/await}
</div>