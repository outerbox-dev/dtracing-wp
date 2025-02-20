<?php
/**
 * Adds project post type.
 *
 * @package QalaTheme\Classes
 */

namespace DtRacing;

class Vehicle_Taxonomy extends Qala_Abstract {

	/**
	 * Slug of the taxonomy
	 *
	 * @var string
	 */
	public const SLUG = 'product-vehicle';

	/**
	 * Set up our hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_vehicle_type_taxonomy' ], 10 );
	}

	/**
	 * Register the Vehicles Taxonomy.
	 *
	 * @return void
	 */
	public function register_vehicle_type_taxonomy() {
		$labels = array(
			'name'              => esc_html__( 'Vehicles', 'dtracing' ),
			'singular_name'     => esc_html__( 'Vehicle', 'dtracing' ),
			'search_items'      => esc_html__( 'Search Vehicle', 'dtracing' ),
			'all_items'         => esc_html__( 'All Vehicles', 'dtracing' ),
			'parent_item'       => esc_html__( 'Parent Vehicle', 'dtracing' ),
			'parent_item_colon' => esc_html__( 'Parent Vehicle', 'dtracing' ),
			'edit_item'         => esc_html__( 'Edit Vehicle', 'dtracing' ),
			'update_item'       => esc_html__( 'Update Vehicle', 'dtracing' ),
			'add_new_item'      => esc_html__( 'Add new Vehicle', 'dtracing' ),
			'new_item_name'     => esc_html__( 'New Vehicle', 'dtracing' ),
			'menu_name'         => esc_html__( 'Vehicles', 'dtracing' ),
		);

		$args = array(
			'hierarchical'          => true,
			'has_archive'           => true,
			'labels'                => $labels,
			'rewrite'               => array(
				'slug'         => self::SLUG,
				'with_front'   => true,
				'hierarchical' => true,
			),
			'show_in_nav_menus'     => true,
			'show_modelcloud'       => true,
			'show_admin_column'     => true,
			'show_in_rest'          => true,
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'rest_base'             => self::SLUG,
		);
		register_taxonomy( self::SLUG, array( 'product' ), $args );
	}
}
