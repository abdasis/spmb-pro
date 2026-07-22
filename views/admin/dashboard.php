<?php
/**
 * View: dashboard statistik.
 *
 * @var array $stats Statistik ringkas.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap spmb-wrap">
	<h1><?php esc_html_e( 'Dashboard SPMB Pro', 'spmb-pro' ); ?></h1>

	<div class="spmb-stats">
		<div class="spmb-stat-card">
			<span class="spmb-stat-num"><?php echo esc_html( number_format_i18n( $stats['total'] ) ); ?></span>
			<span class="spmb-stat-label"><?php esc_html_e( 'Total Pendaftar', 'spmb-pro' ); ?></span>
		</div>
		<div class="spmb-stat-card">
			<span class="spmb-stat-num"><?php echo esc_html( number_format_i18n( $stats['verified'] ) ); ?></span>
			<span class="spmb-stat-label"><?php esc_html_e( 'Terverifikasi', 'spmb-pro' ); ?></span>
		</div>
		<div class="spmb-stat-card">
			<span class="spmb-stat-num"><?php echo esc_html( number_format_i18n( $stats['paid'] ) ); ?></span>
			<span class="spmb-stat-label"><?php esc_html_e( 'Pembayaran Lunas', 'spmb-pro' ); ?></span>
		</div>
	</div>

	<p>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=spmb-pro-applicants' ) ); ?>" class="button button-primary">
			<?php esc_html_e( 'Lihat Pendaftar', 'spmb-pro' ); ?>
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=spmb-pro-settings' ) ); ?>" class="button">
			<?php esc_html_e( 'Pengaturan', 'spmb-pro' ); ?>
		</a>
	</p>
</div>