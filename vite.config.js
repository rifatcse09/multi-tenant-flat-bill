import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/admin-dashboard.js',
            ],
            refresh: true,
        }),
    ],

    // Avoid ENOSPC on Linux when inotify max_user_watches is low (e.g. many projects under /var/www).
    // If dev feels slow, raise the limit instead: sudo sysctl fs.inotify.max_user_watches=524288
    server: {
        watch: {
            usePolling: true,
            interval: 300,
        },
    },
});
