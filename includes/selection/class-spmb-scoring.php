<?php
/**
 * Helper perhitungan skor seleksi.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Scoring {

	/**
	 * Hitung skor prestasi (rapor + achievement) berdasar bobot pengaturan.
	 *
	 * @param object $applicant Pendaftar.
	 * @return float
	 */
	public static function prestasi_score( object $applicant ): float {
		$weights = SPMB_Settings_Repository::get(
			'prestasi_weights',
			array( 'rapor' => 0.7, 'achievement' => 0.3 )
		);
		$w_rapor = (float) ( $weights['rapor'] ?? 0.7 );
		$w_ach   = (float) ( $weights['achievement'] ?? 0.3 );

		$rapor = (float) ( $applicant->rapor_avg ?? 0 );
		$ach   = (float) ( $applicant->achievement_points ?? 0 );

		return ( $rapor * $w_rapor ) + ( $ach * $w_ach );
	}
}