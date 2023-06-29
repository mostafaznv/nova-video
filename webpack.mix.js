let mix = require('laravel-mix')

mix.js('resources/js/field.js', 'dist/js')
    .sass('resources/sass/field.scss', 'dist/css')
    .vue({version: 3})
    .webpackConfig({
        externals: {
            vue: 'Vue',
            'laravel-nova': 'LaravelNova'
        },
        resolve: {
            symlinks: false
        }
    })
