<?php
/**
 * Partial: input field berulang.
 *
 * @var array  $data     Data view.
 * @var string $name     Nama field.
 * @var string $label    Label.
 * @var string $type     Tipe input (text/date/number/email/tel).
 * @var bool   $required Wajib.
 * @var string $value    Nilai.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$errors = $data['field_errors'] ?? array();
$msg    = $errors[ $name ] ?? '';
$value  = $value ?? '';
?>
<p class="spmb-field">
	<label for="spmb-<?php echo esc_attr( $name ); ?>">
		<?php echo esc_html( $label ); ?><?php if ( ! empty( $required ) ) : ?> <span class="required">*</span><?php endif; ?>
	</label>
	<input
		type="<?php echo esc_attr( $type ); ?>"
		id="spmb-<?php echo esc_attr( $name ); ?>"
		name="<?php echo esc_attr( $name ); ?>"
		value="<?php echo esc_attr( $value ); ?>"
		<?php echo ! empty( $required ) ? 'required' : ''; ?>
	/>
	<?php if ( $msg ) : ?>
		<span class="spmb-error"><?php echo esc_html( $msg ); ?></span>
	<?php endif; ?>
</p>