<script lang="ts">
    import type {Log} from "./fullEvent";
    import {backend} from "../../api";

    export let message: Log;
</script>

<div class="flex flex-row items-center space-x-2 my-2 p-3 bg-light dark:bg-dark rounded-lg">
    <div class="flex-shrink-0 w-12 h-12 bg-gray-700 rounded-full">
        <img src="{backend}/v1/profile/avatar/generate.php?id={message.user_id}" alt="AVATAR" class="w-full h-full rounded-full" />
    </div>
    <div class="flex flex-col flex-grow">
        <div class="flex flex-row items-center justify-between">
            <div class="flex flex-row items-center space-x-2">
                <span class="font-bold">{message.user_name}</span>
                <span class="text-xs text-gray-400">{message.timestamp}</span>
            </div>
            {#if message.type.toLowerCase() === "change"}
                <span class="text-xs text-gray-400">AUTOMATISIERT</span>
            {/if}
        </div>
        <div class="text-sm">{message.message}</div>
    </div>
</div>
