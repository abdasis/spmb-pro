<?php
/**
 * Tab: Program / Jurusan per jenjang.
 *
 * @var array $settings Data pengaturan.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr>
	<th scope="row"><?php esc_html_e( 'Daftar Program / Jurusan', 'spmb-pro' ); ?></th>
	<td>
		<?php if ( empty( $settings['jenjang'] ) ) : ?>
			<p class="spmb-empty"><?php esc_html_e( 'Belum ada jenjang aktif.', 'spmb-pro' ); ?></p>
		<?php else : ?>
			<p class="description"><?php esc_html_e( 'Format: kode=nilai. Pisahkan beberapa dengan koma. Contoh: IPA=IPA, IPS=IPS.', 'spmb-pro' ); ?></p>
			<?php foreach ( $settings['jenjang'] as $j ) : ?>
				<p class="spmb-note">
					<label><strong><?php echo esc_html( $j ); ?>:</strong></label><br />
					<input type="text" class="large-text spmb-program-input" data-jenjang="<?php echo esc_attr( $j ); ?>" />
				</p>
				<?php $hidden = array(); ?>
				<?php foreach ( ( $settings['programs'][ $j ] ?? array() ) as $code => $name ) : ?>
					<?php $hidden[] = $code . '=' . $name; ?>
				<?php endforeach; ?>
				<input type="hidden" name="spmb_programs_raw[<?php echo esc_attr( $j ); ?>]" value="<?php echo esc_attr( implode( ',', $hidden ) ); ?>" class="spmb-program-hidden" data-jenjang="<?php echo esc_attr( $j ); ?>" />
				<p class="description" id="prog-preview-<?php echo esc_attr( $j ); ?>"><?php echo esc_html( implode( ', ', $settings['programs'][ $j ] ?? array() ) ); ?></p>
			<?php endforeach; ?>
		<?php endif; ?>
	</td>
</tr>