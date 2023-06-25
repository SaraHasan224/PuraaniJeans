const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/admin.js', 'public/js');
mix.sass('resources/css/init.scss', 'public/css');
mix.css('resources/css/main.css', 'public/css');
    // .js('resources/js/admin.js', 'public/js/admin.js');
mix.version();

// mix.scripts([
//     paths.res + 'js/custom/main.js',
//     // paths.res + 'js/custom/helpers/*.js',
//     // paths.res + 'js/custom/features/*.js'
// ], 'public/js/master.js').version();

// mix.styles([
//     paths.res + 'css/main.css'
// ], 'public/css/master.css').version();
