<?php
/**
 * Ekspor kartu pendaftar PDF (FPDF).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Export_Pdf_Kartu {

	/**
	 * Hasilkan PDF kartu untuk satu pendaftar.
	 *
	 * @param object  $applicant Data pendaftar.
	 * @param object  $payment   Data pembayaran (opsional).
	 * @param string  $mode      'D' (download) atau 'I' (inline).
	 * @return string Konten PDF (mode S tidak dipakai).
	 */
	public static function generate( object $applicant, ?object $payment, string $mode = 'D' ): string {
		SPMB_Export_Factory::load_fpdf();

		$pdf = new SPMB_Kartu_Pdf( 'P', 'mm', 'A4' );
		$pdf->set_data( $applicant, $payment );
		$pdf->AddPage();
		$pdf->render_card();

		$filename = 'Kartu-' . $applicant->registration_number . '.pdf';
		return $pdf->Output( $mode, $filename );
	}
}