let mix = require('laravel-mix');

mix.sass('resources/assets/scss/styles.scss', 'publishable/assets/css')
.js('resources/assets/js/scripts.js', 'publishable/assets/js')
.setPublicPath('publishable');
