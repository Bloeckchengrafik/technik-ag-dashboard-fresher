/** @type {import('tailwindcss').Config}*/
const config = {
    content: ["./src/**/*.{html,js,svelte,ts}", "./index.html"],
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                dark: "#2b2b2b",
                dark_fill: "#484848",
                light: "#e5e5e5",
                light_fill: "#c7c7c7",
                primary: "#d16a51",
                secondary: "#d196d2",
                tertiary: "#45ab95",
            },
            spacing: {
                verylarge: "1000rem",
            }
        },
    },

    plugins: [
        require("@tailwindcss/typography"),
        require("tw-elements/dist/plugin.cjs"),
    ],
};

module.exports = config;
