<?php
/**
 * Validator server-side form pendaftaran.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Form_Validator {

	/**
	 * Validasi input form pendaftaran.
	 *
	 * @param array $input Input mentah (sudah wp_unslash).
	 * @return WP_Error Error kosong bila valid.
	 */
	public static function validate( array $input ): WP_Error {
		$errors = new WP_Error();
		$settings = SPMB_Settings_Repository::all();

		self::validate_required( $input, $errors );
		self::validate_formats( $input, $errors );
		self::validate_jalur_jenjang( $input, $settings, $errors );
		self::validate_jalur_specific( $input, $errors );

		return $errors;
	}

	/**
	 * Cek field wajib.
	 *
	 * @param array     $input  Input.
	 * @param WP_Error  $errors Wadah error.
	 */
	private static function validate_required( array $input, WP_Error $errors ): void {
		$required = array( 'full_name', 'gender', 'jenjang', 'jalur', 'parent_name' );
		foreach ( $required as $field ) {
			if ( empty( $input[ $field ] ) ) {
				$errors->add( $field, __( 'Field wajib diisi.', 'spmb-pro' ) );
			}
		}
	}

	/**
	 * Validasi format NIK, NISN, email, tanggal.
	 *
	 * @param array     $input  Input.
	 * @param WP_Error  $errors Wadah error.
	 */
	private static function validate_formats( array $input, WP_Error $errors ): void {
		if ( ! empty( $input['nik'] ) && ! preg_match( '/^\d{16}$/', $input['nik'] ) ) {
			$errors->add( 'nik', __( 'NIK harus 16 digit angka.', 'spmb-pro' ) );
		}
		if ( ! empty( $input['nisn'] ) && ! preg_match( '/^\d{10}$/', $input['nisn'] ) ) {
			$errors->add( 'nisn', __( 'NISN harus 10 digit angka.', 'spmb-pro' ) );
		}
		if ( ! empty( $input['email'] ) && ! is_email( $input['email'] ) ) {
			$errors->add( 'email', __( 'Format email tidak valid.', 'spmb-pro' ) );
		}
		if ( ! empty( $input['birth_date'] ) ) {
			$d = DateTime::createFromFormat( 'Y-m-d', $input['birth_date'] );
			if ( ! $d || $d->format( 'Y-m-d' ) !== $input['birth_date'] ) {
				$errors->add( 'birth_date', __( 'Format tanggal lahir tidak valid.', 'spmb-pro' ) );
			}
		}
	}

	/**
	 * Validasi jenjang dan jalur aktif.
	 *
	 * @param array    $input    Input.
	 * @param array    $settings Pengaturan.
	 * @param WP_Error $errors   Wadah error.
	 */
	private static function validate_jalur_jenjang( array $input, array $settings, WP_Error $errors ): void {
		if ( ! empty( $input['jenjang'] ) && ! in_array( $input['jenjang'], $settings['jenjang'], true ) ) {
			$errors->add( 'jenjang', __( 'Jenjang tidak aktif.', 'spmb-pro' ) );
		}
		$jalur = $input['jalur'] ?? '';
		if ( $jalur && ! in_array( $jalur, SPMB_Defaults::JALUR, true ) ) {
			$errors->add( 'jalur', __( 'Jalur tidak valid.', 'spmb-pro' ) );
		}
		if ( $jalur && empty( $settings['enabled_jalur'][ $jalur ] ) ) {
			$errors->add( 'jalur', __( 'Jalur tidak aktif.', 'spmb-pro' ) );
		}
	}

	/**
	 * Validasi field spesifik per jalur.
	 *
	 * @param array    $input  Input.
	 * @param WP_Error $errors Wadah error.
	 */
	private static function validate_jalur_specific( array $input, WP_Error $errors ): void {
		$jalur = $input['jalur'] ?? '';

		if ( 'prestasi' === $jalur ) {
			$rapor = (float) ( $input['rapor_avg'] ?? 0 );
			if ( $rapor <= 0 || $rapor > 100 ) {
				$errors->add( 'rapor_avg', __( 'Rata-rata rapor harus antara 0 dan 100.', 'spmb-pro' ) );
			}
		}

		if ( 'afirmasi' === $jalur && empty( $input['afirmasi_category'] ) ) {
			$errors->add( 'afirmasi_category', __( 'Kategori afirmasi wajib diisi.', 'spmb-pro' ) );
		}
	}
}