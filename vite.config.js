import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';


export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/erp/erp.css',
                'resources/js/erp/erp.js',
            ],
            refresh: true,
        }),

    ],
    server: {
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
