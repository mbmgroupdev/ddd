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

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');

mix.styles([
    'public/assets/css/typography.css',
    'public/assets/css/style.css',
    'public/assets/css/bootstrap-float-label.min.css',
    'public/assets/css/toastr.min.css',
    'public/plugins/DataTables/datatables.css',
    //'public/assets/css/custom.css',
    //'public/assets/css/responsive.css',
], 'public/assets/css/all.css');

mix.scripts([
    'public/assets/js/smooth-scrollbar.js',
    'public/assets/js/notify/notify.js',
    'public/assets/js/select2.min.js',
    'public/assets/js/toastr.min.js',
    'public/plugins/DataTables/datatables.min.js',
], 'public/assets/js/all.js');
