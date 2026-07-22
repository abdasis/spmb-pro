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
			require_once $path;
		}
	}

	/**
	 * Ubah nama class menjadi path file.
	 *
	 * @param string $class Nama class.
	 * @return string|false Path absolut atau false bila tidak ditemukan.
	 */
	private static function resolve( string $class ): string|false {
		$relative = substr( $class, 5 ); // hapus prefix "SPMB_".
		$parts    = explode( '_', $relative );

		$kebab = static function ( string $s ): string {
			return strtolower( preg_replace( '/([a-z0-9])([A-Z])/', '$1-$2', $s ) );
		};

		if ( count( $parts ) === 1 ) {
			// Top-level: SPMB_Name -> includes/class-spmb-name.php.
			$file = 'class-spmb-' . $kebab( $parts[0] ) . '.php';
			$path = SPMB_PATH . 'includes/' . $file;
			return file_exists( $path ) ? $path : false;
		}

		// Subdir: SPMB_Subdir_Name -> includes/<kebab-subdir>/class-spmb-<kebab-name>.php.
		$subdir = $kebab( $parts[0] );
		$name   = $kebab( implode( '_', array_slice( $parts, 1 ) ) );
		$path   = SPMB_PATH . 'includes/' . $subdir . '/class-spmb-' . $name . '.php';

		if ( file_exists( $path ) ) {
			return $path;
		}

		// Fallback: seluruh segmen sebagai nama top-level.
		$flat = $kebab( implode( '-', $parts ) );
		$path = SPMB_PATH . 'includes/class-spmb-' . $flat . '.php';
		return file_exists( $path ) ? $path : false;
	}
}