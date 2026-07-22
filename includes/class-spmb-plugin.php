<?php
/**
 * Container utama plugin SPMB Pro.
 *
 * Mendaftarkan hook i18n dan menyediakan singleton. Hook fungsional
 * (menu, shortcode, REST, dll) ditambahkan pada phase berikutnya.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Plugin {

	private static ?SPMB_Plugin $instance = null;

	/**
	 * Ambil instance singleton.
	 */
	public static function instance(): SPMB_Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Inisialisasi hook.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		SPMB_Admin_Menu::register();
		SPMB_Assets::register();
		SPMB_Form_Shortcode::register();
		SPMB_Tracking_Shortcode::register();
		SPMB_Announcement_Shortcode::register();
		SPMB_Form_Handler::register();
		SPMB_Export_Handler::register();
		SPMB_REST_Controller::register();
	}

	/**
	 * Muat file terjemahan plugin.
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'spmb-pro',
			false,
			dirname( SPMB_BASENAME ) . '/lang'
		);
	}
}