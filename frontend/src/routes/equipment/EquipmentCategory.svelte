<script lang="ts">
    import type {EquipmentSpec} from "../../api";
    import EquipmentCard from "./EquipmentCard.svelte";
    import type {UserSpec} from "../../api";
    import {createEventDispatcher} from "svelte";

    export let category: EquipmentSpec[];
    export let categoryName: string;
    export let user: UserSpec;
    export let searchTerm: string;
    let dispatch = createEventDispatcher();
</script>

<h2 class="mt-4 text-2xl">{categoryName}</h2>
<ul>
    {#each category as item}
        {#if item.name.includes(searchTerm)}
            <li>
                <EquipmentCard equipment={item} {user} on:update={e => dispatch('update')}/>
            </li>
        {/if}
    {/each}
</ul>