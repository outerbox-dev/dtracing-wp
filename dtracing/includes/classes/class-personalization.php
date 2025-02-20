<?php
/**
 * Defines the personalization for selecting a vehicle.
 *
 * @package Qala
 */

namespace DtRacing;

class Personalization extends Qala_Abstract {

	public const VEHICLE = 'vehicle';

	/**
	 * Hooks
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'qala_elastic_core_personalizations', [ $this, 'define_vehicle_personalization' ] );
		add_filter( 'qala_elastic_core_add_personalization_to_document', [ $this, 'add_vehicle_data' ], 10, 4 );
		add_filter( 'qala_elastic_core_disable_personalization', [ $this, 'disable_personalization_on_specific_pages' ], 10, 1 );
	}

	/**
	 * Define the personalization / user vehicle (i.e. the saved Elasticsearch field names by which we filter all results), registered in Qala Elastic Core.
	 *
	 * @param array $personalizations
	 * @return void
	 */
	public function define_vehicle_personalization( $personalizations ) {
		$personalizations[ self::VEHICLE ] = [
			Vehicles::FIELD_MAKE,
			Vehicles::FIELD_MODEL,
			Vehicles::FIELD_YEAR,
			Vehicles::FIELD_TRIM,
		];

		return $personalizations;
	}

	/**
	 * Modifies the document (that gets sent to Elasticsearch index by Qala Elastic Core) so it includes all custom vehicle data fields (make, model, year, trim)
	 *
	 * @param array $value Value of personalization field (i.e. what's getting stored in Elasticsearch)
	 * @param array $doc Build document that's about to be sent to Elasticsearch. You can use this to query relevant information about the doc
	 *                   That can help you build $value correctly
	 * @param string $personalization_name Name of the personalization. This will be the key of the field that's stored in Elasticsearch
	 * @param callback $concat_method Method you should use to create the final string in a uniform way, by passing it values for each of the
	 *                 personalization fields as argument, in the order they're defined in.
	 * @return array
	 */
	public function add_vehicle_data( $value, $document, $personalization_name, $concat_function ) {

		// We only care about vehicle personalizations here.
		if ( $personalization_name !== self::VEHICLE ) {
			return $value;
		}

		// Only need to modify products.
		if ( ! isset( $document['object_type'], $document['object_id'] ) || $document['object_type'] !== 'product' ) {
			return $value;
		}

		$product_id = $document['object_id'];

		$product_vehicles = get_the_terms( $product_id, Vehicle_Taxonomy::SLUG );

		if ( ! is_array( $product_vehicles ) ) {
			return $value;
		}

		$vehicles = new Vehicles();
		foreach ( $product_vehicles as $product_vehicle_term ) {
			$data = $vehicles->get_vehicle_data( $product_vehicle_term );

			if ( ! isset( $data[ Vehicles::FIELD_YEARS ] ) ) {
				continue;
			}

			$make  = $data[ Vehicles::FIELD_MAKE ];
			$model = $data[ Vehicles::FIELD_MODEL ];
			$trim  = $data[ Vehicles::FIELD_TRIM ];

			foreach ( $data[ Vehicles::FIELD_YEARS ] as $year ) {
				$value[] = $concat_function( $make, $model, $year, $trim );
			}
		}

		return $value;
	}

	/**
	 * Disables personalization on specific pages, as selected in ACF (Qala Theme Settings -> Qala Elastic Filters).
	 *
	 * @param boolean $is_personalization_allowed
	 * @return boolean
	 */
	public function disable_personalization_on_specific_pages( $is_personalization_allowed ): bool {
		if ( ! is_product_category() ) {
			return $is_personalization_allowed;
		}

		$personalization_options = get_field( 'personalization', 'options' );
		$apply_to_sub_categories = $personalization_options['apply_to_all_sub-categories_as_well'] ?? true;
		$product_categories      = $personalization_options['product_categories_blocklist'] ?? [];

		if ( empty( $product_categories ) ) {
			return $is_personalization_allowed;
		}

		// Check if this category is in $product_categories
		$term = get_queried_object();
		if ( ! $term instanceof \WP_Term ) {
			return $is_personalization_allowed;
		}

		// Exact match on a category, no personalization.
		if ( in_array( $term->term_id, $product_categories, true ) ) {
			return true;
		}

		// Do we need to check parent categories?
		if ( ! $apply_to_sub_categories || empty( $term->parent ) ) {
			return $is_personalization_allowed;
		}

		$parent_term_id = $term->parent;

		// If so, check if any of the parent categories are in $product_categories recursively.
		while ( (int) $parent_term_id > 0 ) {
			if ( in_array( $parent_term_id, $product_categories, true ) ) {
				return true;
			}

			$parent_term = get_term( $parent_term_id );
			if ( ! $parent_term instanceof \WP_Term ) {
				break;
			}

			$parent_term_id = $parent_term->parent ?? 0;
		}

		return $is_personalization_allowed;
	}
}
