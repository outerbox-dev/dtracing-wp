const mix = require( 'laravel-mix' );
const path = require( 'path' );
const siteUrl = process.env.SITE_URL;
const publicPath = path.normalize( 'dist' );

// Require the eslint webpack plugin.
const ESLintPlugin = require( 'eslint-webpack-plugin' );

// Require laravel addon to handle Stylelint.
require( 'laravel-mix-stylelint' );

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 */
// Sets the relative path of the folder we build to.
mix.setPublicPath( publicPath );

// Aliases - Sets up aliases to simplify imports.
mix.alias( {
	'~normalize': path.join( __dirname, 'node_modules/normalize.css' ),
	'@qala': path.resolve( __dirname, '../qala-theme/src/sass' ),
	'@theme': __dirname,
	'~swiper': path.join( __dirname, 'node_modules/swiper' )
} );

// Autoload - Autoload jQuery so it's always available to all JavaScript files.
mix.autoload( {
	jquery: [ '$', 'window.jQuery' ],
} );

// Custom webpack configuration.
mix.webpackConfig( {
	// Externals - Load React and ReactDOM so we can use react dependent npm packages.
	externals: {
		react: 'React',
		'react-dom': 'ReactDOM',
	},
	plugins: [
		// Lint our JS files and attempt to fix issues automatically.
		new ESLintPlugin( {
			context: './src/javascript/',
			fix: true,
			overrideConfigFile: path.resolve( __dirname, '.eslintrc' ),
		} ),
	],
} );

mix.copyDirectory( 'src/fonts/', 'dist/fonts' );

// Javascript - main file.
mix.js( './src/javascript/main.js', 'dist/javascript' ).react();

// Sass - Main CSS file.
mix.sass( './src/sass/style.scss', '/dist/css/main.css' ).options( {
	processCssUrls: false,
} ).stylelint( {
	configFile: path.resolve( __dirname, '.stylelintrc' ),
	context: 'src/sass',
	failOnError: false,
	failOnWarning: false,
	quiet: false,
	fix: true,
} );

// Add sourcemaps with different approaches for dev and production.
mix.sourceMaps( true, 'source-map', 'source-map' );

if ( ! mix.inProduction() ) {
	// BrowserSync - Start a proxied hot reloading webserver at http://localhost:1337.
	mix.browserSync( {
		port: 1337,
		proxy: siteUrl,
		open: false,
		files: [
			'./dist/javascript/main.js',
			'./dist/css/main.css',
		],
	} );
} else {
	// Disable browser notifications in production.
	mix.disableNotifications();
	// Disable clearing of console in production. Makes it easier to see what happens.
	mix.options( {
		clearConsole: false,
	} );
}
