import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    base: '/', // Use root path since assets are being requested from root domain
    build: {
        outDir: 'public/build',
        assetsDir: 'assets',
    },
});
