<?php
/**
 * Handler upload berkas pendaftaran.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Uploader {

	/**
	 * Proses satu berkas upload dan catat ke DB.
	 *
	 * @param array  $file      Elemen $_FILES.
	 * @param string $reg_number Nomor pendaftaran (nama folder).
	 * @param string $doc_type  Tipe dokumen.
	 * @return int|WP_Error ID dokumen atau error.
	 */
	public static function handle( array $file, string $reg_number, string $doc_type ) {
		if ( empty( $file['name'] ) ) {
			return 0; // Field kosong, dilewati.
		}

		self::require_upload_deps();

		$mime_check = self::check_mime_size( $file );
		if ( is_wp_error( $mime_check ) ) {
			return $mime_check;
		}

		$file = self::override_dir( $file, $reg_number, $doc_type );
		$result = wp_handle_upload( $file, array( 'test_form' => false ) );

		if ( is_wp_error( $result ) || empty( $result['file'] ) ) {
			return new WP_Error( 'upload_failed', __( 'Gagal mengunggah berkas.', 'spmb-pro' ) );
		}

		$uploads = wp_upload_dir();
		$relative = str_replace( trailingslashit( $uploads['basedir'] ), '', $result['file'] );

		return $relative;
	}

	/**
	 * Validasi tipe dan ukuran berkas.
	 *
	 * @param array $file Elemen $_FILES.
	 * @return true|WP_Error
	 */
	private static function check_mime_size( array $file ) {
		$settings = SPMB_Settings_Repository::all();
		$allowed  = $settings['allowed_mimes'];
		$max_size = (int) $settings['max_file_size'];

		if ( ! empty( $file['type'] ) && ! in_array( $file['type'], $allowed, true ) ) {
			return new WP_Error( 'mime', __( 'Tipe berkas tidak diizinkan.', 'spmb-pro' ) );
		}
		if ( $max_size > 0 && ! empty( $file['size'] ) && (int) $file['size'] > $max_size ) {
			return new WP_Error( 'size', __( 'Ukuran berkas melebihi batas.', 'spmb-pro' ) );
		}
		return true;
	}

	/**
	 * Atur path tujuan khusus spmb-pro/<regnum>/.
	 *
	 * @param array  $file       Elemen $_FILES.
	 * @param string $reg_number Nomor pendaftaran.
	 * @param string $doc_type   Tipe dokumen.
	 * @return array
	 */
	private static function override_dir( array $file, string $reg_number, string $doc_type ): array {
		$safe = preg_replace( '/[^A-Za-z0-9_-]/', '-', $reg_number );
		$uploads = wp_upload_dir();
		$dir     = trailingslashit( $uploads['basedir'] ) . 'spmb-pro/' . $safe;

		if ( ! is_dir( $dir ) ) {
			wp_mkdir_p( $dir );
		}

		// Rename sederhana: doc_type + ekstensi asli.
		$ext     = pathinfo( $file['name'], PATHINFO_EXTENSION );
		$file['name'] = $doc_type . '.' . $ext;

		add_filter( 'upload_dir', array( __CLASS__, 'filter_upload_dir' ) );
		self::$current_dir = $dir;
		return $file;
	}

	/**
	 * Variabel sementara path tujuan.
	 *
	 * @var string|null
	 */
	private static ?string $current_dir = null;

	/**
	 * Filter upload_dir untuk mengarahkan ke folder spmb-pro.
	 *
	 * @param array $dirs Konfigurasi upload.
	 * @return array
	 */
	public static function filter_upload_dir( array $dirs ): array {
		if ( null === self::$current_dir ) {
			return $dirs;
		}
		$uploads = wp_upload_dir();
		$relative = str_replace( trailingslashit( $uploads['basedir'] ), '', self::$current_dir );
		$dirs['path']   = self::$current_dir;
		$dirs['url']    = trailingslashit( $uploads['baseurl'] ) . $relative;
		$dirs['subdir'] = '/' . $relative;
		return $dirs;
	}

	/**
	 * Muat dependensi fungsi upload WordPress.
	 */
	private static function require_upload_deps(): void {
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
	}
}