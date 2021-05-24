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

mix.js('resources/js/app.js', 'public/assets/js')
    .sass('resources/sass/about.scss', 'public/assets/css')
    .sass('resources/sass/affirmations.scss', 'public/assets/css')
    .sass('resources/sass/auth.scss', 'public/assets/css')
    .sass('resources/sass/app.scss', 'public/assets/css')
    .sass('resources/sass/goals.scss', 'public/assets/css')
    .sass('resources/sass/habits.scss', 'public/assets/css')
    .sass('resources/sass/home.scss', 'public/assets/css')
    .sass('resources/sass/journal.scss', 'public/assets/css')
    .sass('resources/sass/profile.scss', 'public/assets/css')
    .sass('resources/sass/support.scss', 'public/assets/css')
    .sass('resources/sass/todo.scss', 'public/assets/css')
    .sass('resources/sass/email/default.scss', '../resources/views/vendor/mail/html/themes/default.css');

if (mix.inProduction())
{
    mix.version();
}
