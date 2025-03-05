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

 const glob = require('glob');

 glob.sync('resources/scss/*.scss').map(function (file) {
   mix.sass(file, 'public/css').options({
     processCssUrls: false,
   });
 });

 glob.sync('resources/js/*.js').map(function (file) {
   mix.js(file, 'public/js');
 });
