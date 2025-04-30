import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    base: '/build',
    plugins: [
        laravel({
            input: [
                'resources/assets/projects/miazaim/css/app.css',
                'resources/assets/projects/miazaim/js/app.js',
                'resources/assets/projects/miazaim/js/lib/lib.js',
                'resources/assets/projects/miazaim/js/app.jsx',
                'resources/assets/js/app.js'
            ],
            refresh: true,
            hotFile: 'public_html/hot',
        }),
    ],
    build: {
        outDir: 'public_html/build',
        assetsDir: 'assets',
        rollupOptions: {
            output: {
                assetFileNames: (assetInfo) => {
                    if (/\.(woff2?|ttf|eot|otf)$/i.test(assetInfo.name)) {
                        return 'assets/fonts/[name].[hash][extname]';
                    }
                    return 'assets/[name].[hash][extname]';
                },
            },
        },
    },
    resolve: {
        alias: {
            '@': '/resources/assets/projects/miazaim/js',
        },
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
        cors: {
            origin: '*',
            methods: ['GET', 'POST', 'PUT', 'DELETE'],
            allowedHeaders: ['Content-Type', 'Authorization'],
        },
        proxy: {
            // '/assets/miazaim/fonts': {
            //     target: 'http://localhost:80', // Laravel работает на порту 80
            //     changeOrigin: true,
            //     rewrite: (path) => path.replace(/^\/assets\/miazaim\/fonts/, '/assets/miazaim/fonts'),
            // },
            '/assets/miazaim/imgs': {
                target: 'http://localhost',
                rewrite: (path) => path.replace(/^\/assets\/miazaim\/imgs/, '/assets/miazaim/imgs'),
            },
        }
    },
});
