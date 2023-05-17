const mix = require('laravel-mix')
const path = require('path')

mix.setPublicPath('dist')
    .js('resources/js/field.js', 'js')
    .sass('resources/sass/field.scss', 'css')
    .vue({version: 3})
    .webpackConfig({
        externals: {
            vue: 'Vue',
        },
        resolve: {
            symlinks: false
        }
    })
    .alias({
        'laravel-nova': path.join(
            __dirname,
            '../../../vendor/laravel/nova/resources/js/mixins/packages.js'
        )
    })
