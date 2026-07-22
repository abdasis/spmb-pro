<?php
/**
 * Handler unduhan ekspor via admin-post (menghasilkan biner langsung).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Export_Handler {

	/**
	 * Daftarkan hook admin-post.
	 */
	public static function register(): void {
		add_action( 'admin_post_spmb_export', array( __CLASS__, 'handle' ) );
		add_action( 'admin_post_nopriv_spmb_export', array( __CLASS__, 'handle' ) );
	}

	/**
	 * Proses unduhan ekspor.
	 */
	public static function handle(): void {
		if ( ! current_user_can( SPMB_Roles::CAP ) ) {
			wp_die( esc_html__( 'Anda tidak punya akses.', 'spmb-pro' ) );
		}

		$type    = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : ''; // phpcs:ignore
		$jenjang = isset( $_GET['jenjang'] ) ? sanitize_key( wp_unslash( $_GET['jenjang'] ) ) : ''; // phpcs:ignore
		$id      = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0; // phpcs:ignore

		switch ( $type ) {
			case 'pdf-kartu':
				self::export_kartu( $id );
				exit;
			case 'pdf-report':
				self::export_report( $jenjang );
				exit;
			case 'csv':
				self::export_csv( $jenjang );
				exit;
			default:
				wp_die( esc_html__( 'Tipe ekspor tidak valid.', 'spmb-pro' ) );
		}
	}

	/**
	 * Unduh kartu pendaftar PDF.
	 *
	 * @param int $id ID pendaftar.
	 */
	private static function export_kartu( int $id ): void {
		$applicant = SPMB_DB_Applicants::get( $id );
		if ( ! $applicant ) {
			wp_die( esc_html__( 'Pendaftar tidak ditemukan.', 'spmb-pro' ) );
		}
		$payment = SPMB_DB_Payments::for_applicant( $id );

		SPMB_Export_Pdf_Kartu::generate( $applicant, $payment, 'D' );
		exit;
	}

	/**
	 * Unduh laporan seleksi PDF.
	 *
	 * @param string $jenjang Jenjang.
	 */
	private static function export_report( string $jenjang ): void {
		$applicants = self::applicants_for_jenjang( $jenjang );
		SPMB_Export_Pdf_Report::generate( $applicants, $jenjang, 'D' );
		exit;
	}

	/**
	 * Unduh CSV.
	 *
	 * @param string $jenjang Jenjang.
	 */
	private static function export_csv( string $jenjang ): void {
		$applicants = self::applicants_for_jenjang( $jenjang );
		SPMB_Export_Csv::generate( $applicants, 'Pendaftar-' . $jenjang . '.csv' );
		exit;
	}

	/**
	 * Ambi pendaftar untuk satu jenjang (atau semua bila kosong).
	 *
	 * @param string $jenjang Jenjang.
	 * @return array
	 */
	private static function applicants_for_jenjang( string $jenjang ): array {
		global $wpdb;
		$tbl = SPMB_DB_Applicants::table();
		if ( $jenjang ) {
			$sql = $wpdb->prepare( "SELECT * FROM %i WHERE jenjang=%s ORDER BY jalur, final_rank", array( $tbl, $jenjang ) ); // phpcs:ignore WordPress.DB
		} else {
			$sql = $wpdb->prepare( "SELECT * FROM %i ORDER BY jenjang, jalur, final_rank", array( $tbl ) ); // phpcs:ignore WordPress.DB
		}
		return $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB
	}
}