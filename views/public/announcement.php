<?php
/**
 * View: pengumuman hasil seleksi.
 *
 * @var array        $data       Data view.
 * @var object|null  $result     Data pendaftar (bila ditemukan).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$result    = $data['result'];
$published = $data['published'];

$selection_labels = array(
	'pending'     => __( 'Belum diseleksi', 'spmb-pro' ),
	'diterima'    => __( 'DITERIMA', 'spmb-pro' ),
	'cadangan'    => __( 'CADANGAN', 'spmb-pro' ),
	'tidak_lolos' => __( 'TIDAK LOLOS', 'spmb-pro' ),
);
?>
<div class="spmb-form-wrap spmb-announcement-wrap">
	<h2><?php esc_html_e( 'Pengumuman Hasil Seleksi', 'spmb-pro' ); ?></h2>

	<?php if ( ! $published ) : ?>
		<div class="spmb-notice spmb-notice-error">
			<p><?php esc_html_e( 'Pengumuman belum dipublikasikan. Silakan kembali nanti.', 'spmb-pro' ); ?></p>
		</div>
	<?php else : ?>
		<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<p class="spmb-field">
				<label for="spmb-reg"><?php esc_html_e( 'Nomor Pendaftaran', 'spmb-pro' ); ?></label>
				<input type="text" id="spmb-reg" name="spmb_reg" value="<?php echo esc_attr( $data['regnum'] ); ?>" required />
			</p>
			<p><button type="submit" class="spmb-submit"><?php esc_html_e( 'Lihat Hasil', 'spmb-pro' ); ?></button></p>
		</form>

		<?php if ( $data['submitted'] && ! $result ) : ?>
			<div class="spmb-notice spmb-notice-error">
				<p><?php esc_html_e( 'Nomor pendaftaran tidak ditemukan.', 'spmb-pro' ); ?></p>
			</div>
		<?php elseif ( $result ) : ?>
			<div class="spmb-status-result">
				<h3><?php echo esc_html( $result->full_name ); ?></h3>
				<p class="spmb-reg-number"><?php echo esc_html( $result->registration_number ); ?></p>
				<?php $sel = $result->selection_status; ?>
				<p class="spmb-result-badge spmb-result-<?php echo esc_attr( $sel ); ?>">
					<?php echo esc_html( $selection_labels[ $sel ] ?? $sel ); ?>
				</p>
				<table class="spmb-status-table" role="presentation">
					<tr><th><?php esc_html_e( 'Jalur', 'spmb-pro' ); ?></th><td><?php echo esc_html( $result->jalur ); ?></td></tr>
					<?php if ( $result->program_choice_1 ) : ?>
						<tr><th><?php esc_html_e( 'Program', 'spmb-pro' ); ?></th><td><?php echo esc_html( $result->program_choice_1 ); ?></td></tr>
					<?php endif; ?>
					<?php if ( (int) $result->final_rank > 0 ) : ?>
						<tr><th><?php esc_html_e( 'Peringkat', 'spmb-pro' ); ?></th><td><?php echo esc_html( (string) $result->final_rank ); ?></td></tr>
					<?php endif; ?>
				</table>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>