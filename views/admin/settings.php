<?php
/**
 * View: layar pengaturan SPMB Pro.
 *
 * @var array  $settings Data pengaturan.
 * @var string $tab      Tab aktif.
 * @var string $notice   Pesan notice.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tabs = array(
	'general'  => __( 'Umum', 'spmb-pro' ),
	'jenjang'  => __( 'Jenjang & Jalur', 'spmb-pro' ),
	'quota'    => __( 'Kuota', 'spmb-pro' ),
	'program'  => __( 'Program / Jurusan', 'spmb-pro' ),
	'fee'      => __( 'Biaya & Tanggal', 'spmb-pro' ),
	'document' => __( 'Dokumen', 'spmb-pro' ),
	'zonasi'   => __( 'Zonasi Koordinat', 'spmb-pro' ),
);

$tab_url_base = admin_url( 'admin.php?page=spmb-pro-settings' );
?>
<div class="wrap spmb-wrap">
	<h1><?php esc_html_e( 'Pengaturan SPMB Pro', 'spmb-pro' ); ?></h1>

	<?php if ( $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
	<?php endif; ?>

	<h2 class="nav-tab-wrapper">
		<?php foreach ( $tabs as $key => $label ) : ?>
			<a href="<?php echo esc_url( $tab_url_base . '&tab=' . $key ); ?>" class="nav-tab <?php echo $tab === $key ? 'nav-tab-active' : ''; ?>">
				<?php echo esc_html( $label ); ?>
			</a>
		<?php endforeach; ?>
	</h2>

	<form method="post" action="">
		<?php wp_nonce_field( 'spmb_save_settings', 'spmb_settings_nonce' ); ?>
		<input type="hidden" name="spmb_save_settings" value="1" />
		<table class="form-table" role="presentation">
			<?php require SPMB_PATH . 'views/admin/settings-' . $tab . '.php'; ?>
		</table>

		<?php
		$jalur_labels = array(
			'zonasi'       => __( 'Zonasi', 'spmb-pro' ),
			'afirmasi'     => __( 'Afirmasi', 'spmb-pro' ),
			'prestasi'     => __( 'Prestasi', 'spmb-pro' ),
			'perpindahan'  => __( 'Perpindahan Tugas', 'spmb-pro' ),
		);
		?>
		<?php submit_button( __( 'Simpan Pengaturan', 'spmb-pro' ) ); ?>
	</form>
</div>
<?php
// Sediakan label jalur untuk partial kuota/program via variabel global sederhana.
$GLOBALS['spmb_jalur_labels'] = $jalur_labels;