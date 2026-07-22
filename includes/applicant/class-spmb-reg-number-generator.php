<?php
/**
 * Generator nomor pendaftaran format SPMB-YYYY-JENJANG-SEQ6.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Reg_Number_Generator {

	/**
	 * Buat nomor pendaftaran baru untuk tahun+jenjang.
	 *
	 * @param int    $year    Tahun.
	 * @param string $jenjang Jenjang.
	 * @return string
	 */
	public static function make( int $year, string $jenjang ): string {
		$seq = SPMB_DB_Applicants::max_seq( $year, $jenjang );

		for ( $attempt = 0; $attempt < 5; $attempt++ ) {
			$seq++;
			$reg = sprintf( 'SPMB-%d-%s-%06d', $year, $jenjang, $seq );

			if ( null === SPMB_DB_Applicants::get_by_reg( $reg ) ) {
				return $reg;
			}
		}

		// Fallback: paksa dengan seq berikutnya.
		return sprintf( 'SPMB-%d-%s-%06d', $year, $jenjang, $seq );
	}
}