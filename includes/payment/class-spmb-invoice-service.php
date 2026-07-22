<?php
/**
 * Layanan pembuatan invoice pendaftaran.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Invoice_Service {

	/**
	 * Buat invoice untuk satu pendaftar.
	 *
	 * @param int $applicant_id ID pendaftar.
	 * @return int ID invoice, 0 bila gagal.
	 */
	public static function create_for( int $applicant_id ): int {
		$existing = SPMB_DB_Payments::for_applicant( $applicant_id );
		if ( $existing ) {
			return (int) $existing->id;
		}

		$amount = (float) SPMB_Settings_Repository::get( 'fee', 0.0 );
		$year   = (int) current_time( 'Y' );
		$seq    = SPMB_DB_Payments::max_seq( $year ) + 1;
		$number = sprintf( 'INV-%d-%06d', $year, $seq );

		$id = SPMB_DB_Payments::insert(
			array(
				'applicant_id'   => $applicant_id,
				'invoice_number' => $number,
				'amount'         => $amount,
				'status'         => 'unpaid',
			)
		);

		return $id;
	}
}