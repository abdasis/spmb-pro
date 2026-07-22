<?php
/**
 * List table pendaftar SPMB Pro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class SPMB_Admin_List_Table extends WP_List_Table {

	/**
	 * Jumlah per halaman.
	 *
	 * @var int
	 */
	private int $per_page = 25;

	/**
	 * Konstruktor.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'Pendaftar', 'spmb-pro' ),
				'plural'   => __( 'Pendaftar', 'spmb-pro' ),
				'ajax'     => false,
			)
		);
	}

	/**
	 * Ambil kolom tabel.
	 *
	 * @return array
	 */
	public function get_columns(): array {
		return array(
			'cb'                 => '<input type="checkbox" />',
			'registration_number' => __( 'No. Pendaftaran', 'spmb-pro' ),
			'full_name'          => __( 'Nama', 'spmb-pro' ),
			'jenjang'            => __( 'Jenjang', 'spmb-pro' ),
			'jalur'              => __( 'Jalur', 'spmb-pro' ),
			'status'             => __( 'Status', 'spmb-pro' ),
			'payment'            => __( 'Pembayaran', 'spmb-pro' ),
			'created_at'         => __( 'Tanggal', 'spmb-pro' ),
		);
	}

	/**
	 * Kolom sortable.
	 *
	 * @return array
	 */
	protected function get_sortable_columns(): array {
		return array(
			'full_name' => array( 'full_name', false ),
			'created_at' => array( 'created_at', true ),
		);
	}

	/**
	 * Aksi bulk.
	 *
	 * @return array
	 */
	protected function get_bulk_actions(): array {
		return array(
			'verify'   => __( 'Verifikasi', 'spmb-pro' ),
			'reject'    => __( 'Tolak', 'spmb-pro' ),
		);
	}

	/**
	 * Render kolom checkbox.
	 *
	 * @param object $item Baris.
	 */
	public function column_cb( $item ): string {
		return sprintf( '<input type="checkbox" name="applicant[]" value="%d" />', (int) $item->id );
	}

	/**
	 * Render kolom default.
	 *
	 * @param object $item      Baris.
	 * @param string $column_name Nama kolom.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'created_at':
				return esc_html( $item->created_at );
			case 'payment':
				return esc_html( $this->payment_status( (int) $item->id ) );
			default:
				return isset( $item->$column_name ) ? esc_html( $item->$column_name ) : '';
		}
	}

	/**
	 * Kolom nama dengan link ke detail.
	 *
	 * @param object $item Baris.
	 */
	public function column_full_name( $item ): string {
		$detail = admin_url( 'admin.php?page=spmb-pro-applicant&id=' . $item->id );
		$actions = array(
			'view' => '<a href="' . esc_url( $detail ) . '">' . esc_html__( 'Lihat', 'spmb-pro' ) . '</a>',
		);
		return sprintf( '<a href="%s"><strong>%s</strong></a>%s', esc_url( $detail ), esc_html( $item->full_name ), $this->row_actions( $actions ) );
	}

	/**
	 * Status pembayaran pendaftar.
	 *
	 * @param int $applicant_id ID pendaftar.
	 */
	private function payment_status( int $applicant_id ): string {
		$p = SPMB_DB_Payments::for_applicant( $applicant_id );
		return $p ? $p->status : '—';
	}

	/**
	 * Siapkan item tabel.
	 */
	public function prepare_items(): void {
		global $wpdb;
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		$where  = '1=1';
		$values = array();

		$filters = array( 'jenjang', 'jalur', 'status' );
		foreach ( $filters as $f ) {
			if ( ! empty( $_GET[ $f ] ) ) { // phpcs:ignore WordPress.Security
				$where  .= " AND {$f} = %s";
				$values[] = sanitize_text_field( wp_unslash( $_GET[ $f ] ) ); // phpcs:ignore WordPress.Security
			}
		}
		if ( ! empty( $_GET['s'] ) ) { // phpcs:ignore WordPress.Security
			$s = '%' . $wpdb->esc_like( sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) . '%';
			$where  .= ' AND (full_name LIKE %s OR registration_number LIKE %s)';
			$values[] = $s;
			$values[] = $s;
		}

		$order_by = ! empty( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : 'created_at'; // phpcs:ignore
		$order    = ( isset( $_GET['order'] ) && 'asc' === strtolower( wp_unslash( $_GET['order'] ) ) ) ? 'ASC' : 'DESC'; // phpcs:ignore
		$allowed  = array( 'full_name', 'created_at', 'registration_number' );
		$order_by = in_array( $order_by, $allowed, true ) ? $order_by : 'created_at';

		$total = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM %i WHERE {$where}", array_merge( array( SPMB_DB_Applicants::table() ), $values ) ) ); // phpcs:ignore WordPress.DB
		$paged = $this->get_pagenum();
		$offset = ( $paged - 1 ) * $this->per_page;

		$sql = $wpdb->prepare(
			"SELECT * FROM %i WHERE {$where} ORDER BY {$order_by} {$order} LIMIT %d OFFSET %d",
			array_merge( array( SPMB_DB_Applicants::table() ), $values, array( $this->per_page, $offset ) )
		); // phpcs:ignore WordPress.DB
		$this->items = $wpdb->get_results( $sql );

		$this->set_pagination_args(
			array(
				'total_items' => $total,
				'per_page'    => $this->per_page,
				'total_pages' => (int) ceil( $total / $this->per_page ),
			)
		);
	}
}