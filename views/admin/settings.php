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
$dashboard    = admin_url( 'admin.php?page=spmb-pro' );
$tab_labels   = array_combine( array_keys( $tabs ), array_values( $tabs ) );
?>
<div class="wrap spmb-wrap">

	<nav class="spmb-breadcrumb" aria-label="breadcrumb">
		<a href="<?php echo esc_url( $dashboard ); ?>"><?php esc_html_e( 'Dashboard SPMB', 'spmb-pro' ); ?></a>
		<span class="spmb-breadcrumb-sep" aria-hidden="true">/</span>
		<span class="spmb-breadcrumb-current"><?php esc_html_e( 'Pengaturan', 'spmb-pro' ); ?></span>
	</nav>

	<header class="spmb-page-header">
		<h1 class="spmb-page-title"><?php esc_html_e( 'Pengaturan SPMB Pro', 'spmb-pro' ); ?></h1>
		<p class="spmb-page-subtitle"><?php esc_html_e( 'Konfigurasi jenjang, jalur, kuota, biaya, dan dokumen.', 'spmb-pro' ); ?></p>
	</header>

	<?php if ( $notice ) : ?>
		<div class="spmb-banner spmb-banner--success" role="status">
			<div class="spmb-banner-body">
				<span class="spmb-banner-label"><?php esc_html_e( 'Sukses', 'spmb-pro' ); ?></span>
				<span class="spmb-banner-value"><?php echo esc_html( $notice ); ?></span>
			</div>
		</div>
	<?php endif; ?>

	<div class="spmb-segmented" role="tablist" aria-label="<?php esc_attr_e( 'Tab Pengaturan', 'spmb-pro' ); ?>">
		<?php foreach ( $tabs as $key => $label ) : ?>
			<a href="<?php echo esc_url( $tab_url_base . '&tab=' . $key ); ?>"
				class="spmb-segmented-item <?php echo $tab === $key ? 'is-active' : ''; ?>"
				role="tab"
				aria-selected="<?php echo $tab === $key ? 'true' : 'false'; ?>"
			><?php echo esc_html( $label ); ?></a>
		<?php endforeach; ?>
	</div>

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
		<p class="submit">
			<button type="submit" class="spmb-btn spmb-btn-primary" name="submit" value="1"><?php esc_html_e( 'Simpan Pengaturan', 'spmb-pro' ); ?></button>
		</p>
	</form>
</div>
<?php
// Sediakan label jalur untuk partial kuota/program via variabel global sederhana.
$GLOBALS['spmb_jalur_labels'] = $jalur_labels;