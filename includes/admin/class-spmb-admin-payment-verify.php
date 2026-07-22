<?php
/**
 * Layar verifikasi pembayaran.
 *
 * Versi awal: tabel sederhana. Verifikasi penuh pada Phase 4.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Admin_Payment_Verify {

	/**
	 * Render layar verifikasi pembayaran.
	 */
	public static function render(): void {
		if ( ! current_user_can( SPMB_Roles::CAP ) ) {
			wp_die( esc_html__( 'Anda tidak punya akses ke halaman ini.', 'spmb-pro' ) );
		}

		$payments = SPMB_DB_Query::get_rows(
			'spmb_payments',
			array(
				'order_by' => 'id',
				'order'    => 'DESC',
				'limit'    => 50,
			)
		);

		require SPMB_PATH . 'views/admin/payment-verify.php';
	}
}