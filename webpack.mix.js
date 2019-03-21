let mix = require('laravel-mix');

mix.sass('resources/assets/scss/styles.scss', 'resources/assets/dist')
.js('resources/assets/js/scripts.js', 'resources/assets/dist');
