<script lang="ts">
    export let name: string;
    export let id: string;
    export let value: number | null = null;

    let active
    $: activeStuff = (active || value != null ) ? {"data-te-input-state-active": 1} : {};

    export function reset() {
        value = 0;
        value = null;
    }
</script>

<div class="relative mb-3 flex-1">
<select
        class="peer block min-h-[auto] w-full rounded border-2 border-gray-400 dark:border-neutral-700 bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 data-[te-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none dark:text-neutral-200 dark:placeholder:text-neutral-200 [&:not([data-te-input-placeholder-active])]:placeholder:opacity-0"
        id="{id}"
        on:click={() => active=true} on:blur={() => active=false}
        {...activeStuff}
        bind:value
>
    <slot />
</select>
    <label
            for="{id}"
            class="pointer-events-none absolute left-3 peer-data-[te-input-state-active]:bg-light_fill peer-data-[te-input-state-active]:dark:bg-dark_fill bg-transparent top-0 mb-0 max-w-[90%] origin-[0_0] truncate pt-[0.37rem] leading-[1.6] text-neutral-500 transition-all duration-200 ease-out peer-focus:-translate-y-[0.9rem] peer-focus:scale-[0.8] peer-focus:text-primary peer-data-[te-input-state-active]:-translate-y-[0.9rem] peer-data-[te-input-state-active]:scale-[0.8] motion-reduce:transition-none dark:text-neutral-200 dark:peer-focus:text-primary"
    >{name}</label
    >
</div>

<style>
    :global(.force-no-border+*>*) {
        border: none !important;
    }
</style>
