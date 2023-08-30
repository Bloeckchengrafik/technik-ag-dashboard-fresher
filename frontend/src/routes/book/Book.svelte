<script lang="ts">
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import type {UserSpec} from "../../api";
    import {apiGet, apiPost, backend} from "../../api";
    import Footer from "../../lib/Footer.svelte";
    import TextInput from "../../lib/Forms/TextInput.svelte";
    import TextArea from "../../lib/Forms/TextArea.svelte";
    import Select from "../../lib/Forms/Select.svelte";
    import DateTimeInput from "../../lib/Forms/DateTimeInput.svelte";
    import CheckInput from "../../lib/Forms/CheckInput.svelte";
    import Captcha from "../../lib/Forms/Captcha.svelte";
    import SubmitBtn from "../../lib/Forms/SubmitBtn.svelte";

    let user: UserSpec | null = undefined
    let selectRoomReset: () => void
    let selectCategoryReset: () => void

    let presets = [];
    (async () => {
        presets = await apiGet("/v1/preset/all.php").then(r => r.json())
    })()

    let title: string
    let room: number
    let category: number
    let desc: string
    let start: string
    let end: string
    let constStart: string
    let constEnd: string
    let dismantleStart: string
    let dismantleEnd: string
    let help: boolean
    let tech: { [key: string]: boolean } = {}
    let captcha: string
    let captcha_id: string

    let validationError: string = ""
    let ok: boolean = false
    let spinner: boolean = false

    function parseDate(date: string | null): Date | null {
        if (!date) return null
        let [dateStr, timeStr] = date.split("T")
        let [year, month, day] = dateStr.split("-")
        let [hour, minute] = timeStr.split(":")
        return new Date(parseInt(year), parseInt(month) - 1, parseInt(day), parseInt(hour), parseInt(minute))
    }

    function validate(title, room, category, desc, start, end, constStart, constEnd, dismantleStart, dismantleEnd, help, tech, captcha, captcha_id) {
        if (!(title && room && category && desc && start && end && constStart && constEnd && dismantleStart && dismantleEnd && captcha && captcha_id)) {
            ok = false
            validationError = "Bitte fülle alle Felder aus"
            return
        }

        validationError = ""

        let datetimeStart = parseDate(start)
        let datetimeEnd = parseDate(end)
        let datetimeConstStart = parseDate(constStart)
        let datetimeConstEnd = parseDate(constEnd)
        let datetimeDismantleStart = parseDate(dismantleStart)
        let datetimeDismantleEnd = parseDate(dismantleEnd)

        if (datetimeStart >= datetimeEnd) {
            ok = false
            validationError = "Die Veranstaltung kann nicht vor dem Start enden"
            return;
        }

        if (datetimeConstStart >= datetimeConstEnd) {
            ok = false
            validationError = "Der Aufbau kann nicht vor dem Start enden"
            return;
        }

        if (datetimeDismantleStart >= datetimeDismantleEnd) {
            ok = false
            validationError = "Der Abbau kann nicht vor dem Start enden"
            return;
        }

        if (datetimeConstEnd >= datetimeStart) {
            ok = false
            validationError = "Der Aufbau kann nicht nach dem Start enden"
            return;
        }

        if (datetimeDismantleEnd <= datetimeEnd) {
            ok = false
            validationError = "Der Abbau kann nicht vor dem Ende starten"
            return;
        }

        if (captcha.length !== 4) {
            ok = false
            validationError = "Das Captcha muss 4 Zeichen lang sein"
            return;
        }

        if (captcha.toUpperCase() !== captcha) {
            ok = false
            validationError = "Das Captcha muss aus Großbuchstaben bestehen"
            return;
        }

        ok = true
        validationError = ""
    }

    $: validate(title, room, category, desc, start, end, constStart, constEnd, dismantleStart, dismantleEnd, help, tech, captcha, captcha_id)

    async function send() {
        if (!ok) {
            console.log("%c❌ Validation failed", "color: red")
            return
        }
        spinner = true
        let presets = []
        for (let key in tech) {
            if (tech[key]) presets.push(parseInt(key))
        }

        let body = {
            title,
            description: desc,
            type_id: category,
            room_id: room,
            needs_consultation: help,
            from: start,
            to: end,
            construction_from: constStart,
            construction_to: constEnd,
            dismantling_from: dismantleStart,
            dismantling_to: dismantleEnd,
            presets: presets,
            captcha,
            captcha_id: parseInt(captcha_id)
        }

        let answer = await apiPost("/v1/event/create.php", body).then(r => r.json())

        if (answer.error) {
            validationError = answer.error
            spinner = false
            ok = false
            return
        }

        console.log("%c✔️ Validation passed", "color: green")
        console.log("%c👨‍🎤 Event Data", "color: lightblue", answer)

        window.location.href = `/#/event/${answer.id}`
    }
</script>
<AuthGuard requiredPermission={"login"} user={user}/>

<div class="min-h-full max-w-7xl pt-2 px-1 mx-auto">
    <h1 class="text-3xl break-words mb-5">Technik Buchen</h1>

    <div class="card">
        <h2 class="uppercase mb-5 text-xl text-light_muted dark:text-dark_muted">Die Veranstaltung</h2>
        <div class="px-2">
            <TextInput
                    id="Name"
                    type="text"
                    name="Titel"
                    bind:value={title}
            />
        </div>
        <div class="flex w-full gap-2 sm:flex-row flex-col">
            <Select
                    id="Room"
                    name="Raum"
                    bind:value={room}
                    bind:reset={selectRoomReset}
            >
                {#await apiGet("/v1/rooms/all.php").then(r => r.json())}
                {:then rooms}
                    {#each rooms as room}
                        <option value="{room.id}">{room.name}</option>
                    {/each}
                    {(() => {
                        selectRoomReset()
                        return ""
                    })()}
                {:catch error}
                    <option value="0">Keine Räume verfügbar</option>
                {/await}
            </Select>
            <Select
                    id="category"
                    name="Typ"
                    bind:value={category}
                    bind:reset={selectCategoryReset}

            >
                {#await apiGet("/v1/categories/all.php").then(r => r.json())}
                {:then cats}
                    {#each cats as cat}
                        <option value="{cat.id}">{cat.name}</option>
                    {/each}
                    {(() => {
                        selectCategoryReset()
                        return ""
                    })()}
                {:catch error}
                    <option value="0">Keine Kategorien verfügbar</option>
                {/await}
            </Select>
        </div>
        <TextArea
                id="Desc"
                name="Beschreibung"
                bind:value={desc}
        />
    </div>
    <div class="card">
        <h2 class="uppercase mb-5 text-xl text-light_muted dark:text-dark_muted">Zeiten</h2>
        <div class="px-2">
            <span class="text-lg">Veranstaltungszeit</span>
            <br/>
            <br/>
            <div class="flex w-full gap-2 sm:flex-row flex-col">
                <DateTimeInput
                        id="Start"
                        name="Start"
                        bind:value={start}
                />
                <DateTimeInput
                        id="End"
                        name="Ende"
                        bind:value={end}
                />
            </div>
            <span class="text-lg">Aufbau</span>
            <br/>
            <br/>
            <div class="flex w-full gap-2 sm:flex-row flex-col">
                <DateTimeInput
                        id="ConstStart"
                        name="Aufbaustart"
                        bind:value={constStart}
                />
                <DateTimeInput
                        id="ConstEnd"
                        name="Aufbauende"
                        bind:value={constEnd}
                />
            </div>

            <span class="text-lg">Abbau</span>
            <br/>
            <br/>
            <div class="flex w-full gap-2 sm:flex-row flex-col">
                <DateTimeInput
                        id="DismantleStart"
                        name="Abbaustart"
                        bind:value={dismantleStart}
                />
                <DateTimeInput
                        id="DismantleEnd"
                        name="Abbauende"
                        bind:value={dismantleEnd}
                />
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="uppercase mb-5 text-xl text-light_muted dark:text-dark_muted">Technik</h2>
        <div class="px-2">
            <CheckInput
                    id="Help"
                    name="Ich brauche Hilfe bei der Auswahl der Technik"
                    bind:value={help}
            />

            <input type="checkbox" id="techtoggle" class="hidden" checked/>
            <label for="techtoggle" class="text-lg cursor-pointer flex items-center mt-5">Technik</label>
            <div class="pl-2">
                {#each presets as preset}
                    <CheckInput
                            id="preset_{preset.id}"
                            name="{preset.tech}"
                            on:change={(a) => {
                                    tech[preset.id] = a.detail.value
                                    tech = {...tech}
                                }}
                    />
                {/each}
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="uppercase mb-5 text-xl text-light_muted dark:text-dark_muted">Captcha</h2>
        <div class="px-2">
            <Captcha
                    bind:value={captcha}
                    bind:cid={captcha_id}
            />
        </div>

        <div class="px-2 mt-5">
            <span class="text-red-600 dark:text-red-400">{validationError}</span>
        </div>

        <div class="px-2 mt-5">
            <SubmitBtn
                    name="Buchen"
                    spinner={spinner}
                    disabled={!ok}
                    on:click={send}
            />
        </div>
    </div>
</div>
<br/>
<Footer/>

<style lang="postcss">
    #techtoggle:checked ~ div {
        @apply hidden;
    }

    #techtoggle + label::before {
        @apply pr-2 text-sm align-middle;
        content: "▼";
    }

    #techtoggle:checked + label::before {
        content: "▶";
    }
</style>