# SPMB Pro

> **Sistem Penerimaan Murid Baru** untuk situs sekolah berbasis WordPress — pendaftaran online, seleksi PPDB empat jalur (zonasi, afirmasi, prestasi, perpindahan tugas), pembayaran manual, dan pengumuman hasil.

[![WordPress](https://img.shields.io/badge/WordPress-7.0%2B-blue)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-purple)](https://www.php.net)
[![License](https://img.shields.io/badge/License-GPLv2-success)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/version-1.0.0-orange)](#changelog)

---

## Daftar Isi

- [Tentang](#tentang)
- [Fitur](#fitur)
- [Jalur Seleksi](#jalur-seleksi)
- [Persyaratan](#persyaratan)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Shortcode](#shortcode)
- [Struktur Database](#struktur-database)
- [Struktur Plugin](#struktur-plugin)
- [Pengembangan](#pengembangan)
- [Kontribusi](#kontribusi)
- [Keamanan](#keamanan)
- [Lisensi](#lisensi)
- [Changelog](#changelog)

---

## Tentang

**SPMB Pro** adalah plugin WordPress yang mengotomatiskan seluruh alur Penerimaan Murid Baru (PPDB / SPMB) untuk sekolah. Plugin ini dirancang khusus untuk konteks Indonesia, mendukung empat jalur seleksi resmi dan proses verifikasi berbasis admin.

Plugin ini cocok untuk SD, SMP, dan SMA yang ingin mengelola pendaftaran secara digital tanpa membangun sistem terpisah dari nol — cukup pasang di situs WordPress sekolah.

## Fitur

- **Form pendaftaran online** dengan unggah dokumen (akta kelahiran, rapor, pas foto).
- **Seleksi PPDB empat jalur**: zonasi, afirmasi, prestasi, perpindahan tugas — dengan kuota per jalur per jenjang.
- **Ranking otomatis** berdasarkan bobot nilai rapor dan prestasi, lengkap dengan daftar cadangan (waitlist).
- **Pembayaran biaya pendaftaran manual** dengan verifikasi admin sebelum status diverifikasi.
- **Pelacakan status pendaftar** (draft → submitted → verified → selected/rejected).
- **Pengumuman hasil seleksi** yang dapat dipublikasikan dan dilihat publik.
- **Ekspor dokumen**: kartu pendaftar PDF, laporan CSV, dan laporan seleksi PDF.
- **Konfigurasi fleksibel**: jenjang (SD/SMP/SMA), program studi, kategori afirmasi, bobot prestasi, dan jenis dokumen yang diterima.
- **Multibahasa**: siap untuk terjemahan melalui file `.pot` (text domain `spmb-pro`).
- **Penjadwalan housekeeping harian** untuk membersihkan data sementara secara otomatis.

## Jalur Seleksi

| Jalur          | Deskripsi                                                                 |
|----------------|---------------------------------------------------------------------------|
| **Zonasi**     | Berdasarkan jarak domisili ke sekolah (km).                               |
| **Afirmasi**   | Kuota khusus untuk kelompok ekonomi, difabel, atau kategori lainnya.      |
| **Prestasi**   | Ranking berdasarkan bobot nilai rapor (70%) dan poin prestasi (30%).     |
| **Perpindahan**| Kuota untuk pindahan tugas orang tua/wali.                                |

Bobot prestasi (rapor vs achievement) dan kuota tiap jalur dapat dikonfigurasi per jenjang melalui halaman pengaturan.

## Persyaratan

- **WordPress** 7.0 atau lebih baru
- **PHP** 8.2 atau lebih baru
- **MySQL** 5.7 / MariaDB 10.3 atau lebih baru
- Akses tulis ke direktori `wp-content/uploads/spmb-pro`

## Instalasi

1. Unduh rilis terbaru dan ekstrak folder `spmb-pro`.
2. Unggah folder `spmb-pro` ke direktori `wp-content/plugins/` situs WordPress Anda.
3. Masuk ke dashboard WordPress → menu **Plugins**.
4. Aktifkan plugin **SPMB Pro**.
5. Saat aktivasi, plugin akan otomatis membuat tabel database, role, dan jadwal housekeeping harian.
6. Buka menu **SPMB Pro → Pengaturan** untuk konfigurasi awal.
7. Buat halaman WordPress dan tempel shortcode yang relevan (lihat [Shortcode](#shortcode)).

## Konfigurasi

Setelah aktivasi, buka **SPMB Pro → Pengaturan** untuk mengonfigurasi:

- **Identitas sekolah**: nama, alamat, koordinat latitude/longitude (untuk perhitungan jarak zonasi).
- **Jenjang**: aktifkan SD/SMP/SMA sesuai kebutuhan.
- **Jalur seleksi**: aktifkan/nonaktifkan jalur yang berlaku.
- **Kuota**: tentukan jumlah kuota per jalur per jenjang.
- **Program studi**: daftar program pilihan per jenjang.
- **Kategori afirmasi**: contoh `ekonomi`, `diffabel`, `lainnya`.
- **Bobot prestasi**: proporsi nilai rapor vs prestasi (default 70/30).
- **Biaya pendaftaran**: nominal biaya manual.
- **Periode pendaftaran**: tanggal buka dan tutup.
- **Unggahan dokumen**: tipe MIME yang diizinkan (`application/pdf`, `image/jpeg`, `image/png`) dan ukuran maksimum (default 2 MB).

## Shortcode

| Shortcode             | Fungsi                                      |
|-----------------------|---------------------------------------------|
| `[spmb_form]`         | Menampilkan form pendaftaran murid baru.     |
| `[spmb_cek_status]`   | Pelacakan status pendaftaran berdasarkan nomor registrasi. |
| `[spmb_pengumuman]`   | Menampilkan pengumuman hasil seleksi.       |

Tempel shortcode pada halaman WordPress mana pun (blok Shortcode atau editor klasik).

## Struktur Database

Plugin membuat empat tabel kustom (dengan prefix tabel WordPress):

### `{prefix}spmb_applicants`
Data utama pendaftar: identitas, jalur, jenjang, nilai rapor, poin prestasi, jarak, kategori afirmasi, dan status seleksi.

### `{prefix}spmb_documents`
Dokumen yang diunggah pendaftar: tipe dokumen, path file, MIME, ukuran, dan status verifikasi.

### `{prefix}spmb_payments`
Catatan pembayaran biaya pendaftaran: nomor invoice, nominal, metode, status (`unpaid`/`paid`/`verified`), dan verifier.

### `{prefix}spmb_selection_runs`
Riwayat eksekusi seleksi: waktu, operator, jenjang, snapshot konfigurasi, jumlah diterima, dan jumlah cadangan.

> **Uninstal**: jalankan `uninstall.php` untuk menghapus tabel dan opsi. Penghapusan file unggahan dapat dikonfigurasi (`delete_files_on_uninstall`).

## Struktur Plugin

```
spmb-pro/
├── spmb-pro.php                     # File utama plugin + bootstrap
├── uninstall.php                    # Pembersihan saat uninstall
├── readme.txt                       # Readme format WordPress.org
├── README.md                        # Dokumentasi ini
├── index.php                        # Penjaga akses langsung
├── includes/
│   ├── class-spmb-activator.php     # Hook aktivasi (tabel, role, cron)
│   ├── class-spmb-deactivator.php    # Hook deaktivasi (clear cron)
│   ├── class-spmb-installer.php     # Skema database via dbDelta
│   ├── class-spmb-plugin.php        # Kontainer utama plugin
│   ├── class-spmb-roles.php         # Capability & role
│   ├── class-spmb-defaults.php      # Nilai pengaturan default
│   └── autoload/
│       └── class-spmb-autoloader.php # PSR-4 style autoloader
└── lang/
    └── spmb-pro.pot                 # Template terjemahan
```

## Pengembangan

### Standar Kode

- PHP 8.2+ dengan strict typing.
- Penamaan class: `SPMB_*` (prefix) + `class-spmb-*.php`.
- Komentar kode dalam Bahasa Indonesia.
- Bebas emoji pada kode, komentar, dan commit message.

### Menjalankan Secara Lokal

1. Clone repositori ke `wp-content/plugins/spmb-pro`.
2. Aktifkan plugin di situs WordPress pengembangan.
3. Gunakan `wp plugin activate spmb-pro` (WP-CLI) atau aktifkan via dashboard.

### Membangun Terjemahan

```bash
wp i18n make-pot . lang/spmb-pro.pot --domain=spmb-pro
```

## Kontribusi

Kontribusi disambut. Alur:

1. Fork repositori.
2. Buat branch fitur: `git checkout -b fitur/nama-fitur`.
3. Commit dengan pesan konvensional (`feat:`, `fix:`, `chore:`, `style:`, `refactor:`).
4. Buka Pull Request ke branch `main`.

Pastikan kode lolis `php -l` (syntax check) sebelum mengirim PR.

## Keamanan

- Semua file kelas diawali `if ( ! defined( 'ABSPATH' ) ) exit;` untuk mencegah akses langsung.
- Direktori unggahan `uploads/spmb-pro` dilindungi `index.php` dan `.htaccess` (`Options -Indexes`).
- Capability `spmb_manage_applicants` hanya diberikan ke role Administrator.
- Verifikasi nonce, sanitasi input, dan escape output wajib pada setiap titik masuk data.

Melaporkan kerentanan: hubungi maintainer secara privat — jangan buka issue publik untuk celah keamanan.

## Lisensi

Plugin ini dilisensikan di bawah **GPLv2 atau versi lebih baru**.

```
SPMB Pro — Sistem Penerimaan Murid Baru untuk WordPress
Copyright (C) [tahun] WP Sekolah

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
```

Lihat [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) untuk detail lengkap.

## Changelog

### 1.0.0 — Rilis Awal

- Kerangka plugin, autoloader, dan hook aktivasi/deaktivasi.
- Skema database: `spmb_applicants`, `spmb_documents`, `spmb_payments`, `spmb_selection_runs`.
- Role dan capability `spmb_manage_applicants`.
- Pengaturan default: jenjang, jalur, kuota, program, kategori afirmasi, bobot prestasi.
- Penjadwalan housekeeping harian.
- Proteksi direktori unggahan.
- File bahasa `.pot`.

---

Dibuat oleh [WP Sekolah](https://wpsekolah.id).