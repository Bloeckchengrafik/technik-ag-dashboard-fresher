<script>
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import {apiGet, apiPost} from "../../api";
    import CheckInput from "../../lib/Forms/CheckInput.svelte";
    import SubmitBtn from "../../lib/Forms/SubmitBtn.svelte";
    import Footer from "../../lib/Footer.svelte";

    export let params;

    let permissions = apiGet("v1/permissions/list.php").then(r => r.json())
    let permissionsChanged = {}
    let role = apiGet("v1/permissions/groups/get.php?id=" + params.id).then(r => r.json()).then(r => {
        r.permissions.forEach(p => {
            permissionsChanged[p] = true
        })
        return r
    })
</script>

<AuthGuard requiredPermission="userAdministration"/>

<div class="min-h-full max-w-7xl pt-2 px-1 mx-auto mb-2">
    {#await Promise.all([permissions, role])}
        <p>Loading...</p>
    {:then [permissions, role]}
        <h1 class="text-3xl break-words mb-5">
            Rolle <b>{role.name}</b> bearbeiten
        </h1>

        <div class="card">
            {#each permissions as permission}
                <div class="flex flex-row items-center mb-2">
                    <CheckInput id="permission-{permission.id}" value={role.permissions.includes(permission.name)}
                                name="{permission.name}"
                                on:change={e => {
                                    permissionsChanged[permission.name] = e.detail.value
                                }}
                    />
                </div>
            {/each}

            <SubmitBtn name="Speichern"
                       on:click={() => {
                           /** @type {Array<String>} */
                           let permissionNames = []
                           for (let [key, value] of Object.entries(permissionsChanged)) {
                               if (value) {
                                      permissionNames.push(key)
                               } else {
                                      permissionNames = permissionNames.filter(p => p !== key)
                               }
                           }

                           apiPost("v1/permissions/groups/update.php?id=" + params.id, {
                               permissions: permissionNames
                           })
                       }}
            />
        </div>
    {/await}
</div>
<Footer />