<?php
/**
 * Layar dashboard/statistik SPMB Pro.
 *
 * Menyajikan KPI pendaftaran, funnel status, breakdown per jalur/jenjang,
 * ringkasan pembayaran, dan daftar pendaftar terbaru.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Admin_Dashboard {

	/**
	 * Render layar dashboard.
	 */
	public static function render(): void {
		if ( ! current_user_can( SPMB_Roles::CAP ) ) {
			wp_die( esc_html__( 'Anda tidak punya akses ke halaman ini.', 'spmb-pro' ) );
		}

		$stats = self::compute_stats();
		require SPMB_PATH . 'views/admin/dashboard.php';
	}

	/**
	 * Kumpulkan semua statistik untuk dashboard.
	 *
	 * @return array
	 */
	private static function compute_stats(): array {
		global $wpdb;
		$prefix = $wpdb->prefix;

		$by_status  = self::count_by_status( $wpdb, $prefix );
		$selection  = self::count_selection( $wpdb, $prefix );
		$payments   = self::payment_summary( $wpdb, $prefix );
		$by_jalur   = self::count_by_jalur( $wpdb, $prefix );
		$by_jenjang = self::count_by_jenjang( $wpdb, $prefix );
		$recent     = self::recent_applicants( $wpdb, $prefix );

		return array(
			'total'       => $by_status['total'],
			'submitted'   => $by_status['submitted'],
			'verified'    => $by_status['verified'],
			'rejected'    => $by_status['rejected'],
			'draft'       => $by_status['draft'],
			'admitted'    => $selection['admitted'],
			'cadangan'    => $selection['cadangan'],
			'tidak_lolos' => $selection['tidak_lolos'],
			'pending_sel' => $selection['pending'],
			'payments'   => $payments,
			'by_jalur'   => $by_jalur,
			'by_jenjang' => $by_jenjang,
			'recent'     => $recent,
		);
	}

	/**
	 * Hitung jumlah pendaftar per status pendaftaran.
	 *
	 * @param \wpdb $wpdb   Koneksi database.
	 * @param string $prefix Prefix tabel.
	 * @return array
	 */
	private static function count_by_status( \wpdb $wpdb, string $prefix ): array {
		// phpcs:ignore WordPress.DB
		$rows = $wpdb->get_results( "SELECT status, COUNT(*) AS n FROM {$prefix}spmb_applicants GROUP BY status", OBJECT_K );

		$defaults = array(
			'total'      => 0,
			'draft'      => 0,
			'submitted'  => 0,
			'verified'   => 0,
			'rejected'   => 0,
		);

		foreach ( $rows as $status => $row ) {
			$defaults[ $status ] = (int) $row->n;
			$defaults['total']  += (int) $row->n;
		}

		return $defaults;
	}

	/**
	 * Hitung jumlah pendaftar per status seleksi.
	 *
	 * @param \wpdb $wpdb   Koneksi database.
	 * @param string $prefix Prefix tabel.
	 * @return array
	 */
	private static function count_selection( \wpdb $wpdb, string $prefix ): array {
		// phpcs:ignore WordPress.DB
		$rows = $wpdb->get_results( "SELECT selection_status, COUNT(*) AS n FROM {$prefix}spmb_applicants GROUP BY selection_status", OBJECT_K );

		$defaults = array(
			'pending'     => 0,
			'admitted'    => 0,
			'cadangan'    => 0,
			'tidak_lolos' => 0,
		);

		foreach ( $rows as $status => $row ) {
			if ( array_key_exists( $status, $defaults ) ) {
				$defaults[ $status ] = (int) $row->n;
			} else {
				// Status tak dikenali → anggap pending.
				$defaults['pending'] += (int) $row->n;
			}
		}

		return $defaults;
	}

	/**
	 * Ringkasan status pembayaran.
	 *
	 * @param \wpdb $wpdb   Koneksi database.
	 * @param string $prefix Prefix tabel.
	 * @return array
	 */
	private static function payment_summary( \wpdb $wpdb, string $prefix ): array {
		// phpcs:ignore WordPress.DB
		$rows = $wpdb->get_results( "SELECT status, COUNT(*) AS n, COALESCE(SUM(amount),0) AS total FROM {$prefix}spmb_payments GROUP BY status", OBJECT_K );

		$defaults = array(
			'unpaid'   => 0,
			'paid'     => 0,
			'verified' => 0,
			'void'     => 0,
			'revenue'  => 0, // Total nominal terverifikasi.
		);

		foreach ( $rows as $status => $row ) {
			if ( isset( $defaults[ $status ] ) ) {
				$defaults[ $status ] = (int) $row->n;
			}
			if ( 'verified' === $status ) {
				$defaults['revenue'] = (float) $row->total;
			}
		}

		return $defaults;
	}

	/**
	 * Breakdown pendaftar per jalur dengan metrik verifikasi & diterima.
	 *
	 * @param \wpdb $wpdb   Koneksi database.
	 * @param string $prefix Prefix tabel.
	 * @return array
	 */
	private static function count_by_jalur( \wpdb $wpdb, string $prefix ): array {
		// phpcs:ignore WordPress.DB
		$sql = "SELECT a.jalur,
				COUNT(*) AS pendaftar,
				SUM(CASE WHEN a.status='verified' THEN 1 ELSE 0 END) AS verified,
				SUM(CASE WHEN a.selection_status='admitted' THEN 1 ELSE 0 END) AS admitted
			FROM {$prefix}spmb_applicants a
			GROUP BY a.jalur
			ORDER BY pendaftar DESC";
		$rows = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB

		if ( empty( $rows ) ) {
			return array();
		}

		return array_map(
			static function ( $row ) {
				return array(
					'jalur'     => $row->jalur,
					'pendaftar' => (int) $row->pendaftar,
					'verified'  => (int) $row->verified,
					'admitted'  => (int) $row->admitted,
				);
			},
			$rows
		);
	}

	/**
	 * Breakdown pendaftar per jenjang dengan metrik lengkap.
	 *
	 * @param \wpdb $wpdb   Koneksi database.
	 * @param string $prefix Prefix tabel.
	 * @return array
	 */
	private static function count_by_jenjang( \wpdb $wpdb, string $prefix ): array {
		// phpcs:ignore WordPress.DB
		$sql = "SELECT a.jenjang,
				COUNT(*) AS total,
				SUM(CASE WHEN a.status='verified' THEN 1 ELSE 0 END) AS verified,
				SUM(CASE WHEN a.selection_status='admitted' THEN 1 ELSE 0 END) AS admitted
			FROM {$prefix}spmb_applicants a
			GROUP BY a.jenjang
			ORDER BY a.jenjang ASC";
		$rows = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB

		if ( empty( $rows ) ) {
			return array();
		}

		// Hitung pembayaran terverifikasi per jenjang via subquery terpisah (lebih jelas dari JOIN aggregate).
		// phpcs:ignore WordPress.DB
		$pay_rows = $wpdb->get_results(
			"SELECT a.jenjang, COUNT(*) AS paid
			FROM {$prefix}spmb_payments p
			INNER JOIN {$prefix}spmb_applicants a ON a.id = p.applicant_id
			WHERE p.status='verified'
			GROUP BY a.jenjang",
			OBJECT_K
		);

		return array_map(
			static function ( $row ) use ( $pay_rows ) {
				$jenjang = $row->jenjang;
				return array(
					'jenjang'  => $jenjang,
					'total'    => (int) $row->total,
					'verified' => (int) $row->verified,
					'paid'     => isset( $pay_rows[ $jenjang ] ) ? (int) $pay_rows[ $jenjang ]->paid : 0,
					'admitted' => (int) $row->admitted,
				);
			},
			$rows
		);
	}

	/**
	 * Ambil pendaftar terbaru (8 baris) untuk ditampilkan di dashboard.
	 *
	 * @param \wpdb $wpdb   Koneksi database.
	 * @param string $prefix Prefix tabel.
	 * @return array
	 */
	private static function recent_applicants( \wpdb $wpdb, string $prefix ): array {
		// phpcs:ignore WordPress.DB
		$sql = "SELECT id, registration_number, full_name, jalur, jenjang, status, created_at
			FROM {$prefix}spmb_applicants
			ORDER BY created_at DESC
			LIMIT 8";
		$rows = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB

		if ( empty( $rows ) ) {
			return array();
		}

		return array_map(
			static function ( $row ) {
				return array(
					'id'           => (int) $row->id,
					'reg_number'   => $row->registration_number,
					'full_name'    => $row->full_name,
					'jalur'        => $row->jalur,
					'jenjang'      => $row->jenjang,
					'status'       => $row->status,
					'created_at'   => $row->created_at,
					'detail_url'   => admin_url( 'admin.php?page=spmb-pro-applicant&id=' . $row->id ),
				);
			},
			$rows
		);
	}
}