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
            'resources/js/checkout.js',
            'resources/js/order.js',
            'resources/js/user.js',
            'resources/js/chat.js',
            'resources/js/notification.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
