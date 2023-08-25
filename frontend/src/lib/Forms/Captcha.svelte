<script lang="ts">

    import {apiGet} from "../../api";

    export let value

    let imageElement

    let active
    $: activeStuff = (active || value?.length > 0) ? {"data-te-input-state-active": 1} : {};

    export let cid

    async function refreshCaptcha() {
        const response = await apiGet("v1/captcha/new.php")
        let blob = new Blob([await response.blob()], {type: 'image/png'})
        imageElement.src = URL.createObjectURL(blob)
        cid = response.headers.get("X-Captcha-ID")
        console.log("%c✓ Captcha ID: " + cid, "color: #4caf50")
    }

    refreshCaptcha()
</script>

<img src="" alt="Loading..." class="mb-6" tabindex="-1" on:click={refreshCaptcha} on:keydown={refreshCaptcha} bind:this={imageElement}/>

<p class="mb-6">
    Bitte geben Sie die Zeichen aus dem Bild ein. <a href="javascript:void(0)" on:click={refreshCaptcha} on:keydown={refreshCaptcha} class="underline cursor-pointer">Neues Bild laden</a>
</p>

<div class="relative mb-6 w-full" {...activeStuff}>
    <input type="text"
           class="border-b-2 border-b-gray-400 dark:border-b-neutral-700 focus:border-b-primary peer-focus:border-primary peer-data-[te-input-state-active]:border-primary peer block min-h-[auto] w-full border-0 bg-transparent px-0 py-[0.32rem] leading-[2.15] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 data-[te-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none dark:text-neutral-200 dark:placeholder:text-neutral-200 [&:not([data-te-input-placeholder-active])]:placeholder:opacity-0"
           id="captcha"
           bind:value
           {...activeStuff}
           on:click={() => active = true}
           on:blur={() => active = false}
    />
    <label
            for="captcha"
            {...activeStuff}
            class="pointer-events-none absolute left-0 top-0 mb-0 max-w-[90%] origin-[0_0] truncate pt-[0.37rem] leading-[2.15] text-neutral-500 transition-all duration-200 ease-out peer-focus:-translate-y-[1.15rem] peer-focus:scale-[0.8] peer-focus:text-primary peer-data-[te-input-state-active]:-translate-y-[1.15rem] peer-data-[te-input-state-active]:scale-[0.8] motion-reduce:transition-none dark:text-neutral-200 dark:peer-focus:text-primary force-no-border"
    >
        Captcha
    </label>
</div>

<style>
    :global(.force-no-border+*>*) {
        border: none !important;
    }
</style>
