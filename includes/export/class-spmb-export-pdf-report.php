<?php
/**
 * Ekspor laporan seleksi PDF (tabel paginated).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Export_Pdf_Report {

	/**
	 * Hasilkan PDF laporan seleksi untuk satu jenjang.
	 *
	 * @param array  $applicants Daftar pendaftar.
	 * @param string $jenjang    Jenjang.
	 * @param string $mode       'D' download.
	 * @return string
	 */
	public static function generate( array $applicants, string $jenjang, string $mode = 'D' ): string {
		SPMB_Export_Factory::load_fpdf();

		$pdf = new SPMB_Report_Pdf( 'L', 'mm', 'A4' );
		$pdf->set_jenjang( $jenjang );
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFont( 'Helvetica', '', 9 );
		foreach ( $applicants as $a ) {
			$pdf->row( $a );
		}

		$filename = 'Laporan-Seleksi-' . $jenjang . '.pdf';
		return $pdf->Output( $mode, $filename );
	}
}