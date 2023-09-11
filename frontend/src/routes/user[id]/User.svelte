<script>
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import {apiGet, apiPost} from "../../api";
    import CheckInput from "../../lib/Forms/CheckInput.svelte";
    import SubmitBtn from "../../lib/Forms/SubmitBtn.svelte";
    import Footer from "../../lib/Footer.svelte";
    import Swal from "sweetalert2";

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

    let quizzes = apiGet("v1/quiz/foreign.php?id=" + params.id).then(r => r.json())

    let stats = apiGet("v1/profile/foreign/stats.php?id=" + params.id).then(r => r.json())
    let spinner = false

    let scoreString = {
        '-2': 'Sehr schlecht',
        '-1': 'Schlecht',
        '0': 'Neutral',
        '1': 'Gut',
        '2': 'Sehr gut',
    }
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
                       {spinner}
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

                           // spinner for visual feedback
                            spinner = true
                            setTimeout(() => {
                                spinner = false
                            }, 300)
                       }}
            />
        </div>
    {/await}

    <div class="card mb-4">
        <h1 class="text-3xl break-words mb-5">
            Statistiken
        </h1>
        {#await stats}
            <p>Loading...</p>
        {:then statistics}
            <!--
             example json: {"own_events":8,"participated_events":1,"participated_time":"00h 01m"}
             -> something nice with tailwind
             -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div class="p-4 bg-light dark:bg-dark shadow rounded-lg">
                    <h2 class="text-xl">Eigene Events</h2>
                    <p class="text-2xl">{statistics.own_events}</p>
                </div>
                <div class="p-4 bg-light dark:bg-dark shadow rounded-lg">
                    <h2 class="text-xl">Teilgenommene Events</h2>
                    <p class="text-2xl">{statistics.participated_events}</p>
                </div>
                <div class="p-4 bg-light dark:bg-dark shadow rounded-lg">
                    <h2 class="text-xl">Teilgenommene Zeit</h2>
                    <p class="text-2xl">{statistics.participated_time}</p>
                </div>
            </div>
        {/await}
    </div>
    <div class="card mb-4">
        <h1 class="text-3xl break-words mb-5 flex flex-row justify-between">
            Umfragen
            <button class="rounded text-lg bg-primary text-white px-4 py-2 hover:bg-primary_highlight transition-colors"
                    on:click={() => {
                apiPost("v1/message/add.php", {
                    user_id: parseInt(params.id),
                    message: "Zeit eine Umfrage zu beantworten!",
                }).then(()=> {
                    Swal.fire({
                        title: 'Erfolgreich',
                        text: 'Die Umfrage wurde erfolgreich versendet',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    })
                })
            }}>Zur Umfrage Auffordern
            </button>
        </h1>
        {#await quizzes}
            <p>Loading...</p>
        {:then quizzes}
            <ul>
                {#each quizzes as quiz}
                    <li>
                        <h3 class="text-xl inline">{quiz.quiz_name}</h3> <span
                            class="text-neutral-300">({scoreString[quiz.score]})</span>
                    </li>
                {/each}
            </ul>
        {/await}
    </div>
</div>
<Footer/>