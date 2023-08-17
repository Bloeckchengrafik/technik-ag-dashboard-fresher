<script lang="ts">
    import {onMount} from "svelte";

    let interval: number;
    let scrollContainer: HTMLElement;
    let scrollReset: HTMLElement;
    let firstElement: HTMLElement;
    let firstElementPosX: number;

    function scrollThrough() {
        scrollContainer.scrollLeft += 1;
        if (scrollReset.getBoundingClientRect().left - firstElementPosX < 0) {
            scrollContainer.scrollLeft = 0;
        }
    }

    function startScrolling() {
        interval = setInterval(scrollThrough, 20);
        scrollContainer.scrollLeft = 0;
        firstElementPosX = firstElement.getBoundingClientRect().left;
    }

    function stopScrolling() {
        clearInterval(interval);
    }

    onMount(() => {
        startScrolling();
        return stopScrolling;
    });
</script>

<div class="bg-dark_fill w-full overflow-hidden" bind:this={scrollContainer}>
    <span class="text-gray-400 mt-1 ml-5 block absolute">Vergangene Veranstaltungen</span>
    <div class="w-verylarge h-full flex items-center mt-7">
        <div class="trusted-by" bind:this={firstElement}>Zukunftsvision 2050</div>
        <div class="trusted-by">Sommerkonzert</div>
        <div class="trusted-by">Abiball</div>
        <div class="trusted-by">GoeBIT</div>
        <div class="trusted-by">Unterstufenfeier</div>
        <div class="trusted-by">Kammerkonzert</div>
        <div class="trusted-by">GOETHE ROCKT & POPT</div>
        <div class="trusted-by">Goethe Cinema</div>
        <div class="trusted-by">Einschulung</div>
        <div class="trusted-by">Abisturm</div>
        <div class="trusted-by">Grundgesetzübergabe</div>
        <div class="trusted-by">Goetheater</div>
        <div class="trusted-by">Weihnachtskonzert</div>

        <div class="trusted-by">Zeugnisübergabe</div>
        <div class="trusted-by">Musiktheater</div>
        <div class="trusted-by">Videolyrikabend</div>
        <div class="trusted-by">Schulsprecherwahl</div>
        <div class="trusted-by">Tag der offenen Tür</div>
        <div class="trusted-by" bind:this={scrollReset}>Zukunftsvision 2050</div>
        <div class="trusted-by">Sommerkonzert</div>
        <div class="trusted-by">Abiball</div>
        <div class="trusted-by">GoeBIT</div>
        <div class="trusted-by">Unterstufenfeier</div>
        <div class="trusted-by">Kammerkonzert</div>
        <div class="trusted-by">GOETHE ROCKT & POPT</div>
        <div class="trusted-by">Goethe Cinema</div>
        <div class="trusted-by">Einschulung</div>
        <div class="trusted-by">Abisturm</div>
        <div class="trusted-by">Grundgesetzübergabe</div>
        <div class="trusted-by">Goetheater</div>
        <div class="trusted-by">Weihnachtskonzert</div>
        <div class="trusted-by">Zeugnisübergabe</div>
        <div class="trusted-by">Musiktheater</div>
        <div class="trusted-by">Videolyrikabend</div>
        <div class="trusted-by">Schulsprecherwahl</div>
        <div class="trusted-by">Tag der offenen Tür</div>
    </div>
    <span class="mt-1">&nbsp;</span>
</div>


<style lang="postcss">
    .trusted-by {
        @apply inline-block px-5 py-3 text-white text-4xl font-bold;
    }
</style>