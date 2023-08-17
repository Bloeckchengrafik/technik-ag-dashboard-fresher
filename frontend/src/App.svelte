<script lang="ts">
    import Router from "svelte-spa-router";
    import {routes} from "./routes";
    import Nav from "./lib/Nav.svelte";
</script>

<main class="h-5/6">
    <Nav/>
    <Router {routes}/>
</main>
