<?php
/**
 * REST endpoint lookup status pendaftaran.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_REST_Lookup {

	/**
	 * Daftarkan route.
	 */
	public static function register(): void {
		register_rest_route(
			'spmb/v1',
			'/lookup/(?P<regnum>[A-Za-z0-9\-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'lookup' ),
				'permission_callback' => array( SPMB_REST_Permissions::class, 'public_ok' ),
			)
		);
	}

	/**
	 * Cari status pendaftar berdasarkan nomor pendaftaran.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function lookup( WP_REST_Request $request ) {
		$regnum = sanitize_text_field( (string) $request->get_param( 'regnum' ) );
		$applicant = SPMB_DB_Applicants::get_by_reg( $regnum );

		if ( ! $applicant ) {
			return new WP_Error(
				'spmb_not_found',
				__( 'Nomor pendaftaran tidak ditemukan.', 'spmb-pro' ),
				array( 'status' => 404 )
			);
		}

		$payment = SPMB_DB_Payments::for_applicant( (int) $applicant->id );

		// Hanya field non-sensitif.
		return rest_ensure_response(
			array(
				'registration_number' => $applicant->registration_number,
				'full_name'           => $applicant->full_name,
				'jenjang'             => $applicant->jenjang,
				'jalur'               => $applicant->jalur,
				'status'              => $applicant->status,
				'selection_status'    => $applicant->selection_status,
				'final_rank'          => (int) $applicant->final_rank,
				'payment_status'      => $payment ? $payment->status : 'none',
				'invoice_number'      => $payment ? $payment->invoice_number : '',
			)
		);
	}
}