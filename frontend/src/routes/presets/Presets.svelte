<script lang="ts">
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import type {UserSpec} from "../../api";
    import {apiGet, apiPost} from "../../api";
    import Swal from "sweetalert2";
    import Footer from "../../lib/Footer.svelte";
    import {currentTab} from "../../stores";

    $currentTab = "presets";

    let user: UserSpec;
    let allPresets: Promise<[{
        id: number,
        tech: string,
        popularity: number,
    }]> = new Promise(() => {
    });

    async function loadPresets() {
        allPresets = Promise.resolve(await apiGet("v1/preset/all.php").then(r => r.json()));
    }

    loadPresets();
</script>

<AuthGuard requiredPermission="viewPresets" bind:user/>

<div class="min-h-full max-w-7xl pt-2 px-1 mx-auto">
    <h1 class="text-3xl break-words mb-5">
        Presets
        {#if user && user.permission.includes("editPresets")}
            <button class="float-right" on:click={() => {
            // Ask for new name using swal
            Swal.fire({
                title: "Neuer Name",
                input: "text",
                inputPlaceholder: "Name",
                showCancelButton: true,
                confirmButtonText: "Erstellen",
                cancelButtonText: "Abbrechen",
                showLoaderOnConfirm: true,
                preConfirm: async (name) => {
                    await apiPost("v1/preset/new.php", {
                        name: name,
                    });
                    await loadPresets();

                    setTimeout(() => {
                        window.scrollTo({
                            top: document.body.scrollHeight,
                            behavior: "smooth",
                        });
                    }, 100);
                }
            });
        }}>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                     viewBox="0 0 256 256">
                    <path
                        d="M224,128a8,8,0,0,1-8,8H136v80a8,8,0,0,1-16,0V136H40a8,8,0,0,1,0-16h80V40a8,8,0,0,1,16,0v80h80A8,8,0,0,1,224,128Z"></path>
                </svg>
            </button>
        {/if}
    </h1>
    {#await allPresets}
        <p>Loading...</p>
    {:then presets}
        {#if user}
            {#each presets as preset}
                <div class="card">
                    <div class="card-body">
                        <div class="flex flex-row justify-between">
                            <div class="flex-shrink-0 flex-grow-0 flex-basis-0">
                                <p class="text-2xl" style="overflow-wrap: anywhere;max-width: 70vw;">{preset.tech}</p>
                                <p>In <b>{preset.popularity}</b> Veranstaltungen verwendet</p>
                            </div>
                            {#if user.permission.includes("editPresets")}
                                <div class="flex-shrink-0 flex-grow-0 flex-basis-0 flex flex-col gap-2">
                                    <button
                                        class="rounded-md text-center bg-primary hover:bg-primary_highlight text-white px-2 py-1"
                                        on:click={() => {
                                                // Ask for new name using swal
                                                Swal.fire({
                                                    title: "Neuer Name",
                                                    input: "text",
                                                    inputLabel: "Name",
                                                    inputPlaceholder: "Name",
                                                    inputValue: preset.tech,
                                                    showCancelButton: true,
                                                    confirmButtonText: "Speichern",
                                                    cancelButtonText: "Abbrechen",
                                                    showLoaderOnConfirm: true,
                                                    preConfirm: async (name) => {
                                                        if (name === preset.tech) return;
                                                        await apiPost("v1/preset/edit.php", {
                                                            id: preset.id,
                                                            name: name,
                                                        });
                                                        await loadPresets();
                                                    }
                                                });
                                            }}
                                    >
                                        Bearbeiten
                                    </button>
                                    {#if preset.popularity === 0}
                                        <button
                                            class="rounded-md text-center bg-red-500 hover:bg-red-400 text-white px-2 py-1"
                                            on:click={async () => {
                                                    await apiGet(`v1/preset/delete.php?id=${preset.id}`);
                                                    await loadPresets();
                                                }}
                                        >
                                            Löschen
                                        </button>
                                    {/if}
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>
            {/each}
        {/if}
    {/await}
</div>
<br/>
<Footer/>