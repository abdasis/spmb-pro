<?php
/**
 * Enqueue CSS/JS admin dan publik SPMB Pro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Assets {

	/**
	 * Daftarkan hook enqueue.
	 */
	public static function register(): void {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin' ) );
	}

	/**
	 * Enqueue asset admin hanya pada layar SPMB Pro.
	 *
	 * @param string $hook_suffix Suffix hook admin.
	 */
	public static function enqueue_admin( string $hook_suffix ): void {
		$is_spmb = ( false !== strpos( $hook_suffix, 'spmb-pro' ) )
			|| ( 'toplevel_page_spmb-pro' === $hook_suffix );
		if ( ! $is_spmb ) {
			return;
		}

		wp_enqueue_style(
			'spmb-admin',
			SPMB_URL . 'assets/css/spmb-admin.css',
			array(),
			SPMB_VERSION
		);
		wp_enqueue_script(
			'spmb-admin',
			SPMB_URL . 'assets/js/spmb-admin.js',
			array(),
			SPMB_VERSION,
			true
		);
	}
}