<?php
/**
 * Shortcode [spmb_form].
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Form_Shortcode {

	/**
	 * Daftarkan shortcode.
	 */
	public static function register(): void {
		add_shortcode( 'spmb_form', array( __CLASS__, 'render' ) );
	}

	/**
	 * Render form pendaftaran.
	 *
	 * @param array $atts Atribut shortcode.
	 * @return string
	 */
	public static function render( array $atts = array() ): string {
		$atts     = shortcode_atts( array( 'jenjang' => '' ), $atts, 'spmb_form' );
		$settings = SPMB_Settings_Repository::all();

		if ( empty( $settings['jenjang'] ) ) {
			return '<p>' . esc_html__( 'Pendaftaran belum dibuka. Hubungi sekolah.', 'spmb-pro' ) . '</p>';
		}

		self::enqueue_assets();

		$data = self::build_view_data( $atts, $settings );

		ob_start();
		require SPMB_PATH . 'views/public/form.php';
		return (string) ob_get_clean();
	}

	/**
	 * Susun data untuk view.
	 *
	 * @param array $atts     Atribut.
	 * @param array $settings Pengaturan.
	 * @return array
	 */
	private static function build_view_data( array $atts, array $settings ): array {
		$jalur_labels = array(
			'zonasi'      => __( 'Zonasi', 'spmb-pro' ),
			'afirmasi'    => __( 'Afirmasi', 'spmb-pro' ),
			'prestasi'    => __( 'Prestasi', 'spmb-pro' ),
			'perpindahan' => __( 'Perpindahan Tugas', 'spmb-pro' ),
		);

		return array(
			'action'              => admin_url( 'admin-post.php' ),
			'nonce'               => wp_create_nonce( 'spmb_form_nonce' ),
			'settings'            => $settings,
			'fixed_jenjang'       => sanitize_text_field( $atts['jenjang'] ),
			'religions'           => self::religions(),
			'jalur_labels'        => $jalur_labels,
			'status_message'      => self::status_message(),
			'success_reg'         => isset( $_GET['reg'] ) ? sanitize_text_field( wp_unslash( $_GET['reg'] ) ) : '',
			'field_errors'        => self::field_errors(),
		);
	}

	/**
	 * Daftar agama.
	 *
	 * @return array
	 */
	private static function religions(): array {
		return array(
			'islam'     => __( 'Islam', 'spmb-pro' ),
			'kristen'   => __( 'Kristen', 'spmb-pro' ),
			'katolik'   => __( 'Katolik', 'spmb-pro' ),
			'hindu'     => __( 'Hindu', 'spmb-pro' ),
			'buddha'    => __( 'Buddha', 'spmb-pro' ),
			'konghucu'  => __( 'Konghucu', 'spmb-pro' ),
		);
	}

	/**
	 * Pesan status dari query string.
	 *
	 * @return string
	 */
	private static function status_message(): string {
		if ( ! isset( $_GET['spmb_status'] ) ) {
			return '';
		}
		$status = sanitize_key( wp_unslash( $_GET['spmb_status'] ) );
		if ( 'success' === $status ) {
			return __( 'Pendaftaran berhasil.', 'spmb-pro' );
		}
		if ( 'error' === $status ) {
			return __( 'Terdapat kesalahan pada isian. Periksa kembali.', 'spmb-pro' );
		}
		return '';
	}

	/**
	 * Ambil error field dari query string.
	 *
	 * @return array
	 */
	private static function field_errors(): array {
		if ( empty( $_GET['spmb_errors'] ) ) {
			return array();
		}
		$decoded = json_decode( (string) rawurldecode( wp_unslash( $_GET['spmb_errors'] ) ), true );
		return is_array( $decoded ) ? $decoded : array();
	}

	/**
	 * Enqueue asset publik.
	 */
	private static function enqueue_assets(): void {
		wp_enqueue_style(
			'spmb-public',
			SPMB_URL . SPMB_Assets::resolve_css( 'spmb-public' ),
			array(),
			SPMB_VERSION
		);
		wp_enqueue_script(
			'spmb-public',
			SPMB_URL . 'assets/js/spmb-public.js',
			array(),
			SPMB_VERSION,
			true
		);
		wp_localize_script(
			'spmb-public',
			'SPMB_Public',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'restUrl'   => esc_url_raw( rest_url( 'spmb/v1' ) ),
				'nonce'     => wp_create_nonce( 'wp_rest' ),
				'submitUrl' => admin_url( 'admin-post.php' ),
			)
		);
	}
}