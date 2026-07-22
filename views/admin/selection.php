<?php
/**
 * View: pengumuman & seleksi.
 *
 * @var string $notice        Pesan notice.
 * @var bool   $published     Status publikasi.
 * @var array  $jenjang       Daftar jenjang.
 * @var string $active_jenjang Jenjang aktif.
 * @var array  $summary       Ringkasan eligible.
 * @var array  $runs          Riwayat run.
 * @var array  $settings      Pengaturan.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$jalur_labels = array(
	'zonasi'      => __( 'Zonasi', 'spmb-pro' ),
	'afirmasi'    => __( 'Afirmasi', 'spmb-pro' ),
	'prestasi'    => __( 'Prestasi', 'spmb-pro' ),
	'perpindahan' => __( 'Perpindahan Tugas', 'spmb-pro' ),
);
$base = admin_url( 'admin.php?page=spmb-pro-selection' );
?>
<div class="wrap spmb-wrap">
	<h1><?php esc_html_e( 'Pengumuman & Seleksi', 'spmb-pro' ); ?></h1>

	<?php if ( $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
	<?php endif; ?>

	<ul class="subsubsub">
		<?php foreach ( $jenjang as $j ) : ?>
			<li>
				<a href="<?php echo esc_url( add_query_arg( 'jenjang', $j, $base ) ); ?>" class="<?php echo $active_jenjang === $j ? 'current' : ''; ?>"><?php echo esc_html( $j ); ?></a>
			</li>
			<?php if ( $j !== end( $jenjang ) ) : ?> | <?php endif; ?>
		<?php endforeach; ?>
	</ul>

	<div class="spmb-pub-status notice <?php echo $published ? 'notice-success' : 'notice-warning'; ?> inline">
		<p>
			<strong><?php esc_html_e( 'Status Pengumuman:', 'spmb-pro' ); ?></strong>
			<?php echo $published ? esc_html__( 'Dipublikasikan', 'spmb-pro' ) : esc_html__( 'Belum dipublikasikan', 'spmb-pro' ); ?>
		</p>
		<form method="post" action="" style="display:inline">
			<?php wp_nonce_field( 'spmb_selection', 'spmb_selection_nonce' ); ?>
			<input type="hidden" name="jenjang" value="<?php echo esc_attr( $active_jenjang ); ?>" />
			<?php if ( $published ) : ?>
				<input type="hidden" name="spmb_selection_action" value="unpublish" />
				<?php submit_button( __( 'Tarik Pengumuman', 'spmb-pro' ), 'small', 'submit', false ); ?>
			<?php else : ?>
				<input type="hidden" name="spmb_selection_action" value="publish" />
				<?php submit_button( __( 'Publikasikan Pengumuman', 'spmb-pro' ), 'primary small', 'submit', false ); ?>
			<?php endif; ?>
		</form>
	</div>

	<h2><?php esc_html_e( 'Ringkasan Eligible & Kuota', 'spmb-pro' ); ?> — <?php echo esc_html( $active_jenjang ); ?></h2>
	<table class="widefat striped" role="presentation">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Jalur', 'spmb-pro' ); ?></th>
				<th><?php esc_html_e( 'Pendaftar Eligible', 'spmb-pro' ); ?></th>
				<th><?php esc_html_e( 'Kuota', 'spmb-pro' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $summary['per_jalur'] ) ) : ?>
				<tr><td colspan="3"><?php esc_html_e( 'Pilih jenjang.', 'spmb-pro' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $summary['per_jalur'] as $jalur => $data ) : ?>
					<tr>
						<td><?php echo esc_html( $jalur_labels[ $jalur ] ?? $jalur ); ?></td>
						<td><?php echo esc_html( number_format_i18n( $data['eligible'] ) ); ?></td>
						<td><?php echo esc_html( number_format_i18n( $data['quota'] ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
		<?php if ( ! empty( $summary['total_eligible'] ) ) : ?>
			<tfoot>
				<tr>
					<th><?php esc_html_e( 'Total Eligible', 'spmb-pro' ); ?></th>
					<th colspan="2"><?php echo esc_html( number_format_i18n( $summary['total_eligible'] ) ); ?></th>
				</tr>
			</tfoot>
		<?php endif; ?>
	</table>

	<p>
		<form method="post" action="" style="display:inline">
			<?php wp_nonce_field( 'spmb_selection', 'spmb_selection_nonce' ); ?>
			<input type="hidden" name="jenjang" value="<?php echo esc_attr( $active_jenjang ); ?>" />
			<input type="hidden" name="spmb_selection_action" value="run" />
			<?php submit_button( __( 'Jalankan Seleksi', 'spmb-pro' ), 'primary', 'submit', false ); ?>
		</form>
		<?php if ( $active_jenjang ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin-post.php?action=spmb_export&type=csv&jenjang=' . $active_jenjang ) ); ?>" class="button">
				<?php esc_html_e( 'Ekspor CSV', 'spmb-pro' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin-post.php?action=spmb_export&type=pdf-report&jenjang=' . $active_jenjang ) ); ?>" class="button">
				<?php esc_html_e( 'Ekspor Laporan PDF', 'spmb-pro' ); ?>
			</a>
		<?php endif; ?>
	</p>

	<h2><?php esc_html_e( 'Riwayat Seleksi', 'spmb-pro' ); ?></h2>
	<table class="widefat striped" role="presentation">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Waktu', 'spmb-pro' ); ?></th>
				<th><?php esc_html_e( 'Jenjang', 'spmb-pro' ); ?></th>
				<th><?php esc_html_e( 'Diterima', 'spmb-pro' ); ?></th>
				<th><?php esc_html_e( 'Cadangan', 'spmb-pro' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $runs ) ) : ?>
				<tr><td colspan="4"><?php esc_html_e( 'Belum ada riwayat.', 'spmb-pro' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $runs as $run ) : ?>
					<tr>
						<td><?php echo esc_html( $run->run_at ); ?></td>
						<td><?php echo esc_html( $run->jenjang ); ?></td>
						<td><?php echo esc_html( number_format_i18n( (int) $run->admitted_count ) ); ?></td>
						<td><?php echo esc_html( number_format_i18n( (int) $run->waitlist_count ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>