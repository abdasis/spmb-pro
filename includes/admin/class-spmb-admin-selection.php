<?php
/**
 * Layar pengumuman & seleksi.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Admin_Selection {

	/**
	 * Render layar seleksi.
	 */
	public static function render(): void {
		if ( ! current_user_can( SPMB_Roles::CAP ) ) {
			wp_die( esc_html__( 'Anda tidak punya akses ke halaman ini.', 'spmb-pro' ) );
		}

		$notice  = self::handle_actions();
		$settings = SPMB_Settings_Repository::all();
		$published = (bool) $settings['pengumuman_published'];
		$jenjang  = $settings['jenjang'];
		$active_jenjang = isset( $_GET['jenjang'] ) ? sanitize_key( wp_unslash( $_GET['jenjang'] ) ) : ( $jenjang[0] ?? '' ); // phpcs:ignore

		$summary = self::build_summary( $settings, $active_jenjang );
		$runs    = SPMB_DB_Selection::recent_runs( $active_jenjang );

		require SPMB_PATH . 'views/admin/selection.php';
	}

	/**
	 * Tangani aksi: jalankan seleksi, publikasikan/batal.
	 *
	 * @return string Pesan notice.
	 */
	private static function handle_actions(): string {
		if ( empty( $_POST['spmb_selection_action'] ) ) {
			return '';
		}
		check_admin_referer( 'spmb_selection', 'spmb_selection_nonce' );

		$action = sanitize_key( wp_unslash( $_POST['spmb_selection_action'] ) );
		$jenjang = sanitize_key( wp_unslash( $_POST['jenjang'] ?? '' ) );

		switch ( $action ) {
			case 'run':
				$result = SPMB_Selection_Engine::run( $jenjang );
				return sprintf(
					/* translators: 1: jumlah diterima, 2: jumlah cadangan */
					__( 'Seleksi dijalankan. Diterima: %1$d, Cadangan: %2$d.', 'spmb-pro' ),
					$result['admitted'],
					$result['waitlist']
				);
			case 'publish':
				self::set_publish( true );
				return __( 'Pengumuman dipublikasikan.', 'spmb-pro' );
			case 'unpublish':
				self::set_publish( false );
				return __( 'Pengumuman ditarik kembali.', 'spmb-pro' );
		}
		return '';
	}

	/**
	 * Set flag publikasi pengumuman.
	 *
	 * @param bool $publish Publikasikan atau tarik.
	 */
	private static function set_publish( bool $publish ): void {
		$settings = SPMB_Settings_Repository::all();
		$settings['pengumuman_published'] = $publish;
		SPMB_Settings_Repository::save( $settings );
	}

	/**
	 * Susun ringkasan eligible vs kuota per jalur.
	 *
	 * @param array  $settings Pengaturan.
	 * @param string $jenjang  Jenjang aktif.
	 * @return array
	 */
	private static function build_summary( array $settings, string $jenjang ): array {
		if ( ! $jenjang ) {
			return array();
		}
		$eligible = SPMB_DB_Selection::eligible_applicants( $jenjang );
		$quotas   = $settings['quotas'][ $jenjang ] ?? array();

		$per_jalur = array();
		foreach ( SPMB_Defaults::JALUR as $j ) {
			$members = array_filter( $eligible, fn( $a ) => $a->jalur === $j );
			$per_jalur[ $j ] = array(
				'eligible' => count( $members ),
				'quota'    => $quotas[ $j ] ?? 0,
			);
		}
		return array(
			'total_eligible' => count( $eligible ),
			'per_jalur'      => $per_jalur,
		);
	}
}