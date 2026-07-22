<?php
/**
 * Partial: upload field.
 *
 * @var array  $data  Data view.
 * @var string $name  Nama field.
 * @var string $label Label.
 * @var bool   $required Wajib.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$errors = $data['field_errors'] ?? array();
$msg    = $errors[ $name ] ?? '';
?>
<p class="spmb-field">
	<label for="spmb-<?php echo esc_attr( $name ); ?>">
		<?php echo esc_html( $label ); ?><?php if ( ! empty( $required ) ) : ?> <span class="required">*</span><?php endif; ?>
	</label>
	<input type="file" id="spmb-<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php echo ! empty( $required ) ? 'required' : ''; ?> />
	<?php if ( $msg ) : ?>
		<span class="spmb-error"><?php echo esc_html( $msg ); ?></span>
	<?php endif; ?>
</p>