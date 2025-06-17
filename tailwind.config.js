/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        './vendor/filament/**/*.blade.php', // Tambahkan ini jika menggunakan Filament
        './vendor/laravel/**/*.blade.php', // Tambahkan ini
    ],
    theme: {
        extend: {
            colors: {
                'yellow-custom': '#FFD700',
                'black-custom': '#1A1A1A',
                'white-custom': '#FFFFFF',
            },
        },
    },
    plugins: [],
};
