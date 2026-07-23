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
$base         = admin_url( 'admin.php?page=spmb-pro-selection' );
$dashboard    = admin_url( 'admin.php?page=spmb-pro' );
?>
<div class="wrap spmb-wrap">

	<nav class="spmb-breadcrumb" aria-label="breadcrumb">
		<a href="<?php echo esc_url( $dashboard ); ?>"><?php esc_html_e( 'Dashboard SPMB', 'spmb-pro' ); ?></a>
		<span class="spmb-breadcrumb-sep" aria-hidden="true">/</span>
		<span class="spmb-breadcrumb-current"><?php esc_html_e( 'Pengumuman & Seleksi', 'spmb-pro' ); ?></span>
	</nav>

	<header class="spmb-page-header">
		<h1 class="spmb-page-title"><?php esc_html_e( 'Pengumuman & Seleksi', 'spmb-pro' ); ?></h1>
		<p class="spmb-page-subtitle"><?php esc_html_e( 'Jalankan seleksi per jenjang, kelola publikasi pengumuman, dan ekspor laporan.', 'spmb-pro' ); ?></p>
	</header>

	<?php if ( $notice ) : ?>
		<div class="spmb-banner spmb-banner--success" role="status">
			<div class="spmb-banner-body">
				<span class="spmb-banner-label"><?php esc_html_e( 'Sukses', 'spmb-pro' ); ?></span>
				<span class="spmb-banner-value"><?php echo esc_html( $notice ); ?></span>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $jenjang ) ) : ?>
		<div class="spmb-segmented" role="tablist" aria-label="<?php esc_attr_e( 'Jenjang', 'spmb-pro' ); ?>">
			<?php foreach ( $jenjang as $j ) : ?>
				<a href="<?php echo esc_url( add_query_arg( 'jenjang', $j, $base ) ); ?>"
					class="spmb-segmented-item <?php echo $active_jenjang === $j ? 'is-active' : ''; ?>"
					role="tab"
					aria-selected="<?php echo $active_jenjang === $j ? 'true' : 'false'; ?>"
				><?php echo esc_html( $j ); ?></a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( $active_jenjang ) : ?>
		<div class="spmb-banner <?php echo $published ? 'spmb-banner--success' : 'spmb-banner--warning'; ?>">
			<div class="spmb-banner-body">
				<span class="spmb-banner-label"><?php esc_html_e( 'Status Pengumuman', 'spmb-pro' ); ?></span>
				<span class="spmb-banner-value">
					<?php
					if ( $published ) {
						esc_html_e( 'Dipublikasikan — siswa dapat melihat hasil.', 'spmb-pro' );
					} else {
						esc_html_e( 'Belum dipublikasikan — siswa tidak dapat melihat hasil.', 'spmb-pro' );
					}
					?>
				</span>
			</div>
			<div class="spmb-banner-action">
				<form method="post" action="">
					<?php wp_nonce_field( 'spmb_selection', 'spmb_selection_nonce' ); ?>
					<input type="hidden" name="jenjang" value="<?php echo esc_attr( $active_jenjang ); ?>" />
					<?php if ( $published ) : ?>
						<input type="hidden" name="spmb_selection_action" value="unpublish" />
						<button type="submit" class="spmb-btn spmb-btn-danger-ghost"><?php esc_html_e( 'Tarik Pengumuman', 'spmb-pro' ); ?></button>
					<?php else : ?>
						<input type="hidden" name="spmb_selection_action" value="publish" />
						<button type="submit" class="spmb-btn spmb-btn-primary"><?php esc_html_e( 'Publikasikan Pengumuman', 'spmb-pro' ); ?></button>
					<?php endif; ?>
				</form>
			</div>
		</div>
	<?php endif; ?>

	<section class="spmb-section">
		<h2 class="spmb-section-title">
			<?php esc_html_e( 'Ringkasan Eligible & Kuota', 'spmb-pro' ); ?>
			<span class="spmb-section-meta">— <?php echo esc_html( $active_jenjang ); ?></span>
		</h2>
		<table class="spmb-table" role="presentation">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Jalur', 'spmb-pro' ); ?></th>
					<th scope="col" class="num"><?php esc_html_e( 'Pendaftar Eligible', 'spmb-pro' ); ?></th>
					<th scope="col" class="num"><?php esc_html_e( 'Kuota', 'spmb-pro' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Status', 'spmb-pro' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $summary['per_jalur'] ) ) : ?>
					<tr class="spmb-table-empty"><td colspan="4"><?php esc_html_e( 'Pilih jenjang untuk melihat ringkasan.', 'spmb-pro' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $summary['per_jalur'] as $jalur => $data ) : ?>
						<?php
						$eligible = (int) $data['eligible'];
						$quota    = (int) $data['quota'];
						$over     = $eligible > $quota;
						?>
						<tr>
							<td><?php echo esc_html( $jalur_labels[ $jalur ] ?? $jalur ); ?></td>
							<td class="num"><?php echo esc_html( number_format_i18n( $eligible ) ); ?></td>
							<td class="num"><?php echo esc_html( number_format_i18n( $quota ) ); ?></td>
							<td>
								<?php if ( $over ) : ?>
									<span class="spmb-quota-badge spmb-quota-badge--over"><?php esc_html_e( 'Eligible melebihi kuota', 'spmb-pro' ); ?></span>
								<?php elseif ( $eligible > 0 ) : ?>
									<span class="spmb-quota-badge spmb-quota-badge--ok"><?php esc_html_e( 'Sesuai', 'spmb-pro' ); ?></span>
								<?php else : ?>
									<span class="spmb-quota-badge spmb-quota-badge--empty"><?php esc_html_e( 'Kosong', 'spmb-pro' ); ?></span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
			<?php if ( ! empty( $summary['total_eligible'] ) ) : ?>
				<tfoot>
					<tr>
						<th scope="row"><?php esc_html_e( 'Total Eligible', 'spmb-pro' ); ?></th>
						<th class="num" colspan="3"><?php echo esc_html( number_format_i18n( $summary['total_eligible'] ) ); ?></th>
					</tr>
				</tfoot>
			<?php endif; ?>
		</table>
	</section>

	<?php if ( $active_jenjang ) : ?>
		<div class="spmb-action-bar">
			<form method="post" action="">
				<?php wp_nonce_field( 'spmb_selection', 'spmb_selection_nonce' ); ?>
				<input type="hidden" name="jenjang" value="<?php echo esc_attr( $active_jenjang ); ?>" />
				<input type="hidden" name="spmb_selection_action" value="run" />
				<button type="submit" class="spmb-btn spmb-btn-primary"><?php esc_html_e( 'Jalankan Seleksi', 'spmb-pro' ); ?></button>
			</form>
			<a href="<?php echo esc_url( admin_url( 'admin-post.php?action=spmb_export&type=csv&jenjang=' . $active_jenjang ) ); ?>" class="spmb-btn spmb-btn-ghost">
				<?php esc_html_e( 'Ekspor CSV', 'spmb-pro' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin-post.php?action=spmb_export&type=pdf-report&jenjang=' . $active_jenjang ) ); ?>" class="spmb-btn spmb-btn-ghost">
				<?php esc_html_e( 'Ekspor Laporan PDF', 'spmb-pro' ); ?>
			</a>
		</div>
	<?php endif; ?>

	<section class="spmb-section">
		<h2 class="spmb-section-title"><?php esc_html_e( 'Riwayat Seleksi', 'spmb-pro' ); ?></h2>
		<table class="spmb-table" role="presentation">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Waktu', 'spmb-pro' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Jenjang', 'spmb-pro' ); ?></th>
					<th scope="col" class="num"><?php esc_html_e( 'Diterima', 'spmb-pro' ); ?></th>
					<th scope="col" class="num"><?php esc_html_e( 'Cadangan', 'spmb-pro' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $runs ) ) : ?>
					<tr class="spmb-table-empty"><td colspan="4"><?php esc_html_e( 'Belum ada riwayat seleksi.', 'spmb-pro' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $runs as $run ) : ?>
						<tr>
							<td class="mono"><?php echo esc_html( $run->run_at ); ?></td>
							<td><?php echo esc_html( $run->jenjang ); ?></td>
							<td class="num"><?php echo esc_html( number_format_i18n( (int) $run->admitted_count ) ); ?></td>
							<td class="num"><?php echo esc_html( number_format_i18n( (int) $run->waitlist_count ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</section>

</div>