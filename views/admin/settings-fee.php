<?php
/**
 * Tab: Biaya & Tanggal.
 *
 * @var array $settings Data pengaturan.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr>
	<th scope="row"><label for="fee"><?php esc_html_e( 'Biaya Pendaftaran (Rp)', 'spmb-pro' ); ?></label></th>
	<td><input name="spmb_settings[fee]" id="fee" type="number" min="0" step="1000" class="regular-text" value="<?php echo esc_attr( $settings['fee'] ); ?>" /></td>
</tr>
<tr>
	<th scope="row"><label for="registration_open"><?php esc_html_e( 'Tanggal Buka Pendaftaran', 'spmb-pro' ); ?></label></th>
	<td><input name="spmb_settings[registration_open]" id="registration_open" type="date" value="<?php echo esc_attr( $settings['registration_open'] ); ?>" /></td>
</tr>
<tr>
	<th scope="row"><label for="registration_close"><?php esc_html_e( 'Tanggal Tutup Pendaftaran', 'spmb-pro' ); ?></label></th>
	<td><input name="spmb_settings[registration_close]" id="registration_close" type="date" value="<?php echo esc_attr( $settings['registration_close'] ); ?>" /></td>
</tr>