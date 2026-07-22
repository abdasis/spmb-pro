<?php
/**
 * Jalur Zonasi — ranking berdasarkan jarak (dekat dulu).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Jalur_Zonasi {

	/**
	 * Nama jalur.
	 *
	 * @return string
	 */
	public static function jalur(): string {
		return 'zonasi';
	}

	/**
	 * Urutkan pendaftar berdasarkan skor jalur.
	 *
	 * Aturan: distance_km asc, lalu birth_date asc (lebih muda), lalu created_at asc.
	 *
	 * @param array $applicants Daftar pendaftar eligible.
	 * @return array Sudah terurut, tiap item diberi kunci 'score'.
	 */
	public static function rank( array $applicants ): array {
		usort( $applicants, array( __CLASS__, 'compare' ) );

		$out = array();
		foreach ( $applicants as $a ) {
			$score = (float) ( $a->distance_km ?? 0 );
			$out[] = array(
				'applicant' => $a,
				'score'     => $score,
			);
		}
		return $out;
	}

	/**
	 * Pembanding dua pendaftar.
	 *
	 * @param object $a Pendaftar A.
	 * @param object $b Pendaftar B.
	 */
	private static function compare( object $a, object $b ): int {
		$da = (float) ( $a->distance_km ?? 0 );
		$db = (float) ( $b->distance_km ?? 0 );
		if ( $da !== $db ) {
			return $da < $db ? -1 : 1;
		}
		// Tiebreak: lebih muda (birth_date lebih besar) didahulukan.
		$ba = $a->birth_date ?? '0000-00-00';
		$bb = $b->birth_date ?? '0000-00-00';
		if ( $ba !== $bb ) {
			return $ba > $bb ? -1 : 1;
		}
		return strcmp( $a->created_at ?? '', $b->created_at ?? '' );
	}
}