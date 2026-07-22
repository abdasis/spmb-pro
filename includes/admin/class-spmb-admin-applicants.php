<?php
/**
 * Layar daftar pendaftar.
 *
 * Versi awal: tabel sederhana. List table lengkap pada Phase 4.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Admin_Applicants {

	/**
	 * Render layar daftar pendaftar.
	 */
	public static function render(): void {
		if ( ! current_user_can( SPMB_Roles::CAP ) ) {
			wp_die( esc_html__( 'Anda tidak punya akses ke halaman ini.', 'spmb-pro' ) );
		}

		$applicants = SPMB_DB_Query::get_rows(
			'spmb_applicants',
			array(
				'order_by' => 'id',
				'order'    => 'DESC',
				'limit'    => 50,
			)
		);

		require SPMB_PATH . 'views/admin/applicants-list.php';
	}
}