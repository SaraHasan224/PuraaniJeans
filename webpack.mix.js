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

var paths = {
    // 'plugins': './resources/plugins/',
    'res': './resources/',
    // 'ionicons': './node_modules/ionicons/',
    // 'pusher': './node_modules/pusher-js/',
    // 'bootstrap_tagsinput': './node_modules/bootstrap-tagsinput/',
    // 'dom_to_iamge': './node_modules/dom-to-image/',
    // 'intl_tel_input': './node_modules/intl-tel-input/',
    // 'custom': './resources/js/',
    // 'inputSpinner': './node_modules/bootstrap-input-spinner/',
    // 'owl': './node_modules/owl.carousel/dist/',
};

//mix.js('resources/js/app.js', 'public/js')
// .postCss('resources/css/app.css', 'public/css', [
//     //
// ]);

mix.styles([
    paths.res + 'css/main.css'
], 'public/css/master.css').version();

mix.scripts([
    paths.res + 'scripts/main.js'
], 'public/js/master.js').version();
