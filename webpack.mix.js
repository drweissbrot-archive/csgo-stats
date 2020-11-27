const mix = require('laravel-mix')

mix.js('resources/js/app.js', 'public/js')
.stylus('resources/stylus/app.styl', 'public/css')
.copy('resources/img/*', 'public/images')
.copy('resources/img/weapons/*', 'public/images')
.copy('resources/img/gamemodes/*', 'public/images')

if (mix.inProduction()) {
	mix.version()
}
