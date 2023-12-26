<script lang="ts">
    import {apiToken} from "../api";
    import type {UserSpec, Permission} from "../api";

    export let requiredPermission: Permission | null = null;
    export let user: UserSpec | null = null;
    export let onlyLoggedOut: boolean = false;
    export let doRedirect = true;

    let jwt = $apiToken;

    (async () => {
        try {
            let payload = atob(jwt.split('.')[1])
            let verified = JSON.parse(payload)

            let exp = verified.exp
            if (exp < Date.now() / 1000) {
                // JWT is expired
                console.log("%c⚠️ %c JWT is expired", "font-weight: bold", "color: red; font-weight: normal")
                apiToken.set(null)
                if (doRedirect) window.location.href = '/#/login?redirect=' + encodeURIComponent(window.location.hash)
                return
            }

            if (onlyLoggedOut) {
                // We're logged in, but we're not supposed to be
                if (doRedirect) window.location.href = '/#/dash'
                user = null
                return
            }

            user = verified.user as UserSpec

            console.info(`
%c🎓 %c Logged in as ${user.firstname} ${user.lastname}
%c┠ %c User ID: ${user.id}
%c┠ %c User Email: ${user.email}
%c┠ %c User Groups: ${JSON.stringify(user.groups)}
%c┖ %c User Permissions: ${JSON.stringify(user.permission)}

`,
                "font-weight: bold", "color: lime; font-weight: normal",
                "font-weight: bold", "font-weight: normal",
                "font-weight: bold", "font-weight: normal",
                "font-weight: bold", "font-weight: normal",
                "font-weight: bold", "font-weight: normal"
            )

            if (requiredPermission && !user.permission.includes(requiredPermission)) {
                console.log("%c⚠️ %c User does not have required permission " + requiredPermission, "font-weight: bold", "color: red; font-weight: normal")
                if (doRedirect) window.location.href = '/#/login?redirect=' + encodeURIComponent(window.location.hash)
                return
            }

        } catch (e) {
            user = null
            // Not a valid JWT - so we're not logged in
            if (onlyLoggedOut) {
                return // We're not logged in, and we're not supposed to be
            }

            console.log("%c⚠️ %c Invalid JWT", "font-weight: bold", "color: red; font-weight: normal")

            // We're not logged in, but we're supposed to be
            apiToken.set(null)
            if (doRedirect) window.location.href = '/#/login?redirect=' + encodeURIComponent(window.location.hash)
            return
        }
    })()
</script>