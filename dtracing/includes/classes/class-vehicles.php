<?php

declare(strict_types=1);
/**
 * Class for manipulating vehicles data.
 *
 * @package Qala
 */

namespace DtRacing;

use WP_Term;

class Vehicles {

	public const FIELD_MAKE  = 'make';
	public const FIELD_MODEL = 'model';
	public const FIELD_YEAR  = 'year';
	public const FIELD_YEARS = 'years';
	public const FIELD_TRIM  = 'trim';

	/**
	 * Returns data for all vehicles prepared for the vehicle selection form on frontend.
	 *
	 * @return array
	 */
	public function get_form_data(): array {
		$prepared_vehicles_data = [];
		$vehicle_terms          = get_terms(
			[
				'taxonomy'   => Vehicle_Taxonomy::SLUG,
				'hide_empty' => true,
			]
		);

		foreach ( $vehicle_terms as $vehicle_term ) {
			if ( empty( $vehicle_term ) ) {
				continue;
			}

			$vehicle = $this->get_vehicle_data( $vehicle_term );

			if ( empty( $vehicle ) ) {
				continue;
			}

			$make  = $vehicle[ self::FIELD_MAKE ];
			$model = $vehicle[ self::FIELD_MODEL ];
			$years = $vehicle[ self::FIELD_YEARS ];
			$trim  = $vehicle[ self::FIELD_TRIM ];

			foreach ( $years as $year ) {
				$prepared_vehicles_data[ $make ][ $model ][ $year ][] = $trim;
			}
		}

		return $prepared_vehicles_data;
	}

	/**
	 * Undocumented function
	 *
	 * @param \WP_Term $vehicle_term
	 * @return array
	 */
	public function get_vehicle_data( \WP_Term $vehicle_term ): array {
		$fields = get_fields( $vehicle_term );

		if ( ! isset( $fields[ self::FIELD_MAKE ], $fields[ self::FIELD_MODEL ], $fields[ self::FIELD_YEAR ], $fields[ self::FIELD_TRIM ] ) ) {
			return [];
		}

		return [
			self::FIELD_MAKE  => $fields[ self::FIELD_MAKE ],
			self::FIELD_MODEL => $fields[ self::FIELD_MODEL ],
			self::FIELD_YEARS => $this->split_years( $fields[ self::FIELD_YEAR ] ),
			self::FIELD_TRIM  => $fields[ self::FIELD_TRIM ],
		];
	}

	/**
	 * Splits the year field set as 1975-2022 into individual years
	 *
	 * @param string $years Years, could be set as 1975-2022 or just as a single year
	 * @return array
	 */
	private function split_years( string $years_spread ): array {
		$years = [];

		// Check if we have a dash, if so we need to split.
		if ( strpos( $years_spread, '-' ) !== false ) {

			$year_range = explode( '-', $years_spread );

			// Something weird is happening if we have more than 1 dash.
			if ( count( $year_range ) !== 2 ) {
				return [];
			}

			$start_year = (int) trim( $year_range[0] );
			$end_year   = (int) trim( $year_range[1] );

			// Make sure our end year is greater or equal than start year.
			if ( $end_year < $start_year ) {
				return [];
			}

			// If they're the same year, just return that.
			if ( $end_year === $start_year ) {
				return [ $end_year ];
			}

			// We need to fill in the values based on start / end year;
			for ( $i = $start_year; $i <= $end_year; $i++ ) {
				$years[] = $i;
			}
		} elseif ( is_numeric( trim( $years_spread ) ) ) {
			$years[] = trim( $years_spread );
		}

		return $years;
	}
}
