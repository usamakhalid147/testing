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

mix.webpackConfig(webpack => {
    return {
        plugins: [
            new webpack.ProvidePlugin({
                Vue: 'vue',
                $: 'jquery',
                jQuery: 'jquery',
            }),
        ]
    };
});

mix.setResourceRoot('../');

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .scripts([
        'resources/js/common.js',
    ], 'public/js/common.js')
    .js('resources/js/admin_app.js', 'public/admin_assets/js')
    .sass('resources/sass/admin_app.scss', 'public/admin_assets/css')
    .copyDirectory('resources/js/admin','public/admin_assets/js')
	.js('resources/js/host_app.js', 'public/host_assets/js')
	.sass('resources/sass/host_app.scss', 'public/host_assets/css')
    .vue();
