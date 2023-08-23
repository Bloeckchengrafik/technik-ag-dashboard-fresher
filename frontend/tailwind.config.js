/** @type {import('tailwindcss').Config}*/
const config = {
    content: ["./src/**/*.{html,js,svelte,ts}", "./index.html"],
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                dark: "#2b2b2b",
                dark_fill: "#484848",
                light: "#f2f2f2",
                light_fill: "#d2cac7",
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
