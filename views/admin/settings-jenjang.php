<?php
/**
 * Tab: Jenjang & Jalur.
 *
 * @var array $settings Data pengaturan.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$jenjang_all    = array( 'SD', 'SMP', 'SMA' );
$jenjang_labels = array(
	'SD'  => __( 'SD / MI', 'spmb-pro' ),
	'SMP' => __( 'SMP / MTs', 'spmb-pro' ),
	'SMA' => __( 'SMA / SMK / MA', 'spmb-pro' ),
);
$jalur_labels   = array(
	'zonasi'      => __( 'Zonasi', 'spmb-pro' ),
	'afirmasi'    => __( 'Afirmasi', 'spmb-pro' ),
	'prestasi'    => __( 'Prestasi', 'spmb-pro' ),
	'perpindahan' => __( 'Perpindahan Tugas', 'spmb-pro' ),
);
?>
<tr>
	<th scope="row"><?php esc_html_e( 'Jenjang Aktif', 'spmb-pro' ); ?></th>
	<td>
		<fieldset class="spmb-fieldset">
			<?php foreach ( $jenjang_all as $j ) : ?>
				<label>
					<input type="checkbox" name="spmb_settings[jenjang][]" value="<?php echo esc_attr( $j ); ?>" <?php checked( in_array( $j, $settings['jenjang'], true ) ); ?> />
					<?php echo esc_html( $jenjang_labels[ $j ] ); ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
	</td>
</tr>
<tr>
	<th scope="row"><?php esc_html_e( 'Jalur Seleksi Aktif', 'spmb-pro' ); ?></th>
	<td>
		<fieldset class="spmb-fieldset">
			<?php foreach ( $jalur_labels as $code => $label ) : ?>
				<label>
					<input type="checkbox" name="spmb_settings[enabled_jalur][<?php echo esc_attr( $code ); ?>]" value="1" <?php checked( ! empty( $settings['enabled_jalur'][ $code ] ) ); ?> />
					<?php echo esc_html( $label ); ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
	</td>
</tr>
<tr>
	<th scope="row"><label for="afirmasi_categories"><?php esc_html_e( 'Kategori Afirmasi', 'spmb-pro' ); ?></label></th>
	<td>
		<input name="spmb_settings[afirmasi_categories]" id="afirmasi_categories" type="text" class="large-text" value="<?php echo esc_attr( implode( ', ', $settings['afirmasi_categories'] ) ); ?>" />
		<p class="description"><?php esc_html_e( 'Pisahkan dengan koma. Contoh: ekonomi, diffabel, lainnya.', 'spmb-pro' ); ?></p>
	</td>
</tr>
<tr>
	<th scope="row"><?php esc_html_e( 'Bobot Prestasi', 'spmb-pro' ); ?></th>
	<td>
		<fieldset class="spmb-fieldset">
			<label><?php esc_html_e( 'Rapor', 'spmb-pro' ); ?>
				<input type="number" step="0.01" min="0" max="1" name="spmb_settings[prestasi_weights][rapor]" value="<?php echo esc_attr( $settings['prestasi_weights']['rapor'] ); ?>" class="small-text" />
			</label>
			<label><?php esc_html_e( 'Prestasi', 'spmb-pro' ); ?>
				<input type="number" step="0.01" min="0" max="1" name="spmb_settings[prestasi_weights][achievement]" value="<?php echo esc_attr( $settings['prestasi_weights']['achievement'] ); ?>" class="small-text" />
			</label>
		</fieldset>
	</td>
</tr>