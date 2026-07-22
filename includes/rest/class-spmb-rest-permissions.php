<?php
/**
 * Callback permission REST API SPMB Pro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_REST_Permissions {

	/**
	 * Akses publik.
	 *
	 * @return true
	 */
	public static function public_ok(): bool {
		return true;
	}

	/**
	 * Cek capability pengelolaan.
	 *
	 * @return bool|WP_Error
	 */
	public static function can_manage() {
		if ( ! current_user_can( SPMB_Roles::CAP ) ) {
			return new WP_Error(
				'spmb_forbidden',
				__( 'Anda tidak punya akses.', 'spmb-pro' ),
				array( 'status' => 403 )
			);
		}
		return true;
	}
}