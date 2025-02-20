<?php
/**
 * Setup theme
 *
 * @package Qala
 */

namespace DtRacing;

class Theme_Setup extends Qala_Abstract {

	/**
	 * Hooks
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'after_setup_theme', [ $this, 'setup_theme' ], 20 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_theme_assets' ], 20 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ], 10 );
		add_filter( 'woocommerce_short_description', [ $this, 'convert_to_p' ] );
	}

	/**
	 * Do the basic theme setup.
	 *
	 * @return void
	 */
	public function setup_theme() {
		$this->load_textdomain();
		$this->theme_supports();
		$this->image_sizes();
	}

	/**
	 * Load the themes text domain for translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_theme_textdomain( apply_filters( 'qala_text_domain', 'dtracing' ), get_stylesheet_directory() . '/languages' );
	}

	/**
	 * Setup the theme supports.
	 *
	 * @return void
	 */
	public function theme_supports() {
		// Uncomment to use the simple footer in Qala.
		//add_theme_support( 'qala_simple_footer' );
	}

	/**
	 * Register image sizes and modify some default sizes.
	 *
	 * @return void
	 */
	public function image_sizes() {
		set_post_thumbnail_size( 266, 178, true );
		add_image_size( 'thumbnail', 266, 178, true );
		add_image_size( 'archive', 268, 178, true );
		add_image_size( 'meta-og-image', 1200, 630, true );
	}

	/**
	 * Enqueue the themes .css and .js files.
	 *
	 * @return void
	 */
	public function enqueue_theme_assets() {
		$template_dir = get_stylesheet_directory();
		$template_uri = get_stylesheet_directory_uri();
		$main_css     = '/dist/css/main.css';
		$main_js      = '/dist/javascript/main.js';

		// Enqueue main css.
		wp_enqueue_style(
			'dtracing',
			$template_uri . $main_css,
			[],
//			self::get_asset_version( $template_dir . $main_css )
			'1.1'
		);

		// Google Maps API
		$google_maps_api_key = get_field( 'google_maps_api', 'options' );
		if ( $google_maps_api_key ) {
			wp_enqueue_script( 'google-maps-api', "https://maps.googleapis.com/maps/api/js?v=3&key=${google_maps_api_key}", [], 3, true );
		}

		// Make sure that we have the elastic-core-script as dependency if any plugin uses it.
		$dependencies = [ 'jquery', 'wp-hooks' ];
		if ( class_exists( \QalaElasticCore\PackageFactory::class ) ) {
			$dependencies[] = 'qala-elastic-core-script';
		}

		// Enqueue main js.
		wp_enqueue_script(
			'dtracing-main-scripts',
			$template_uri . $main_js,
			$dependencies,
			self::get_asset_version( $template_dir . $main_js ),
			true
		);

		// Enqueue WP core comment javascript if is on a single post and comments are enabled.
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		$vehicles = new Vehicles();
		wp_localize_script(
			'dtracing-main-scripts',
			__NAMESPACE__,
			[
				'vehicles'  => $vehicles->get_form_data(),
				'shop_page' => get_permalink( wc_get_page_id( 'shop' ) ),
				'ajaxurl'   => admin_url('admin-ajax.php')
			],
		);
	}

	/**
	 * Enqueue block editor assets.
	 *
	 * @return void
	 */
	public function enqueue_block_editor_assets() {
		$template_dir     = get_template_directory();
		$template_uri     = get_template_directory_uri();
		$block_editor_css = '/dist/css/main.css';

		// Enqueue main css.
		wp_enqueue_style(
			'dtracing',
			$template_uri . $block_editor_css,
			[],
//			self::get_asset_version( $template_dir . $block_editor_css )
			'1.2'
		);
	}

	/**
	 * Get asset modification time.
	 * Used for asset versioning that wont be cached in browser.
	 *
	 * @param $filepath
	 *
	 * @return string
	 */
	public static function get_asset_version( $filepath ) {
		// Bail early with an empty string if the file does not exist or is not a local asset.
		if ( ! file_exists( $filepath ) || false === stripos( $filepath, 'wp-content/themes' ) ) {
			return '';
		}

		return gmdate( 'YmdHi', filemtime( $filepath ) );
	}

	/**
	 * Strip HTML tags from string and convert to paragraphs.
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function convert_to_p( $content ) {
		return wpautop( wp_strip_all_tags( $content ) );
	}
}
