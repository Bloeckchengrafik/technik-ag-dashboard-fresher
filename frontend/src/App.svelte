<script lang="ts">
    import Router from "svelte-spa-router";
    import {routes} from "./routes";
    import Nav from "./lib/Nav.svelte";
    import {onMount} from "svelte";
    import {refreshToken} from "./api"
    import "@sweetalert2/theme-dark/dark.css";

    onMount(() => {
        let int = setInterval(async () => { await refreshToken(); }, 30000);
        return () => clearInterval(int);
    });
</script>

<main class="h-5/6">
    <Nav/>
    <Router {routes}/>

    <div class="absolute bottom-2 right-2 text-transparent">Macht sinn</div>
</main>
