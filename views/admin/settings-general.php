<?php
/**
 * Tab: Umum.
 *
 * @var array $settings Data pengaturan.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr>
	<th scope="row"><label for="school_name"><?php esc_html_e( 'Nama Sekolah', 'spmb-pro' ); ?></label></th>
	<td><input name="spmb_settings[school_name]" id="school_name" type="text" class="regular-text" value="<?php echo esc_attr( $settings['school_name'] ); ?>" /></td>
</tr>
<tr>
	<th scope="row"><label for="school_address"><?php esc_html_e( 'Alamat Sekolah', 'spmb-pro' ); ?></label></th>
	<td><textarea name="spmb_settings[school_address]" id="school_address" class="large-text" rows="3"><?php echo esc_textarea( $settings['school_address'] ); ?></textarea></td>
</tr>
<tr>
	<th scope="row"><?php esc_html_e( 'Publikasikan Pengumuman', 'spmb-pro' ); ?></th>
	<td>
		<label>
			<input type="checkbox" name="spmb_settings[pengumuman_published]" value="1" <?php checked( $settings['pengumuman_published'] ); ?> />
			<?php esc_html_e( 'Tampilkan hasil seleksi di halaman publik pengumuman.', 'spmb-pro' ); ?>
		</label>
	</td>
</tr>
<tr>
	<th scope="row"><?php esc_html_e( 'Hapus File saat Uninstall', 'spmb-pro' ); ?></th>
	<td>
		<label>
			<input type="checkbox" name="spmb_settings[delete_files_on_uninstall]" value="1" <?php checked( $settings['delete_files_on_uninstall'] ); ?> />
			<?php esc_html_e( 'Hapus berkas pendaftar di folder uploads saat plugin dihapus.', 'spmb-pro' ); ?>
		</label>
	</td>
</tr>