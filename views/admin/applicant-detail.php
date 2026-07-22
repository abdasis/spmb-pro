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

$back = admin_url( 'admin.php?page=spmb-pro-applicants' );
$uploads = wp_upload_dir();

$fields = array(
	'registration_number' => __( 'No. Pendaftaran', 'spmb-pro' ),
	'full_name'          => __( 'Nama', 'spmb-pro' ),
	'nisn'               => __( 'NISN', 'spmb-pro' ),
	'nik'                => __( 'NIK', 'spmb-pro' ),
	'gender'             => __( 'Jenis Kelamin', 'spmb-pro' ),
	'birth_place'        => __( 'Tempat Lahir', 'spmb-pro' ),
	'birth_date'         => __( 'Tanggal Lahir', 'spmb-pro' ),
	'religion'           => __( 'Agama', 'spmb-pro' ),
	'address'            => __( 'Alamat', 'spmb-pro' ),
	'phone'              => __( 'No. HP', 'spmb-pro' ),
	'email'              => __( 'Email', 'spmb-pro' ),
	'origin_school'      => __( 'Asal Sekolah', 'spmb-pro' ),
	'parent_name'        => __( 'Nama Orang Tua', 'spmb-pro' ),
	'parent_phone'       => __( 'No. HP Orang Tua', 'spmb-pro' ),
	'parent_job'         => __( 'Pekerjaan Orang Tua', 'spmb-pro' ),
	'parent_income'      => __( 'Penghasilan', 'spmb-pro' ),
	'jenjang'            => __( 'Jenjang', 'spmb-pro' ),
	'jalur'              => __( 'Jalur', 'spmb-pro' ),
	'program_choice_1'   => __( 'Pilihan Program 1', 'spmb-pro' ),
	'program_choice_2'   => __( 'Pilihan Program 2', 'spmb-pro' ),
	'distance_km'        => __( 'Jarak (km)', 'spmb-pro' ),
	'rapor_avg'          => __( 'Rata-rata Rapor', 'spmb-pro' ),
	'achievement_points' => __( 'Poin Prestasi', 'spmb-pro' ),
	'afirmasi_category'  => __( 'Kategori Afirmasi', 'spmb-pro' ),
	'selection_status'   => __( 'Status Seleksi', 'spmb-pro' ),
	'final_rank'         => __( 'Peringkat', 'spmb-pro' ),
);
?>
<div class="wrap spmb-wrap">
	<h1><?php esc_html_e( 'Detail Pendaftar', 'spmb-pro' ); ?>
		<a href="<?php echo esc_url( $back ); ?>" class="page-title-action"><?php esc_html_e( 'Kembali', 'spmb-pro' ); ?></a>
		<a href="<?php echo esc_url( admin_url( 'admin-post.php?action=spmb_export&type=pdf-kartu&id=' . $applicant->id ) ); ?>" class="page-title-action"><?php esc_html_e( 'Unduh Kartu PDF', 'spmb-pro' ); ?></a>
	</h1>

	<?php if ( $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
	<?php endif; ?>

	<div class="spmb-detail-grid">
		<div class="spmb-detail-card">
			<h2><?php esc_html_e( 'Biodata', 'spmb-pro' ); ?></h2>
			<table class="form-table" role="presentation">
				<?php foreach ( $fields as $key => $label ) : ?>
					<tr>
						<th><?php echo esc_html( $label ); ?></th>
						<td><?php echo esc_html( $applicant->$key ?? '—' ); ?></td>
					</tr>
				<?php endforeach; ?>
			</table>

			<h2><?php esc_html_e( 'Status Pendaftar', 'spmb-pro' ); ?></h2>
			<form method="post" action="">
				<?php wp_nonce_field( 'spmb_detail', 'spmb_detail_nonce' ); ?>
				<input type="hidden" name="spmb_detail_action" value="set_status" />
				<select name="status">
					<?php foreach ( array( 'submitted', 'verified', 'rejected' ) as $st ) : ?>
						<option value="<?php echo esc_attr( $st ); ?>" <?php selected( $applicant->status, $st ); ?>><?php echo esc_html( $st ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php submit_button( __( 'Simpan Status', 'spmb-pro' ), 'small', 'submit', false ); ?>
			</form>
		</div>

		<div class="spmb-detail-card">
			<h2><?php esc_html_e( 'Dokumen', 'spmb-pro' ); ?></h2>
			<?php if ( empty( $documents ) ) : ?>
				<p><?php esc_html_e( 'Tidak ada dokumen.', 'spmb-pro' ); ?></p>
			<?php else : ?>
				<ul class="spmb-doc-list">
					<?php foreach ( $documents as $doc ) : ?>
						<li>
							<strong><?php echo esc_html( $doc->doc_type ); ?></strong> —
							<a href="<?php echo esc_url( trailingslashit( $uploads['baseurl'] ) . $doc->file_path ); ?>" target="_blank"><?php esc_html_e( 'Lihat', 'spmb-pro' ); ?></a>
							<form method="post" action="" style="display:inline">
								<?php wp_nonce_field( 'spmb_detail', 'spmb_detail_nonce' ); ?>
								<input type="hidden" name="spmb_detail_action" value="verify_doc" />
								<input type="hidden" name="doc_id" value="<?php echo esc_attr( $doc->id ); ?>" />
								<label>
									<input type="checkbox" name="is_verified" value="1" <?php checked( $doc->is_verified, 1 ); ?> onchange="this.form.submit()" />
									<?php esc_html_e( 'Terverifikasi', 'spmb-pro' ); ?>
								</label>
							</form>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="spmb-detail-card">
			<h2><?php esc_html_e( 'Pembayaran', 'spmb-pro' ); ?></h2>
			<?php if ( ! $payment ) : ?>
				<p><?php esc_html_e( 'Tidak ada tagihan.', 'spmb-pro' ); ?></p>
			<?php else : ?>
				<p><strong><?php esc_html_e( 'No. Invoice', 'spmb-pro' ); ?>:</strong> <?php echo esc_html( $payment->invoice_number ); ?></p>
				<p><strong><?php esc_html_e( 'Jumlah', 'spmb-pro' ); ?>:</strong> Rp <?php echo esc_html( number_format_i18n( (float) $payment->amount ) ); ?></p>
				<p><strong><?php esc_html_e( 'Status', 'spmb-pro' ); ?>:</strong> <?php echo esc_html( $payment->status ); ?></p>
				<?php if ( 'unpaid' === $payment->status ) : ?>
					<form method="post" action="">
						<?php wp_nonce_field( 'spmb_detail', 'spmb_detail_nonce' ); ?>
						<input type="hidden" name="spmb_detail_action" value="pay_paid" />
						<?php submit_button( __( 'Tandai Lunas', 'spmb-pro' ), 'small', 'submit', false ); ?>
					</form>
				<?php elseif ( 'paid' === $payment->status ) : ?>
					<form method="post" action="">
						<?php wp_nonce_field( 'spmb_detail', 'spmb_detail_nonce' ); ?>
						<input type="hidden" name="spmb_detail_action" value="pay_verify" />
						<?php submit_button( __( 'Verifikasi', 'spmb-pro' ), 'primary small', 'submit', false ); ?>
					</form>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</div>