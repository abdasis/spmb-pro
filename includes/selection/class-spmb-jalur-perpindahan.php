<?php
/**
 * Jalur Perpindahan Tugas — ranking berdasarkan waktu submit (awal dulu).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Jalur_Perpindahan {

	/**
	 * Nama jalur.
	 *
	 * @return string
	 */
	public static function jalur(): string {
		return 'perpindahan';
	}

	/**
	 * Urutkan pendaftar berdasarkan tanggal submit asc.
	 *
	 * @param array $applicants Daftar pendaftar eligible.
	 * @return array Terurut created_at asc.
	 */
	public static function rank( array $applicants ): array {
		$out = array();
		foreach ( $applicants as $a ) {
			$out[] = array(
				'applicant' => $a,
				'score'     => 0,
			);
		}
		usort( $out, array( __CLASS__, 'compare' ) );
		return $out;
	}

	/**
	 * Pembanding created_at asc.
	 *
	 * @param array $x Item A.
	 * @param array $y Item B.
	 */
	private static function compare( array $x, array $y ): int {
		$ca = $x['applicant']->created_at ?? '';
		$cb = $y['applicant']->created_at ?? '';
		return strcmp( $ca, $cb );
	}
}