<?php
/**
 * CRUD tabel spmb_documents.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_DB_Documents {

	/**
	 * Nama tabel lengkap.
	 *
	 * @return string
	 */
	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'spmb_documents';
	}

	/**
	 * Sisipkan metadata dokumen.
	 *
	 * @param array $data Data kolom => nilai.
	 * @return int
	 */
	public static function insert( array $data ): int {
		global $wpdb;
		$data['uploaded_at'] = $data['uploaded_at'] ?? current_time( 'mysql' );
		$ok = $wpdb->insert( self::table(), $data ); // phpcs:ignore WordPress.DB
		return $ok ? (int) $wpdb->insert_id : 0;
	}

	/**
	 * Perbarui dokumen.
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
	 * Ambil semua dokumen milik satu pendaftar.
	 *
	 * @param int $applicant_id ID pendaftar.
	 * @return object[]
	 */
	public static function for_applicant( int $applicant_id ): array {
		return SPMB_DB_Query::get_rows(
			'spmb_documents',
			array(
				'where'    => array( 'applicant_id' => $applicant_id ),
				'order_by' => 'doc_type',
			)
		);
	}

	/**
	 * Hapus dokumen berdasarkan ID (data saja; berkas fisik ditangani terpisah).
	 *
	 * @param int $id ID.
	 * @return bool
	 */
	public static function delete( int $id ): bool {
		global $wpdb;
		return (bool) $wpdb->delete( self::table(), array( 'id' => $id ) ); // phpcs:ignore WordPress.DB
	}
}