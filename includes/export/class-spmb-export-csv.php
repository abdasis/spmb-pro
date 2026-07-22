<?php
/**
 * Ekspor CSV daftar pendaftar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Export_Csv {

	/**
	 * Hasilkan CSV daftar pendaftar.
	 *
	 * @param array  $applicants Daftar pendaftar.
	 * @param string $filename   Nama file.
	 * @return string Konten CSV.
	 */
	public static function generate( array $applicants, string $filename = 'pendaftar.csv' ): string {
		while ( ob_get_level() ) {
			ob_end_clean();
		}

		SPMB_Export_Factory::download_headers( $filename, 'text/csv; charset=' . get_bloginfo( 'charset' ) );

		$out = fopen( 'php://output', 'w' );

		// Header kolom.
		fputcsv( $out, self::headers() );

		foreach ( $applicants as $a ) {
			fputcsv( $out, self::row( $a ) );
		}

		fclose( $out );
		return '';
	}

	/**
	 * Daftar header kolom.
	 *
	 * @return array
	 */
	private static function headers(): array {
		return array(
			__( 'No. Pendaftaran', 'spmb-pro' ),
			__( 'Nama', 'spmb-pro' ),
			__( 'Jenjang', 'spmb-pro' ),
			__( 'Jalur', 'spmb-pro' ),
			__( 'Program', 'spmb-pro' ),
			__( 'Status', 'spmb-pro' ),
			__( 'Status Seleksi', 'spmb-pro' ),
			__( 'Peringkat', 'spmb-pro' ),
			__( 'Jarak (km)', 'spmb-pro' ),
			__( 'Rapor', 'spmb-pro' ),
		);
	}

	/**
	 * Satu baris CSV.
	 *
	 * @param object $a Pendaftar.
	 * @return array
	 */
	private static function row( object $a ): array {
		return array(
			$a->registration_number,
			$a->full_name,
			$a->jenjang,
			$a->jalur,
			$a->program_choice_1,
			$a->status,
			$a->selection_status,
			(int) $a->final_rank,
			(float) $a->distance_km,
			(float) $a->rapor_avg,
		);
	}
}