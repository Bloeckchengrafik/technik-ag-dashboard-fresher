<script lang="ts">
    import type {UserSpec} from "../../api";
    import {apiGet, apiPost, backend} from "../../api";
    import TextInput from "../../lib/Forms/TextInput.svelte";
    import CheckInput from "../../lib/Forms/CheckInput.svelte";
    import Loader from "../../lib/Loader.svelte";
    import SubmitBtn from "../../lib/Forms/SubmitBtn.svelte";
    import Swal from "sweetalert2";

    export let user: UserSpec;
    let inputfield: HTMLInputElement
</script>

<div class="card">
    <div class="flex gap-5 flex-col">
        <h1 class="text-2xl">Homepage</h1>
        <input type="file" bind:this={inputfield}/>
        <button on:click={() => {
            
        }}>Submit</button>
    </div>
</div>