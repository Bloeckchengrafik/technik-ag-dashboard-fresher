<script lang="ts">
    import Footer from "../../lib/Footer.svelte";
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import type {UserSpec} from "../../api";
    import {apiGet} from "../../api";
    import {onMount} from "svelte";
    import Loader from "../../lib/Loader.svelte";
    import EventList from "./EventList.svelte";
    import {currentTab} from "../../stores";

    let user: UserSpec = null;

    async function query(deps: any[] = []): Promise<any> {
        if (user === null) {
            return new Promise(() => {
            });
        }

        if (user.permission.includes("viewAllEvents")) {
            return apiGet("v1/event/shortList.php").then((res) => res.json());
        } else {
            return fetch("v1/event/shortListByOrganizer.php").then((res) => res.json());
        }
    }

    let q: any
    $: q = query([user]);

    onMount(() => {
        let interval = setInterval(async () => {
            q = Promise.resolve(await query());
        }, 10000);

        return () => {
            clearInterval(interval);
        }
    });

    let needsUpdate = apiGet("v1/profile/student/check_to_be_updated.php").then((res) => res.json()).then((res) => !!res.needs_update);

    $currentTab = "dash";
</script>
<AuthGuard requiredPermission={"showAsUser"} bind:user/>

<div class="min-h-full max-w-7xl pt-2 px-1 mx-auto">
    {#await needsUpdate}
    {:then doesNeedUpdate}
        {#if doesNeedUpdate}
            <div class="border-primary border-2 p-2 mb-3 rounded">
                <h1 class="text-3xl break-words">Ein neues Jahr ist angebrochen! Bitte überprüfe deine Daten <a
                        href="/#/settings" class="underline">hier.</a></h1>
            </div>
        {/if}
    {/await}

    <h1 class="text-3xl break-words mb-5">Aktuelle Events</h1>

    {#await q}
        <div class="bg-light_fill dark:bg-dark_fill rounded shadow-lg p-5 mt-4">
            <div class="mt-5 flex justify-center items-center">
                <Loader/>
            </div>
        </div>
    {:then list}
        <EventList events={list}/>
    {/await}


</div>
<br/>
<Footer/>