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
                cream: "#FBF7EF",
                cream2: "F3EDE0",
                cream3:"B9B4A5",
                ink: "#1C1A17",
                amber: "#F5A524",
                coral: "#E8674A",
                teal: "#2F9E8F",
                periwinkle: "#6C7BC2",
                hitam:"22252B",
                hitam2: "5B5A55",
                putih: "#FFFFFF",
                border:"E7E0D2",
            },
        },
    },

    plugins: [forms],
};
