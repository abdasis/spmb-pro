<?php
/**
 * CRUD tabel spmb_applicants.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_DB_Applicants {

	/**
	 * Nama tabel lengkap.
	 *
	 * @return string
	 */
	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'spmb_applicants';
	}

	/**
	 * Sisipkan satu pendaftar.
	 *
	 * @param array $data Data kolom => nilai.
	 * @return int ID baris baru, 0 bila gagal.
	 */
	public static function insert( array $data ): int {
		global $wpdb;
		$now   = current_time( 'mysql' );
		$defaults = array(
			'created_at' => $now,
			'updated_at' => $now,
			'status'     => 'submitted',
		);
		$data = array_merge( $defaults, $data );
		$ok   = $wpdb->insert( self::table(), $data ); // phpcs:ignore WordPress.DB
		return $ok ? (int) $wpdb->insert_id : 0;
	}

	/**
	 * Perbarui baris berdasarkan ID.
	 *
	 * @param int   $id   ID.
	 * @param array $data Data.
	 * @return bool
	 */
	public static function update( int $id, array $data ): bool {
		global $wpdb;
		$data['updated_at'] = current_time( 'mysql' );
		return (bool) $wpdb->update( self::table(), $data, array( 'id' => $id ) ); // phpcs:ignore WordPress.DB
	}

	/**
	 * Ambil baris berdasarkan ID.
	 *
	 * @param int $id ID.
	 * @return object|null
	 */
	public static function get( int $id ): ?object {
		return SPMB_DB_Query::get_row( 'spmb_applicants', array( 'id' => $id ) );
	}

	/**
	 * Ambil baris berdasarkan nomor pendaftaran.
	 *
	 * @param string $reg Nomor pendaftaran.
	 * @return object|null
	 */
	public static function get_by_reg( string $reg ): ?object {
		return SPMB_DB_Query::get_row( 'spmb_applicants', array( 'registration_number' => $reg ) );
	}

	/**
	 * Ambil nomor urut maksimum untuk kombinasi tahun+jenjang.
	 *
	 * @param int    $year    Tahun.
	 * @param string $jenjang Jenjang.
	 * @return int
	 */
	public static function max_seq( int $year, string $jenjang ): int {
		global $wpdb;
		$pattern = $wpdb->esc_like( "SPMB-{$year}-{$jenjang}-" ) . '%';
		$sql     = $wpdb->prepare(
			"SELECT MAX(CAST(SUBSTRING(registration_number, %d) AS UNSIGNED)) FROM %i WHERE registration_number LIKE %s",
			strlen( "SPMB-{$year}-{$jenjang}-" ) + 1,
			self::table(),
			$pattern
		);
		$max     = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB
		return $max ? (int) $max : 0;
	}
}