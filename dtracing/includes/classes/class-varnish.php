<?php
/**
 * Adds project post type.
 *
 * @package QalaTheme\Classes
 */

namespace DtRacing;

class Varnish extends Qala_Abstract {

	/**
	 * Set up our hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'ac_varnish_enhancements/filters/acf_field_groups_to_purge', [ $this, 'add_vehicle_acf_field_group' ] );
	}

	/**
	 * Add the vehicle ACF group (where we define ACF stuff) to the array so it causes the cache purge.
	 *
	 * @return array
	 */
	public function add_vehicle_acf_field_group( $groups ) {
		$groups[] = 'group_6422d67ecf2d1'; // site/public/wp-content/themes/dtracing/acf-json/group_6422d67ecf2d1.json
		return $groups;
	}
}
