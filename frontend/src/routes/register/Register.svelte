<script lang="ts">
    import Footer from "../../lib/Footer.svelte";
    import AuthLayout from "../../lib/AuthLayout/AuthLayout.svelte";
    import TextInput from "../../lib/AuthLayout/TextInput.svelte";
    import SubmitBtn from "../../lib/AuthLayout/SubmitBtn.svelte";
    import SubQuestionLink from "../../lib/AuthLayout/SubQuestionLink.svelte";
    import {apiPost, apiToken} from "../../api";
    import AuthGuard from "../../lib/AuthGuard.svelte";

    let goOn = false;
    let goOnSpinner = false;
    let goOnText = "Weiter";
    $: goOnSpinner = (currentAction == "email" && email && email.length > 0) ||
        (currentAction == "name" && firstname && firstname.length > 0 && lastname && lastname.length > 0);

    let email: string
    let firstname: string
    let lastname: string
    let code: string
    let password: string
    let passwordRepeat: string
    let tutor: string
    let year: string

    let error: string

    let nextAction: "name" | "verify" | "finish" | "attachStudentStuff" = "name"
    let currentAction: "email" | "name" | "verify" | "finish" = "email"

    $: error = currentAction.substring(0, 0) // When the user switches to the next action, the error should be reset

    let userDataCache = {}

    async function emailValidate(email: string) {
        if (currentAction !== "email") return;
        // Check if the email is already registered
        if (email == null || email.length === 0) {
            goOn = false;
            goOnText = "Weiter";
            return;
        }

        // validate email
        if (/^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(email) === false) {
            goOn = false;
            goOnSpinner = false;
            goOnText = "Weiter";
            return;
        }

        let {userExists, needsCompleteRegistration}: {
            userExists: boolean
            needsCompleteRegistration: boolean
        } = await apiPost("v1/login/register_checkmail.php", {
            email
        }).then(r => r.json())

        if (userExists && !needsCompleteRegistration) {
            goOn = false;
            goOnSpinner = false;
            goOnText = "Du hast bereits ein Konto";
        } else if (userExists && needsCompleteRegistration) {
            goOn = true;
            goOnSpinner = false;
            nextAction = "verify"
            goOnText = "Account vervollständigen";
        } else {
            goOn = true;
            goOnSpinner = false;
            nextAction = "name"
            goOnText = "Weiter";
        }
    }

    async function nameValidate(firstname: string, lastname: string) {
        if (currentAction !== "name") return;
        console.log("nameValidate", currentAction, firstname, lastname)
        if (firstname == null || firstname.length === 0 || lastname == null || lastname.length === 0) {
            goOn = false;
            goOnSpinner = false;
            goOnText = "Weiter";
            return;
        }

        goOn = true;
        goOnSpinner = false;
        goOnText = "Weiter";
    }

    async function codeValidate(code: string, password: string, passwordRepeat: string) {
        if (currentAction !== "verify") return;
        if (code == null || code.length !== 6) {
            goOn = false;
            goOnSpinner = false;
            goOnText = "Weiter";
            return;
        }

        if (password == null || password.length < 8) {
            goOn = false;
            goOnSpinner = false;
            goOnText = "Passwort zu kurz"
            return;
        }

        if (passwordRepeat != password) {
            goOn = false;
            goOnSpinner = false;
            goOnText = "Passwörter stimmen nicht überein"
            return;
        }

        goOn = true;
        goOnSpinner = false;
        goOnText = "Weiter";
    }

    async function finishValidate(tutor: string, year: number) {
        if (currentAction !== "finish") return;
        if (tutor == null || tutor.length === 0 || year == null || year < 5 || year > 13) {
            goOn = false;
            goOnSpinner = false;
            goOnText = "Weiter";
            return;
        }

        goOn = true;
        goOnSpinner = false;
        goOnText = "Weiter";
    }

    // debounce
    let timeout = null;
    $: {
        if (timeout) clearTimeout(timeout);
        timeout = setTimeout(async () => {
            await emailValidate(email)
            await nameValidate(firstname, lastname)
            await codeValidate(code, password, passwordRepeat)
            await finishValidate(tutor, parseInt(year))
        }, 500);
    }

    function toName() {
        currentAction = "name"
        nextAction = "verify"
        userDataCache["email"] = email
        console.log(userDataCache)
        goOnSpinner = false;
        goOn = false;
        goOnText = "Weiter"
    }

    async function toVerify() {
        userDataCache["firstname"] = firstname
        userDataCache["lastname"] = lastname

        if (currentAction === "email") {
            // Resend email
            await apiPost("v1/login/register_resend_confirmation.php", {
                email: email,
            }).then(r => r.json())
        } else {
            // Register user
            let result = await apiPost("v1/login/register.php", {
                ...userDataCache,
            }).then(r => r.json())

            if (result.error) {
                error = result.error
                return;
            }
        }

        currentAction = "verify"
        nextAction = "finish"
        goOnSpinner = false;
        goOn = false;
        goOnText = "Weiter"
    }

    async function toFinish() {
        // Finish registration
        let result = await apiPost("v1/login/register_finish.php", {
            email: email,
            key: code,
            password: password,
        }).then(r => r.json())

        if (result.error) {
            error = result.error
            return;
        } else {
            apiToken.set(result.jwt)
        }

        currentAction = "finish"
        nextAction = "attachStudentStuff"
        goOnSpinner = false;
        goOn = false;
        goOnText = "Weiter"
    }

    async function toAttachStudentStuff() {
        // Attach student stuff
        let result = await apiPost("v1/login/register_attach_studentinfo.php", {
            tutor: tutor,
            year: parseInt(year)
        }).then(r => r.json())

        if (result.error) {
            error = result.error
            return;
        }

        window.location.href = "/#/dash"
    }

</script>
<AuthGuard onlyLoggedOut={true} />
<AuthLayout
        title="Registrieren"
>
    <form class="px-2" on:submit|preventDefault={async () => {
        switch (nextAction) {
            case "name":
                toName();
                break;
            case "verify":
                await toVerify();
                break;
            case "finish":
                await toFinish();
                break;
            case "attachStudentStuff":
                await toAttachStudentStuff();
                break;
        }
    }}>
        {#if currentAction === "email"}
            <TextInput
                    id="email"
                    name="Email-Adresse"
                    type="email"
                    bind:value={email}
            />
        {:else if currentAction === "name"}
            <TextInput
                    id="firstname"
                    name="Vorname"
                    type="text"
                    bind:value={firstname}
            />
            <TextInput
                    id="lastname"
                    name="Nachname"
                    type="text"
                    bind:value={lastname}
            />
        {:else if currentAction === "verify"}
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
                    id="passwordRepeat"
                    name="Passwort wiederholen"
                    type="password"
                    bind:value={passwordRepeat}
            />
        {:else if currentAction === "finish"}
            <p class="text-center lg:text-left max-w-[400px]">
                Dein Account wurde erfolgreich erstellt. Wenn du Schüler*in bist, kannst du hier noch deine Klasse
                und deinen Jahrgang angeben.
                <br/>
                <br/>
            </p>

            <TextInput
                    id="class"
                    name="Klassenlehrer*in / Tutor*in"
                    type="text"
                    bind:value={tutor}
            />

            <TextInput
                    id="year"
                    name="Jahrgang"
                    type="number"
                    bind:value={year}
            />

            <SubmitBtn name={"Ich bin kein*e Schüler*in"} href="/#/dash"/>
            <br />
            <br />
        {/if}


        <div class="text-center lg:text-left w-full">
            <p class="text-red-500 text-sm">{error}</p>
        </div>

        <div class="text-center lg:text-left w-full">
            <SubmitBtn name={goOnText} disabled={!goOn} spinner={goOnSpinner}/>

            <SubQuestionLink
                    title="Hast du schon ein Konto?"
                    cta="Anmelden"
                    ctaUrl="login"
            />
        </div>
    </form>
</AuthLayout>
<Footer/>
