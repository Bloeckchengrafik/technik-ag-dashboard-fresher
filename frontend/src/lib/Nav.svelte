<script lang="ts">
    import logo from "../assets/goetec.png";
    import {link} from "svelte-spa-router";
    import {onMount} from "svelte";

    let open = false;
    let isOnTop = true;

    function scrollHandler() {
        isOnTop = window.scrollY < 10;
    }

    onMount(() => {
        window.addEventListener("scroll", scrollHandler);
        return () => window.removeEventListener("scroll", scrollHandler);
    });
</script>

<div class="fixed w-full {!isOnTop || open ? 'bg-dark_fill shadow-lg' : ''} z-50 transition-colors">
    <nav class="max-w-7xl mx-auto relative">
        <div class="flex flex-wrap items-center justify-between p-5 mx-auto">
            <a href="/" class="flex items-center flex-shrink-0 mr-6 text-white" use:link>
                <img src={logo} alt="logo" class="w-20"/>
            </a>
            <div class="block lg:hidden">
                <button class="flex items-center px-3 py-2 text-white hover:text-white border-none"
                        on:click={() => open = !open}>
                    <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <title>Menu</title>
                        <path d="M0 0h20v1.818H0V0zm0 7.273h20v1.818H0V7.273zm0 7.273h20v1.818H0v-1.818z"/>
                    </svg>
                </button>
            </div>
            <div class="w-full block flex-grow lg:flex lg:items-center lg:w-auto" class:hidden={!open}>
                <div class="text-sm lg:flex-grow">
                    <a href="/about" class="block mt-4 lg:inline-block lg:mt-0 mr-4" use:link>
                        Anmelden
                    </a>
                    <a href="/contact" class="block mt-4 lg:inline-block lg:mt-0 mr-4" use:link>
                        Registrieren
                    </a>
                    <a href="/services" class="block mt-4 lg:inline-block lg:mt-0 mr-4"
                       use:link>
                        Systemstatus
                    </a>
                    <a href="//goethe-bensheim.de/" class="block mt-4 lg:inline-block lg:mt-0 mr-4">
                        Zum Goethe-Gymnasium
                    </a>
                </div>
                <div>
                    <a href="/contact" use:link>
                        <button
                                class="inline-block px-4 py-2 text-sm font-bold leading-none text-white bg-blue-500 border border-blue-500 rounded hover:border-transparent hover:text-blue-500 hover:bg-white mt-4 lg:mt-0">
                            Jetzt Buchen
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</div>

<div class="h-20"
></div>