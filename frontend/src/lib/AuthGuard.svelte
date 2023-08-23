<script lang="ts">
    import {apiToken} from "../api";
    import type {UserSpec, Permission} from "../api";
    import * as jose from 'jose'

    export let requiredPermission: Permission | null = null;
    export let user: UserSpec | null = null;
    export let onlyLoggedOut: boolean = false;
    export let doRedirect = true;

    let jwtPubKey = import.meta.env.VITE_BACKEND_JWT_PUBKEY
    let jwt = $apiToken;

    (async () => {
        let pubKey = await jose.importSPKI(jwtPubKey, 'RS256')

        try {
            let verified = await jose.jwtVerify(jwt, pubKey, {
                algorithms: ['RS256']
            })

            if (onlyLoggedOut) {
                // We're logged in, but we're not supposed to be
                if (doRedirect) window.location.href = '/#/dash'
                user = null
                return
            }

            user = verified.payload.user as UserSpec

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