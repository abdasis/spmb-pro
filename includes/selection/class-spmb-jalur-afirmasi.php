<?php
/**
 * Jalur Afirmasi — ranking berdasarkan penghasilan (rendah dulu).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Jalur_Afirmasi {

	/**
	 * Nama jalur.
	 *
	 * @return string
	 */
	public static function jalur(): string {
		return 'afirmasi';
	}

	/**
	 * Urutkan pendaftar berdasarkan penghasilan asc.
	 *
	 * @param array $applicants Daftar pendaftar eligible.
	 * @return array Terurut parent_income asc, lalu created_at asc.
	 */
	public static function rank( array $applicants ): array {
		$out = array();
		foreach ( $applicants as $a ) {
			$out[] = array(
				'applicant' => $a,
				'score'     => (float) ( $a->parent_income ?? 0 ),
			);
		}
		usort( $out, array( __CLASS__, 'compare' ) );
		return $out;
	}

	/**
	 * Pembanding penghasilan asc.
	 *
	 * @param array $x Item A.
	 * @param array $y Item B.
	 */
	private static function compare( array $x, array $y ): int {
		$ia = (float) ( $x['applicant']->parent_income ?? 0 );
		$ib = (float) ( $y['applicant']->parent_income ?? 0 );
		if ( $ia !== $ib ) {
			return $ia < $ib ? -1 : 1;
		}
		return strcmp( $x['applicant']->created_at ?? '', $y['applicant']->created_at ?? '' );
	}
}