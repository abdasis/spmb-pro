<?php
/**
 * Instalasi skema database SPMB Pro via dbDelta.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Installer {

	/**
	 * Jalankan instalasi: buat tabel dan seed option.
	 */
	public static function install(): void {
		self::create_tables();
		self::seed_options();
		update_option( 'spmb_db_version', SPMB_VERSION );
	}

	/**
	 * Buat empat tabel custom menggunakan dbDelta.
	 */
	private static function create_tables(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset = $wpdb->get_charset_collate();
		$prefix  = $wpdb->prefix;

		foreach ( self::schemas( $prefix ) as $sql ) {
			dbDelta( $sql . ' ' . $charset );
		}
	}

	/**
	 * Daftar statement CREATE TABLE.
	 *
	 * @param string $prefix Prefix tabel.
	 * @return array<string>
	 */
	private static function schemas( string $prefix ): array {
		$applicants = "CREATE TABLE {$prefix}spmb_applicants (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  registration_number varchar(32) NOT NULL,
  jenjang varchar(8) NOT NULL DEFAULT '',
  jalur varchar(16) NOT NULL DEFAULT '',
  full_name varchar(150) NOT NULL DEFAULT '',
  nisn varchar(20) NOT NULL DEFAULT '',
  nik varchar(32) NOT NULL DEFAULT '',
  gender varchar(16) NOT NULL DEFAULT '',
  birth_place varchar(100) NOT NULL DEFAULT '',
  birth_date date NULL,
  religion varchar(32) NOT NULL DEFAULT '',
  address text NULL,
  phone varchar(32) NOT NULL DEFAULT '',
  email varchar(100) NOT NULL DEFAULT '',
  origin_school varchar(150) NOT NULL DEFAULT '',
  origin_school_npsn varchar(20) NOT NULL DEFAULT '',
  parent_name varchar(150) NOT NULL DEFAULT '',
  parent_phone varchar(32) NOT NULL DEFAULT '',
  parent_job varchar(100) NOT NULL DEFAULT '',
  parent_income decimal(12,2) NOT NULL DEFAULT 0.00,
  program_choice_1 varchar(64) NOT NULL DEFAULT '',
  program_choice_2 varchar(64) NOT NULL DEFAULT '',
  distance_km decimal(6,2) NOT NULL DEFAULT 0.00,
  rapor_avg decimal(5,2) NOT NULL DEFAULT 0.00,
  achievement_points int(11) NOT NULL DEFAULT 0,
  afirmasi_category varchar(32) NOT NULL DEFAULT '',
  status varchar(20) NOT NULL DEFAULT 'draft',
  selection_status varchar(20) NOT NULL DEFAULT 'pending',
  final_rank int(11) NOT NULL DEFAULT 0,
  created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  created_by bigint(20) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY  (id),
  UNIQUE KEY registration_number (registration_number),
  KEY jenjang_jalur_status (jenjang, jalur, selection_status),
  KEY status (status),
  KEY created_at (created_at)
)";

		$documents = "CREATE TABLE {$prefix}spmb_documents (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  applicant_id bigint(20) unsigned NOT NULL DEFAULT 0,
  doc_type varchar(32) NOT NULL DEFAULT '',
  file_path varchar(255) NOT NULL DEFAULT '',
  file_url varchar(255) NOT NULL DEFAULT '',
  mime_type varchar(100) NOT NULL DEFAULT '',
  file_size int(11) NOT NULL DEFAULT 0,
  uploaded_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  is_verified tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY  (id),
  KEY applicant_id (applicant_id),
  KEY doc_type (doc_type)
)";

		$payments = "CREATE TABLE {$prefix}spmb_payments (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  applicant_id bigint(20) unsigned NOT NULL DEFAULT 0,
  invoice_number varchar(40) NOT NULL DEFAULT '',
  amount decimal(12,2) NOT NULL DEFAULT 0.00,
  status varchar(20) NOT NULL DEFAULT 'unpaid',
  payment_method varchar(64) NOT NULL DEFAULT '',
  paid_at datetime NULL,
  verified_at datetime NULL,
  verified_by bigint(20) unsigned NOT NULL DEFAULT 0,
  note text NULL,
  created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (id),
  UNIQUE KEY invoice_number (invoice_number),
  UNIQUE KEY applicant_id (applicant_id),
  KEY status (status)
)";

		$runs = "CREATE TABLE {$prefix}spmb_selection_runs (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  run_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  run_by bigint(20) unsigned NOT NULL DEFAULT 0,
  jenjang varchar(8) NOT NULL DEFAULT '',
  config_snapshot longtext NULL,
  admitted_count int(11) NOT NULL DEFAULT 0,
  waitlist_count int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY  (id),
  KEY jenjang (jenjang),
  KEY run_at (run_at)
)";

		return array( $applicants, $documents, $payments, $runs );
	}

	/**
	 * Seed pengaturan default bila belum ada.
	 */
	private static function seed_options(): void {
		if ( false === get_option( SPMB_Defaults::OPTION_KEY ) ) {
			add_option( SPMB_Defaults::OPTION_KEY, SPMB_Defaults::settings() );
		}
	}
}