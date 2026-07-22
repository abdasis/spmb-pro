<?php
/**
 * Helper kueri database shared untuk SPMB Pro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPMB_DB_Query {

	/**
	 * Ambil satu baris berdasarkan kondisi WHERE sederhana.
	 *
	 * @param string $table Nama tabel (tanpa prefix).
	 * @param array  $where Pasangan kolom => nilai.
	 * @return object|null
	 */
	public static function get_row( string $table, array $where ): ?object {
		global $wpdb;
		$full  = $wpdb->prefix . $table;
		$sql   = "SELECT * FROM {$full}";
		$conds = array();
		$vals  = array();
		foreach ( $where as $col => $val ) {
			$conds[] = "`{$col}` = %s";
			$vals[]  = $val;
		}
		if ( $conds ) {
			$sql .= ' WHERE ' . implode( ' AND ', $conds );
		}
		$sql .= ' LIMIT 1';
		$row = $wpdb->get_row( $wpdb->prepare( $sql, $vals ) ); // phpcs:ignore WordPress.DB
		return $row ? $row : null;
	}

	/**
	 * Ambil beberapa baris dengan filter opsional.
	 *
	 * @param string $table Nama tabel.
	 * @param array  $args  Argumen: where, order_by, order, limit, offset.
	 * @return object[]
	 */
	public static function get_rows( string $table, array $args = array() ): array {
		global $wpdb;
		$full = $wpdb->prefix . $table;
		$sql  = "SELECT * FROM {$full}";

		$vals  = array();
		$conds = array();
		foreach ( $args['where'] ?? array() as $col => $val ) {
			$conds[] = "`{$col}` = %s";
			$vals[]  = $val;
		}
		if ( $conds ) {
			$sql .= ' WHERE ' . implode( ' AND ', $conds );
		}
		if ( ! empty( $args['order_by'] ) ) {
			$dir   = ( ( $args['order'] ?? 'ASC' ) === 'DESC' ) ? 'DESC' : 'ASC';
			$sql  .= " ORDER BY `{$args['order_by']}` {$dir}";
		}
		if ( ! empty( $args['limit'] ) ) {
			$sql  .= ' LIMIT %d';
			$vals[] = (int) $args['limit'];
		}
		if ( ! empty( $args['offset'] ) ) {
			$sql  .= ' OFFSET %d';
			$vals[] = (int) $args['offset'];
		}
		return $wpdb->get_results( $wpdb->prepare( $sql, $vals ) ); // phpcs:ignore WordPress.DB
	}

	/**
	 * Hitung jumlah baris dengan filter.
	 *
	 * @param string $table Nama tabel.
	 * @param array  $where Pasangan kolom => nilai.
	 * @return int
	 */
	public static function count( string $table, array $where = array() ): int {
		global $wpdb;
		$full  = $wpdb->prefix . $table;
		$sql   = "SELECT COUNT(*) FROM {$full}";
		$conds = array();
		$vals  = array();
		foreach ( $where as $col => $val ) {
			$conds[] = "`{$col}` = %s";
			$vals[]  = $val;
		}
		if ( $conds ) {
			$sql .= ' WHERE ' . implode( ' AND ', $conds );
		}
		return (int) $wpdb->get_var( $wpdb->prepare( $sql, $vals ) ); // phpcs:ignore WordPress.DB
	}
}