<?php
/**
 * CRUD tabel spmb_payments.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_DB_Payments {

	/**
	 * Nama tabel lengkap.
	 *
	 * @return string
	 */
	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'spmb_payments';
	}

	/**
	 * Sisipkan satu pembayaran.
	 *
	 * @param array $data Data kolom => nilai.
	 * @return int
	 */
	public static function insert( array $data ): int {
		global $wpdb;
		$data['created_at'] = $data['created_at'] ?? current_time( 'mysql' );
		$ok = $wpdb->insert( self::table(), $data ); // phpcs:ignore WordPress.DB
		return $ok ? (int) $wpdb->insert_id : 0;
	}

	/**
	 * Perbarui pembayaran.
	 *
	 * @param int   $id   ID.
	 * @param array $data Data.
	 * @return bool
	 */
	public static function update( int $id, array $data ): bool {
		global $wpdb;
		return (bool) $wpdb->update( self::table(), $data, array( 'id' => $id ) ); // phpcs:ignore WordPress.DB
	}

	/**
	 * Ambi pembayaran berdasarkan ID pendaftar.
	 *
	 * @param int $applicant_id ID pendaftar.
	 * @return object|null
	 */
	public static function for_applicant( int $applicant_id ): ?object {
		return SPMB_DB_Query::get_row( 'spmb_payments', array( 'applicant_id' => $applicant_id ) );
	}

	/**
	 * Ambi nomor urut invoice maksimum untuk tahun.
	 *
	 * @param int $year Tahun.
	 * @return int
	 */
	public static function max_seq( int $year ): int {
		global $wpdb;
		$pattern = $wpdb->esc_like( "INV-{$year}-" ) . '%';
		$sql     = $wpdb->prepare(
			"SELECT MAX(CAST(SUBSTRING(invoice_number, %d) AS UNSIGNED)) FROM %i WHERE invoice_number LIKE %s",
			strlen( "INV-{$year}-" ) + 1,
			self::table(),
			$pattern
		);
		$max     = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB
		return $max ? (int) $max : 0;
	}
}