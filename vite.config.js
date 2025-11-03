import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/product.js',
                'resources/js/cart.js',
                'resources/css/menu.css',
                'resources/js/giftcard.js',
                'resources/js/checkout.js'],

            refresh: true,
        }),
        tailwindcss(),
    ],
});
