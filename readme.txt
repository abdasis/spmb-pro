=== SPMB Pro ===
Contributors: wpsekolah
Tags: ppdb, spmb, penerimaan murid, sekolah, pendaftaran online
Requires at least: 6.7
Tested up to: 7.0
Requires PHP: 8.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Sistem Penerimaan Murid Baru untuk sekolah WordPress: pendaftaran online, seleksi PPDB 4 jalur, pembayaran manual, dan pengumuman.

== Description ==

SPMB Pro mengelola seluruh alur penerimaan murid baru sekolah:

* Form pendaftaran online dengan upload dokumen (akta, rapor, foto).
* Seleksi PPDB 4 jalur: zonasi, afirmasi, prestasi, dan perpindahan tugas.
* Kuota per jalur dengan ranking otomatis dan daftar cadangan.
* Pembayaran biaya pendaftaran manual dengan verifikasi admin.
* Pelacakan status dan pengumuman hasil pendaftaran.
* Ekspor kartu pendaftar PDF, laporan CSV, dan laporan seleksi PDF.
* Konfigurasi jenjang (SD/SMP/SMA), program, dan dokumen.

= Shortcode =

* [spmb_form] — form pendaftaran.
* [spmb_cek_status] — cek status pendaftaran.
* [spmb_pengumuman] — pengumuman hasil seleksi.

== Installation ==

1. Unggah folder `spmb-pro` ke `wp-content/plugins/`.
2. Aktifkan plugin melalui menu Plugins.
3. Buka menu SPMB Pro > Pengaturan untuk konfigurasi.
4. Buat halaman dengan shortcode di atas.

== Changelog ==

= 1.0.0 =
* Form pendaftaran online multi-langkah dengan upload dokumen.
* Seleksi PPDB 4 jalur (zonasi, afirmasi, prestasi, perpindahan tugas) dengan kuota dan ranking otomatis.
* Dashboard admin: KPI pendaftaran, funnel status, breakdown per jalur dan jenjang.
* Verifikasi pendaftar dan dokumen, bulk aksi verifikasi/tolak.
* Pembayaran biaya pendaftaran manual dengan verifikasi admin.
* Pengumuman hasil seleksi dan pelacakan status publik via shortcode.
* Ekspor kartu pendaftar PDF, laporan CSV, dan laporan seleksi PDF.
* REST API lookup jarak, data referensi, dan endpoint admin.
* Integrasi tema block FSE via override CSS progressive enhancement.
* Konfigurasi jenjang (SD/SMP/SMA), program, kuota, dan dokumen.

== Bundled Libraries ==

* FPDF 1.9 oleh Olivier Plathey — pembuatan PDF. Sumber: http://www.fpdf.org/ (bebas digunakan, kompatibel GPLv2).