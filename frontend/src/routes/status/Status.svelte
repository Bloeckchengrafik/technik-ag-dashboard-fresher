<script lang="ts">
    import Footer from "../../lib/Footer.svelte";
    import Scheduler from "../../lib/Scheduler.svelte";
    import {apiGet} from "../../api";
    import OK from "./OK.svelte";
    import Down from "./Down.svelte";

    let status: Promise<{
        php: boolean,
        mysql: boolean,
        ggb: boolean,
    }> = new Promise(() => {
    }); // Wait forever

    async function checkStatus() {
        status = Promise.resolve(await apiGet("").then(r => r.json()).then(r => r["systems"]));
    }
</script>
<Scheduler interval={1000} scheduledFn={checkStatus}/>

<div class="h-full flex items-center justify-center">
    <div class="w-min">
        <h1 class="text-5xl font-black break-words">System-status</h1>
        <br/>
        {#await status}
            Warte auf Antwort...
        {:then theStatus}
            <div class="flex items-center gap-2">
                <svelte:component this={theStatus.mysql?OK:Down} /> MySQL
            </div>
            <div class="flex items-center gap-2 pt-2">
                <svelte:component this={theStatus.php?OK:Down} /> PHP
            </div>
            <div class="flex items-center gap-2  pt-2">
                <svelte:component this={theStatus.ggb?OK:Down} /> Goethe-Homepage
            </div>
        {:catch error}
            Fehler beim Laden der Daten
        {/await}
    </div>
</div>
<Footer/>
