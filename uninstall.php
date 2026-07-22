<?php
/**
 * Uninstall SPMB Pro — hanya dijalankan saat plugin dihapus.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$prefix = $wpdb->prefix;
$tables = array(
	"{$prefix}spmb_applicants",
	"{$prefix}spmb_documents",
	"{$prefix}spmb_payments",
	"{$prefix}spmb_selection_runs",
);

foreach ( $tables as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB
}

delete_option( SPMB_Defaults::OPTION_KEY );
delete_option( 'spmb_db_version' );

// Hapus capability dari semua role.
SPMB_Roles::remove();

// Opsional hapus folder uploads bila diaktifkan admin.
if ( get_option( SPMB_Defaults::OPTION_KEY ) ) {
	$settings = get_option( SPMB_Defaults::OPTION_KEY );
} else {
	$settings = SPMB_Defaults::settings();
}

$settings = is_array( $settings ) ? $settings : SPMB_Defaults::settings();
if ( ! empty( $settings['delete_files_on_uninstall'] ) ) {
	$uploads = wp_upload_dir();
	$dir    = trailingslashit( $uploads['basedir'] ) . 'spmb-pro';
	if ( is_dir( $dir ) ) {
		// Hapus rekursif sederhana.
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS ),
			RecursiveIteratorIterator::CHILD_FIRST
		);
		foreach ( $files as $f ) {
			$f->isDir() ? rmdir( $f->getRealPath() ) : unlink( $f->getRealPath() );
		}
		rmdir( $dir );
	}
}