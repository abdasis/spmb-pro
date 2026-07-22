<?php
/**
 * Layar daftar pendaftar dengan WP_List_Table.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Admin_Applicants {

	/**
	 * Render layar daftar pendaftar.
	 */
	public static function render(): void {
		if ( ! current_user_can( SPMB_Roles::CAP ) ) {
			wp_die( esc_html__( 'Anda tidak punya akses ke halaman ini.', 'spmb-pro' ) );
		}

		self::handle_bulk();

		$table = new SPMB_Admin_List_Table();
		$table->prepare_items();

		require SPMB_PATH . 'views/admin/applicants-list.php';
	}

	/**
	 * Tangani aksi bulk (verify/reject).
	 */
	private static function handle_bulk(): void {
		$action = isset( $_GET['spmb_bulk'] ) ? sanitize_key( wp_unslash( $_GET['spmb_bulk'] ) ) : ''; // phpcs:ignore WordPress.Security
		$ids    = isset( $_GET['applicant'] ) ? array_map( 'absint', (array) wp_unslash( $_GET['applicant'] ) ) : array(); // phpcs:ignore

		if ( ! $action || empty( $ids ) ) {
			return;
		}
		check_admin_referer( 'spmb_bulk_applicants' );

		$status = ( 'verify' === $action ) ? 'verified' : 'rejected';
		foreach ( $ids as $id ) {
			SPMB_DB_Applicants::update( $id, array( 'status' => $status ) );
		}
	}
}