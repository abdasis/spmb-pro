<?php
/**
 * View: form pendaftaran publik.
 *
 * @var array $data Data view dari SPMB_Form_Shortcode::build_view_data.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$s       = $data['settings'];
$success = ( isset( $_GET['spmb_status'] ) && 'success' === sanitize_key( wp_unslash( $_GET['spmb_status'] ) ) ) ? true : false;

$err = function ( string $name ) use ( $data ): string {
	$m = $data['field_errors'][ $name ] ?? '';
	return $m ? '<span class="spmb-error">' . esc_html( $m ) . '</span>' : '';
};
$input = function ( string $name, string $label, string $type = 'text', bool $req = false ) use ( $data, $err ): void {
	?>
	<p class="spmb-field">
		<label for="spmb-<?php echo esc_attr( $name ); ?>">
			<?php echo esc_html( $label ); ?><?php if ( $req ) : ?> <span class="required">*</span><?php endif; ?>
		</label>
		<input type="<?php echo esc_attr( $type ); ?>" id="spmb-<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php echo $req ? 'required' : ''; ?> />
		<?php echo $err( $name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped sudah escape ?>
	</p>
	<?php
};
?>
<div class="spmb-form-wrap">
	<?php if ( $success && $data['success_reg'] ) : ?>
		<div class="spmb-notice spmb-notice-success">
			<h2><?php esc_html_e( 'Pendaftaran Berhasil', 'spmb-pro' ); ?></h2>
			<p><?php esc_html_e( 'Nomor pendaftaran Anda:', 'spmb-pro' ); ?></p>
			<p class="spmb-reg-number"><?php echo esc_html( $data['success_reg'] ); ?></p>
			<p><?php esc_html_e( 'Simpan nomor ini untuk cek status dan pengumuman.', 'spmb-pro' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( $data['status_message'] && ! $success ) : ?>
		<div class="spmb-notice spmb-notice-error"><p><?php echo esc_html( $data['status_message'] ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( $data['action'] ); ?>" enctype="multipart/form-data" class="spmb-form" novalidate>
		<input type="hidden" name="action" value="spmb_submit" />
		<input type="hidden" name="spmb_form_nonce" value="<?php echo esc_attr( $data['nonce'] ); ?>" />
		<input type="text" name="website_url" class="spmb-honeypot" tabindex="-1" autocomplete="off" />

		<fieldset class="spmb-step" data-step="1">
			<legend><?php esc_html_e( 'Data Calon Siswa', 'spmb-pro' ); ?></legend>
			<?php $input( 'full_name', __( 'Nama Lengkap', 'spmb-pro' ), 'text', true ); ?>
			<?php $input( 'nisn', __( 'NISN', 'spmb-pro' ) ); ?>
			<?php $input( 'nik', __( 'NIK', 'spmb-pro' ) ); ?>
			<p class="spmb-field">
				<label for="spmb-gender"><?php esc_html_e( 'Jenis Kelamin', 'spmb-pro' ); ?> <span class="required">*</span></label>
				<select id="spmb-gender" name="gender" required>
					<option value=""><?php esc_html_e( 'Pilih', 'spmb-pro' ); ?></option>
					<option value="l"><?php esc_html_e( 'Laki-laki', 'spmb-pro' ); ?></option>
					<option value="p"><?php esc_html_e( 'Perempuan', 'spmb-pro' ); ?></option>
				</select>
				<?php echo $err( 'gender' ); // phpcs:ignore WordPress.Security ?>
			</p>
			<?php $input( 'birth_place', __( 'Tempat Lahir', 'spmb-pro' ) ); ?>
			<?php $input( 'birth_date', __( 'Tanggal Lahir', 'spmb-pro' ), 'date' ); ?>
			<p class="spmb-field">
				<label for="spmb-religion"><?php esc_html_e( 'Agama', 'spmb-pro' ); ?></label>
				<select id="spmb-religion" name="religion">
					<option value=""><?php esc_html_e( 'Pilih', 'spmb-pro' ); ?></option>
					<?php foreach ( $data['religions'] as $code => $label ) : ?>
						<option value="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<?php $input( 'address', __( 'Alamat', 'spmb-pro' ) ); ?>
			<?php $input( 'phone', __( 'No. HP Siswa', 'spmb-pro' ), 'tel' ); ?>
			<?php $input( 'email', __( 'Email', 'spmb-pro' ), 'email' ); ?>
		</fieldset>

		<fieldset class="spmb-step" data-step="2">
			<legend><?php esc_html_e( 'Data Orang Tua', 'spmb-pro' ); ?></legend>
			<?php $input( 'parent_name', __( 'Nama Orang Tua/Wali', 'spmb-pro' ), 'text', true ); ?>
			<?php $input( 'parent_phone', __( 'No. HP Orang Tua/Wali', 'spmb-pro' ), 'tel' ); ?>
			<?php $input( 'parent_job', __( 'Pekerjaan Orang Tua/Wali', 'spmb-pro' ) ); ?>
			<?php $input( 'parent_income', __( 'Penghasilan Per Bulan (Rp)', 'spmb-pro' ), 'number' ); ?>
		</fieldset>

		<fieldset class="spmb-step" data-step="3">
			<legend><?php esc_html_e( 'Asal Sekolah', 'spmb-pro' ); ?></legend>
			<?php $input( 'origin_school', __( 'Asal Sekolah', 'spmb-pro' ) ); ?>
			<?php $input( 'origin_school_npsn', __( 'NPSN Asal Sekolah', 'spmb-pro' ) ); ?>
		</fieldset>

		<fieldset class="spmb-step" data-step="4">
			<legend><?php esc_html_e( 'Pilihan Jalur & Program', 'spmb-pro' ); ?></legend>
			<p class="spmb-field">
				<label for="spmb-jenjang"><?php esc_html_e( 'Jenjang', 'spmb-pro' ); ?> <span class="required">*</span></label>
				<select id="spmb-jenjang" name="jenjang" required <?php echo $data['fixed_jenjang'] ? 'disabled' : ''; ?>>
					<?php if ( $data['fixed_jenjang'] ) : ?>
						<option value="<?php echo esc_attr( $data['fixed_jenjang'] ); ?>" selected><?php echo esc_html( $data['fixed_jenjang'] ); ?></option>
						<input type="hidden" name="jenjang" value="<?php echo esc_attr( $data['fixed_jenjang'] ); ?>" />
					<?php else : ?>
						<option value=""><?php esc_html_e( 'Pilih', 'spmb-pro' ); ?></option>
						<?php foreach ( $s['jenjang'] as $j ) : ?>
							<option value="<?php echo esc_attr( $j ); ?>"><?php echo esc_html( $j ); ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
				<?php echo $err( 'jenjang' ); // phpcs:ignore WordPress.Security ?>
			</p>
			<p class="spmb-field">
				<label for="spmb-jalur"><?php esc_html_e( 'Jalur Seleksi', 'spmb-pro' ); ?> <span class="required">*</span></label>
				<select id="spmb-jalur" name="jalur" required>
					<option value=""><?php esc_html_e( 'Pilih', 'spmb-pro' ); ?></option>
					<?php foreach ( $data['jalur_labels'] as $code => $label ) : ?>
						<?php if ( ! empty( $s['enabled_jalur'][ $code ] ) ) : ?>
							<option value="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
				<?php echo $err( 'jalur' ); // phpcs:ignore WordPress.Security ?>
			</p>
			<?php $input( 'program_choice_1', __( 'Pilihan Program 1', 'spmb-pro' ) ); ?>
			<?php $input( 'program_choice_2', __( 'Pilihan Program 2', 'spmb-pro' ) ); ?>
		</fieldset>

		<fieldset class="spmb-step spmb-step-jalur" data-step="5" data-jalur="zonasi">
			<legend><?php esc_html_e( 'Data Zonasi', 'spmb-pro' ); ?></legend>
			<?php $input( 'distance_km', __( 'Jarak Rumah ke Sekolah (km)', 'spmb-pro' ), 'number' ); ?>
		</fieldset>

		<fieldset class="spmb-step spmb-step-jalur" data-step="5" data-jalur="prestasi">
			<legend><?php esc_html_e( 'Data Prestasi', 'spmb-pro' ); ?></legend>
			<?php $input( 'rapor_avg', __( 'Rata-rata Nilai Rapor', 'spmb-pro' ), 'number' ); ?>
			<?php $input( 'achievement_points', __( 'Poin Prestasi', 'spmb-pro' ), 'number' ); ?>
		</fieldset>

		<fieldset class="spmb-step spmb-step-jalur" data-step="5" data-jalur="afirmasi">
			<legend><?php esc_html_e( 'Data Afirmasi', 'spmb-pro' ); ?></legend>
			<p class="spmb-field">
				<label for="spmb-afirmasi_category"><?php esc_html_e( 'Kategori Afirmasi', 'spmb-pro' ); ?></label>
				<select id="spmb-afirmasi_category" name="afirmasi_category">
					<option value=""><?php esc_html_e( 'Pilih', 'spmb-pro' ); ?></option>
					<?php foreach ( $s['afirmasi_categories'] as $cat ) : ?>
						<option value="<?php echo esc_attr( $cat ); ?>"><?php echo esc_html( $cat ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php echo $err( 'afirmasi_category' ); // phpcs:ignore WordPress.Security ?>
			</p>
		</fieldset>

		<fieldset class="spmb-step" data-step="6">
			<legend><?php esc_html_e( 'Dokumen', 'spmb-pro' ); ?></legend>
			<?php $up = function ( string $name, string $label, bool $req = false ) use ( $err ): void { ?>
				<p class="spmb-field">
					<label for="spmb-<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?><?php if ( $req ) : ?> <span class="required">*</span><?php endif; ?></label>
					<input type="file" id="spmb-<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" />
					<?php echo $err( $name ); // phpcs:ignore WordPress.Security ?>
				</p>
			<?php }; ?>
			<?php $up( 'akta_kelahiran', __( 'Akta Kelahiran', 'spmb-pro' ) ); ?>
			<?php $up( 'rapor', __( 'Rapor', 'spmb-pro' ) ); ?>
			<?php $up( 'foto', __( 'Pas Foto', 'spmb-pro' ) ); ?>
			<?php $up( 'kk', __( 'Kartu Keluarga (opsional)', 'spmb-pro' ) ); ?>
		</fieldset>

		<div class="spmb-form-actions">
			<button type="button" class="spmb-prev" disabled><?php esc_html_e( 'Sebelumnya', 'spmb-pro' ); ?></button>
			<button type="button" class="spmb-next"><?php esc_html_e( 'Selanjutnya', 'spmb-pro' ); ?></button>
			<button type="submit" class="spmb-submit"><?php esc_html_e( 'Kirim Pendaftaran', 'spmb-pro' ); ?></button>
		</div>
	</form>
</div>