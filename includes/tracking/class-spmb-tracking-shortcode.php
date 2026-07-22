<?php
/**
 * Shortcode [spmb_cek_status] — cek status pendaftaran.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Tracking_Shortcode {

	/**
	 * Daftarkan shortcode.
	 */
	public static function register(): void {
		add_shortcode( 'spmb_cek_status', array( __CLASS__, 'render' ) );
	}

	/**
	 * Render widget cek status.
	 *
	 * @return string
	 */
	public static function render(): string {
		$result = self::lookup();

		wp_enqueue_style( 'spmb-public', SPMB_URL . 'assets/css/spmb-public.css', array(), SPMB_VERSION );

		ob_start();
		require SPMB_PATH . 'views/public/tracking.php';
		return (string) ob_get_clean();
	}

	/**
	 * Cari pendaftar bila ada query.
	 *
	 * @return object|null
	 */
	private static function lookup(): ?object {
		if ( empty( $_GET['spmb_reg'] ) ) {
			return null;
		}
		$regnum = sanitize_text_field( wp_unslash( $_GET['spmb_reg'] ) );
		return SPMB_DB_Applicants::get_by_reg( $regnum );
	}
}