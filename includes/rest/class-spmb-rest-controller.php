<?php
/**
 * Pusat registrasi route REST SPMB Pro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_REST_Controller {

	/**
	 * Daftarkan hook rest_api_init.
	 */
	public static function register(): void {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	/**
	 * Daftarkan seluruh route.
	 */
	public static function register_routes(): void {
		SPMB_REST_Distance::register();
		SPMB_REST_Lookup::register();
		SPMB_REST_Admin::register();

		// Route tambahan (seleksi, ekspor) didaftarkan pada phase berikutnya.
		do_action( 'spmb_rest_register_routes' );
	}
}