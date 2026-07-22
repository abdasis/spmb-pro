<?php
/**
 * REST endpoint admin SPMB Pro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_REST_Admin {

	/**
	 * Daftarkan route admin.
	 */
	public static function register(): void {
		register_rest_route(
			'spmb/v1',
			'/applicants/(?P<id>\d+)/status',
			array(
				'methods'             => 'PUT',
				'callback'            => array( __CLASS__, 'set_status' ),
				'permission_callback' => array( SPMB_REST_Permissions::class, 'can_manage' ),
			)
		);

		register_rest_route(
			'spmb/v1',
			'/payments/(?P<id>\d+)/verify',
			array(
				'methods'             => 'PUT',
				'callback'            => array( __CLASS__, 'verify_payment' ),
				'permission_callback' => array( SPMB_REST_Permissions::class, 'can_manage' ),
			)
		);

		register_rest_route(
			'spmb/v1',
			'/selection/run',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'run_selection' ),
				'permission_callback' => array( SPMB_REST_Permissions::class, 'can_manage' ),
				'args'                => array(
					'jenjang' => array( 'required' => true, 'type' => 'string' ),
				),
			)
		);

		register_rest_route(
			'spmb/v1',
			'/selection/publish',
			array(
				'methods'             => 'PUT',
				'callback'            => array( __CLASS__, 'toggle_publish' ),
				'permission_callback' => array( SPMB_REST_Permissions::class, 'can_manage' ),
				'args'                => array(
					'published' => array( 'required' => true, 'type' => 'boolean' ),
				),
			)
		);

		register_rest_route(
			'spmb/v1',
			'/export/(?P<type>[a-z-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'export' ),
				'permission_callback' => array( SPMB_REST_Permissions::class, 'can_manage' ),
			)
		);
	}

	/**
	 * Endpoint ekspor (metadata; unduhan biner via admin-post).
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public static function export( WP_REST_Request $request ) {
		$type = sanitize_key( (string) $request->get_param( 'type' ) );
		return rest_ensure_response(
			array(
				'type'      => $type,
				'download_url' => add_query_arg(
					array(
						'action'   => 'spmb_export',
						'type'     => $type,
						'jenjang'  => sanitize_key( (string) $request->get_param( 'jenjang' ) ),
						'id'       => absint( $request->get_param( 'id' ) ),
					),
					admin_url( 'admin-post.php' )
				),
			)
		);
	}

	/**
	 * Jalankan seleksi via REST.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function run_selection( WP_REST_Request $request ) {
		$jenjang = sanitize_key( (string) $request->get_param( 'jenjang' ) );
		$result  = SPMB_Selection_Engine::run( $jenjang );
		return rest_ensure_response( $result );
	}

	/**
	 * Ubah flag publikasi pengumuman.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public static function toggle_publish( WP_REST_Request $request ) {
		$publish = (bool) $request->get_param( 'published' );
		$settings = SPMB_Settings_Repository::all();
		$settings['pengumuman_published'] = $publish;
		SPMB_Settings_Repository::save( $settings );
		return rest_ensure_response( array( 'published' => $publish ) );
	}

	/**
	 * Ubah status pendaftar.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function set_status( WP_REST_Request $request ) {
		$id     = (int) $request->get_param( 'id' );
		$status = sanitize_key( (string) $request->get_param( 'status' ) );

		if ( ! in_array( $status, array( 'submitted', 'verified', 'rejected' ), true ) ) {
			return new WP_Error( 'spmb_invalid_status', __( 'Status tidak valid.', 'spmb-pro' ), array( 'status' => 400 ) );
		}

		SPMB_DB_Applicants::update( $id, array( 'status' => $status ) );
		return rest_ensure_response( array( 'id' => $id, 'status' => $status ) );
	}

	/**
	 * Verifikasi pembayaran.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function verify_payment( WP_REST_Request $request ) {
		$id = (int) $request->get_param( 'id' );

		$payment = self::get_payment( $id );
		if ( ! $payment ) {
			return new WP_Error( 'spmb_no_payment', __( 'Tagihan tidak ditemukan.', 'spmb-pro' ), array( 'status' => 404 ) );
		}

		// Transisi: unpaid -> paid -> verified.
		if ( 'unpaid' === $payment->status ) {
			SPMB_Payment_Status::mark_paid( $id );
			return rest_ensure_response( array( 'id' => $id, 'status' => 'paid' ) );
		}
		if ( 'paid' === $payment->status ) {
			SPMB_Payment_Status::verify( $id, get_current_user_id() );
			return rest_ensure_response( array( 'id' => $id, 'status' => 'verified' ) );
		}

		return rest_ensure_response( array( 'id' => $id, 'status' => $payment->status ) );
	}

	/**
	 * Ambi baris payment.
	 *
	 * @param int $id ID.
	 * @return object|null
	 */
	private static function get_payment( int $id ): ?object {
		global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM %i WHERE id=%d', SPMB_DB_Payments::table(), $id ) ); // phpcs:ignore WordPress.DB
		return $row ? $row : null;
	}
}