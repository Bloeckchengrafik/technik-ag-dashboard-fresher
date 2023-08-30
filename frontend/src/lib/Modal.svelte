<script lang="ts">
    import {createEventDispatcher, onMount} from "svelte";

    let dialog: HTMLDialogElement

    let dispatch = createEventDispatcher()

    onMount(() => {
        dialog.showModal()

        return () => {
            dialog.close()
        }
    })
</script>

<dialog id="dialog" bind:this={dialog} aria-modal="true" on:close={() => dispatch('close')} class="bg-light dark:bg-dark rounded text-black dark:text-white">
    <div class="relative mb-10">
        <button class="absolute top-0 right-0 cursor-pointer text-2xl" on:click={() => dispatch('close')}>
            &times;
        </button>
    </div>
    <slot />
</dialog>