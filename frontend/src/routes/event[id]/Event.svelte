<script lang="ts">
    import Footer from "../../lib/Footer.svelte";
    import Loader from "../../lib/Loader.svelte";
    import {apiGet} from "../../api";
    import type {UserSpec} from "../../api";
    import {onMount} from "svelte";
    import EventView from "./EventView.svelte";
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import {currentTab} from "../../stores";

    export let params: { id: string };
    let id = parseInt(params.id);

    let user: UserSpec
    let pauseReload = false;

    let fullEvent = apiGet(`v1/event/full.php?id=${id}`).then(res => res.json());

    onMount(() => {
        let interval = setInterval(async () => {
            if (pauseReload) return;
            fullEvent = Promise.resolve(await apiGet(`v1/event/full.php?id=${id}`).then(res => res.json()));
        }, 10000);

        return () => clearInterval(interval);
    });

    $currentTab = "dash";
</script>
<AuthGuard requiredPermission="login" bind:user/>
<div class="min-h-full mb-4">
    {#await fullEvent}
        <div class="h-full flex justify-center items-center flex-col">
            <Loader/>
            <span class="text-3xl">Loading Event Data...</span>
        </div>
    {:then event}
        {#if user}
            <EventView {event} {user} bind:pauseReload on:update={async () => {
                fullEvent = Promise.resolve(await apiGet(`v1/event/full.php?id=${id}`).then(res => res.json()));
            }}/>
        {:else}
            <div class="h-full flex justify-center items-center flex-col">
                <span class="text-3xl">Please Login to view this event</span>
            </div>
        {/if}
    {:catch error}
        <div class="h-full flex justify-center items-center flex-col">
            <span class="text-3xl">Error Loading Event Data</span>
            <span class="text-3xl">{error.message}</span>
        </div>
    {/await}
</div>
<Footer/>