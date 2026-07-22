<?php
/**
 * Registrasi capability dan role SPMB Pro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Roles {

	/**
	 * Capability utama pengelolaan pendaftar.
	 */
	public const CAP = 'spmb_manage_applicants';

	/**
	 * Tambahkan capability ke role administrator.
	 */
	public static function add(): void {
		$admin = get_role( 'administrator' );
		if ( $admin instanceof WP_Role ) {
			$admin->add_cap( self::CAP );
		}
	}

	/**
	 * Hapus capability dari semua role.
	 */
	public static function remove(): void {
		foreach ( wp_roles()->roles as $slug => $role ) {
			$role_obj = get_role( $slug );
			if ( $role_obj instanceof WP_Role ) {
				$role_obj->remove_cap( self::CAP );
			}
		}
	}
}