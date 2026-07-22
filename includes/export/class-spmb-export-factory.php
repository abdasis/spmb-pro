<?php
/**
 * Factory ekspor: muat FPDF on-demand, dispatch tipe ekspor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Export_Factory {

	/**
	 * Muat FPDF bila belum.
	 */
	public static function load_fpdf(): void {
		if ( defined( 'FPDF_VERSION' ) || class_exists( 'FPDF' ) ) {
			return;
		}
		if ( ! defined( 'FPDF_FONTPATH' ) ) {
			define( 'FPDF_FONTPATH', SPMB_PATH . 'includes/libraries/fpdf/font/' );
		}
		require_once SPMB_PATH . 'includes/libraries/fpdf/fpdf.php';
	}

	/**
	 * Kirim header unduhan.
	 *
	 * @param string $filename Nama file.
	 * @param string $type     Tipe mime.
	 * @param int    $size     Ukuran (opsional).
	 */
	public static function download_headers( string $filename, string $type = 'application/pdf', int $size = 0 ): void {
		// Hapus buffer agar header bersih.
		while ( ob_get_level() ) {
			ob_end_clean();
		}
		nocache_headers();
		header( 'Content-Type: ' . $type );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		if ( $size ) {
			header( 'Content-Length: ' . $size );
		}
	}
}