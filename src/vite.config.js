import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import symfonyPlugin from "vite-plugin-symfony";
import tailwindcss from '@tailwindcss/vite';
import path from 'path';

export default defineConfig({
    plugins: [
        vue(),
        symfonyPlugin(),
        tailwindcss()
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'assets/js'),
        },
    },
    server: {
        host: true,
        port: 5173,
        strictPort: true,
        cors: true,
        origin: 'http://localhost:5173',
        hmr: {
            host: 'localhost',
            port: 5173,
        },
    },
    build: {
        rollupOptions: {
            input: {
                app: "./assets/js/app.js",
            },
        }
    },
});