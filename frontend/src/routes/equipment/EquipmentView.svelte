<script lang="ts">
    export let json: any;
</script>

{#if json}
    <pre>{JSON.stringify(json, null, 2)}</pre>
{/if}
