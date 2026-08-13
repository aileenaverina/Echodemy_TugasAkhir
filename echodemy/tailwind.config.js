import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                display: ["Fredoka", "sans-serif"],
                body: ["Inter", "sans-serif"],
            },
            colors: {
                cream: "#F7F2E9",
                ink: "#1C1A17",
                amber: "#E8A84C",
                coral: "#E96E4F",
                teal: "#3E8E82",
                periwinkle: "#7B87C9",
            },
        },
    },

    plugins: [forms],
};
