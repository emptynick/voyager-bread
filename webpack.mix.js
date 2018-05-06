let mix = require('laravel-mix');

mix.sass('resources/assets/scss/app.scss', 'publishable/assets/css').js('resources/assets/js/app.js', 'publishable/assets/js').setPublicPath('publishable');
