/** @type {import('tailwindcss').Config}*/
const config = {
    content: ["./src/**/*.{html,js,svelte,ts}", "./index.html"],
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                dark: "#2b2b2b",
                dark_fill: "#484848",
                light: "#e7e9ef",
                light_fill: "#f5f5f5",
                light_muted: "#5e5e5e",
                dark_muted: "#c9c9c9",
                primary: "#d16a51",
                primary_highlight: "#e7765b",
                secondary: "#d196d2",
                secondary_highlight: "#e1aee1",
                tertiary: "#45ab95",
                tertiary_highlight: "#5cc1a8",
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
