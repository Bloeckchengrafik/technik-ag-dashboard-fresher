<script lang="ts">
    import logo from "../assets/goetec.png";
    import {link} from "svelte-spa-router";
    import {onMount} from "svelte";
    import type {UserSpec} from "../api";
    import {apiToken, backend, logout, refreshToken} from "../api";
    import * as jose from "jose";
    import {currentTab} from "../stores";

    let open = false;
    let isOnTop = true;
    let user: UserSpec | null = null;
    let userPermission = "Unbekannt";

    let jwtPubKey = import.meta.env.VITE_BACKEND_JWT_PUBKEY

    $: (async () => {
        let pubKey = await jose.importSPKI(jwtPubKey, 'RS256')

        try {
            let verified = await jose.jwtVerify($apiToken, pubKey, {
                algorithms: ['RS256']
            })

            user = verified.payload.user as UserSpec

            if (user.permission.includes("showAsAdmin")) userPermission = "Administrator"
            else if (user.permission.includes("showAsManager")) userPermission = "Manager"
            else if (user.permission.includes("showAsTechnician")) userPermission = "Techniker"
            else if (user.permission.includes("showAsUser")) userPermission = "Benutzer"
        } catch (e) {
        }
    })();

    function scrollHandler() {
        isOnTop = window.scrollY < 10;
    }

    onMount(() => {
        window.addEventListener("scroll", scrollHandler);
        return () => window.removeEventListener("scroll", scrollHandler);
    });
</script>

<div class="fixed w-full {!isOnTop || open ? 'bg-light_fill dark:bg-dark_fill shadow-lg' : ''} z-50 transition-colors">
    <nav class="max-w-7xl mx-auto relative">
        <div class="flex flex-wrap items-center justify-between p-5 mx-auto">
            <a href="/" class="flex items-center flex-shrink-0 mr-6 text-white" use:link>
                <img src={logo} alt="logo" class="w-20"/>
            </a>
            <div class="block lg:hidden">
                <button class="flex items-center px-3 py-2 dark:text-white dark:hover:text-white text-dark hover:text-dark border-none"
                        on:click={() => open = !open}>
                    <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <title>Menu</title>
                        <path d="M0 0h20v1.818H0V0zm0 7.273h20v1.818H0V7.273zm0 7.273h20v1.818H0v-1.818z"/>
                    </svg>
                </button>
            </div>
            <div class="w-full block flex-grow lg:flex lg:items-center lg:w-auto" class:hidden={!open}>
                <div class="text-sm lg:flex-grow">
                    {#if user == null}
                        <a href="/login" class="block mt-4 lg:inline-block lg:mt-0 mr-4 all" use:link
                           class:active={$currentTab === "login"}>
                            Anmelden
                        </a>
                        <a href="/register" class="block mt-4 lg:inline-block lg:mt-0 mr-4 all" use:link
                           class:active={$currentTab === "register"}>
                            Registrieren
                        </a>
                        <a href="/status" class="block mt-4 lg:inline-block lg:mt-0 mr-4 all"
                           class:active={$currentTab === "status"}
                           use:link>
                            Systemstatus
                        </a>
                        <a href="//goethe-bensheim.de/" class="block mt-4 lg:inline-block lg:mt-0 mr-4">
                            Zum Goethe-Gymnasium
                        </a>
                    {:else}
                        <a href="/dash" class="block mt-4 lg:inline-block lg:mt-0 mr-4 all" use:link
                           class:active={$currentTab === "dash"}>
                            Dashboard
                        </a>
                        {#if user.permission.includes("equipmentView")}
                            <a href="/equipment" class="block mt-4 lg:inline-block lg:mt-0 mr-4 all" use:link
                               class:active={$currentTab === "equipment"}>
                                Equipment
                            </a>
                        {/if}
                        {#if user.permission.includes("viewPresets")}
                            <a href="/presets" class="block mt-4 lg:inline-block lg:mt-0 mr-4 all" use:link
                               class:active={$currentTab === "presets"}>
                                Presets
                            </a>
                        {/if}
                        <a href="/settings" class="block mt-4 lg:inline-block lg:mt-0 mr-4 all" use:link
                           class:active={$currentTab === "settings"}>
                            Einstellungen
                        </a>
                        {#if user.permission.includes("denyBooking") === false}
                            <a href="/book" class="block mt-4 lg:inline-block lg:mt-0 mr-4 all" use:link
                               class:active={$currentTab === "booking"}>
                                Buchen
                            </a>
                        {/if}
                    {/if}
                </div>
                <div class="flex items-center gap-3">
                    {#if user == null}
                        <a href="/book" use:link>
                            <button
                                    class="inline-block px-4 py-2 text-sm font-bold leading-none text-white bg-blue-500 border border-blue-500 rounded hover:border-transparent hover:text-blue-500 hover:bg-white mt-4 lg:mt-0">
                                Jetzt Buchen
                            </button>
                        </a>
                    {:else}
                        <img src="{backend}v1/profile/avatar/generate.php?id={user.id}" alt="Avatar"
                             class="w-10 h-10 rounded-full"/>
                        <a href="/settings" class="block mt-4 lg:inline-block lg:mt-0 mr-4" use:link>
                            <span>{user.firstname} {user.lastname}</span><br/>
                            <span class="text-s opacity-80">{userPermission}</span>
                        </a>
                        <div class="inline">
                            <a class="block mt-4 lg:inline-block lg:mt-0" href="/#"
                               on:click|preventDefault={refreshToken}>
                                <button
                                        class="inline-block px-4 lg:py-2 text-sm font-bold leading-none bg-light_fill text-black dark:text-white dark:bg-dark_fill border border-transparent rounded-l hover:border-transparent hover:opacity-75 mt-4 lg:mt-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                         fill="currentColor"
                                         viewBox="0 0 256 256">
                                        <path d="M238,56v48a6,6,0,0,1-6,6H184a6,6,0,0,1,0-12h32.55l-30.38-27.8c-.06-.06-.12-.13-.19-.19a82,82,0,1,0-1.7,117.65,6,6,0,0,1,8.24,8.73A93.46,93.46,0,0,1,128,222h-1.28A94,94,0,1,1,194.37,61.4L226,90.35V56a6,6,0,1,1,12,0Z"></path>
                                    </svg>
                                </button>
                            </a><a class="block lg:mt-4 lg:inline-block sm:mt-0 mr-4" href="/#"
                                   on:click|preventDefault={logout}>
                            <button
                                    class="inline-block px-4 lg:py-2 text-sm font-bold leading-none bg-light_fill text-black dark:text-white dark:bg-dark_fill border border-transparent lg:border-l-light lg:dark:border-l-dark rounded-r hover:border-transparent hover:opacity-75 mt-4 lg:mt-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                     fill="currentColor"
                                     viewBox="0 0 256 256">
                                    <path d="M122,128V48a6,6,0,0,1,12,0v80a6,6,0,0,1-12,0Zm57.28-77A6,6,0,0,0,172.72,61C196.41,76.47,210,100.88,210,128a82,82,0,0,1-164,0c0-27.12,13.59-51.53,37.28-67A6,6,0,0,0,76.72,51C49.57,68.68,34,96.75,34,128a94,94,0,0,0,188,0C222,96.75,206.43,68.68,179.28,51Z"></path>
                                </svg>
                            </button>
                        </a>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </nav>
</div>

<div class="h-20"
></div>

<style lang="postcss">
    .all {
        @apply border-b-2 border-transparent hover:border-primary transition-colors;
    }

    .active {
        @apply border-b-2 border-primary;
    }
</style>