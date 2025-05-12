import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    base: '/build',
    plugins: [
        laravel({
            input: [
                'resources/assets/projects/ctr/css/app.css',
                'resources/assets/projects/ctr/css/style.css',
                'resources/assets/projects/ctr/js/app.js',
                'resources/assets/projects/ctr/js/lib/lib.js',
                'resources/assets/projects/ctr/js/app.jsx',
                'resources/assets/projects/ctr/js/scripts.js',
                'resources/assets/js/app.js'
            ],
            refresh: true,
            hotFile: 'public_html/hot',
        }),
        react(),
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
            '@': '/resources/assets/projects/ctr/js',
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
            // '/assets/ctr/fonts': {
            //     target: 'http://localhost:80', // Laravel работает на порту 80
            //     changeOrigin: true,
            //     rewrite: (path) => path.replace(/^\/assets\/ctr\/fonts/, '/assets/ctr/fonts'),
            // },
            '/assets/ctr/imgs': {
                target: 'http://localhost',
                rewrite: (path) => path.replace(/^\/assets\/ctr\/imgs/, '/assets/ctr/imgs'),
            },
        }
    },
});
