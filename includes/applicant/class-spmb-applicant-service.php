<?php
/**
 * Layanan pembuatan pendaftar lengkap (data + dokumen + invoice).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Applicant_Service {

	/**
	 * Buat pendaftar baru dari input tervalidasi.
	 *
	 * @param array $input Data pendaftar tervalidasi.
	 * @return int ID pendaftar, 0 bila gagal.
	 */
	public static function create( array $input ): int {
		$now    = current_time( 'mysql' );
		$year   = (int) current_time( 'Y' );
		$jenjang = $input['jenjang'];

		$reg_number = SPMB_Reg_Number_Generator::make( $year, $jenjang );

		$row = self::map_fields( $input, $reg_number );

		$applicant_id = SPMB_DB_Applicants::insert( $row );
		if ( ! $applicant_id ) {
			return 0;
		}

		SPMB_Invoice_Service::create_for( $applicant_id );

		do_action( 'spmb_applicant_created', $applicant_id, $reg_number );

		return $applicant_id;
	}

	/**
	 * Petakan input ke kolom tabel applicants.
	 *
	 * @param array  $input     Input.
	 * @param string $reg_number Nomor pendaftaran.
	 * @return array
	 */
	private static function map_fields( array $input, string $reg_number ): array {
		$fields = array(
			'registration_number' => $reg_number,
			'jenjang'             => $input['jenjang'],
			'jalur'               => $input['jalur'],
			'full_name'           => $input['full_name'],
			'nisn'                => $input['nisn'] ?? '',
			'nik'                 => $input['nik'] ?? '',
			'gender'              => $input['gender'] ?? '',
			'birth_place'         => $input['birth_place'] ?? '',
			'birth_date'          => $input['birth_date'] ?? null,
			'religion'            => $input['religion'] ?? '',
			'address'             => $input['address'] ?? '',
			'phone'               => $input['phone'] ?? '',
			'email'               => $input['email'] ?? '',
			'origin_school'       => $input['origin_school'] ?? '',
			'origin_school_npsn'  => $input['origin_school_npsn'] ?? '',
			'parent_name'         => $input['parent_name'] ?? '',
			'parent_phone'        => $input['parent_phone'] ?? '',
			'parent_job'          => $input['parent_job'] ?? '',
			'parent_income'       => (float) ( $input['parent_income'] ?? 0 ),
			'program_choice_1'    => $input['program_choice_1'] ?? '',
			'program_choice_2'    => $input['program_choice_2'] ?? '',
			'distance_km'         => (float) ( $input['distance_km'] ?? 0 ),
			'rapor_avg'           => (float) ( $input['rapor_avg'] ?? 0 ),
			'achievement_points'  => (int) ( $input['achievement_points'] ?? 0 ),
			'afirmasi_category'   => $input['afirmasi_category'] ?? '',
			'status'              => 'submitted',
			'selection_status'    => 'pending',
		);

		if ( empty( $fields['birth_date'] ) ) {
			$fields['birth_date'] = null;
		}

		return $fields;
	}
}