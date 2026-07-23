<?php
/**
 * Tab: Dokumen upload.
 *
 * @var array $settings Data pengaturan.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mime_options = array(
	'application/pdf' => 'PDF',
	'image/jpeg'      => 'JPEG',
	'image/png'       => 'PNG',
);
?>
<tr>
	<th scope="row"><?php esc_html_e( 'Tipe Berkas Diizinkan', 'spmb-pro' ); ?></th>
	<td>
		<fieldset class="spmb-fieldset">
			<?php foreach ( $mime_options as $mime => $label ) : ?>
				<label>
					<input type="checkbox" name="spmb_settings[allowed_mimes][]" value="<?php echo esc_attr( $mime ); ?>" <?php checked( in_array( $mime, $settings['allowed_mimes'], true ) ); ?> />
					<?php echo esc_html( $label ); ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
	</td>
</tr>
<tr>
	<th scope="row"><label for="max_file_size"><?php esc_html_e( 'Ukuran Maksimum (byte)', 'spmb-pro' ); ?></label></th>
	<td>
		<input name="spmb_settings[max_file_size]" id="max_file_size" type="number" min="0" class="regular-text" value="<?php echo esc_attr( $settings['max_file_size'] ); ?>" />
		<p class="description"><?php echo esc_html( sprintf( /* translators: %s: byte */ __( 'Default %s byte (2 MB).', 'spmb-pro' ), '2097152' ) ); ?></p>
	</td>
</tr>