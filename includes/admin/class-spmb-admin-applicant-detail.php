<?php
/**
 * Layar detail pendaftar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Admin_Applicant_Detail {

	/**
	 * Render layar detail pendaftar.
	 */
	public static function render(): void {
		if ( ! current_user_can( SPMB_Roles::CAP ) ) {
			wp_die( esc_html__( 'Anda tidak punya akses ke halaman ini.', 'spmb-pro' ) );
		}

		$id      = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0; // phpcs:ignore WordPress.Security
		$notice  = self::handle_actions( $id );
		$applicant = $id ? SPMB_DB_Applicants::get( $id ) : null;

		if ( ! $applicant ) {
			echo '<div class="wrap"><p>' . esc_html__( 'Pendaftar tidak ditemukan.', 'spmb-pro' ) . '</p></div>';
			return;
		}

		$documents = SPMB_DB_Documents::for_applicant( $id );
		$payment   = SPMB_DB_Payments::for_applicant( $id );

		require SPMB_PATH . 'views/admin/applicant-detail.php';
	}

	/**
	 * Tangani aksi (ubah status, verifikasi dokumen, verifikasi bayar).
	 *
	 * @param int $id ID pendaftar.
	 * @return string Pesan notice.
	 */
	private static function handle_actions( int $id ): string {
		if ( empty( $_POST['spmb_detail_action'] ) ) {
			return '';
		}
		check_admin_referer( 'spmb_detail', 'spmb_detail_nonce' );

		$action = sanitize_key( wp_unslash( $_POST['spmb_detail_action'] ) );

		switch ( $action ) {
			case 'set_status':
				$status = sanitize_key( wp_unslash( $_POST['status'] ?? '' ) );
				SPMB_DB_Applicants::update( $id, array( 'status' => $status ) );
				return __( 'Status diperbarui.', 'spmb-pro' );
			case 'verify_doc':
				$doc_id = absint( $_POST['doc_id'] ?? 0 );
				$flag   = ! empty( $_POST['is_verified'] );
				SPMB_DB_Documents::update( $doc_id, array( 'is_verified' => $flag ? 1 : 0 ) );
				return __( 'Status dokumen diperbarui.', 'spmb-pro' );
			case 'pay_paid':
				$payment = SPMB_DB_Payments::for_applicant( $id );
				if ( $payment && SPMB_Payment_Status::mark_paid( (int) $payment->id ) ) {
					return __( 'Pembayaran ditandai lunas.', 'spmb-pro' );
				}
				return '';
			case 'pay_verify':
				$payment = SPMB_DB_Payments::for_applicant( $id );
				if ( $payment && SPMB_Payment_Status::verify( (int) $payment->id, get_current_user_id() ) ) {
					return __( 'Pembayaran terverifikasi.', 'spmb-pro' );
				}
				return '';
		}
		return '';
	}
}