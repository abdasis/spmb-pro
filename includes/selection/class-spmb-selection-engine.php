<?php
/**
 * Engine seleksi PPDB 3 jalur per jenjang.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Selection_Engine {

	/**
	 * Jalankan seleksi untuk satu jenjang.
	 *
	 * @param string $jenjang Jenjang.
	 * @return array Ringkasan: admitted, waitlist, per_jalur, run_id.
	 */
	public static function run( string $jenjang ): array {
		$settings = SPMB_Settings_Repository::all();
		$quotas   = $settings['quotas'][ $jenjang ] ?? array_fill_keys( SPMB_Defaults::JALUR, 0 );

		$eligible   = SPMB_DB_Selection::eligible_applicants( $jenjang );
		$eligible_ids = array_map( fn( $a ) => (int) $a->id, $eligible );

		// Reset semua pendaftar jenjang ini ke pending sebelum seleksi ulang.
		self::reset_jenjang( $jenjang );

		$summary = array(
			'admitted'  => 0,
			'waitlist'  => 0,
			'per_jalur' => array(),
		);

		$jalur_classes = self::jalur_classes();
		foreach ( $jalur_classes as $class ) {
			$jalur = $class::jalur();

			if ( empty( $settings['enabled_jalur'][ $jalur ] ) ) {
				continue;
			}

			$members  = self::filter_jalur( $eligible, $jalur );
			$ranked   = $class::rank( $members );
			$quota    = (int) ( $quotas[ $jalur ] ?? 0 );
			$applied  = SPMB_Quota_Manager::apply( $ranked, $quota );

			SPMB_Quota_Manager::persist( $applied['diterima'], $applied['cadangan'] );

			$summary['admitted']  += count( $applied['diterima'] );
			$summary['waitlist']  += count( $applied['cadangan'] );
			$summary['per_jalur'][ $jalur ] = array(
				'quota'    => $quota,
				'ranked'   => count( $ranked ),
				'diterima' => count( $applied['diterima'] ),
				'cadangan' => count( $applied['cadangan'] ),
			);
		}

		// Tandai pendaftar tidak eligible sebagai tidak_lolos.
		self::mark_ineligible_in_jenjang( $jenjang, $eligible_ids );

		$run_id = SPMB_DB_Selection::insert_run(
			array(
				'run_by'         => get_current_user_id(),
				'jenjang'        => $jenjang,
				'config_snapshot' => wp_json_encode( $quotas ),
				'admitted_count' => $summary['admitted'],
				'waitlist_count' => $summary['waitlist'],
			)
		);
		$summary['run_id'] = $run_id;

		return $summary;
	}

	/**
	 * Daftar class jalur aktif.
	 *
	 * @return array
	 */
	private static function jalur_classes(): array {
		return array(
			SPMB_Jalur_Zonasi::class,
			SPMB_Jalur_Afirmasi::class,
			SPMB_Jalur_Prestasi::class,
			SPMB_Jalur_Perpindahan::class,
		);
	}

	/**
	 * Saring pendaftar sesuai jalur yang dipilih.
	 *
	 * @param array  $applicants Daftar.
	 * @param string $jalur      Jalur.
	 * @return array
	 */
	private static function filter_jalur( array $applicants, string $jalur ): array {
		return array_values( array_filter( $applicants, fn( $a ) => $a->jalur === $jalur ) );
	}

	/**
	 * Reset status seleksi semua pendaftar jenjang ke pending.
	 *
	 * @param string $jenjang Jenjang.
	 */
	private static function reset_jenjang( string $jenjang ): void {
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE %i SET selection_status = 'pending', final_rank = 0 WHERE jenjang = %s",
				array( SPMB_DB_Applicants::table(), $jenjang )
			)
		); // phpcs:ignore WordPress.DB
	}

	/**
	 * Tandai pendaftar jenjang yang tidak eligible sebagai tidak_lolos.
	 *
	 * @param string $jenjang      Jenjang.
	 * @param array  $eligible_ids ID eligible.
	 */
	private static function mark_ineligible_in_jenjang( string $jenjang, array $eligible_ids ): void {
		global $wpdb;
		$table = SPMB_DB_Applicants::table();
		if ( empty( $eligible_ids ) ) {
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE %i SET selection_status = 'tidak_lolos' WHERE jenjang = %s AND selection_status = 'pending'",
					array( $table, $jenjang )
				)
			); // phpcs:ignore WordPress.DB
			return;
		}

		// Placeholders untuk IN clause.
		$placeholders = implode( ',', array_fill( 0, count( $eligible_ids ), '%d' ) );
		$sql = "UPDATE %i SET selection_status = 'tidak_lolos' WHERE jenjang = %s AND selection_status = 'pending' AND id NOT IN ({$placeholders})";
		$wpdb->query( $wpdb->prepare( $sql, array_merge( array( $table, $jenjang ), $eligible_ids ) ) ); // phpcs:ignore WordPress.DB
	}
}