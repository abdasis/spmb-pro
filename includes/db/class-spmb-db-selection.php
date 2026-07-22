<?php
/**
 * CRUD tabel spmb_selection_runs dan helper penulisan hasil seleksi.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_DB_Selection {

	/**
	 * Nama tabel runs.
	 *
	 * @return string
	 */
	public static function runs_table(): string {
		global $wpdb;
		return $wpdb->prefix . 'spmb_selection_runs';
	}

	/**
	 * Catat satu run seleksi.
	 *
	 * @param array $data Data run.
	 * @return int ID run, 0 bila gagal.
	 */
	public static function insert_run( array $data ): int {
		global $wpdb;
		$data['run_at'] = $data['run_at'] ?? current_time( 'mysql' );
		$ok = $wpdb->insert( self::runs_table(), $data ); // phpcs:ignore WordPress.DB
		return $ok ? (int) $wpdb->insert_id : 0;
	}

	/**
	 * Ambi daftar run untuk satu jenjang.
	 *
	 * @param string $jenjang Jenjang.
	 * @param int    $limit   Batas.
	 * @return object[]
	 */
	public static function recent_runs( string $jenjang = '', int $limit = 10 ): array {
		global $wpdb;
		if ( $jenjang ) {
			$sql = $wpdb->prepare(
				"SELECT * FROM %i WHERE jenjang = %s ORDER BY run_at DESC LIMIT %d",
				array( self::runs_table(), $jenjang, $limit )
			); // phpcs:ignore WordPress.DB
		} else {
			$sql = $wpdb->prepare(
				"SELECT * FROM %i ORDER BY run_at DESC LIMIT %d",
				array( self::runs_table(), $limit )
			); // phpcs:ignore WordPress.DB
		}
		return $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB
	}

	/**
	 * Ambi pendaftar eligible (verified + payment verified) untuk satu jenjang.
	 *
	 * @param string $jenjang Jenjang.
	 * @return object[]
	 */
	public static function eligible_applicants( string $jenjang ): array {
		global $wpdb;
		$sql = $wpdb->prepare(
			"SELECT a.* FROM %i a
			 INNER JOIN %i p ON p.applicant_id = a.id
			 WHERE a.jenjang = %s AND a.status = 'verified' AND p.status = 'verified'
			 ORDER BY a.id ASC",
			array( SPMB_DB_Applicants::table(), SPMB_DB_Payments::table(), $jenjang )
		); // phpcs:ignore WordPress.DB
		return $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB
	}

	/**
	 * Tulis hasil seleksi satu pendaftar.
	 *
	 * @param int    $applicant_id ID pendaftar.
	 * @param string $selection_status Status seleksi.
	 * @param int    $rank         Peringkat.
	 */
	public static function write_result( int $applicant_id, string $selection_status, int $rank ): void {
		SPMB_DB_Applicants::update(
			$applicant_id,
			array(
				'selection_status' => $selection_status,
				'final_rank'       => $rank,
			)
		);
	}
}