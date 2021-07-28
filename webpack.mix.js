// Laravel Mix.
let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
// Add Sass globbing
mix.webpackConfig({
  node: {
    fs: "empty"
  },
  resolve: {
    extensions: ['.js', '.vue'],
    mainFiles: ['index'],
    alias: {
      'Components': __dirname + '/resources/js/components',
      'Tools': __dirname + '/resources/js/components/tools',
      'Store': __dirname + '/resources/js/store',
      'Mixins': __dirname + '/resources/js/mixins',
      'Helpers': __dirname + '/resources/js/helpers',
      'Placeholders': __dirname + '/resources/js/placeholders',
    }
  },
  module: {
    rules: [
      {
        test: /\.scss$/,
        loader: 'import-glob-loader'
      },
    ]
  },
});

mix.js('resources/js/app.js', 'public/js')
  .sass('resources/sass/app.scss', 'public/css');


// Extract vendor libraries so that when we update components users dont need to download
// these libraries.
if (process.env.NODE_ENV !== 'testing') {
  mix.extract(['vue', 'jquery']);
}

/**
 * This enables debugging Vue.js components inchrome's inspector.
 */
if (process.env.NODE_ENV === 'development') {
  mix.webpackConfig({ devtool: "inline-source-map" });
}
