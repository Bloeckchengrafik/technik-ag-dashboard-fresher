import Home from "./routes/Home.svelte";
import NotFound from "./routes/catchers/NotFound.svelte";
import Status from "./routes/status/Status.svelte";
import Register from "./routes/register/Register.svelte";

export const routes = {
    "/": Home,
    "/status": Status,
    "/register": Register,
    "*": NotFound
}