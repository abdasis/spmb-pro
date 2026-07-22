<?php
/**
 * Handler POST form pendaftaran (fallback selain REST).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Form_Handler {

	/**
	 * Daftarkan hook POST.
	 */
	public static function register(): void {
		add_action( 'admin_post_nopriv_spmb_submit', array( __CLASS__, 'handle' ) );
		add_action( 'admin_post_spmb_submit', array( __CLASS__, 'handle' ) );
	}

	/**
	 * Proses submit form pendaftaran.
	 */
	public static function handle(): void {
		if ( ! check_ajax_referer( 'spmb_form_nonce', 'spmb_form_nonce', false ) ) {
			self::fail( array( '_form' => __( 'Sesi tidak valid. Muat ulang halaman.', 'spmb-pro' ) ) );
		}

		if ( ! empty( $_POST['website_url'] ) ) {
			self::fail( array( '_form' => __( 'Terjadi kesalahan validasi.', 'spmb-pro' ) ) );
		}

		$input = self::collect_input();

		$errors = SPMB_Form_Validator::validate( $input );
		if ( $errors->has_errors() ) {
			self::fail( self::errors_to_array( $errors ) );
		}

		$applicant_id = SPMB_Applicant_Service::create( $input );
		if ( ! $applicant_id ) {
			self::fail( array( '_form' => __( 'Gagal menyimpan pendaftaran. Coba lagi.', 'spmb-pro' ) ) );
		}

		$reg_number = SPMB_DB_Applicants::get( $applicant_id )->registration_number;

		$upload_errors = self::handle_uploads( $applicant_id, $reg_number );
		if ( ! empty( $upload_errors ) ) {
			// Data tersimpan, dokumen parsial: catat sebagai catatan, lanjutkan.
			self::log_upload_errors( $applicant_id, $upload_errors );
		}

		self::maybe_send_email( $applicant_id );

		$redirect = add_query_arg(
			array(
				'spmb_status' => 'success',
				'reg'         => $reg_number,
			),
			wp_get_referer()
		);
		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * Kumpulkan dan sanitasi input dari POST.
	 *
	 * @return array
	 */
	private static function collect_input(): array {
		$post = wp_unslash( $_POST );
		$clean = array();

		$text_fields = array(
			'full_name', 'nisn', 'nik', 'gender', 'birth_place', 'birth_date', 'religion',
			'address', 'phone', 'email', 'origin_school', 'origin_school_npsn',
			'parent_name', 'parent_phone', 'parent_job', 'parent_income',
			'program_choice_1', 'program_choice_2', 'jenjang', 'jalur',
			'afirmasi_category', 'distance_km', 'rapor_avg', 'achievement_points',
		);

		foreach ( $text_fields as $f ) {
			if ( isset( $post[ $f ] ) ) {
				$clean[ $f ] = is_scalar( $post[ $f ] ) ? sanitize_text_field( $post[ $f ] ) : '';
			}
		}
		if ( ! empty( $clean['address'] ) ) {
			$clean['address'] = sanitize_textarea_field( $post['address'] );
		}

		return $clean;
	}

	/**
	 * Tangani upload dokumen.
	 *
	 * @param int    $applicant_id ID pendaftar.
	 * @param string $reg_number   Nomor pendaftaran.
	 * @return array Daftar error per dokumen.
	 */
	private static function handle_uploads( int $applicant_id, string $reg_number ): array {
		$doc_types = array( 'akta_kelahiran', 'rapor', 'foto', 'kk' );
		$errors    = array();

		foreach ( $doc_types as $type ) {
			if ( empty( $_FILES[ $type ]['name'] ) ) {
				continue;
			}
			$relative = SPMB_Uploader::handle( $_FILES[ $type ], $reg_number, $type );
			if ( is_wp_error( $relative ) ) {
				$errors[ $type ] = $relative->get_error_message();
				continue;
			}
			if ( ! $relative ) {
				continue;
			}
			self::record_document( $applicant_id, $type, $relative, $_FILES[ $type ] );
		}

		return $errors;
	}

	/**
	 * Catat dokumen ke DB.
	 *
	 * @param int    $applicant_id ID pendaftar.
	 * @param string $type         Tipe dokumen.
	 * @param string $relative     Path relatif.
	 * @param array  $file         Elemen $_FILES.
	 */
	private static function record_document( int $applicant_id, string $type, string $relative, array $file ): void {
		$uploads = wp_upload_dir();
		SPMB_DB_Documents::insert(
			array(
				'applicant_id' => $applicant_id,
				'doc_type'     => $type,
				'file_path'    => $relative,
				'file_url'     => trailingslashit( $uploads['baseurl'] ) . $relative,
				'mime_type'    => $file['type'] ?? '',
				'file_size'    => (int) ( $file['size'] ?? 0 ),
			)
		);
	}

	/**
	 * Ubah WP_Error menjadi array kode => pesan.
	 *
	 * @param WP_Error $errors Error.
	 * @return array
	 */
	private static function errors_to_array( WP_Error $errors ): array {
		$out = array();
		foreach ( $errors->get_error_codes() as $code ) {
			$out[ $code ] = $errors->get_error_message( $code );
		}
		return $out;
	}

	/**
	 * Kirim email konfirmasi bila ada.
	 *
	 * @param int $applicant_id ID pendaftar.
	 */
	private static function maybe_send_email( int $applicant_id ): void {
		$applicant = SPMB_DB_Applicants::get( $applicant_id );
		if ( ! $applicant || empty( $applicant->email ) ) {
			return;
		}
		$subject = __( 'Pendaftaran SPMB Diterima', 'spmb-pro' );
		$message = sprintf(
			/* translators: %s: nomor pendaftaran */
			__( 'Pendaftaran Anda diterima. Nomor pendaftaran: %s', 'spmb-pro' ),
			$applicant->registration_number
		);
		wp_mail( $applicant->email, $subject, $message );
	}

	/**
	 * Catat error upload pada note payment (sebagai jejak).
	 *
	 * @param int   $applicant_id ID pendaftar.
	 * @param array $errors       Error upload.
	 */
	private static function log_upload_errors( int $applicant_id, array $errors ): void {
		$payment = SPMB_DB_Payments::for_applicant( $applicant_id );
		if ( ! $payment ) {
			return;
		}
		$note = wp_json_encode( $errors );
		SPMB_DB_Payments::update( (int) $payment->id, array( 'note' => $note ) );
	}

	/**
	 * Gagal validasi: redirect kembali dengan error.
	 *
	 * @param array $errors Error.
	 */
	private static function fail( array $errors ): void {
		$redirect = add_query_arg(
			array(
				'spmb_status' => 'error',
				'spmb_errors' => rawurlencode( wp_json_encode( $errors ) ),
			),
			wp_get_referer()
		);
		wp_safe_redirect( $redirect );
		exit;
	}
}