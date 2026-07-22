<?php
/**
 * Subclass FPDF untuk laporan seleksi (tabel paginated).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Report_Pdf extends FPDF {

	/**
	 * Jenjang laporan.
	 *
	 * @var string
	 */
	private string $jenjang = '';

	/**
	 * Set jenjang.
	 *
	 * @param string $jenjang Jenjang.
	 */
	public function set_jenjang( string $jenjang ): void {
		$this->jenjang = $jenjang;
	}

	/**
	 * Header halaman.
	 */
	public function Header(): void {
		$settings = SPMB_Settings_Repository::all();
		$this->SetFont( 'Helvetica', 'B', 14 );
		$this->Cell( 0, 7, 'Laporan Seleksi - ' . $this->jenjang, 0, 1, 'C' );
		$this->SetFont( 'Helvetica', '', 9 );
		$this->Cell( 0, 5, $settings['school_name'] ?: get_bloginfo( 'name' ), 0, 1, 'C' );
		$this->Ln( 3 );

		$this->render_table_header();
	}

	/**
	 * Footer halaman.
	 */
	public function Footer(): void {
		$this->SetY( -15 );
		$this->SetFont( 'Helvetica', 'I', 8 );
		$this->Cell( 0, 10, 'Halaman ' . $this->PageNo() . '/{nb}', 0, 0, 'C' );
	}

	/**
	 * Header tabel.
	 */
	private function render_table_header(): void {
		$this->SetFillColor( 34, 113, 177 );
		$this->SetTextColor( 255 );
		$this->SetFont( 'Helvetica', 'B', 9 );

		$widths = array( 10, 45, 30, 18, 25, 20, 25, 20 );
		$labels = array(
			$this->to_latin( __( 'No', 'spmb-pro' ) ),
			$this->to_latin( __( 'Nama', 'spmb-pro' ) ),
			$this->to_latin( __( 'No. Daftar', 'spmb-pro' ) ),
			$this->to_latin( __( 'Jalur', 'spmb-pro' ) ),
			$this->to_latin( __( 'Program', 'spmb-pro' ) ),
			$this->to_latin( __( 'Skor', 'spmb-pro' ) ),
			$this->to_latin( __( 'Status', 'spmb-pro' ) ),
			$this->to_latin( __( 'Peringkat', 'spmb-pro' ) ),
		);

		foreach ( $widths as $i => $w ) {
			$this->Cell( $w, 7, $labels[ $i ], 1, 0, 'C', true );
		}
		$this->Ln();
		$this->SetTextColor( 0 );
	}

	/**
	 * Satu baris data.
	 *
	 * @param object $a Pendaftar.
	 */
	public function row( object $a ): void {
		$widths = array( 10, 45, 30, 18, 25, 20, 25, 20 );

		if ( $this->GetY() > 180 ) {
			$this->AddPage();
		}

		$score = ( 'prestasi' === $a->jalur ) ? SPMB_Scoring::prestasi_score( $a ) : (float) ( $a->distance_km ?? 0 );

		$data = array(
			$this->to_latin( (string) $a->final_rank ),
			$this->to_latin( $a->full_name ),
			$a->registration_number,
			$a->jalur,
			$this->to_latin( $a->program_choice_1 ),
			number_format( $score, 2, ',', '' ),
			$a->selection_status,
			(string) $a->final_rank,
		);

		foreach ( $widths as $i => $w ) {
			$this->Cell( $w, 6, $data[ $i ], 1 );
		}
		$this->Ln();
	}

	/**
	 * Konversi teks UTF-8 ke CP1252.
	 *
	 * @param string $s Teks.
	 * @return string
	 */
	private function to_latin( string $s ): string {
		return function_exists( 'iconv' ) ? @iconv( 'UTF-8', 'CP1252//TRANSLIT//IGNORE', $s ) : $s;
	}
}