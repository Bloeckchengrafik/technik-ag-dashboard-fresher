<script lang="ts">
    import type {FullEvent} from "./fullEvent";
    import ChatMessage from "./ChatMessage.svelte";
    import TextInput from "../../lib/Forms/TextInput.svelte";
    import {createEventDispatcher} from "svelte";
    import {apiPost} from "../../api";

    export let event: FullEvent;

    export let pauseReload: boolean = false;

    let dispatcher = createEventDispatcher();

    let textInputData: string = "";
    $: pauseReload = textInputData.length > 0;
</script>

<h1 class="text-3xl font-bold mb-4">Chat- und Systemnachrichten</h1>

{#each event.logs.sort(
    (a, b) => new Date(a.timestamp.replace(" ", "T")).getTime() - new Date(b.timestamp.replace(" ", "T")).getTime()
) as logEntry}
    <ChatMessage message={logEntry}/>
{/each}

<div class="flex flex-col mt-3">
    <div class="flex flex-row items-center gap-3">
        <TextInput
                id="chatbox"
                name="Nachricht"
                bind:value={textInputData}
        />
        <button class="px-4 py-1 h-min bg-primary text-white rounded-md shadow-md hover:bg-primary_highlight flex flex-row items-center gap-2"
                on:click={async () => {
                    if (textInputData.length === 0) return;
                    await apiPost("v1/event/chat/send.php", {
                        event_id: event.id,
                        chat_message: textInputData
                    });
                    dispatcher("update");
                    textInputData = "";
                    setTimeout(() => {
                        // smooth scroll to bottom
                        window.scrollTo({
                            top: document.body.scrollHeight,
                            behavior: "smooth"
                        });
                    }, 100);
                }}
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 256 256">
                <path d="M227.32,28.68a16,16,0,0,0-15.66-4.08l-.15,0L19.57,82.84a16,16,0,0,0-2.42,29.84l85.62,40.55,40.55,85.62A15.86,15.86,0,0,0,157.74,248q.69,0,1.38-.06a15.88,15.88,0,0,0,14-11.51l58.2-191.94c0-.05,0-.1,0-.15A16,16,0,0,0,227.32,28.68ZM157.83,231.85l-.05.14L118.42,148.9l47.24-47.25a8,8,0,0,0-11.31-11.31L107.1,137.58,24,98.22l.14,0L216,40Z"></path>
            </svg>
            Senden
        </button>
    </div>
</div>

