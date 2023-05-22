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
    'plugins': './resources/plugins/',
    'intl_tel_input': './node_modules/intl-tel-input/',
    'swal': './node_modules/sweetalert/',
    'owl': './node_modules/owl.carousel/dist/',
    'bootstrap_tagsinput': './node_modules/bootstrap-tagsinput/',
    'dist': './resources/dist/',
    // 'custom': './resources/js/',
};

//mix.js('resources/js/app.js', 'public/js')
// .postCss('resources/css/app.css', 'public/css', [
//     //
// ]);

// mix.sass('resources/sass/*', 'public/css/master.scss');
mix.styles([
    paths.plugins + 'fontawesome-free/css/all.min.css',
    paths.plugins + 'tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css',
    paths.plugins + 'datatables/css/jquery.dataTables.min.css',
    paths.plugins + 'datatables/css/select.dataTables.min.css',
    paths.plugins + 'datatables/css/buttons.dataTables.min.css',
    paths.plugins + 'datatables/css/responsive.dataTables.min.css',
    paths.plugins + "datatables-rowreorder/css/rowReorder.bootstrap4.min.css",
    paths.plugins + 'icheck-bootstrap/icheck-bootstrap.min.css',
    paths.plugins + 'overlayScrollbars/css/OverlayScrollbars.min.css',
    paths.plugins + 'daterangepicker/daterangepicker.css',
    paths.plugins + 'bootstrap-colorpicker/css/bootstrap-colorpicker.min.css',
    paths.plugins + 'summernote/summernote-bs4.css',
    paths.plugins + 'bootstrap4-duallistbox/bootstrap-duallistbox.min.css',
    paths.plugins + "croppie/croppie.css",
    paths.intl_tel_input + "/build/css/intlTelInput.css",
    paths.bootstrap_tagsinput + "/dist/bootstrap-tagsinput.css",
    paths.dist + 'css/sweetalert.min.css',
    // paths.dist + 'css/adminlte.min.css',
    paths.dist + 'css/select2.min.css',
    paths.dist + 'css/jquery-ui.css',
    paths.owl + 'assets/owl.carousel.min.css',
    paths.dist + 'css/custom.css',
    paths.dist + 'css/bootstrap-side-modals.css',
    
    paths.res + 'css/main.css'
], 'public/css/master.css').version();

mix.scripts([
    paths.plugins + "jquery/jquery.min.js",
    paths.plugins + "jquery-ui/jquery-ui.min.js",
    paths.plugins + "bootstrap/js/bootstrap.bundle.min.js",
    paths.plugins + "sweetalert2/sweetalert2.all.js",
    paths.plugins + "sweetalert2/sweetalert2.js",
    paths.plugins + "sweetalert2/sweetalert2.min.js",
    paths.plugins + "bootstrap-colorpicker/js/bootstrap-colorpicker.min.js",
    paths.plugins + "datatables/jquery.dataTables.min.js",
    paths.plugins + "datatables/dataTables.responsive.min.js",
    paths.plugins + "datatables/dataTables.select.min.js",
    paths.plugins + "datatables/dataTables.buttons.min.js",
    // paths.plugins + "datatables-rowreorder/js/dataTables.rowReorder.js",
    // paths.plugins + "datatables-bs4/js/dataTables.bootstrap4.min.js",
    // paths.plugins + "datatables-responsive/js/dataTables.responsive.min.js",
    // paths.plugins + "datatables-responsive/js/responsive.bootstrap4.min.js",
    paths.plugins + "moment/moment.min.js",
    paths.intl_tel_input  + "/build/js/intlTelInput.js",
    paths.plugins + "daterangepicker/daterangepicker.js",
    paths.plugins + "tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js",
    paths.plugins + "overlayScrollbars/js/jquery.overlayScrollbars.min.js",
    paths.plugins + "bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js",
    paths.plugins + "bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js",
    paths.plugins + 'summernote/summernote-bs4.min.js',
    paths.plugins + "croppie/croppie.js",
    paths.dist + "js/repeater.js",
    paths.dist + "js/jquery.validate.min.js",
    paths.dist + "js/jquery.inputmask.bundle.min.js",
    // paths.ionicons + 'dist/js/ionicons.js',
    // paths.custom + 'modules/alerts.js',
    paths.owl + 'owl.carousel.min.js',

    paths.res + 'js/custom/main.js',
    paths.res + 'js/custom/helpers/*.js',
    paths.res + 'js/custom/features/*.js'
], 'public/js/master.js').version();
