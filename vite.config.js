import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/modules/users/users.index.js',
                'resources/js/modules/users/users.form.js',
                'resources/js/modules/availability/availability.form.js',
                'resources/js/modules/schedule/schedule.create.js',
                'resources/js/modules/schedule/schedule.list.js',
            ],
            refresh: true,
        }),
    ],
});
