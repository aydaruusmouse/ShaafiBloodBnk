import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    base: '/hargeisa/', // Match your subdirectory path
    build: {
        outDir: 'public/build',
        assetsDir: 'assets',
    },
});
