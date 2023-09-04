<script lang="ts">
    import {createEventDispatcher, onMount} from "svelte";

    let dialog: HTMLDivElement

    let dispatch = createEventDispatcher()

    onMount(() => {
        return () => {
            dialog.remove()
            dispatch('close')
        }
    })
</script>


<div bind:this={dialog} class="fixed inset-0 z-50 flex items-center justify-center w-full h-full bg-black bg-opacity-50">
    <div id="dialog"
         class="bg-light dark:bg-dark rounded text-black dark:text-white p-4 max-h-[100vh] max-w-[100vw] overflow-y-auto">
        <div class="relative mb-10">
            <button class="absolute top-0 right-0 cursor-pointer text-2xl" on:click={() => {
                dispatch('close')
                dialog.remove()
            }}>
                &times;
            </button>
        </div>
        <slot/>
    </div>
</div>
