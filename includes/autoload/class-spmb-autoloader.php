<?php
/**
 * Autoloader untuk class SPMB_*.
 *
 * Konvensi: class SPMB_<Subdir>_<Name> dipetakan ke file
 * includes/<kebab-subdir>/class-spmb-<kebab-name>.php, dan class top-level
 * SPMB_<Name> dipetakan ke includes/class-spmb-<kebab-name>.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Autoloader {

	/**
	 * Daftarkan autoloader ke spl stack.
	 */
	public static function register(): void {
		spl_autoload_register( array( __CLASS__, 'load' ) );
	}

	/**
	 * Muat class berdasarkan nama.
	 *
	 * @param string $class Nama class lengkap.
	 */
	public static function load( string $class ): void {
		if ( ! str_starts_with( $class, 'SPMB_' ) ) {
			return;
		}

		$path = self::resolve( $class );
		if ( false !== $path && is_readable( $path ) ) {
			// Subclass PDF bergantung pada FPDF; muat dulu bila perlu.
			if ( false !== strpos( $path, '/export/' ) && ! class_exists( 'FPDF' ) ) {
				self::load_fpdf();
			}
			require_once $path;
		}
	}

	/**
	 * Muat library FPDF bila belum ada.
	 */
	private static function load_fpdf(): void {
		if ( ! defined( 'FPDF_FONTPATH' ) ) {
			define( 'FPDF_FONTPATH', SPMB_PATH . 'includes/libraries/fpdf/font/' );
		}
		$fpdf = SPMB_PATH . 'includes/libraries/fpdf/fpdf.php';
		if ( is_readable( $fpdf ) ) {
			require_once $fpdf;
		}
	}

	/**
	 * Ubah nama class menjadi path file.
	 *
	 * Konvensi: file bernama class-spmb-<kebab-nama-lengkap>.php.
	 * Pencarian dilakukan di semua subdirektori includes/ lalu di root includes/.
	 *
	 * @param string $class Nama class.
	 * @return string|false Path absolut atau false bila tidak ditemukan.
	 */
	private static function resolve( string $class ): string|false {
		$relative = substr( $class, 5 ); // hapus prefix "SPMB_".
		$parts    = explode( '_', $relative );

		$kebab = static function ( string $s ): string {
			$s = str_replace( '_', '-', $s );
			$s = preg_replace( '/([a-z0-9])([A-Z])/', '$1-$2', $s );
			return strtolower( $s );
		};

		$filename = 'class-spmb-' . $kebab( implode( '-', $parts ) ) . '.php';
		$base     = SPMB_PATH . 'includes/';

		// Cari di setiap subdirektori (satu tingkat).
		foreach ( glob( $base . '*', GLOB_ONLYDIR ) as $dir ) {
			$path = $dir . '/' . $filename;
			if ( file_exists( $path ) ) {
				return $path;
			}
		}

		// Fallback: root includes/.
		$path = $base . $filename;
		return file_exists( $path ) ? $path : false;
	}
}