import "./app.postcss";
import App from "./App.svelte";

function doDarkMode() {
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }

    if (location.hash === "" || location.hash === "#" || location.hash === "#/") {
        document.documentElement.classList.add('dark'); // force dark mode
    }
}

doDarkMode();

// when url hash changes
window.addEventListener('hashchange', () => {
    doDarkMode();
});

function warnConsole() {
    console.warn("%cWarte", "font-size: 20px; font-weight: bold;");
    console.warn("%cFalls dich jemand dazu aufgefordert hat, etwas zu kopieren und hier einzufügen, handelt es sich in 11 von 10 Fällen um einen Betrugsversuch.", "font-size: 16px;");
    console.warn("%cEtwas hier einzufügen könnte Angreifern Zugriff auf deinen Account geben.", "font-size: 16px;");
    console.warn("%cWenn du nicht ganz genau weißt, was du da tust, dann schließe das Fenster und bleib auf der sicheren Seite.", "font-size: 16px;");
}

setInterval(() => {
    warnConsole();
}, 5000);
warnConsole()

const app = new App({
    target: document.getElementById("app"),
});

export default app;
