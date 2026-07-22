<?php
/**
 * Layar pengumuman & seleksi.
 *
 * Versi awal: placeholder. Engine seleksi pada Phase 6.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Admin_Selection {

	/**
	 * Render layar seleksi.
	 */
	public static function render(): void {
		if ( ! current_user_can( SPMB_Roles::CAP ) ) {
			wp_die( esc_html__( 'Anda tidak punya akses ke halaman ini.', 'spmb-pro' ) );
		}

		$published = (bool) SPMB_Settings_Repository::get( 'pengumuman_published' );
		require SPMB_PATH . 'views/admin/selection.php';
	}
}