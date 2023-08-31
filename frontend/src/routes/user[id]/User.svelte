<script>
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import {apiGet, apiPost} from "../../api";
    import CheckInput from "../../lib/Forms/CheckInput.svelte";
    import SubmitBtn from "../../lib/Forms/SubmitBtn.svelte";
    import Footer from "../../lib/Footer.svelte";

    export let params;

    let permissions = apiGet("v1/permissions/list.php").then(r => r.json())
    let permissionsChanged = {}
    let rolesChanged = {}
    let roles = apiGet("v1/permissions/groups/list.php").then(r => r.json())
    let user = apiGet("v1/profile/foreign/get.php?id=" + params.id).then(r => r.json()).then(r => {
        r.permission.forEach(p => {
            permissionsChanged[p] = true
        })

        r.groups.forEach(p => {
            rolesChanged[p] = true
        })

        return r
    })
</script>

<AuthGuard requiredPermission="userAdministration"/>

<div class="min-h-full max-w-7xl pt-2 px-1 mx-auto mb-2">
    {#await Promise.all([permissions, user, roles])}
        <p>Loading...</p>
    {:then [permissions, user, roles]}
        <h1 class="text-3xl break-words mb-5">
            Benutzer <b>{user.firstname} {user.lastname}</b> bearbeiten
        </h1>

        <div class="card">
            <h2 class="text-xl mb-2">Berechtigungen</h2>
            {#each permissions as permission}
                <div class="flex flex-row items-center mb-2">
                    <CheckInput id="permission-{permission.id}" value={user.permission.includes(permission.name)}
                                name="{permission.name}"
                                on:change={e => {
                                    permissionsChanged[permission.name] = e.detail.value
                                }}
                    />
                </div>
            {/each}

            <br/>
            <h2 class="text-xl mb-2">Rollen</h2>
            {#each roles as role}
                <div class="flex flex-row items-center mb-2">
                    <CheckInput id="role-{role.id}" value={user.groups.includes(role.name)}
                                name="{role.name}"
                                on:change={e => {
                                    rolesChanged[role.name] = e.detail.value
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

                            /** @type {Array<String>} */
                            let roleNames = []
                            for (let [key, value] of Object.entries(rolesChanged)) {
                                if (value) {
                                    roleNames.push(key)
                                } else {
                                    roleNames = roleNames.filter(p => p !== key)
                                }
                            }

                           apiPost("v1/profile/foreign/update.php?id=" + params.id, {
                               permissions: permissionNames,
                               groups: roleNames
                           })
                       }}
            />
        </div>
    {/await}
</div>
<Footer/>