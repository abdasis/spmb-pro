<?php
/**
 * Transisi status pembayaran.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_Payment_Status {

	/**
	 * Daftar status valid.
	 */
	public const VALID = array( 'unpaid', 'paid', 'verified', 'void' );

	/**
	 * Tandai invoice lunas (paid).
	 *
	 * @param int $payment_id ID payment.
	 * @return bool
	 */
	public static function mark_paid( int $payment_id ): bool {
		$current = self::get( $payment_id );
		if ( ! $current || 'unpaid' !== $current->status ) {
			return false;
		}
		return SPMB_DB_Payments::update(
			$payment_id,
			array( 'status' => 'paid', 'paid_at' => current_time( 'mysql' ) )
		);
	}

	/**
	 * Verifikasi pembayaran (paid -> verified).
	 *
	 * @param int $payment_id ID payment.
	 * @param int $user_id    ID admin.
	 * @return bool
	 */
	public static function verify( int $payment_id, int $user_id = 0 ): bool {
		$current = self::get( $payment_id );
		if ( ! $current || 'paid' !== $current->status ) {
			return false;
		}
		$ok = SPMB_DB_Payments::update(
			$payment_id,
			array(
				'status'      => 'verified',
				'verified_at' => current_time( 'mysql' ),
				'verified_by' => $user_id ? $user_id : get_current_user_id(),
			)
		);
		if ( $ok ) {
			do_action( 'spmb_payment_verified', $payment_id );
		}
		return $ok;
	}

	/**
	 * Batalkan invoice (void).
	 *
	 * @param int $payment_id ID payment.
	 * @return bool
	 */
	public static function void( int $payment_id ): bool {
		$current = self::get( $payment_id );
		if ( ! $current || 'verified' === $current->status ) {
			return false;
		}
		return SPMB_DB_Payments::update( $payment_id, array( 'status' => 'void' ) );
	}

	/**
	 * Ambi baris payment.
	 *
	 * @param int $payment_id ID.
	 * @return object|null
	 */
	private static function get( int $payment_id ): ?object {
		global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM %i WHERE id=%d', SPMB_DB_Payments::table(), $payment_id ) ); // phpcs:ignore WordPress.DB
		return $row ? $row : null;
	}
}