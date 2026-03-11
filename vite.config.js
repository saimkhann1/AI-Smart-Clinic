// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path'; // <--- Yeh line main ne add ki hai

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js', // Is path ko absolute nahi '/' se shuru nahi hona chahiye
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    // <--- YEH Pura SECTION MAIN NE ADD KIYA HAI setup @ alias for path resolution
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'), // Force explicit path
        },
    },
});