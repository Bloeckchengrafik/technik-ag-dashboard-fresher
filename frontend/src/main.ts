import "./app.postcss";
import App from "./App.svelte";

function doDarkMode() {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
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

const app = new App({
    target: document.getElementById("app"),
});

export default app;
