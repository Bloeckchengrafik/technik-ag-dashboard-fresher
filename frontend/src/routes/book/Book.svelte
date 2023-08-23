<script lang="ts">
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import type {UserSpec} from "../../api";
    import Footer from "../../lib/Footer.svelte";

    let user: UserSpec | null = undefined
</script>
<AuthGuard requiredPermission={"showAsUser"} user={user}/>

<div class="min-h-full max-w-7xl pt-2 mx-auto flex">
    <h1 class="text-3xl font-black break-words mb-5">Technik Buchen</h1>

</div>
<Footer />