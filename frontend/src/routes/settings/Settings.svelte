<script lang="ts">
    import {currentTab} from "../../stores";
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import type {UserSpec} from "../../api";
    import Footer from "../../lib/Footer.svelte";
    import UserSettings from "./UserSettings.svelte";
    import AdminSettings from "./AdminSettings.svelte";

    $currentTab = "settings";
    let user: UserSpec;
    let modalOpen = false;

    let currentPageSection: "user" | "admin" = "user";
</script>

<AuthGuard requiredPermission={"showAsUser"} bind:user/>

<svelte:body on:click={(e) => {
    if (modalOpen) {
        modalOpen = false;
        e.stopPropagation();
    }
}}/>

<div class="min-h-full max-w-7xl pt-2 px-1 mx-auto">
    <h1 class="text-3xl break-words mb-5">
        {#if currentPageSection === "user"}
            Benutzereinstellungen
        {:else if currentPageSection === "admin"}
            Administration
        {/if}
        {#if user?.permission.includes("userAdministration")}
            <button class="text-sm dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200" on:click|stopPropagation={() => {
            modalOpen = true;
        }}>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                     viewBox="0 0 256 256">
                    <path d="M213.66,101.66l-80,80a8,8,0,0,1-11.32,0l-80-80A8,8,0,0,1,53.66,90.34L128,164.69l74.34-74.35a8,8,0,0,1,11.32,11.32Z"></path>
                </svg>
                {#if modalOpen}
                    <button class="rounded-md p-2 absolute bg-light_fill dark:bg-dark_fill shadow-lg text-black dark:text-gray-200 cursor-default"
                            on:click|preventDefault|stopPropagation
                    >
                        <ul>
                            <li class="hover:bg-gray-700 dark:hover:bg-opacity-50 hover:bg-opacity-20 p-1 rounded-md">
                                <button class="w-full text-left" on:click={() => {
                                currentPageSection = "user";
                                setTimeout(() => {
                                    modalOpen = false;
                                }, 1);
                            }}>
                                    <span class="text-sm">Benutzereinstellungen</span>
                                </button>
                            </li>

                            <li class="hover:bg-gray-700 dark:hover:bg-opacity-50 hover:bg-opacity-20 p-1 rounded-md">
                                <button class="w-full text-left" on:click={() => {
                                currentPageSection = "admin";
                                setTimeout(() => {
                                    modalOpen = false;
                                }, 1);
                            }}>
                                    <span class="text-sm">Administration</span>
                                </button>
                            </li>
                        </ul>
                    </button>
                {/if}
            </button>
        {/if}
    </h1>

    {#if user}
        {#if currentPageSection === "user"}
            <UserSettings {user}/>
        {:else if currentPageSection === "admin"}
            <AdminSettings/>
        {/if}
    {/if}

</div>
<br/>
<Footer/>