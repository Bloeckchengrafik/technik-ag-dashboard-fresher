<script lang="ts">
    import {onMount} from "svelte";

    export let scheduledFn: () => void;
    export let interval: number;

    let intervalId: number;
    onMount(() => {
        scheduledFn()
        intervalId = setInterval(scheduledFn, interval);
        return () => clearInterval(intervalId);
    });
</script>