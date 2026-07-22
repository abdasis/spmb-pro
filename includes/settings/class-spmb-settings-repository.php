<?php
/**
 * Akses tunggal ke pengaturan SPMB Pro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Settings_Repository {

	private static ?array $cache = null;

	/**
	 * Ambil seluruh pengaturan (merge dengan default).
	 *
	 * @return array
	 */
	public static function all(): array {
		if ( null === self::$cache ) {
			$saved     = get_option( SPMB_Defaults::OPTION_KEY, array() );
			$saved     = is_array( $saved ) ? $saved : array();
			self::$cache = wp_parse_args( $saved, SPMB_Defaults::settings() );
		}
		return self::$cache;
	}

	/**
	 * Ambil satu nilai pengaturan.
	 *
	 * @param string $key     Kunci.
	 * @param mixed  $default Default bila tidak ada.
	 * @return mixed
	 */
	public static function get( string $key, $default = null ) {
		$all = self::all();
		return array_key_exists( $key, $all ) ? $all[ $key ] : $default;
	}

	/**
	 * Simpan seluruh pengaturan.
	 *
	 * @param array $settings Array pengaturan.
	 */
	public static function save( array $settings ): void {
		$clean = self::sanitize( $settings );
		update_option( SPMB_Defaults::OPTION_KEY, $clean );
		self::$cache = $clean;
	}

	/**
	 * Reset cache (dipakai setelah penyimpanan eksternal).
	 */
	public static function flush(): void {
		self::$cache = null;
	}

	/**
	 * Sanitasi nilai pengaturan sesuai tipe.
	 *
	 * @param array $input Input mentah.
	 * @return array
	 */
	public static function sanitize( array $input ): array {
		$defaults = SPMB_Defaults::settings();
		$out      = array();

		$out['school_name']      = sanitize_text_field( $input['school_name'] ?? $defaults['school_name'] );
		$out['school_address']   = sanitize_textarea_field( $input['school_address'] ?? $defaults['school_address'] );
		$out['school_latitude']  = sanitize_text_field( $input['school_latitude'] ?? $defaults['school_latitude'] );
		$out['school_longitude'] = sanitize_text_field( $input['school_longitude'] ?? $defaults['school_longitude'] );

		$out['jenjang']        = self::sanitize_jenjang( $input['jenjang'] ?? array() );
		$out['enabled_jalur']   = self::sanitize_jalur_flags( $input['enabled_jalur'] ?? array() );
		$out['quotas']          = self::sanitize_quotas( $input['quotas'] ?? array(), $out['jenjang'] );
		$out['programs']        = self::sanitize_programs_raw( $input['programs'] ?? array(), $input['programs_raw'] ?? array(), $out['jenjang'] );
		$out['afirmasi_categories'] = array_values( array_filter( array_map( 'sanitize_text_field', (array) ( $input['afirmasi_categories'] ?? $defaults['afirmasi_categories'] ) ) ) );

		$out['prestasi_weights'] = array(
			'rapor'       => floatval( $input['prestasi_weights']['rapor'] ?? $defaults['prestasi_weights']['rapor'] ),
			'achievement' => floatval( $input['prestasi_weights']['achievement'] ?? $defaults['prestasi_weights']['achievement'] ),
		);

		$out['fee']                = floatval( $input['fee'] ?? $defaults['fee'] );
		$out['registration_open']  = sanitize_text_field( $input['registration_open'] ?? $defaults['registration_open'] );
		$out['registration_close'] = sanitize_text_field( $input['registration_close'] ?? $defaults['registration_close'] );
		$out['allowed_mimes']      = array_values( array_filter( (array) ( $input['allowed_mimes'] ?? $defaults['allowed_mimes'] ) ) );
		$out['max_file_size']      = absint( $input['max_file_size'] ?? $defaults['max_file_size'] );
		$out['pengumuman_published']    = ! empty( $input['pengumuman_published'] );
		$out['delete_files_on_uninstall'] = ! empty( $input['delete_files_on_uninstall'] );

		return $out;
	}

	/**
	 * Sanitasi daftar jenjang (SD/SMP/SMA).
	 *
	 * @param mixed $raw Input.
	 * @return array
	 */
	private static function sanitize_jenjang( $raw ): array {
		$allowed = array( 'SD', 'SMP', 'SMA' );
		$clean   = array();
		foreach ( (array) $raw as $j ) {
			$j = sanitize_text_field( $j );
			if ( in_array( $j, $allowed, true ) && ! in_array( $j, $clean, true ) ) {
				$clean[] = $j;
			}
		}
		return $clean ?: array( 'SMP' );
	}

	/**
	 * Sanitasi flag enable per jalur.
	 *
	 * @param mixed $raw Input.
	 * @return array
	 */
	private static function sanitize_jalur_flags( $raw ): array {
		$out = array_fill_keys( SPMB_Defaults::JALUR, false );
		foreach ( (array) $raw as $jalur => $val ) {
			if ( in_array( $jalur, SPMB_Defaults::JALUR, true ) ) {
				$out[ $jalur ] = (bool) $val;
			}
		}
		return $out;
	}

	/**
	 * Sanitasi kuota per jenjang per jalur.
	 *
	 * @param mixed $raw   Input.
	 * @param array $jenjang Daftar jenjang.
	 * @return array
	 */
	private static function sanitize_quotas( $raw, array $jenjang ): array {
		$out = array();
		foreach ( $jenjang as $j ) {
			$out[ $j ] = array();
			foreach ( SPMB_Defaults::JALUR as $jalur ) {
				$out[ $j ][ $jalur ] = absint( $raw[ $j ][ $jalur ] ?? 0 );
			}
		}
		return $out;
	}

	/**
	 * Sanitasi daftar program per jenjang dari format kode=nilai dipisah koma.
	 *
	 * @param mixed $raw     Input program (format array kode=>nilai), bila ada.
	 * @param mixed $raw_str Input program mentah (format "kode=nilai,kode=nilai").
	 * @param array $jenjang Daftar jenjang.
	 * @return array
	 */
	private static function sanitize_programs_raw( $raw, $raw_str, array $jenjang ): array {
		$out = array();
		foreach ( $jenjang as $j ) {
			$pairs   = array();
			$raw_str = (array) $raw_str;
			$string  = $raw_str[ $j ] ?? '';

			if ( $string ) {
				foreach ( explode( ',', $string ) as $pair ) {
					$parts = explode( '=', $pair, 2 );
					if ( 2 === count( $parts ) ) {
						$code = sanitize_text_field( $parts[0] );
						$name = sanitize_text_field( $parts[1] );
						if ( $code ) {
							$pairs[ $code ] = $name;
						}
					}
				}
			} elseif ( isset( $raw[ $j ] ) && is_array( $raw[ $j ] ) ) {
				foreach ( $raw[ $j ] as $code => $name ) {
					$code = sanitize_text_field( $code );
					if ( $code ) {
						$pairs[ $code ] = sanitize_text_field( $name );
					}
				}
			}
			$out[ $j ] = $pairs;
		}
		return $out;
	}
}