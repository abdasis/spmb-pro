<?php
/**
 * REST endpoint perhitungan jarak (zonasi).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_REST_Distance {

	/**
	 * Daftarkan route.
	 */
	public static function register(): void {
		register_rest_route(
			'spmb/v1',
			'/distance',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'compute' ),
				'permission_callback' => array( SPMB_REST_Permissions::class, 'public_ok' ),
				'args'                => array(
					'lat' => array( 'required' => true, 'type' => 'number' ),
					'lng' => array( 'required' => true, 'type' => 'number' ),
				),
			)
		);
	}

	/**
	 * Hitung jarak haversine dari rumah ke sekolah.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function compute( WP_REST_Request $request ) {
		$settings = SPMB_Settings_Repository::all();
		$school_lat = (float) $settings['school_latitude'];
		$school_lng = (float) $settings['school_longitude'];

		if ( ! $school_lat || ! $school_lng ) {
			return new WP_Error( 'spmb_no_school_coord', __( 'Koordinat sekolah belum diatur.', 'spmb-pro' ), array( 'status' => 400 ) );
		}

		$lat = (float) $request->get_param( 'lat' );
		$lng = (float) $request->get_param( 'lng' );

		if ( ! $lat || ! $lng ) {
			return new WP_Error( 'spmb_invalid_coord', __( 'Koordinat tidak valid.', 'spmb-pro' ), array( 'status' => 400 ) );
		}

		$km = self::haversine( $lat, $lng, $school_lat, $school_lng );

		return rest_ensure_response( array( 'distance_km' => round( $km, 2 ) ) );
	}

	/**
	 * Rumus haversine (km).
	 *
	 * @param float $lat1 Latitude asal.
	 * @param float $lng1 Longitude asal.
	 * @param float $lat2 Latitude tujuan.
	 * @param float $lng2 Longitude tujuan.
	 * @return float
	 */
	private static function haversine( float $lat1, float $lng1, float $lat2, float $lng2 ): float {
		$earth = 6371.0;
		$dlat  = deg2rad( $lat2 - $lat1 );
		$dlng  = deg2rad( $lng2 - $lng1 );
		$a     = sin( $dlat / 2 ) * sin( $dlat / 2 ) + cos( deg2rad( $lat1 ) ) * cos( deg2rad( $lat2 ) ) * sin( $dlng / 2 ) * sin( $dlng / 2 );
		$c     = 2 * atan2( sqrt( $a ), sqrt( 1 - $a ) );
		return $earth * $c;
	}
}