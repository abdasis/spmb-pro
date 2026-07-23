<?php
/**
 * View: dashboard statistik.
 *
 * @var array $stats Statistik ringkas.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$base_url   = admin_url( 'admin.php' );
$applicants = add_query_arg( 'page', 'spmb-pro-applicants', $base_url );
$payments   = add_query_arg( 'page', 'spmb-pro-payments', $base_url );
$selection  = add_query_arg( 'page', 'spmb-pro-selection', $base_url );
$settings   = add_query_arg( 'page', 'spmb-pro-settings', $base_url );

$total = $stats['total'];

// Persen aman terhadap pembagi nol.
$percent = static function ( int $num, int $denom ): string {
	return $denom > 0
		? number_format_i18n( round( ( $num / $denom ) * 100, 1 ) ) . '%'
		: '0%';
};

// Status pendaftaran → kelas badge.
$status_badge = static function ( string $status ): string {
	$map = array(
		'draft'     => '--pending',
		'submitted' => '--info',
		'verified'  => '--success',
		'rejected'  => '--error',
	);
	return $map[ $status ] ?? '--pending';
};

// Status pendaftaran → label terjemahan (sumber: SPMB_Defaults).
$status_label = static function ( string $status ): string {
	return SPMB_Defaults::status_label( $status );
};

// Format tanggal WP sesuai zona waktu situs.
$fmt_date = static function ( string $mysql ): string {
	return $mysql ? wp_date( get_option( 'date_format' ), strtotime( $mysql ) ) : '&mdash;';
};

// Render empty state: ikon + judul + deskripsi + CTA opsional.
$empty_state = static function ( string $icon, string $title, string $desc, string $cta = '' ): void {
	echo '<div class="spmb-empty">';
	echo '<span class="dashicons spmb-empty-icon ' . esc_attr( $icon ) . '" aria-hidden="true"></span>';
	echo '<span class="spmb-empty-title">' . esc_html( $title ) . '</span>';
	echo '<span class="spmb-empty-desc">' . esc_html( $desc ) . '</span>';
	if ( '' !== $cta ) {
		echo '<span class="spmb-empty-action">' . $cta . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';
};

// Render stat card: ikon monochrome + label + angka + meta.
$stat_card = static function ( string $icon, string $label, string $num, string $meta ): void {
	echo '<div class="spmb-stat-card">';
	echo '<span class="spmb-stat-head"><span class="dashicons ' . esc_attr( $icon ) . ' spmb-stat-icon" aria-hidden="true"></span><span class="spmb-stat-label">' . esc_html( $label ) . '</span></span>';
	echo '<span class="spmb-stat-num">' . esc_html( $num ) . '</span>';
	echo '<span class="spmb-stat-meta">' . esc_html( $meta ) . '</span>';
	echo '</div>';
};
?>
<div class="wrap spmb-wrap">

	<nav class="spmb-breadcrumb" aria-label="breadcrumb">
		<span class="spmb-breadcrumb-current"><?php esc_html_e( 'Dashboard SPMB', 'spmb-pro' ); ?></span>
	</nav>

	<header class="spmb-page-header">
		<h1 class="spmb-page-title"><?php esc_html_e( 'Dashboard SPMB Pro', 'spmb-pro' ); ?></h1>
		<p class="spmb-page-subtitle"><?php esc_html_e( 'Ringkasan pendaftaran, verifikasi, seleksi, dan pembayaran.', 'spmb-pro' ); ?></p>
	</header>

	<div class="spmb-stats">
		<?php
		$stat_card( 'dashicons-groups', __( 'Total Pendaftar', 'spmb-pro' ), number_format_i18n( $total ), __( 'Seluruh jenjang & jalur', 'spmb-pro' ) );
		$stat_card( 'dashicons-yes', __( 'Terverifikasi', 'spmb-pro' ), number_format_i18n( $stats['verified'] ), $percent( $stats['verified'], $total ) . ' ' . __( 'dari total', 'spmb-pro' ) );
		$stat_card( 'dashicons-money-alt', __( 'Pembayaran Terverifikasi', 'spmb-pro' ), number_format_i18n( $stats['payments']['verified'] ), $percent( $stats['payments']['verified'], $total ) . ' ' . __( 'dari total', 'spmb-pro' ) );
		$stat_card( 'dashicons-awards', __( 'Diterima', 'spmb-pro' ), number_format_i18n( $stats['admitted'] ), $percent( $stats['admitted'], $total ) . ' ' . __( 'dari total', 'spmb-pro' ) );
		$stat_card( 'dashicons-clock', __( 'Cadangan', 'spmb-pro' ), number_format_i18n( $stats['cadangan'] ), $percent( $stats['cadangan'], $total ) . ' ' . __( 'dari total', 'spmb-pro' ) );
		?>
	</div>

	<div class="spmb-action-bar">
		<a href="<?php echo esc_url( $applicants ); ?>" class="spmb-btn spmb-btn-primary">
			<?php esc_html_e( 'Lihat Pendaftar', 'spmb-pro' ); ?>
		</a>
		<a href="<?php echo esc_url( $payments ); ?>" class="spmb-btn spmb-btn-ghost">
			<?php esc_html_e( 'Verifikasi Pembayaran', 'spmb-pro' ); ?>
		</a>
		<a href="<?php echo esc_url( $selection ); ?>" class="spmb-btn spmb-btn-ghost">
			<?php esc_html_e( 'Jalur Seleksi', 'spmb-pro' ); ?>
		</a>
		<a href="<?php echo esc_url( $settings ); ?>" class="spmb-btn spmb-btn-ghost">
			<?php esc_html_e( 'Pengaturan', 'spmb-pro' ); ?>
		</a>
	</div>

	<section class="spmb-section">
		<h2 class="spmb-section-title"><?php esc_html_e( 'Breakdown per Jalur', 'spmb-pro' ); ?></h2>

		<?php if ( empty( $stats['by_jalur'] ) ) : ?>
			<?php
			$empty_state(
				'dashicons-list-view',
				__( 'Belum ada data pendaftar', 'spmb-pro' ),
				__( 'Data breakdown per jalur akan muncul otomatis setelah ada pendaftar masuk.', 'spmb-pro' ),
			);
			?>
		<?php else : ?>
		<table class="spmb-table">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Jalur', 'spmb-pro' ); ?></th>
					<th scope="col" class="num"><?php esc_html_e( 'Pendaftar', 'spmb-pro' ); ?></th>
					<th scope="col" class="num"><?php esc_html_e( 'Terverifikasi', 'spmb-pro' ); ?></th>
					<th scope="col" class="num"><?php esc_html_e( 'Diterima', 'spmb-pro' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $stats['by_jalur'] as $row ) : ?>
					<tr>
						<td><?php echo esc_html( ucfirst( $row['jalur'] ) ); ?></td>
						<td class="num"><?php echo esc_html( number_format_i18n( $row['pendaftar'] ) ); ?></td>
						<td class="num"><?php echo esc_html( number_format_i18n( $row['verified'] ) ); ?></td>
						<td class="num"><?php echo esc_html( number_format_i18n( $row['admitted'] ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
	</section>

	<section class="spmb-section">
		<h2 class="spmb-section-title"><?php esc_html_e( 'Breakdown per Jenjang', 'spmb-pro' ); ?></h2>

		<?php if ( empty( $stats['by_jenjang'] ) ) : ?>
			<?php
			$empty_state(
				'dashicons-list-view',
				__( 'Belum ada data pendaftar', 'spmb-pro' ),
				__( 'Data breakdown per jenjang akan muncul otomatis setelah ada pendaftar masuk.', 'spmb-pro' ),
			);
			?>
		<?php else : ?>
		<table class="spmb-table">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Jenjang', 'spmb-pro' ); ?></th>
					<th scope="col" class="num"><?php esc_html_e( 'Total', 'spmb-pro' ); ?></th>
					<th scope="col" class="num"><?php esc_html_e( 'Verified', 'spmb-pro' ); ?></th>
					<th scope="col" class="num"><?php esc_html_e( 'Paid', 'spmb-pro' ); ?></th>
					<th scope="col" class="num"><?php esc_html_e( 'Admitted', 'spmb-pro' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $stats['by_jenjang'] as $row ) : ?>
					<tr>
						<td><?php echo esc_html( $row['jenjang'] ); ?></td>
						<td class="num"><?php echo esc_html( number_format_i18n( $row['total'] ) ); ?></td>
						<td class="num"><?php echo esc_html( number_format_i18n( $row['verified'] ) ); ?></td>
						<td class="num"><?php echo esc_html( number_format_i18n( $row['paid'] ) ); ?></td>
						<td class="num"><?php echo esc_html( number_format_i18n( $row['admitted'] ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
	</section>

	<section class="spmb-section">
		<h2 class="spmb-section-title"><?php esc_html_e( 'Ringkasan Pembayaran', 'spmb-pro' ); ?></h2>

		<table class="spmb-table">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Status', 'spmb-pro' ); ?></th>
					<th scope="col" class="num"><?php esc_html_e( 'Jumlah', 'spmb-pro' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php esc_html_e( 'Belum Bayar', 'spmb-pro' ); ?></td>
					<td class="num"><?php echo esc_html( number_format_i18n( $stats['payments']['unpaid'] ) ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Sudah Bayar (belum diverifikasi)', 'spmb-pro' ); ?></td>
					<td class="num"><?php echo esc_html( number_format_i18n( $stats['payments']['paid'] ) ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Void', 'spmb-pro' ); ?></td>
					<td class="num"><?php echo esc_html( number_format_i18n( $stats['payments']['void'] ) ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Pendapatan Terverifikasi', 'spmb-pro' ); ?></td>
					<td class="num">Rp <?php echo esc_html( number_format_i18n( (float) $stats['payments']['revenue'] ) ); ?></td>
				</tr>
			</tbody>
		</table>
	</section>

	<section class="spmb-section">
		<h2 class="spmb-section-title">
			<?php esc_html_e( 'Pendaftar Terbaru', 'spmb-pro' ); ?>
			<span class="spmb-section-meta"><?php esc_html_e( '8 baris terakhir', 'spmb-pro' ); ?></span>
		</h2>

		<?php if ( empty( $stats['recent'] ) ) : ?>
			<?php
			$empty_state(
				'dashicons-groups',
				__( 'Belum ada pendaftar', 'spmb-pro' ),
				__( 'Pendaftar terbaru akan tampil di sini setelah formulir PPDB disubmit.', 'spmb-pro' ),
				'<a href="' . esc_url( $applicants ) . '" class="spmb-btn spmb-btn-ghost">' . esc_html__( 'Buka halaman pendaftar', 'spmb-pro' ) . '</a>',
			);
			?>
		<?php else : ?>
		<table class="spmb-table">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'No. Registrasi', 'spmb-pro' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Nama', 'spmb-pro' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Jalur', 'spmb-pro' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Jenjang', 'spmb-pro' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Status', 'spmb-pro' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Tanggal', 'spmb-pro' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $stats['recent'] as $row ) : ?>
					<tr>
						<td class="mono">
							<a href="<?php echo esc_url( $row['detail_url'] ); ?>"><?php echo esc_html( $row['reg_number'] ); ?></a>
						</td>
						<td><?php echo esc_html( $row['full_name'] ); ?></td>
						<td><?php echo esc_html( ucfirst( $row['jalur'] ) ); ?></td>
						<td><?php echo esc_html( $row['jenjang'] ); ?></td>
						<td>
							<span class="spmb-status-badge <?php echo esc_attr( $status_badge( $row['status'] ) ); ?>">
								<?php echo esc_html( $status_label( $row['status'] ) ); ?>
							</span>
						</td>
						<td><?php echo $fmt_date( $row['created_at'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
	</section>

</div>