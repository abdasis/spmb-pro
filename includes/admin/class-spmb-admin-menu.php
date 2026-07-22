<?php
/**
 * Registrasi menu admin SPMB Pro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Admin_Menu {

	/**
	 * Hook ke admin_menu.
	 */
	public static function register(): void {
		add_action( 'admin_menu', array( __CLASS__, 'add_menus' ) );
	}

	/**
	 * Tambahkan menu utama dan submenu.
	 */
	public static function add_menus(): void {
		$cap   = SPMB_Roles::CAP;
		$icon  = 'dashicons-welcome-learn-more';

		add_menu_page(
			__( 'SPMB Pro', 'spmb-pro' ),
			__( 'SPMB Pro', 'spmb-pro' ),
			$cap,
			'spmb-pro',
			array( SPMB_Admin_Dashboard::class, 'render' ),
			$icon,
			26
		);

		add_submenu_page(
			'spmb-pro',
			__( 'Dashboard', 'spmb-pro' ),
			__( 'Dashboard', 'spmb-pro' ),
			$cap,
			'spmb-pro',
			array( SPMB_Admin_Dashboard::class, 'render' )
		);

		add_submenu_page(
			'spmb-pro',
			__( 'Pendaftar', 'spmb-pro' ),
			__( 'Pendaftar', 'spmb-pro' ),
			$cap,
			'spmb-pro-applicants',
			array( SPMB_Admin_Applicants::class, 'render' )
		);

		add_submenu_page(
			'spmb-pro',
			__( 'Verifikasi Pembayaran', 'spmb-pro' ),
			__( 'Verifikasi Pembayaran', 'spmb-pro' ),
			$cap,
			'spmb-pro-payments',
			array( SPMB_Admin_Payment_Verify::class, 'render' )
		);

		add_submenu_page(
			'spmb-pro',
			__( 'Pengumuman & Seleksi', 'spmb-pro' ),
			__( 'Pengumuman & Seleksi', 'spmb-pro' ),
			$cap,
			'spmb-pro-selection',
			array( SPMB_Admin_Selection::class, 'render' )
		);

		add_submenu_page(
			'spmb-pro',
			__( 'Pengaturan', 'spmb-pro' ),
			__( 'Pengaturan', 'spmb-pro' ),
			$cap,
			'spmb-pro-settings',
			array( SPMB_Admin_Settings::class, 'render' )
		);
	}
}