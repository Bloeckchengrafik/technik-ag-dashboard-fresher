<script lang="ts">

    import ImageSection from "./ImageSection.svelte";

    export let title: string = 'Login';
</script>
<div class="min-h-full flex items-center justify-center flex-col mt-5">
    <h1 class="text-3xl font-black break-words mb-5">{title}</h1>
    <div class="h-full">
        <!-- Left column container with background-->
        <div class="g-6 flex h-full flex-wrap items-center justify-center lg:justify-between">
            <ImageSection />

            <!-- Right column container -->
            <div class="mb-12 md:mb-0 md:w-8/12 lg:w-5/12 xl:w-5/12">
                <slot />
            </div>
        </div>
    </div>
</div>