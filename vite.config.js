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
                "resources/css/web/web.css",
                "resources/js/web/web.js",
                "resources/css/cliente/cliente.css",
                "resources/js/cliente/cliente.js",
                "resources/css/erp/entregafest/invitacion.css",
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
