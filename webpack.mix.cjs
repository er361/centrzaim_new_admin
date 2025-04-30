const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */


const applications = [
    'miazaim',
    // 'mikrozaymi24',
    // 'malinazaim',
    // 'srochnozaym',
    // 'lovizaem',
    // 'zaempodrukoi',
];

// applications.forEach((application) => {
    // mix.sass(`resources/assets/projects/${application}/saas/app.scss`, `assets/${application}/css/app.css`)
        // .copy(`resources/assets/projects/${application}/imgs`, `public_html/assets/${application}/imgs`)
        // .copy(`resources/assets/projects/${application}/fonts`, `public_html/assets/${application}/fonts`);
// })

mix.setPublicPath('public_html')
    .js('resources/assets/js/app.js', 'public_html/assets/js')
    .copy('resources/assets/js/libs', 'public_html/assets/js/libs')
    .version()
    .options({
        processCssUrls: false
    })
    .webpackConfig({
        output: {
            chunkFilename: "assets/js/[id].[hash].bundle.js",
        },
    }).sourceMaps(false, 'source-map');
;
