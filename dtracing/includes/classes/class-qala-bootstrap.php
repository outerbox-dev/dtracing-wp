<?php
/**
 * Bootstrap file for our themes class-* files.
 *
 * @author: Jonathan de Jong, Richard Sweeney
 * @package Qala
 */

namespace DtRacing;

class Qala_Bootstrap {

	protected $inc_path;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->inc_path = apply_filters( 'dtracing_bootstrap_inc_path', trailingslashit( get_stylesheet_directory() ) . 'includes/classes/' );
	}

	/**
	 * Returns an array of all the files we want to bootstrap for the theme.
	 *
	 * @return array
	 */
	protected function get_files_list() : array {
		$files              = glob( $this->inc_path . 'class-*.php' );
		$bootstrap_filename = basename( __FILE__ );

		// Remove this class itself and the abstract class which our other classes extends.
		$files = array_filter(
			$files,
			function ( $file ) use ( $bootstrap_filename ) {
				if ( basename( $file ) === $bootstrap_filename ) {
					return false;
				}

				if ( false !== strpos( $file, 'class-qala-abstract.php' ) ) {
					return false;
				}

				return true;
			}
		);

		// Allow for custom folders in the directory.
		$extra_paths = apply_filters( 'dtracing_bootstrap_extra_paths', [], $this );

		if ( empty( $extra_paths ) ) {
			return apply_filters( 'dtracing_bootstrap_files', $files );
		}

		$extra_files = [];
		foreach ( $extra_paths as $extra_path ) {
			$extra_files = array_merge( $extra_files, glob( $this->inc_path . $extra_path . '/class-*.php' ) );
		}

		return apply_filters( 'dtracing_bootstrap_files', array_merge( $files, $extra_files ) );
	}

	/**
	 * Converts the class file name into a valid class name.
	 *
	 * @param string $file
	 * @return string
	 */
	protected function get_class_name_from_file( string $file ) : string {
		$file_name = basename( $file );

		// Example: class-my-hooks.php into Qala\My_Hooks
		$class_name = str_replace(
			[ 'class-', '-', '.php' ],
			[ '', '_', '' ],
			$file_name
		);

		return 'DtRacing\\' . ucwords( $class_name, '_' );
	}

	/**
	 * Used to require our abstract classes.
	 * New abstract classes must be added here.
	 *
	 * @return void
	 */
	public function require_abstracts() {
		require get_stylesheet_directory() . '/includes/classes/class-qala-abstract.php';
	}

	/**
	 * Used to require our interfaces.
	 * New interfaces must be added here.
	 *
	 * @return void
	 */
	public function require_interfaces() {

	}

	/**
	 * Boot the application
	 *
	 * @return void
	 */
	public function boot() : void {
		$files = $this->get_files_list();
		if ( empty( $files ) ) {
			return;
		}
		// require our abstract classes.
		$this->require_abstracts();
		$this->require_interfaces();

		foreach ( $files as $file ) {
			// We perform an extra check here because there might be files
			// added through the "qala_bootstrap_files" hook that no longer exists.
			if ( ! file_exists( $file ) ) {
				continue;
			}

			require $file;
			$class_name = $this->get_class_name_from_file( $file );
			new $class_name();
		}
	}
}

( new Qala_Bootstrap() )->boot();
