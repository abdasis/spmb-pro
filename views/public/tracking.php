<?php
/**
 * View: cek status pendaftaran.
 *
 * @var object|null $result Data pendaftar (atau null).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$status_labels = array(
	'draft'      => __( 'Draft', 'spmb-pro' ),
	'submitted'  => __( 'Diterima (menunggu verifikasi)', 'spmb-pro' ),
	'verified'   => __( 'Terverifikasi', 'spmb-pro' ),
	'rejected'   => __( 'Ditolak', 'spmb-pro' ),
);

$payment_labels = array(
	'none'     => __( 'Tidak ada tagihan', 'spmb-pro' ),
	'unpaid'   => __( 'Belum dibayar', 'spmb-pro' ),
	'paid'     => __( 'Sudah dibayar (menunggu verifikasi)', 'spmb-pro' ),
	'verified' => __( 'Lunas / Terverifikasi', 'spmb-pro' ),
	'void'     => __( 'Dibatalkan', 'spmb-pro' ),
);

$current_url = esc_url( home_url( add_query_arg( array() ) ) );
?>
<div class="spmb-form-wrap spmb-tracking-wrap">
	<h2><?php esc_html_e( 'Cek Status Pendaftaran', 'spmb-pro' ); ?></h2>
	<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<input type="hidden" name="spmb_reg_action" value="status" />
		<p class="spmb-field">
			<label for="spmb-reg"><?php esc_html_e( 'Nomor Pendaftaran', 'spmb-pro' ); ?></label>
			<input type="text" id="spmb-reg" name="spmb_reg" value="<?php echo esc_attr( $result ? $result->registration_number : '' ); ?>" required />
		</p>
		<p><button type="submit" class="spmb-submit"><?php esc_html_e( 'Cek Status', 'spmb-pro' ); ?></button></p>
	</form>

	<?php if ( isset( $_GET['spmb_reg'] ) && ! $result ) : // phpcs:ignore WordPress.Security ?>
		<div class="spmb-notice spmb-notice-error">
			<p><?php esc_html_e( 'Nomor pendaftaran tidak ditemukan.', 'spmb-pro' ); ?></p>
		</div>
	<?php elseif ( $result ) : ?>
		<div class="spmb-status-result">
			<h3><?php echo esc_html( $result->full_name ); ?></h3>
			<?php
			$payment = SPMB_DB_Payments::for_applicant( (int) $result->id );
			$pay_key = $payment ? $payment->status : 'none';
			?>
			<table class="spmb-status-table" role="presentation">
				<tr><th><?php esc_html_e( 'No. Pendaftaran', 'spmb-pro' ); ?></th><td><?php echo esc_html( $result->registration_number ); ?></td></tr>
				<tr><th><?php esc_html_e( 'Jenjang', 'spmb-pro' ); ?></th><td><?php echo esc_html( $result->jenjang ); ?></td></tr>
				<tr><th><?php esc_html_e( 'Jalur', 'spmb-pro' ); ?></th><td><?php echo esc_html( $result->jalur ); ?></td></tr>
				<tr><th><?php esc_html_e( 'Status Pendaftaran', 'spmb-pro' ); ?></th><td><?php echo esc_html( $status_labels[ $result->status ] ?? $result->status ); ?></td></tr>
				<tr><th><?php esc_html_e( 'Status Pembayaran', 'spmb-pro' ); ?></th><td><?php echo esc_html( $payment_labels[ $pay_key ] ?? $pay_key ); ?></td></tr>
				<?php if ( $payment && in_array( $payment->status, array( 'unpaid', 'paid' ), true ) ) : ?>
					<tr><th><?php esc_html_e( 'No. Invoice', 'spmb-pro' ); ?></th><td><?php echo esc_html( $payment->invoice_number ); ?></td></tr>
					<tr><th><?php esc_html_e( 'Jumlah', 'spmb-pro' ); ?></th><td>Rp <?php echo esc_html( number_format_i18n( (float) $payment->amount ) ); ?></td></tr>
				<?php endif; ?>
			</table>
		</div>
	<?php endif; ?>
</div>