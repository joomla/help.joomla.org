const mix = require('laravel-mix');

// Configure base path for mix stuff going to web
mix.setPublicPath('www/media/');

// Configure base path for media assets
mix.setResourceRoot('/media/');

// Core app CSS
mix
    .sourceMaps()
    .sass('assets/scss/help.scss', 'css')
    .options({
        postCss: [
            require('autoprefixer')()
        ]
    })
;

// Version assets
mix.version();
