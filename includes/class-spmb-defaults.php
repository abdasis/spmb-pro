<?php
/**
 * Nilai default pengaturan SPMB Pro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Defaults {

	/**
	 * Kunci option pengaturan.
	 */
	public const OPTION_KEY = 'spmb_pro_settings';

	/**
	 * Daftar jalur seleksi yang didukung.
	 */
	public const JALUR = array( 'zonasi', 'afirmasi', 'prestasi', 'perpindahan' );

	/**
	 * Kembalikan array pengaturan default.
	 *
	 * @return array
	 */
	public static function settings(): array {
		$quota_smp = array_fill_keys( self::JALUR, 0 );

		return array(
			'school_name'             => '',
			'school_address'          => '',
			'school_latitude'         => '',
			'school_longitude'        => '',
			'jenjang'                 => array( 'SMP' ),
			'enabled_jalur'           => array_fill_keys( self::JALUR, true ),
			'quotas'                  => array( 'SMP' => $quota_smp ),
			'programs'                => array( 'SMP' => array() ),
			'afirmasi_categories'     => array( 'ekonomi', 'diffabel', 'lainnya' ),
			'prestasi_weights'        => array(
				'rapor'       => 0.7,
				'achievement' => 0.3,
			),
			'fee'                     => 0.0,
			'registration_open'       => '',
			'registration_close'      => '',
			'allowed_mimes'           => array( 'application/pdf', 'image/jpeg', 'image/png' ),
			'max_file_size'           => 2097152,
			'pengumuman_published'    => false,
			'delete_files_on_uninstall' => false,
		);
	}

	/**
	 * Label terbaca untuk slug jalur.
	 *
	 * @param string $slug Slug jalur.
	 * @return string
	 */
	public static function jalur_label( string $slug ): string {
		$labels = array(
			'zonasi'      => __( 'Zonasi', 'spmb-pro' ),
			'afirmasi'    => __( 'Afirmasi', 'spmb-pro' ),
			'prestasi'    => __( 'Prestasi', 'spmb-pro' ),
			'perpindahan' => __( 'Perpindahan Tugas', 'spmb-pro' ),
		);
		return $labels[ $slug ] ?? ucfirst( $slug );
	}

	/**
	 * Label terbaca untuk slug status pendaftar.
	 *
	 * @param string $slug Slug status.
	 * @return string
	 */
	public static function status_label( string $slug ): string {
		$labels = array(
			'draft'     => __( 'Draft', 'spmb-pro' ),
			'submitted' => __( 'Submitted', 'spmb-pro' ),
			'verified'  => __( 'Terverifikasi', 'spmb-pro' ),
			'rejected'  => __( 'Ditolak', 'spmb-pro' ),
		);
		return $labels[ $slug ] ?? ucfirst( $slug );
	}
}