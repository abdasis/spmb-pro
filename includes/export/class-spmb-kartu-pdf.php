<?php
/**
 * Subclass FPDF untuk render kartu pendaftar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Kartu_Pdf extends FPDF {

	/**
	 * Data pendaftar.
	 *
	 * @var object|null
	 */
	private $applicant;

	/**
	 * Data pembayaran.
	 *
	 * @var object|null
	 */
	private $payment;

	/**
	 * Set data pendaftar dan pembayaran.
	 *
	 * @param object      $applicant Pendaftar.
	 * @param object|null $payment   Pembayaran.
	 */
	public function set_data( object $applicant, ?object $payment ): void {
		$this->applicant = $applicant;
		$this->payment  = $payment;
	}

	/**
	 * Render isi kartu pendaftar.
	 */
	public function render_card(): void {
		$settings = SPMB_Settings_Repository::all();

		// Header sekolah.
		$this->SetFont( 'Helvetica', 'B', 16 );
		$this->Cell( 0, 8, $this->to_latin( $settings['school_name'] ?: get_bloginfo( 'name' ) ), 0, 1, 'C' );
		$this->SetFont( 'Helvetica', '', 10 );
		$this->MultiCell( 0, 5, $this->to_latin( $settings['school_address'] ), 0, 'C' );
		$this->Ln( 4 );

		// Garis pemisah.
		$this->SetDrawColor( 34, 113, 177 );
		$this->SetLineWidth( 0.6 );
		$this->Line( 15, $this->GetY(), 195, $this->GetY() );
		$this->Ln( 6 );

		$this->SetFont( 'Helvetica', 'B', 13 );
		$this->Cell( 0, 8, $this->to_latin( __( 'KARTU PENDAFTAR', 'spmb-pro' ) ), 0, 1, 'C' );
		$this->Ln( 2 );

		$this->render_rows();
		$this->render_payment_block();
	}

	/**
	 * Render baris data biodata.
	 */
	private function render_rows(): void {
		$this->SetFont( 'Helvetica', '', 11 );
		$a = $this->applicant;

		$rows = array(
			__( 'No. Pendaftaran', 'spmb-pro' ) => $a->registration_number,
			__( 'Nama', 'spmb-pro' )           => $a->full_name,
			__( 'NISN', 'spmb-pro' )           => $a->nisn,
			__( 'Jenjang', 'spmb-pro' )        => $a->jenjang,
			__( 'Jalur', 'spmb-pro' )          => $a->jalur,
			__( 'Program', 'spmb-pro' )        => $a->program_choice_1,
			__( 'Tempat Lahir', 'spmb-pro' )   => $a->birth_place,
			__( 'Tanggal Lahir', 'spmb-pro' )  => $a->birth_date,
			__( 'No. HP', 'spmb-pro' )         => $a->phone,
		);

		foreach ( $rows as $label => $value ) {
			$this->SetFont( 'Helvetica', 'B', 11 );
			$this->Cell( 50, 7, $label, 0, 0 );
			$this->Cell( 5, 7, ':', 0, 0 );
			$this->SetFont( 'Helvetica', '', 11 );
			$this->Cell( 0, 7, $this->to_latin( (string) $value ), 0, 1 );
		}
	}

	/**
	 * Render blok pembayaran bila ada.
	 */
	private function render_payment_block(): void {
		if ( ! $this->payment ) {
			return;
		}
		$this->Ln( 4 );
		$this->SetDrawColor( 200, 200, 200 );
		$this->Line( 15, $this->GetY(), 195, $this->GetY() );
		$this->Ln( 4 );

		$this->SetFont( 'Helvetica', 'B', 11 );
		$this->Cell( 50, 7, $this->to_latin( __( 'No. Invoice', 'spmb-pro' ) ), 0, 0 );
		$this->Cell( 5, 7, ':', 0, 0 );
		$this->SetFont( 'Helvetica', '', 11 );
		$this->Cell( 0, 7, $this->payment->invoice_number, 0, 1 );

		$this->SetFont( 'Helvetica', 'B', 11 );
		$this->Cell( 50, 7, $this->to_latin( __( 'Biaya', 'spmb-pro' ) ), 0, 0 );
		$this->Cell( 5, 7, ':', 0, 0 );
		$this->SetFont( 'Helvetica', '', 11 );
		$this->Cell( 0, 7, 'Rp ' . number_format( (float) $this->payment->amount, 0, ',', '.' ), 0, 1 );

		$this->SetFont( 'Helvetica', 'B', 11 );
		$this->Cell( 50, 7, $this->to_latin( __( 'Status', 'spmb-pro' ) ), 0, 0 );
		$this->Cell( 5, 7, ':', 0, 0 );
		$this->SetFont( 'Helvetica', '', 11 );
		$this->Cell( 0, 7, $this->payment->status, 0, 1 );
	}

	/**
	 * Konversi karakter non-latin (mis. teks ber-tanda diakritik) ke aman FPDF latin1.
	 *
	 * @param string $s Teks.
	 * @return string
	 */
	private function to_latin( string $s ): string {
		return function_exists( 'iconv' ) ? @iconv( 'UTF-8', 'CP1252//TRANSLIT//IGNORE', $s ) : $s;
	}
}