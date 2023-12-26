<script lang="ts">
    import type {UserSpec} from "../../api";
    import {apiGet, apiPost, backend} from "../../api";
    import TextInput from "../../lib/Forms/TextInput.svelte";
    import CheckInput from "../../lib/Forms/CheckInput.svelte";
    import Loader from "../../lib/Loader.svelte";
    import SubmitBtn from "../../lib/Forms/SubmitBtn.svelte";
    import Swal from "sweetalert2";

    export let user: UserSpec;

    apiGet("v1/profile/student/mark_updated.php");

    let studentInfo = apiGet("v1/profile/student/get.php").then(r => r.json());

    let amStudent = false;
    let tutor = null;
    let year = null;

    let initialized = false;
</script>

<div class="card">
    <div class="flex gap-5">
        <img src="{backend}v1/profile/avatar/generate.php?id={user.id}" alt="Avatar" class="rounded-lg w-32 h-32">
        <div class="flex flex-col flex-grow justify-center">
            <div class="flex mt-3">
                <TextInput id="firstname" name="First name" value="{user.firstname}" readonly={true}/>
                <TextInput id="lastname" name="Last name" value="{user.lastname}" readonly={true}/>
            </div>
            <TextInput id="email" name="Email" value="{user.email}" readonly={true}/>
        </div>
    </div>

    {#await studentInfo}
        <div class="mt-5 flex justify-center items-center">
            <Loader/>
        </div>
    {:then info}
        {(() => {
            if (initialized) return "";
            amStudent = !info.error;

            if (amStudent) {
                tutor = info.tutor;
                year = `${info.year}`;
            } else {
                tutor = null;
                year = null;
            }

            initialized = true;

            return ""
        })()}
        <CheckInput id="student" name="Ich bin ein Schüler" bind:value={amStudent}/>

        <br/>
        {#if amStudent}
            <TextInput id="tutor" name="Tutor" bind:value={tutor}/>
            <TextInput id="year" type="number" name="Jahrgang" bind:value={year}/>
        {/if}

        <SubmitBtn name="Speichern" on:click={async () => {
            if (amStudent) {
                await apiPost("v1/profile/student/update.php", {
                    isStudent: amStudent,
                    tutor: tutor,
                    year: year
                })
            } else {
                await apiPost("v1/profile/student/update.php", {
                    isStudent: amStudent
                })
            }

            await Swal.fire({
                title: "Erfolgreich gespeichert",
                icon: "success",
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        }}
                   disabled={amStudent && (tutor == null || year == null || year < 5 || year > 13 || tutor === "")}/>
    {/await}

</div>