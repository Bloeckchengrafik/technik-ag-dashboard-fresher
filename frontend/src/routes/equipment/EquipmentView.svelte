<script lang="ts">
    import type {SortedEquipmentSpec} from "../../api";
    import EquipmentCategory from "./EquipmentCategory.svelte";
    import type {UserSpec} from "../../api";
    import {createEventDispatcher} from "svelte";

    export let json: SortedEquipmentSpec;
    export let user: UserSpec;
    let dispatch = createEventDispatcher();
</script>

{#each Object.keys(json).sort((a, b) => a.localeCompare(b, undefined, {numeric: true, sensitivity: "base"})) as key}
    <EquipmentCategory categoryName="{key}" category={json[key]} {user} on:update={() => dispatch("update")}/>
{/each}
