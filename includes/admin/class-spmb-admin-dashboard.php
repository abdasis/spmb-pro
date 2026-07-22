<?php
/**
 * Layar dashboard/statistik SPMB Pro.
 *
 * Konten ringan akan diperkaya pada phase berikutnya.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Admin_Dashboard {

	/**
	 * Render layar dashboard.
	 */
	public static function render(): void {
		if ( ! current_user_can( SPMB_Roles::CAP ) ) {
			wp_die( esc_html__( 'Anda tidak punya akses ke halaman ini.', 'spmb-pro' ) );
		}

		$stats = self::compute_stats();
		require SPMB_PATH . 'views/admin/dashboard.php';
	}

	/**
	 * Hitung statistik ringkas pendaftar.
	 *
	 * @return array
	 */
	private static function compute_stats(): array {
		global $wpdb;
		$prefix = $wpdb->prefix;

		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$prefix}spmb_applicants" ); // phpcs:ignore WordPress.DB
		$verified = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$prefix}spmb_applicants WHERE status='verified'" ); // phpcs:ignore WordPress.DB
		$paid = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$prefix}spmb_payments WHERE status='verified'" ); // phpcs:ignore WordPress.DB

		return array(
			'total'    => $total,
			'verified' => $verified,
			'paid'     => $paid,
		);
	}
}