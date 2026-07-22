<?php
/**
 * Jalur Prestasi — ranking berdasarkan skor rapor + prestasi (tinggi dulu).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Jalur_Prestasi {

	/**
	 * Nama jalur.
	 *
	 * @return string
	 */
	public static function jalur(): string {
		return 'prestasi';
	}

	/**
	 * Urutkan pendaftar berdasarkan skor prestasi.
	 *
	 * @param array $applicants Daftar pendaftar eligible.
	 * @return array Terurut skor desc, tiebreak rapor desc, lalu created_at asc.
	 */
	public static function rank( array $applicants ): array {
		$scored = array();
		foreach ( $applicants as $a ) {
			$scored[] = array(
				'applicant' => $a,
				'score'     => SPMB_Scoring::prestasi_score( $a ),
			);
		}
		usort( $scored, array( __CLASS__, 'compare' ) );
		return $scored;
	}

	/**
	 * Pembanding skor desc.
	 *
	 * @param array $x Item A.
	 * @param array $y Item B.
	 */
	private static function compare( array $x, array $y ): int {
		if ( $x['score'] !== $y['score'] ) {
			return $x['score'] > $y['score'] ? -1 : 1;
		}
		$ra = (float) ( $x['applicant']->rapor_avg ?? 0 );
		$rb = (float) ( $y['applicant']->rapor_avg ?? 0 );
		if ( $ra !== $rb ) {
			return $ra > $rb ? -1 : 1;
		}
		return strcmp( $x['applicant']->created_at ?? '', $y['applicant']->created_at ?? '' );
	}
}