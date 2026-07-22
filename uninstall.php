<?php
/**
 * Uninstall SPMB Pro — hanya dijalankan saat plugin dihapus.
 *
 * Uninstall berjalan TANPA plugin termuat, jadi tidak boleh bergantung
 * pada autoloader atau class SPMB_*.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$option_key = 'spmb_pro_settings';
$settings   = get_option( $option_key, array() );
$settings   = is_array( $settings ) ? $settings : array();

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

delete_option( $option_key );
delete_option( 'spmb_db_version' );

// Hapus capability spmb_manage_applicants dari semua role.
$cap = 'spmb_manage_applicants';
foreach ( wp_roles()->roles as $slug => $role ) {
	$role_obj = get_role( $slug );
	if ( $role_obj instanceof WP_Role ) {
		$role_obj->remove_cap( $cap );
	}
}

// Opsional hapus folder uploads bila diaktifkan admin.
if ( ! empty( $settings['delete_files_on_uninstall'] ) ) {
	$uploads = wp_upload_dir();
	$dir    = trailingslashit( $uploads['basedir'] ) . 'spmb-pro';
	if ( is_dir( $dir ) ) {
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