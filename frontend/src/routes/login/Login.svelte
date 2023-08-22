<script lang="ts">
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import AuthLayout from "../../lib/AuthLayout/AuthLayout.svelte";
    import Footer from "../../lib/Footer.svelte";
    import SubmitBtn from "../../lib/AuthLayout/SubmitBtn.svelte";
    import SubQuestionLink from "../../lib/AuthLayout/SubQuestionLink.svelte";
    import TextInput from "../../lib/AuthLayout/TextInput.svelte";
    import {apiPost, apiToken} from "../../api";

    let error = ''
    let enabled = false
    let loading = false

    let email: string
    let password: string

    $: enabled = (!!email) && (!!password) &&
        /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(email) &&
        !loading

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
            window.location.href = "/#/dash"
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
                    ctaUrl="login"
            />
        </div>
    </form>
</AuthLayout>
<Footer/>