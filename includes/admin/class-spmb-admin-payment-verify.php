<?php
/**
 * Layar verifikasi pembayaran.
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

		$notice = self::handle_actions();

		$filter_status = isset( $_GET['pay_status'] ) ? sanitize_key( wp_unslash( $_GET['pay_status'] ) ) : ''; // phpcs:ignore
		$payments      = self::get_payments( $filter_status );

		require SPMB_PATH . 'views/admin/payment-verify.php';
	}

	/**
	 * Tangani aksi tandai lunas/verifikasi/void.
	 *
	 * @return string Pesan notice.
	 */
	private static function handle_actions(): string {
		if ( empty( $_POST['spmb_pay_action'] ) ) {
			return '';
		}
		check_admin_referer( 'spmb_pay', 'spmb_pay_nonce' );

		$pid    = absint( $_POST['payment_id'] ?? 0 );
		$action = sanitize_key( wp_unslash( $_POST['spmb_pay_action'] ) );

		switch ( $action ) {
			case 'paid':
				return SPMB_Payment_Status::mark_paid( $pid ) ? __( 'Ditandai lunas.', 'spmb-pro' ) : '';
			case 'verify':
				return SPMB_Payment_Status::verify( $pid, get_current_user_id() ) ? __( 'Pembayaran terverifikasi.', 'spmb-pro' ) : '';
			case 'void':
				return SPMB_Payment_Status::void( $pid ) ? __( 'Invoice dibatalkan.', 'spmb-pro' ) : '';
		}
		return '';
	}

	/**
	 * Ambi daftar payment dengan join pendaftar.
	 *
	 * @param string $status Filter status.
	 * @return array
	 */
	private static function get_payments( string $status ): array {
		global $wpdb;
		$sql = $wpdb->prepare(
			"SELECT p.*, a.full_name, a.registration_number
			 FROM %i p LEFT JOIN %i a ON p.applicant_id = a.id",
			array( SPMB_DB_Payments::table(), SPMB_DB_Applicants::table() )
		); // phpcs:ignore WordPress.DB

		if ( $status ) {
			$sql = $wpdb->prepare(
				"SELECT p.*, a.full_name, a.registration_number
				 FROM %i p LEFT JOIN %i a ON p.applicant_id = a.id
				 WHERE p.status = %s ORDER BY p.id DESC LIMIT 100",
				array( SPMB_DB_Payments::table(), SPMB_DB_Applicants::table(), $status )
			);
		} else {
			$sql = $wpdb->prepare(
				"SELECT p.*, a.full_name, a.registration_number
				 FROM %i p LEFT JOIN %i a ON p.applicant_id = a.id
				 ORDER BY p.id DESC LIMIT 100",
				array( SPMB_DB_Payments::table(), SPMB_DB_Applicants::table() )
			);
		}
		return $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB
	}
}