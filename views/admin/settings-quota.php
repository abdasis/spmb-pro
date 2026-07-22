<?php
/**
 * Tab: Kuota per jenjang per jalur.
 *
 * @var array $settings Data pengaturan.
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
?>
<tr>
	<th scope="row"><?php esc_html_e( 'Kuota Per Jalur', 'spmb-pro' ); ?></th>
	<td>
		<?php if ( empty( $settings['jenjang'] ) ) : ?>
			<p><?php esc_html_e( 'Belum ada jenjang aktif. Aktifkan jenjang terlebih dahulu.', 'spmb-pro' ); ?></p>
		<?php else : ?>
			<table class="widefat striped" role="presentation">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Jenjang', 'spmb-pro' ); ?></th>
						<?php foreach ( $jalur_labels as $label ) : ?>
							<th><?php echo esc_html( $label ); ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $settings['jenjang'] as $j ) : ?>
						<tr>
							<td><strong><?php echo esc_html( $j ); ?></strong></td>
							<?php foreach ( $jalur_labels as $code => $label ) : ?>
								<td>
									<input type="number" min="0" name="spmb_settings[quotas][<?php echo esc_attr( $j ); ?>][<?php echo esc_attr( $code ); ?>]" value="<?php echo esc_attr( $settings['quotas'][ $j ][ $code ] ?? 0 ); ?>" class="small-text" />
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</td>
</tr>