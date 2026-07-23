<?php
/**
 * View: verifikasi pembayaran.
 *
 * @var array  $payments Daftar pembayaran (join pendaftar).
 * @var string $notice   Pesan notice.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$base       = admin_url( 'admin.php?page=spmb-pro-payments' );
$dashboard  = admin_url( 'admin.php?page=spmb-pro' );
$active_pay = isset( $_GET['pay_status'] ) ? sanitize_key( wp_unslash( $_GET['pay_status'] ) ) : '';

$pay_tabs = array(
	''         => __( 'Semua', 'spmb-pro' ),
	'unpaid'   => __( 'Belum Bayar', 'spmb-pro' ),
	'paid'     => __( 'Sudah Bayar', 'spmb-pro' ),
	'verified' => __( 'Terverifikasi', 'spmb-pro' ),
);

$status_badge_map = array(
	'unpaid'   => 'spmb-status-badge spmb-status-badge--pending',
	'paid'     => 'spmb-status-badge spmb-status-badge--warning',
	'verified' => 'spmb-status-badge spmb-status-badge--success',
	'void'     => 'spmb-status-badge spmb-status-badge--error',
);

// Render form aksi pembayaran: nonce + hidden field + tombol.
$pay_form = static function ( object $p, string $action, string $btn_class, string $label ): void {
	?>
	<form method="post" action="" class="spmb-form-inline">
		<?php wp_nonce_field( 'spmb_pay', 'spmb_pay_nonce' ); ?>
		<input type="hidden" name="payment_id" value="<?php echo esc_attr( $p->id ); ?>" />
		<input type="hidden" name="spmb_pay_action" value="<?php echo esc_attr( $action ); ?>" />
		<button type="submit" class="spmb-btn <?php echo esc_attr( $btn_class ); ?>"><?php echo esc_html( $label ); ?></button>
	</form>
	<?php
};
?>
<div class="wrap spmb-wrap">

	<nav class="spmb-breadcrumb" aria-label="breadcrumb">
		<a href="<?php echo esc_url( $dashboard ); ?>"><?php esc_html_e( 'Dashboard SPMB', 'spmb-pro' ); ?></a>
		<span class="spmb-breadcrumb-sep" aria-hidden="true">/</span>
		<span class="spmb-breadcrumb-current"><?php esc_html_e( 'Verifikasi Pembayaran', 'spmb-pro' ); ?></span>
	</nav>

	<header class="spmb-page-header">
		<h1 class="spmb-page-title"><?php esc_html_e( 'Verifikasi Pembayaran', 'spmb-pro' ); ?></h1>
		<p class="spmb-page-subtitle"><?php esc_html_e( 'Konfirmasi penerimaan biaya pendaftaran.', 'spmb-pro' ); ?></p>
	</header>

	<?php if ( $notice ) : ?>
		<div class="spmb-banner spmb-banner--success" role="status">
			<div class="spmb-banner-body">
				<span class="spmb-banner-label"><?php esc_html_e( 'Sukses', 'spmb-pro' ); ?></span>
				<span class="spmb-banner-value"><?php echo esc_html( $notice ); ?></span>
			</div>
		</div>
	<?php endif; ?>

	<div class="spmb-segmented" role="tablist" aria-label="<?php esc_attr_e( 'Status Pembayaran', 'spmb-pro' ); ?>">
		<?php foreach ( $pay_tabs as $key => $label ) : ?>
			<a href="<?php echo esc_url( $key ? add_query_arg( 'pay_status', $key, $base ) : $base ); ?>"
				class="spmb-segmented-item <?php echo $active_pay === $key ? 'is-active' : ''; ?>"
				role="tab"
				aria-selected="<?php echo $active_pay === $key ? 'true' : 'false'; ?>"
			><?php echo esc_html( $label ); ?></a>
		<?php endforeach; ?>
	</div>

	<table class="spmb-table" role="presentation">
		<thead>
			<tr>
				<th scope="col"><?php esc_html_e( 'No. Invoice', 'spmb-pro' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Pendaftar', 'spmb-pro' ); ?></th>
				<th scope="col" class="num"><?php esc_html_e( 'Jumlah', 'spmb-pro' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Status', 'spmb-pro' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Aksi', 'spmb-pro' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $payments ) ) : ?>
				<tr class="spmb-table-empty"><td colspan="5"><?php esc_html_e( 'Tidak ada data.', 'spmb-pro' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $payments as $p ) : ?>
					<tr>
						<td class="mono"><?php echo esc_html( $p->invoice_number ); ?></td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=spmb-pro-applicant&id=' . $p->applicant_id ) ); ?>">
								<?php echo esc_html( $p->full_name ?: '—' ); ?>
							</a>
							<div class="spmb-pay-row spmb-pay-row--tight">
								<span class="spmb-info-value spmb-info-value--muted spmb-info-value--sm"><?php echo esc_html( $p->registration_number ?: '' ); ?></span>
							</div>
						</td>
						<td class="num">Rp <?php echo esc_html( number_format_i18n( (float) $p->amount ) ); ?></td>
						<td>
							<span class="<?php echo esc_attr( $status_badge_map[ $p->status ] ?? 'spmb-status-badge spmb-status-badge--pending' ); ?>"><?php echo esc_html( $p->status ); ?></span>
						</td>
						<td class="spmb-pay-actions">
							<?php
							if ( 'unpaid' === $p->status ) {
								$pay_form( $p, 'paid', 'spmb-btn-ghost', __( 'Lunas', 'spmb-pro' ) );
								$pay_form( $p, 'void', 'spmb-btn-danger-ghost', __( 'Batal', 'spmb-pro' ) );
							} elseif ( 'paid' === $p->status ) {
								$pay_form( $p, 'verify', 'spmb-btn-primary', __( 'Verifikasi', 'spmb-pro' ) );
								$pay_form( $p, 'void', 'spmb-btn-danger-ghost', __( 'Batal', 'spmb-pro' ) );
							}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>

</div>