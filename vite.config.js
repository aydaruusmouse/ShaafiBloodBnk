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

console.log('Vite config:', {
    base: '/',
    build: {
        outDir: 'public/build',
        assetsDir: 'assets',
    },
});
console.log('Environment:', process.env.NODE_ENV);
console.log('Base:', process.env.BASE_URL);
console.log('App URL:', process.env.APP_URL);
console.log('App Name:', process.env.APP_NAME);
console.log('App Environment:', process.env.APP_ENV);
console.log('App Debug:', process.env.APP_DEBUG);
console.log('App URL:', process.env.APP_URL);
console.log('App Name:', process.env.APP_NAME);
console.log('App Environment:', process.env.APP_ENV);
console.log('App Debug:', process.env.APP_DEBUG);