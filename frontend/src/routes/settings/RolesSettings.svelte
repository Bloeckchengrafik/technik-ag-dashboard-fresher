<script lang="ts">
    import Swal from "sweetalert2";
    import {apiGet, apiPost, backend} from "../../api";
    import SubmitBtn from "../../lib/Forms/SubmitBtn.svelte";
    import TextInput from "../../lib/Forms/TextInput.svelte";

    let roles = apiGet("v1/permissions/groups/list.php").then(r => r.json())

    let newGroupName = ""
</script>

{#await roles}
    <p>loading...</p>
{:then theRoles}
    <div class="flex flex-col space-y-4">
        {#each theRoles as role}
            <a href="/#/grp/{role.id}"
               class="flex items-center p-2 pl-4 justify-between rounded-md bg-light_fill dark:bg-dark_fill">
                <div class="flex items-center space-x-4">
                    <div class="flex flex-col">
                        <p class="text-lg font-semibold">{role.name}</p>
                        <p class="text-sm text-gray-400">{role.users.length} Benutzer | {role.permissions.length}
                            verknüpfte Rechte</p>
                    </div>
                </div>

                <div>
                    <button class="flex items-center justify-center mr-2 w-8 h-8 rounded bg-primary hover:bg-primary_highlight focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 float-right">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                             viewBox="0 0 256 256">
                            <path d="M128,80a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Zm88-29.84q.06-2.16,0-4.32l14.92-18.64a8,8,0,0,0,1.48-7.06,107.21,107.21,0,0,0-10.88-26.25,8,8,0,0,0-6-3.93l-23.72-2.64q-1.48-1.56-3-3L186,40.54a8,8,0,0,0-3.94-6,107.71,107.71,0,0,0-26.25-10.87,8,8,0,0,0-7.06,1.49L130.16,40Q128,40,125.84,40L107.2,25.11a8,8,0,0,0-7.06-1.48A107.6,107.6,0,0,0,73.89,34.51a8,8,0,0,0-3.93,6L67.32,64.27q-1.56,1.49-3,3L40.54,70a8,8,0,0,0-6,3.94,107.71,107.71,0,0,0-10.87,26.25,8,8,0,0,0,1.49,7.06L40,125.84Q40,128,40,130.16L25.11,148.8a8,8,0,0,0-1.48,7.06,107.21,107.21,0,0,0,10.88,26.25,8,8,0,0,0,6,3.93l23.72,2.64q1.49,1.56,3,3L70,215.46a8,8,0,0,0,3.94,6,107.71,107.71,0,0,0,26.25,10.87,8,8,0,0,0,7.06-1.49L125.84,216q2.16.06,4.32,0l18.64,14.92a8,8,0,0,0,7.06,1.48,107.21,107.21,0,0,0,26.25-10.88,8,8,0,0,0,3.93-6l2.64-23.72q1.56-1.48,3-3L215.46,186a8,8,0,0,0,6-3.94,107.71,107.71,0,0,0,10.87-26.25,8,8,0,0,0-1.49-7.06Zm-16.1-6.5a73.93,73.93,0,0,1,0,8.68,8,8,0,0,0,1.74,5.48l14.19,17.73a91.57,91.57,0,0,1-6.23,15L187,173.11a8,8,0,0,0-5.1,2.64,74.11,74.11,0,0,1-6.14,6.14,8,8,0,0,0-2.64,5.1l-2.51,22.58a91.32,91.32,0,0,1-15,6.23l-17.74-14.19a8,8,0,0,0-5-1.75h-.48a73.93,73.93,0,0,1-8.68,0,8,8,0,0,0-5.48,1.74L100.45,215.8a91.57,91.57,0,0,1-15-6.23L82.89,187a8,8,0,0,0-2.64-5.1,74.11,74.11,0,0,1-6.14-6.14,8,8,0,0,0-5.1-2.64L46.43,170.6a91.32,91.32,0,0,1-6.23-15l14.19-17.74a8,8,0,0,0,1.74-5.48,73.93,73.93,0,0,1,0-8.68,8,8,0,0,0-1.74-5.48L40.2,100.45a91.57,91.57,0,0,1,6.23-15L69,82.89a8,8,0,0,0,5.1-2.64,74.11,74.11,0,0,1,6.14-6.14A8,8,0,0,0,82.89,69L85.4,46.43a91.32,91.32,0,0,1,15-6.23l17.74,14.19a8,8,0,0,0,5.48,1.74,73.93,73.93,0,0,1,8.68,0,8,8,0,0,0,5.48-1.74L155.55,40.2a91.57,91.57,0,0,1,15,6.23L173.11,69a8,8,0,0,0,2.64,5.1,74.11,74.11,0,0,1,6.14,6.14,8,8,0,0,0,5.1,2.64l22.58,2.51a91.32,91.32,0,0,1,6.23,15l-14.19,17.74A8,8,0,0,0,199.87,123.66Z"></path>
                        </svg>
                    </button>

                    <button class="flex items-center justify-center mr-2 w-8 h-8 rounded bg-primary hover:bg-primary_highlight focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 float-right"
                            on:click|preventDefault={async () => {
                                let ans = Swal.fire({
                                    title: 'Gruppe löschen?',
                                    text: "Die Gruppe wird unwiderruflich gelöscht!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Ja, löschen!',
                                    cancelButtonText: 'Abbrechen'
                                })

                                if (!(await ans).isConfirmed) return;

                                await apiPost("v1/permissions/groups/delete.php", {
                                    id: role.id
                                })

                                roles = Promise.resolve(await apiGet("v1/permissions/groups/list.php").then(r => r.json()));
                            }}
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                             viewBox="0 0 256 256">
                            <path d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM96,40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8v8H96Zm96,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z"></path>
                        </svg>
                    </button>
                </div>
            </a>
        {/each}
    </div>
{:catch error}
    <p>error: {error.message}</p>
{/await}

<div class="mt-4 flex space-y-4 space-x-2 ">
    <div class="flex-1">
        <TextInput id="name" name="Name" bind:value={newGroupName}/>
    </div>
    <div class="flex-grow-0">
        <SubmitBtn name="Neue Gruppe" on:click={async () => {
            let group = await apiPost("v1/permissions/groups/new.php", {
                name: newGroupName
            });

            roles = Promise.resolve(await apiGet("v1/permissions/groups/list.php").then(r => r.json()));
            newGroupName = "";
        }} disabled={newGroupName.length < 3}
        >Neue Gruppe
        </SubmitBtn>
    </div>
</div>