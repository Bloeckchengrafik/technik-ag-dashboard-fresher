import Home from "./routes/Home.svelte";
import NotFound from "./routes/catchers/NotFound.svelte";
import Status from "./routes/status/Status.svelte";
import Register from "./routes/register/Register.svelte";
import Login from "./routes/login/Login.svelte";
import ResetPassword from "./routes/reset_password/ResetPassword.svelte";
import Book from "./routes/book/Book.svelte";
import Event from "./routes/event[id]/Event.svelte";
import Dash from "./routes/dash/Dash.svelte";

export const routes = {
    "/": Home,
    "/status": Status,
    "/register": Register,
    "/login": Login,
    "/reset-password": ResetPassword,
    "/book": Book,
    "/event/:id": Event,
    "/dash": Dash,
    "*": NotFound
}