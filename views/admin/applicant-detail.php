<?php
/**
 * View: detail pendaftar.
 *
 * @var object  $applicant Data pendaftar.
 * @var array   $documents Daftar dokumen.
 * @var object  $payment   Data pembayaran.
 * @var string  $notice    Pesan notice.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$dashboard = admin_url( 'admin.php?page=spmb-pro' );
$applicants = admin_url( 'admin.php?page=spmb-pro-applicants' );
$uploads   = wp_upload_dir();

$fields = array(
	'registration_number' => __( 'No. Pendaftaran', 'spmb-pro' ),
	'full_name'           => __( 'Nama', 'spmb-pro' ),
	'nisn'                => __( 'NISN', 'spmb-pro' ),
	'nik'                 => __( 'NIK', 'spmb-pro' ),
	'gender'              => __( 'Jenis Kelamin', 'spmb-pro' ),
	'birth_place'         => __( 'Tempat Lahir', 'spmb-pro' ),
	'birth_date'          => __( 'Tanggal Lahir', 'spmb-pro' ),
	'religion'            => __( 'Agama', 'spmb-pro' ),
	'address'             => __( 'Alamat', 'spmb-pro' ),
	'phone'               => __( 'No. HP', 'spmb-pro' ),
	'email'               => __( 'Email', 'spmb-pro' ),
	'origin_school'       => __( 'Asal Sekolah', 'spmb-pro' ),
	'parent_name'         => __( 'Nama Orang Tua', 'spmb-pro' ),
	'parent_phone'        => __( 'No. HP Orang Tua', 'spmb-pro' ),
	'parent_job'          => __( 'Pekerjaan Orang Tua', 'spmb-pro' ),
	'parent_income'       => __( 'Penghasilan', 'spmb-pro' ),
	'jenjang'             => __( 'Jenjang', 'spmb-pro' ),
	'jalur'               => __( 'Jalur', 'spmb-pro' ),
	'program_choice_1'    => __( 'Pilihan Program 1', 'spmb-pro' ),
	'program_choice_2'    => __( 'Pilihan Program 2', 'spmb-pro' ),
	'distance_km'         => __( 'Jarak (km)', 'spmb-pro' ),
	'rapor_avg'           => __( 'Rata-rata Rapor', 'spmb-pro' ),
	'achievement_points'  => __( 'Poin Prestasi', 'spmb-pro' ),
	'afirmasi_category'   => __( 'Kategori Afirmasi', 'spmb-pro' ),
	'selection_status'    => __( 'Status Seleksi', 'spmb-pro' ),
	'final_rank'          => __( 'Peringkat', 'spmb-pro' ),
);

$status_badge_map = array(
	'submitted' => 'spmb-status-badge spmb-status-badge--pending',
	'verified'  => 'spmb-status-badge spmb-status-badge--success',
	'rejected'  => 'spmb-status-badge spmb-status-badge--error',
);
?>
<div class="wrap spmb-wrap">

	<nav class="spmb-breadcrumb" aria-label="breadcrumb">
		<a href="<?php echo esc_url( $dashboard ); ?>"><?php esc_html_e( 'Dashboard SPMB', 'spmb-pro' ); ?></a>
		<span class="spmb-breadcrumb-sep" aria-hidden="true">/</span>
		<a href="<?php echo esc_url( $applicants ); ?>"><?php esc_html_e( 'Pendaftar', 'spmb-pro' ); ?></a>
		<span class="spmb-breadcrumb-sep" aria-hidden="true">/</span>
		<span class="spmb-breadcrumb-current"><?php echo esc_html( $applicant->full_name ?: $applicant->registration_number ); ?></span>
	</nav>

	<header class="spmb-page-header">
		<h1 class="spmb-page-title"><?php esc_html_e( 'Detail Pendaftar', 'spmb-pro' ); ?></h1>
		<p class="spmb-page-subtitle"><?php echo esc_html( $applicant->registration_number . ' — ' . ( $applicant->full_name ?: '—' ) ); ?></p>
		<div class="spmb-page-actions">
			<a href="<?php echo esc_url( $applicants ); ?>" class="spmb-btn spmb-btn-ghost"><?php esc_html_e( 'Kembali', 'spmb-pro' ); ?></a>
			<a href="<?php echo esc_url( admin_url( 'admin-post.php?action=spmb_export&type=pdf-kartu&id=' . $applicant->id ) ); ?>" class="spmb-btn spmb-btn-ghost"><?php esc_html_e( 'Unduh Kartu PDF', 'spmb-pro' ); ?></a>
		</div>
	</header>

	<?php if ( $notice ) : ?>
		<div class="spmb-banner spmb-banner--success" role="status">
			<div class="spmb-banner-body">
				<span class="spmb-banner-label"><?php esc_html_e( 'Sukses', 'spmb-pro' ); ?></span>
				<span class="spmb-banner-value"><?php echo esc_html( $notice ); ?></span>
			</div>
		</div>
	<?php endif; ?>

	<div class="spmb-detail-grid">
		<div class="spmb-detail-card">
			<h2 class="spmb-section-title"><?php esc_html_e( 'Biodata', 'spmb-pro' ); ?></h2>
			<ul class="spmb-info-list">
				<?php foreach ( $fields as $key => $label ) : ?>
					<li>
						<span class="spmb-info-label"><?php echo esc_html( $label ); ?></span>
						<span class="spmb-info-value<?php echo empty( $applicant->$key ) ? ' spmb-info-value--muted' : ''; ?>"><?php echo esc_html( $applicant->$key ?? '—' ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>

			<h2 class="spmb-section-title spmb-section-title--spaced"><?php esc_html_e( 'Status Pendaftar', 'spmb-pro' ); ?></h2>
			<p class="spmb-note">
				<span class="spmb-info-label spmb-status-now"><?php esc_html_e( 'Status Saat Ini', 'spmb-pro' ); ?></span>
				<span class="<?php echo esc_attr( $status_badge_map[ $applicant->status ] ?? 'spmb-status-badge spmb-status-badge--pending' ); ?>"><?php echo esc_html( $applicant->status ); ?></span>
			</p>
			<form method="post" action="">
				<?php wp_nonce_field( 'spmb_detail', 'spmb_detail_nonce' ); ?>
				<input type="hidden" name="spmb_detail_action" value="set_status" />
				<div class="spmb-filter-bar">
					<select name="status" aria-label="<?php esc_attr_e( 'Status', 'spmb-pro' ); ?>">
						<?php foreach ( array( 'submitted', 'verified', 'rejected' ) as $st ) : ?>
							<option value="<?php echo esc_attr( $st ); ?>" <?php selected( $applicant->status, $st ); ?>><?php echo esc_html( $st ); ?></option>
						<?php endforeach; ?>
					</select>
					<button type="submit" class="spmb-btn spmb-btn-primary"><?php esc_html_e( 'Simpan Status', 'spmb-pro' ); ?></button>
				</div>
			</form>
		</div>

		<div class="spmb-detail-card">
			<h2 class="spmb-section-title"><?php esc_html_e( 'Dokumen', 'spmb-pro' ); ?></h2>
			<?php if ( empty( $documents ) ) : ?>
				<p class="spmb-empty"><?php esc_html_e( 'Tidak ada dokumen.', 'spmb-pro' ); ?></p>
			<?php else : ?>
				<?php foreach ( $documents as $doc ) : ?>
					<div class="spmb-doc-item">
						<div class="spmb-doc-meta">
							<span class="spmb-doc-type"><?php echo esc_html( $doc->doc_type ); ?></span>
							<a href="<?php echo esc_url( trailingslashit( $uploads['baseurl'] ) . $doc->file_path ); ?>" target="_blank" class="spmb-doc-link"><?php esc_html_e( 'Lihat berkas', 'spmb-pro' ); ?></a>
						</div>
						<form method="post" action="" class="spmb-doc-verify">
							<?php wp_nonce_field( 'spmb_detail', 'spmb_detail_nonce' ); ?>
							<input type="hidden" name="spmb_detail_action" value="verify_doc" />
							<input type="hidden" name="doc_id" value="<?php echo esc_attr( $doc->id ); ?>" />
							<label>
								<input type="checkbox" name="is_verified" value="1" <?php checked( $doc->is_verified, 1 ); ?> onchange="this.form.submit()" />
								<?php esc_html_e( 'Terverifikasi', 'spmb-pro' ); ?>
							</label>
						</form>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<div class="spmb-detail-card">
			<h2 class="spmb-section-title"><?php esc_html_e( 'Pembayaran', 'spmb-pro' ); ?></h2>
			<?php if ( ! $payment ) : ?>
				<p class="spmb-empty"><?php esc_html_e( 'Tidak ada tagihan.', 'spmb-pro' ); ?></p>
			<?php else : ?>
				<ul class="spmb-pay-rows">
					<li class="spmb-pay-row">
						<span class="spmb-pay-label"><?php esc_html_e( 'No. Invoice', 'spmb-pro' ); ?></span>
						<span class="spmb-pay-value"><?php echo esc_html( $payment->invoice_number ); ?></span>
					</li>
					<li class="spmb-pay-row">
						<span class="spmb-pay-label"><?php esc_html_e( 'Jumlah', 'spmb-pro' ); ?></span>
						<span class="spmb-pay-value">Rp <?php echo esc_html( number_format_i18n( (float) $payment->amount ) ); ?></span>
					</li>
					<li class="spmb-pay-row">
						<span class="spmb-pay-label"><?php esc_html_e( 'Status', 'spmb-pro' ); ?></span>
						<span class="spmb-pay-value"><?php echo esc_html( $payment->status ); ?></span>
					</li>
				</ul>
				<?php if ( 'unpaid' === $payment->status ) : ?>
					<form method="post" action="">
						<?php wp_nonce_field( 'spmb_detail', 'spmb_detail_nonce' ); ?>
						<input type="hidden" name="spmb_detail_action" value="pay_paid" />
						<button type="submit" class="spmb-btn spmb-btn-primary"><?php esc_html_e( 'Tandai Lunas', 'spmb-pro' ); ?></button>
					</form>
				<?php elseif ( 'paid' === $payment->status ) : ?>
					<form method="post" action="">
						<?php wp_nonce_field( 'spmb_detail', 'spmb_detail_nonce' ); ?>
						<input type="hidden" name="spmb_detail_action" value="pay_verify" />
						<button type="submit" class="spmb-btn spmb-btn-primary"><?php esc_html_e( 'Verifikasi', 'spmb-pro' ); ?></button>
					</form>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>

</div>