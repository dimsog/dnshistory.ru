import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    build: {
        sourcemap: true
    },
    plugins: [
        vue(),
        laravel({
            input: ['resources/assets/scss/app.scss', 'resources/assets/js/app.ts'],
            refresh: true,
        }),
    ],
});
