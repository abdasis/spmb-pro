<?php
/**
 * Hook deaktivasi plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Deactivator {

	/**
	 * Hook yang dijalankan saat plugin dinonaktifkan.
	 *
	 * Tabel, option, dan file uploads TIDAK dihapus.
	 */
	public static function deactivate(): void {
		wp_clear_scheduled_hook( 'spmb_daily_housekeeping' );
		flush_rewrite_rules();
	}
}