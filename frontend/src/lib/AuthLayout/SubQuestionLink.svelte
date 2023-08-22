<script lang="ts">
    export let title: string;
    export let cta: string;
    export let ctaUrl: string;
</script>

<p class="mb-0 mt-2 pt-1 text-sm font-semibold">
    {title}
    <a
            href="/#/{ctaUrl}"
            class="text-primary transition duration-150 ease-in-out hover:text-danger-600 focus:text-danger-600 active:text-danger-700"
    >
        {cta}
    </a>
</p>