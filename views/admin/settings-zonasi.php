<?php
/**
 * Tab: Koordinat sekolah untuk zonasi.
 *
 * @var array $settings Data pengaturan.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr>
	<th scope="row"><label for="school_latitude"><?php esc_html_e( 'Latitude Sekolah', 'spmb-pro' ); ?></label></th>
	<td><input name="spmb_settings[school_latitude]" id="school_latitude" type="text" class="regular-text" value="<?php echo esc_attr( $settings['school_latitude'] ); ?>" /></td>
</tr>
<tr>
	<th scope="row"><label for="school_longitude"><?php esc_html_e( 'Longitude Sekolah', 'spmb-pro' ); ?></label></th>
	<td><input name="spmb_settings[school_longitude]" id="school_longitude" type="text" class="regular-text" value="<?php echo esc_attr( $settings['school_longitude'] ); ?>" /></td>
</tr>
<tr>
	<th scope="row"><?php esc_html_e( 'Catatan Zonasi', 'spmb-pro' ); ?></th>
	<td><p class="description"><?php esc_html_e( 'Koordinat ini dipakai menghitung jarak rumah calon siswa ke sekolah untuk ranking jalur zonasi.', 'spmb-pro' ); ?></p></td>
</tr>