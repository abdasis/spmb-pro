<?php
/**
 * Hook aktivasi plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Activator {

	/**
	 * Hook yang dijalankan saat plugin diaktifkan.
	 */
	public static function activate(): void {
		SPMB_Installer::install();
		SPMB_Roles::add();

		if ( ! wp_next_scheduled( 'spmb_daily_housekeeping' ) ) {
			wp_schedule_event( time(), 'daily', 'spmb_daily_housekeeping' );
		}

		self::create_uploads_dir();

		flush_rewrite_rules();
	}

	/**
	 * Buat subdirektori uploads/spmb-pro beserta file proteksi.
	 */
	private static function create_uploads_dir(): void {
		$uploads = wp_upload_dir();
		$basedir  = trailingslashit( $uploads['basedir'] );
		$dir     = $basedir . 'spmb-pro';

		if ( ! is_dir( $dir ) ) {
			wp_mkdir_p( $dir );
		}

		$index = $dir . '/index.php';
		if ( ! file_exists( $index ) ) {
			file_put_contents( $index, "<?php\n// Silence is golden." );
		}

		$htaccess = $dir . '/.htaccess';
		if ( ! file_exists( $htaccess ) ) {
			file_put_contents( $htaccess, "Options -Indexes\n" );
		}
	}
}