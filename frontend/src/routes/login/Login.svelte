<script lang="ts">
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import AuthLayout from "../../lib/Forms/AuthLayout.svelte";
    import Footer from "../../lib/Footer.svelte";
    import SubmitBtn from "../../lib/Forms/SubmitBtn.svelte";
    import SubQuestionLink from "../../lib/Forms/SubQuestionLink.svelte";
    import TextInput from "../../lib/Forms/TextInput.svelte";
    import {apiPost, apiToken} from "../../api";
    import {querystring} from "svelte-spa-router";
    import {help} from "tailwindcss/src/oxide/cli/help";
    import {currentTab} from "../../stores";

    let error = ''
    let enabled = false
    let loading = false

    let email: string
    let password: string

    $: enabled = (!!email) && (!!password) &&
        /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(email) &&
        !loading

    $currentTab = "login";
</script>
<AuthGuard onlyLoggedOut={true}/>
<AuthLayout title="Anmelden">
    <form class="px-2" on:submit|preventDefault={async () => {
        console.log("%c ✳️ Login form submitted", "color: #00ff00");
        loading = true
        error = ''
        let data = await apiPost("/v1/login/login.php", {
            email,
            password
        }).then(res => res.json())

        if (data.error) {
            error = data.error
            loading = false
        } else {
            $apiToken = data.jwt
            let query = new URLSearchParams($querystring)
            if (query.has("redirect") && !query.get("redirect").includes(".")) {
                console.log("%c ✳️ Redirecting to " + query.get("redirect"), "color: #00ff00");
                window.location.href = query.get("redirect")
            } else window.location.href = "/#/dash"
        }
    }}>
        <TextInput
                id="email"
                name="E-Mail"
                type="email"
                bind:value={email}
        />

        <TextInput
                id="password"
                name="Passwort"
                type="password"
                bind:value={password}
        />

        <div class="text-center lg:text-left w-full">
            <p class="text-red-500 text-sm">{error}</p>
        </div>
        <br />

        <div class="text-center lg:text-left w-full">
            <SubmitBtn disabled={!enabled} spinner={loading}/>

            <SubQuestionLink
                    title="Hast du noch kein Konto?"
                    cta="Registrieren"
                    ctaUrl="register"
            />
            <SubQuestionLink
                    title="Passwort vergessen?"
                    cta="Zurücksetzen"
                    ctaUrl="reset-password"
            />
        </div>
    </form>
</AuthLayout>
<Footer/>