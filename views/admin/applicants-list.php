<?php
/**
 * View: daftar pendaftar dengan list table.
 *
 * @var SPMB_Admin_List_Table $table Instance list table.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings = SPMB_Settings_Repository::all();
$jenjang = $settings['jenjang'];
$jalur  = array_filter(
	SPMB_Defaults::JALUR,
	function ( $j ) use ( $settings ) {
		return ! empty( $settings['enabled_jalur'][ $j ] );
	}
);
$statuses = array( 'submitted', 'verified', 'rejected' );
$base = admin_url( 'admin.php?page=spmb-pro-applicants' );
?>
<div class="wrap spmb-wrap">
	<h1><?php esc_html_e( 'Pendaftar', 'spmb-pro' ); ?></h1>

	<form method="get" action="">
		<input type="hidden" name="page" value="spmb-pro-applicants" />
		<input type="hidden" name="spmb_bulk" value="" id="spmb_bulk_action" />
		<?php wp_nonce_field( 'spmb_bulk_applicants', 'spmb_bulk_nonce' ); ?>

		<div class="spmb-filter-bar">
			<select name="jenjang">
				<option value=""><?php esc_html_e( 'Semua Jenjang', 'spmb-pro' ); ?></option>
				<?php foreach ( $jenjang as $j ) : ?>
					<option value="<?php echo esc_attr( $j ); ?>" <?php selected( isset( $_GET['jenjang'] ) ? sanitize_key( wp_unslash( $_GET['jenjang'] ) ) : '', $j ); ?>><?php echo esc_html( $j ); ?></option>
				<?php endforeach; ?>
			</select>
			<select name="jalur">
				<option value=""><?php esc_html_e( 'Semua Jalur', 'spmb-pro' ); ?></option>
				<?php foreach ( $jalur as $j ) : ?>
					<option value="<?php echo esc_attr( $j ); ?>" <?php selected( isset( $_GET['jalur'] ) ? sanitize_key( wp_unslash( $_GET['jalur'] ) ) : '', $j ); ?>><?php echo esc_html( $j ); ?></option>
				<?php endforeach; ?>
			</select>
			<select name="status">
				<option value=""><?php esc_html_e( 'Semua Status', 'spmb-pro' ); ?></option>
				<?php foreach ( $statuses as $st ) : ?>
					<option value="<?php echo esc_attr( $st ); ?>" <?php selected( isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : '', $st ); ?>><?php echo esc_html( $st ); ?></option>
				<?php endforeach; ?>
			</select>
			<input type="search" name="s" placeholder="<?php esc_attr_e( 'Cari nama / no. pendaftaran', 'spmb-pro' ); ?>" value="<?php echo isset( $_GET['s'] ) ? esc_attr( wp_unslash( $_GET['s'] ) ) : ''; ?>" />
			<?php submit_button( __( 'Filter', 'spmb-pro' ), '', 'filter_action', false ); ?>
		</div>

		<?php $table->search_box( __( 'Cari', 'spmb-pro' ), 'spmb-search' ); ?>
		<?php $table->display(); ?>
	</form>
</div>
<script>
(function(){
	document.querySelectorAll('.spmb-wrap form').forEach(function(form){
		form.addEventListener('submit', function(e){
			var action = form.querySelector('select[name="action"], select[name="action2"]');
			if (action) {
				var val = action.options[action.selectedIndex].value;
				if (val === 'verify' || val === 'reject') {
					document.getElementById('spmb_bulk_action').value = val;
				}
			}
		});
	});
})();
</script>