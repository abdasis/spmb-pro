<?php
/**
 * Shortcode [spmb_pengumuman] — pengumuman hasil seleksi.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Announcement_Shortcode {

	/**
	 * Daftarkan shortcode.
	 */
	public static function register(): void {
		add_shortcode( 'spmb_pengumuman', array( __CLASS__, 'render' ) );
	}

	/**
	 * Render widget pengumuman.
	 *
	 * @return string
	 */
	public static function render(): string {
		$published = (bool) SPMB_Settings_Repository::get( 'pengumuman_published' );
		$result    = $published ? self::lookup() : null;

		wp_enqueue_style( 'spmb-public', SPMB_URL . SPMB_Assets::resolve_css( 'spmb-public' ), array(), SPMB_VERSION );

		$data = array(
			'published' => $published,
			'result'    => $result,
			'submitted' => ! empty( $_GET['spmb_reg'] ), // phpcs:ignore WordPress.Security
			'regnum'    => isset( $_GET['spmb_reg'] ) ? sanitize_text_field( wp_unslash( $_GET['spmb_reg'] ) ) : '', // phpcs:ignore WordPress.Security
		);

		ob_start();
		require SPMB_PATH . 'views/public/announcement.php';
		return (string) ob_get_clean();
	}

	/**
	 * Cari pendaftar bila ada query.
	 *
	 * @return object|null
	 */
	private static function lookup(): ?object {
		if ( empty( $_GET['spmb_reg'] ) ) {
			return null;
		}
		$regnum = sanitize_text_field( wp_unslash( $_GET['spmb_reg'] ) );
		return SPMB_DB_Applicants::get_by_reg( $regnum );
	}
}