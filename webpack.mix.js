const mix = require('laravel-mix')

require('./nova.mix')

mix.setPublicPath('dist')
    .js('resources/js/field.js', 'js')
    .sass('resources/sass/field.scss', 'css')
    .vue({version: 3})
    .nova('mostafaznv/nova-video')

if (mix.inProduction()) {
    mix.version()
}
