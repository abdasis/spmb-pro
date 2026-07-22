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

$base = admin_url( 'admin.php?page=spmb-pro-payments' );
?>
<div class="wrap spmb-wrap">
	<h1><?php esc_html_e( 'Verifikasi Pembayaran', 'spmb-pro' ); ?></h1>

	<?php if ( $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
	<?php endif; ?>

	<ul class="subsubsub">
		<li><a href="<?php echo esc_url( $base ); ?>"><?php esc_html_e( 'Semua', 'spmb-pro' ); ?></a> |</li>
		<li><a href="<?php echo esc_url( add_query_arg( 'pay_status', 'unpaid', $base ) ); ?>"><?php esc_html_e( 'Belum Bayar', 'spmb-pro' ); ?></a> |</li>
		<li><a href="<?php echo esc_url( add_query_arg( 'pay_status', 'paid', $base ) ); ?>"><?php esc_html_e( 'Sudah Bayar', 'spmb-pro' ); ?></a> |</li>
		<li><a href="<?php echo esc_url( add_query_arg( 'pay_status', 'verified', $base ) ); ?>"><?php esc_html_e( 'Terverifikasi', 'spmb-pro' ); ?></a></li>
	</ul>

	<table class="widefat striped" role="presentation">
		<thead>
			<tr>
				<th><?php esc_html_e( 'No. Invoice', 'spmb-pro' ); ?></th>
				<th><?php esc_html_e( 'Pendaftar', 'spmb-pro' ); ?></th>
				<th><?php esc_html_e( 'Jumlah', 'spmb-pro' ); ?></th>
				<th><?php esc_html_e( 'Status', 'spmb-pro' ); ?></th>
				<th><?php esc_html_e( 'Aksi', 'spmb-pro' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $payments ) ) : ?>
				<tr><td colspan="5"><?php esc_html_e( 'Tidak ada data.', 'spmb-pro' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $payments as $p ) : ?>
					<tr>
						<td><?php echo esc_html( $p->invoice_number ); ?></td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=spmb-pro-applicant&id=' . $p->applicant_id ) ); ?>">
								<?php echo esc_html( $p->full_name ?: '—' ); ?>
							</a>
							<div class="description"><?php echo esc_html( $p->registration_number ?: '' ); ?></div>
						</td>
						<td>Rp <?php echo esc_html( number_format_i18n( (float) $p->amount ) ); ?></td>
						<td><?php echo esc_html( $p->status ); ?></td>
						<td>
							<form method="post" action="" style="display:inline">
								<?php wp_nonce_field( 'spmb_pay', 'spmb_pay_nonce' ); ?>
								<input type="hidden" name="payment_id" value="<?php echo esc_attr( $p->id ); ?>" />
								<?php if ( 'unpaid' === $p->status ) : ?>
									<input type="hidden" name="spmb_pay_action" value="paid" />
									<?php submit_button( __( 'Lunas', 'spmb-pro' ), 'small', 'submit', false ); ?>
								<?php elseif ( 'paid' === $p->status ) : ?>
									<input type="hidden" name="spmb_pay_action" value="verify" />
									<?php submit_button( __( 'Verifikasi', 'spmb-pro' ), 'primary small', 'submit', false ); ?>
								<?php elseif ( in_array( $p->status, array( 'unpaid', 'paid' ), true ) ) : ?>
									<input type="hidden" name="spmb_pay_action" value="void" />
									<?php submit_button( __( 'Batal', 'spmb-pro' ), 'delete small', 'submit', false ); ?>
								<?php endif; ?>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>