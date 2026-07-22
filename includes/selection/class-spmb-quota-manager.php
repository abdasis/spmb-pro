<?php
/**
 * Penerapan kuota pada daftar terurut per jalur.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Quota_Manager {

	/**
	 * Terapkan kuota pada daftar terurut.
	 *
	 * @param array $ranked Daftar terurut (item: ['applicant'=>obj,'score'=>float]).
	 * @param int   $quota  Jumlah kuota.
	 * @return array {
	 *     @type array $diterima  Item yang diterima.
	 *     @type array $cadangan  Sisa sebagai cadangan.
	 * }
	 */
	public static function apply( array $ranked, int $quota ): array {
		$diterima = array_slice( $ranked, 0, max( 0, $quota ) );
		$cadangan = array_slice( $ranked, max( 0, $quota ) );

		return array(
			'diterima' => $diterima,
			'cadangan' => $cadangan,
		);
	}

	/**
	 * Tulis hasil ke DB: diterima diberi rank berurutan, cadangan lanjut rank.
	 *
	 * @param array $diterima Item diterima.
	 * @param array $cadangan Item cadangan.
	 */
	public static function persist( array $diterima, array $cadangan ): void {
		$rank = 0;
		foreach ( $diterima as $item ) {
			$rank++;
			SPMB_DB_Selection::write_result( (int) $item['applicant']->id, 'diterima', $rank );
		}
		foreach ( $cadangan as $item ) {
			$rank++;
			SPMB_DB_Selection::write_result( (int) $item['applicant']->id, 'cadangan', $rank );
		}
	}

	/**
	 * Tandai pendaftar tidak eligible sebagai tidak_lolos.
	 *
	 * @param array $ineligible IDs pendaftar.
	 */
	public static function mark_ineligible( array $ineligible ): void {
		foreach ( $ineligible as $id ) {
			SPMB_DB_Selection::write_result( (int) $id, 'tidak_lolos', 0 );
		}
	}
}