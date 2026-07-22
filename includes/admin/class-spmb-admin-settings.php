<?php
/**
 * Layar pengaturan SPMB Pro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Admin_Settings {

	/**
	 * Render layar pengaturan dan proses penyimpanan.
	 */
	public static function render(): void {
		if ( ! current_user_can( SPMB_Roles::CAP ) ) {
			wp_die( esc_html__( 'Anda tidak punya akses ke halaman ini.', 'spmb-pro' ) );
		}

		$notice = self::handle_save();
		$settings = SPMB_Settings_Repository::all();
		$tab     = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security

		require SPMB_PATH . 'views/admin/settings.php';
	}

	/**
	 * Tangani penyimpanan form pengaturan.
	 *
	 * @return string Pesan notice (kosong bila tidak ada aksi).
	 */
	private static function handle_save(): string {
		if ( empty( $_POST['spmb_save_settings'] ) ) {
			return '';
		}
		check_admin_referer( 'spmb_save_settings', 'spmb_settings_nonce' );

		SPMB_Settings_Repository::save( wp_unslash( $_POST['spmb_settings'] ?? array() ) );

		return __( 'Pengaturan disimpan.', 'spmb-pro' );
	}
}