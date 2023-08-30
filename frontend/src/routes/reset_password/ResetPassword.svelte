<script lang="ts">
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import AuthLayout from "../../lib/Forms/AuthLayout.svelte";
    import Footer from "../../lib/Footer.svelte";
    import SubmitBtn from "../../lib/Forms/SubmitBtn.svelte";
    import SubQuestionLink from "../../lib/Forms/SubQuestionLink.svelte";
    import TextInput from "../../lib/Forms/TextInput.svelte";
    import {apiPost, apiToken} from "../../api";
    import {currentTab} from "../../stores";

    let error = ''
    let enabled = false
    let verifying = false

    let loading = false
    let email: string

    let code: string
    let password: string
    let password2: string

    $: enabled = ((!verifying && (!!email) && /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(email)) ||
        (verifying && (!!code) && (!!password) && (!!password2) && (password === password2) && (password.length >= 8))) && !loading

    $currentTab = "login";
</script>
<AuthGuard onlyLoggedOut={true}/>
<AuthLayout title="Anmelden">
    <form class="px-2" on:submit|preventDefault={async () => {
        console.log("%c ✳️ Reset Password form submitted", "color: #00ff00");

        loading = true
        if (!verifying) {
            error = ''
            const res = await apiPost('v1/login/password_reset.php', {email}).then(res => res.json())
            loading = false
            if (res.error) {
                error = res.error
            } else {
                verifying = true
            }
        } else {
            error = ''
            const res = await apiPost('v1/login/password_reset_finish.php', {email, key: code, password}).then(res => res.json())
            loading = false
            if (res.error) {
                error = res.error
            } else {
                apiToken.set(res.jwt)
                window.location.href = '/#/dash'
            }
        }

    }}>
        {#if !verifying}
            <TextInput
                    id="email"
                    name="E-Mail"
                    type="email"
                    bind:value={email}
            />
        {:else}
            <p class="text-center lg:text-left max-w-[400px]">
                Wir haben dir eine E-Mail an <b>{email}</b> geschickt. Bitte gib den Code aus der E-Mail ein, um deine
                E-Mail-Adresse zu bestätigen.
                <br/>
                <br/>
            </p>
            <TextInput
                    id="code"
                    name="Code"
                    type="text"
                    bind:value={code}
            />

            <TextInput
                    id="password"
                    name="Passwort"
                    type="password"
                    bind:value={password}
            />

            <TextInput
                    id="password2"
                    name="Passwort wiederholen"
                    type="password"
                    bind:value={password2}
            />
        {/if}

        <div class="text-center lg:text-left w-full">
            <p class="text-red-500 text-sm">{error}</p>
        </div>
        <br/>

        <div class="text-center lg:text-left w-full">
            <SubmitBtn disabled={!enabled} spinner={loading}/>

            <SubQuestionLink
                    title="Zurück zum"
                    cta="Login"
                    ctaUrl="/login"
            />
        </div>
    </form>
</AuthLayout>
<Footer/>