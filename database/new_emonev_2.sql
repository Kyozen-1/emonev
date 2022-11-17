-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 17 Nov 2022 pada 09.04
-- Versi server: 5.7.33
-- Versi PHP: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `new_emonev_2`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `akun_opds`
--

CREATE TABLE `akun_opds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `color_layout` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nav_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `placement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `behaviour` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `layout` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `radius` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `akun_opds`
--

INSERT INTO `akun_opds` (`id`, `name`, `email`, `email_verified_at`, `password`, `opd_id`, `remember_token`, `created_at`, `updated_at`, `color_layout`, `nav_color`, `placement`, `behaviour`, `layout`, `radius`) VALUES
(1, 'Dinas Kebudayaan, Pariwisata, Pemuda, Dan Olahraga Kota Madiun', 'disdik_madiun@email.com', NULL, '$2y$10$XPCWcGAkfJGxwxVvxtpo4.A4tG5LjFPqmq7FXkbMJwgVszH5Dp3Qy', 1, NULL, '2022-10-27 06:42:48', '2022-10-27 06:42:48', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Biro Administrasi Pimpinan Madiun', 'biroadministrasipimpinan_madiun@email.com', NULL, '$2y$10$NLpaxaY8ArHiNHdXwaTPlOFmq0nJ3sDtXPH0irAEpwevJd5lXutA6', 2, NULL, '2022-10-28 22:39:28', '2022-11-05 13:54:06', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_opds`
--

CREATE TABLE `jenis_opds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `jenis_opds`
--

INSERT INTO `jenis_opds` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(2, 'SEKRETARIAT DAERAH', '2022-09-27 08:38:14', '2022-09-27 08:38:14'),
(3, 'DINAS DAERAH', '2022-09-27 08:38:19', '2022-09-27 08:38:19'),
(4, 'SEKRETARIAT DPRD', '2022-09-27 08:38:44', '2022-09-27 08:38:44'),
(5, 'LEMBAGA TEKNIS DAERAH', '2022-09-27 08:38:56', '2022-09-27 08:38:56'),
(6, 'SATUAN POLISI PAMONG PRAJA', '2022-09-27 08:40:02', '2022-09-27 08:40:02'),
(7, 'LEMBAGA LAINNYA', '2022-09-27 08:40:10', '2022-09-27 08:40:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kabupatens`
--

CREATE TABLE `kabupatens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provinsi_id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kabupatens`
--

INSERT INTO `kabupatens` (`id`, `provinsi_id`, `nama`, `created_at`, `updated_at`) VALUES
(62, 5, 'Kabupaten Madiun', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kecamatans`
--

CREATE TABLE `kecamatans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kabupaten_id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kecamatans`
--

INSERT INTO `kecamatans` (`id`, `kabupaten_id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 62, 'Balerejo', '2022-07-16 21:17:01', '2022-07-16 21:17:01'),
(2, 62, 'Dagangan', '2022-07-16 21:17:06', '2022-07-16 21:17:06'),
(3, 62, 'Dolopo', '2022-07-16 21:17:14', '2022-07-16 21:17:14'),
(4, 62, 'Geger', '2022-07-16 21:17:19', '2022-07-16 21:17:19'),
(5, 62, 'Gemarang', '2022-07-16 21:17:23', '2022-07-16 21:17:23'),
(6, 62, 'Jiwan', '2022-07-16 21:17:26', '2022-07-16 21:17:26'),
(7, 62, 'Kare', '2022-07-16 21:17:31', '2022-07-16 21:17:31'),
(8, 62, 'Kebon Sari', '2022-07-16 21:17:36', '2022-07-16 21:17:36'),
(9, 62, 'Madiun', '2022-07-16 21:17:40', '2022-07-16 21:17:40'),
(10, 62, 'Mejayan', '2022-07-16 21:17:43', '2022-07-16 21:17:43'),
(11, 62, 'Pilangkenceng', '2022-07-16 21:17:49', '2022-07-16 21:17:49'),
(12, 62, 'Saradan', '2022-07-16 21:17:56', '2022-07-16 21:17:56'),
(13, 62, 'Sawahan', '2022-07-16 21:18:01', '2022-07-16 21:18:01'),
(14, 62, 'Wonoasri', '2022-07-16 21:18:07', '2022-07-16 21:18:07'),
(15, 62, 'Wungu', '2022-07-16 21:18:11', '2022-07-16 21:18:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kegiatans`
--

CREATE TABLE `kegiatans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_aturan` enum('Sebelum Perubahan','Sesudah Perubahan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kegiatans`
--

INSERT INTO `kegiatans` (`id`, `program_id`, `kode`, `deskripsi`, `tahun_perubahan`, `status_aturan`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, 1, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(2, 1, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(3, 1, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(4, 1, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(5, 1, '5', 'Peningkatan Sarana dan Prasarana Disiplin Pegawai', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(6, 1, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(7, 1, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(8, 1, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(9, 1, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(10, 2, '1', 'Pengelolaan Pendidikan Sekolah Dasar', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(11, 2, '2', 'Pengelolaan Pendidikan Sekolah Menengah Pertama', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(12, 2, '3', 'Pengelolaan Pendidikan Anak Usia Dini (PAUD)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(13, 2, '4', 'Pengelolaan Pendidikan Nonformal/Kesetaraan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(14, 348, '1', 'Penetapan Kurikulum Muatan Lokal Pendidikan Dasar', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(15, 348, '2', 'Penetapan Kurikulum Muatan Lokal Pendidikan Anak Usia Dini dan Pendidikan Nonformal', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(16, 349, '1', 'Pemerataan Kuantitas dan Kualitas Pendidik dan Tenaga Kependidikan bagi Satuan Pendidikan Dasar, PAUD, dan Pendidikan Nonformal/Kesetaraan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(17, 10, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(18, 10, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(19, 10, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(20, 10, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(21, 10, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(22, 10, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(23, 10, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(24, 10, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(25, 10, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(26, 10, '10', 'Peningkatan Pelayanan BLUD', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(27, 11, '1', 'Penyediaan Fasilitas Pelayanan Kesehatan untuk UKM dan UKP Kewenangan Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(28, 11, '2', 'Penyediaan Layanan Kesehatan untuk UKM dan UKP Rujukan Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(29, 11, '3', 'Penyelenggaraan Sistem Informasi Kesehatan secara Terintegrasi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(30, 11, '4', 'Penerbitan Izin Rumah Sakit Kelas C dan D serta Fasilitas Pelayanan Kesehatan Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(31, 26, '1', 'Pemberian Izin Praktik Tenaga Kesehatan di Wilayah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(32, 26, '2', 'Perencanaan Kebutuhan dan Pendayagunaan Sumberdaya Manusia Kesehatan untuk UKP dan UKM di Wilayah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(33, 26, '3', 'Pengembangan Mutu dan Peningkatan Kompetensi Teknis Sumber Daya Manusia Kesehatan Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(34, 350, '1', 'Pemberian Izin Apotek, Toko Obat, Toko Alat Kesehatan dan Optikal, Usaha Mikro Obat Tradisional (UMOT)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(35, 350, '2', 'Pemberian Sertifikat Produksi untuk Sarana Produksi Alat Kesehatan Kelas 1 tertentu dan Perbekalan Kesehatan Rumah Tangga Kelas 1 Tertentu Perusahaan Rumah Tangga', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(36, 350, '3', 'Penerbitan Sertifikat Produksi Pangan Industri Rumah Tangga dan Nomor P-IRT sebagai Izin Produksi, untuk Produk Makanan Minuman Tertentu yang dapat Diproduksi oleh Industri Rumah Tangga', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(37, 350, '4', 'Penerbitan Sertifikat Laik Higiene Sanitasi Tempat Pengelolaan Makanan (TPM) antara lain Jasa Boga, Rumah Makan/Restoran dan Depot Air Minum (DAM)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(38, 350, '5', 'Penerbitan Stiker Pembinaan pada Makanan Jajanan dan Sentra Makanan Jajanan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(39, 350, '6', 'Pemeriksaan dan Tindak Lanjut Hasil Pemeriksaan Post Market pada Produksi dan Produk Makanan Minuman Industri Rumah Tangga', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(40, 12, '1', 'Advokasi, Pemberdayaan, Kemitraan, Peningkatan Peran serta Masyarakat dan Lintas Sektor Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(41, 12, '2', 'Pelaksanaan Sehat dalam rangka Promotif Preventif Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(42, 12, '3', 'Pengembangan dan Pelaksanaan Upaya Kesehatan Bersumber Daya Masyarakat (UKBM) Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(43, 351, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(44, 351, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(45, 351, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(46, 351, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(47, 351, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(48, 351, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(49, 351, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(50, 351, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(51, 351, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(52, 351, '10', 'Peningkatan Pelayanan BLUD', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(53, 352, '1', 'Penyediaan Fasilitas Pelayanan Kesehatan untuk UKM dan UKP Kewenangan Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(54, 352, '2', 'Penyediaan Layanan Kesehatan untuk UKM dan UKP Rujukan Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(55, 352, '3', 'Penyelenggaraan Sistem Informasi Kesehatan secara Terintegrasi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(56, 352, '4', 'Penerbitan Izin Rumah Sakit Kelas C dan D serta Fasilitas Pelayanan Kesehatan Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(57, 353, '1', 'Pemberian Izin Praktik Tenaga Kesehatan di Wilayah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(58, 353, '2', 'Perencanaan Kebutuhan dan Pendayagunaan Sumberdaya Manusia Kesehatan untuk UKP dan UKM di Wilayah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(59, 353, '3', 'Pengembangan Mutu dan Peningkatan Kompetensi Teknis Sumber Daya Manusia Kesehatan Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(60, 332, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(61, 332, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(62, 332, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(63, 332, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(64, 332, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(65, 332, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(66, 332, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(67, 332, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(68, 332, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(69, 332, '10', 'Peningkatan Pelayanan BLUD', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(70, 354, '1', 'Penyediaan Fasilitas Pelayanan Kesehatan untuk UKM dan UKP Kewenangan Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(71, 354, '2', 'Penyediaan Layanan Kesehatan untuk UKM dan UKP Rujukan Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(72, 354, '3', 'Penyelenggaraan Sistem Informasi Kesehatan secara Terintegrasi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(73, 354, '4', 'Penerbitan Izin Rumah Sakit Kelas C dan D serta Fasilitas Pelayanan Kesehatan Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(74, 355, '1', 'Pemberian Izin Praktik Tenaga Kesehatan di Wilayah Kabupaten/Kot', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(75, 355, '2', 'Perencanaan Kebutuhan dan Pendayagunaan Sumberdaya Manusia Kesehatan untuk UKP dan UKM di Wilayah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(76, 355, '3', 'Pengembangan Mutu dan Peningkatan Kompetensi Teknis Sumber Daya Manusia Kesehatan Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(77, 43, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(78, 43, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(79, 43, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(80, 43, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(81, 43, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(82, 43, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(83, 43, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(84, 43, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(85, 43, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(86, 44, '1', 'Pengelolaan SDA dan Bangunan Pengaman Pantai pada Wilayah Sungai (WS) dalam 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(87, 44, '2', 'Pengembangan dan Pengelolaan Sistem Irigasi Primer dan Sekunder pada Daerah Irigasi yang Luasnya dibawah 1000 Ha dalam 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(88, 356, '1', 'Pengelolaan dan Pengembangan Sistem Penyediaan Air Minum (SPAM) di Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(89, 357, '1', 'Pengelolaan dan Pengembangan Sistem Air Limbah Domestik dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(90, 45, '1', 'Pengelolaan dan Pengembangan Sistem Drainase yang Terhubung Langsung dengan Sungai dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(91, 358, '1', 'Penyelenggaraan Infrastruktur pada Permukiman di Kawasan Strategis Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(92, 341, '1', 'Penyelenggaraan Bangunan Gedung di Wilayah Daerah Kabupaten/Kota, Pemberian Izin Mendirikan Bangunan (IMB) dan Sertifikat Laik Fungsi Bangunan Gedung', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(93, 359, '1', 'Penyelenggaraan Penataan Bangunan dan Lingkungannya di Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(94, 360, '1', 'Penyelenggaraan Jalan Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(95, 361, '1', 'Penyelenggaraan Pelatihan Tenaga Terampil Konstruksi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(96, 361, '2', 'Penyelenggaraan Sistem Informasi Jasa Konstruksi Cakupan Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(97, 361, '3', 'Penerbitan Izin Usaha Jasa Konstruksi Nasional (Non Kecil dan Kecil)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(98, 361, '4', 'Pengawasan Tertib Usaha, Tertib Penyelenggaraan dan Tertib Pemanfaatan Jasa Konstruksi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(99, 362, '1', 'Penetapan Rencana Tata Ruang Wilayah (RTRW) dan Rencana Rinci Tata Ruang (RRTR) Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(100, 362, '2', 'Koordinasi dan Sinkronisasi Perencanaan Tata Ruang Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(101, 362, '3', 'Koordinasi dan Sinkronisasi Pemanfaatan Ruang Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(102, 362, '4', 'Koordinasi dan Sinkronisasi Pengendalian Pemanfaatan Ruang Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(103, 58, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(104, 58, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(105, 58, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(106, 58, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(107, 58, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(108, 58, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(109, 58, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(110, 58, '9', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(111, 59, '1', 'Pendataan Penyediaan dan Rehabilitasi Rumah Korban Bencana atau Relokasi Program Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(112, 59, '2', 'Sosialisasi dan Persiapan Penyediaan dan Rehabilitasi Rumah Korban Bencana atau Relokasi Program Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(113, 59, '3', 'Pembangunan dan Rehabilitasi Rumah Korban Bencana atau Relokasi Program Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(114, 59, '4', 'Pendistribusian dan Serah Terima Rumah bagi Korban Bencana atau Relokasi Program Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(115, 59, '5', 'Pembinaan Pengelolaan Rumah Susun Umum dan/atau Rumah Khusus', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(116, 59, '6', 'Penerbitan Izin Pembangunan dan Pengembangan Perumahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(117, 59, '7', 'Penerbitan Sertifikat Kepemilikan Bangunan Gedung (SKGB)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(118, 363, '1', 'Penerbitan Izin Pembangunan dan Pengembangan Kawasan Permukiman', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(119, 363, '2', 'Penataan dan Peningkatan Kualitas Kawasan Permukiman Kumuh dengan Luas di Bawah 10 (sepuluh) Ha', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(120, 363, '3', 'Peningkatan Kualitas Kawasan Permukiman Kumuh dengan Luas di Bawah 10 (sepuluh) Ha', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(121, 364, '1', 'Pencegahan Perumahan dan Kawasan Permukiman Kumuh pada Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(122, 365, '1', 'Urusan Penyelenggaraan PSU Perumahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(123, 60, '1', 'Sertifikasi dan Registrasi bagi Orang atau Badan Hukum yang Melaksanakan Perancangan dan Perencanaan Rumah serta Perencanaan Prasarana, Sarana dan Utilitas Umum PSU Tingkat Kemampuan Kecil', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(124, 64, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(125, 64, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(126, 64, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(127, 64, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(128, 64, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(129, 64, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(130, 64, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(131, 64, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(132, 64, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(133, 65, '1', 'Penanganan Gangguan Ketenteraman dan Ketertiban Umum dalam 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(134, 65, '2', 'Penegakan Peraturan Daerah Kabupaten/Kota dan Peraturan Bupati/Wali Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(135, 65, '3', 'Pembinaan Penyidik Pegawai Negeri Sipil (PPNS) Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(136, 366, '1', 'Pencegahan, Pengendalian, Pemadaman, Penyelamatan, dan Penanganan Bahan Berbahaya dan Beracun Kebakaran dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(137, 366, '2', 'Inspeksi Peralatan Proteksi Kebakaran', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(138, 366, '3', 'Investigasi Kejadian Kebakaran', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(139, 366, '4', 'Pemberdayaan Masyarakat dalam Pencegahan Kebakaran', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(140, 366, '5', 'Penyelenggaraan Operasi Pencarian dan Pertolongan terhadap Kondisi Membahayakan Manusia', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(141, 367, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(142, 367, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(143, 367, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(144, 367, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(145, 367, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(146, 367, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(147, 367, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(148, 367, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(149, 367, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(150, 368, '1', 'Pelayanan Informasi Rawan Bencana Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(151, 368, '2', 'Pelayanan Pencegahan dan Kesiapsiagaan terhadap Bencana', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(152, 368, '3', 'Pelayanan Penyelamatan dan Evakuasi Korban Bencana', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(153, 368, '4', 'Penataan Sistem Dasar Penanggulangan Bencana', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(154, 82, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(155, 82, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(156, 82, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(157, 82, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(158, 82, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(159, 82, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(160, 82, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(161, 82, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(162, 82, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(163, 83, '1', 'Pemberdayaan Sosial Komunitas Adat Terpencil (KAT)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(164, 83, '2', 'Pengumpulan Sumbangan dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(165, 83, '3', 'Pengembangan Potensi Sumber Kesejahteraan Sosial Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(166, 369, '1', 'Rehabilitasi Sosial Dasar Penyandang Disabilitas Terlantar, Anak Terlantar, Lanjut Usia Terlantar, serta Gelandangan Pengemis di Luar Panti Sosial', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(167, 369, '2', 'Rehabilitasi Sosial Penyandang Masalah Kesejahteraan Sosial (PMKS) Lainnya Bukan Korban HIV/AIDS dan NAPZA di Luar Panti Sosial', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(168, 370, '1', 'Pemeliharaan Anak-Anak Terlantar', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(169, 370, '2', 'Pengelolaan Data Fakir Miskin Cakupan Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(170, 371, '1', 'Perlindungan Sosial Korban Bencana Alam dan Sosial Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(171, 371, '2', 'Penyelenggaraan Pemberdayaan Masyarakat terhadap Kesiapsiagaan Bencana Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(172, 88, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(173, 88, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(174, 88, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(175, 88, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(176, 88, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(177, 88, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(178, 88, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(179, 88, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(180, 88, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(181, 372, '1', 'Pelaksanaan Pelatihan berdasarkan Unit Kompetensi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(182, 372, '2', 'Pembinaan Lembaga Pelatihan Kerja Swasta', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(183, 372, '3', 'Perizinan dan Pendaftaran Lembaga Pelatihan Kerja', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(184, 372, '4', 'Konsultansi Produktivitas pada Perusahaan Kecil', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(185, 372, '5', 'Pengukuran Produktivitas Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(186, 373, '1', 'Pelayanan Antarkerja di Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(187, 373, '2', 'Penerbitan Izin Lembaga Penempatan Tenaga Kerja Swasta (LPTKS) dalam 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(188, 373, '3', 'Pengelolaan Informasi Pasar Kerja', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(189, 373, '4', 'Pelindungan PMI (Pra dan Purna Penempatan) di Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(190, 373, '5', 'Penerbitan Perpanjangan IMTA yang Lokasi Kerja dalam 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(191, 374, '1', 'Pengesahan Peraturan Perusahaan dan Pendaftaran Perjanjian Kerja Bersama untuk Perusahaan yang hanya Beroperasi dalam 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(192, 374, '2', 'Pencegahan dan Penyelesaian Perselisihan Hubungan Industrial, Mogok Kerja dan Penutupan Perusahaan di Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(193, 98, '1', 'Pelembagaan Pengarusutamaan Gender (PUG) pada Lembaga Pemerintah Kewenangan Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(194, 98, '2', 'Pemberdayaan Perempuan Bidang Politik, Hukum, Sosial, dan Ekonomi pada Organisasi Kemasyarakatan Kewenangan Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(195, 98, '3', 'Penguatan dan Pengembangan Lembaga Penyedia Layanan Pemberdayaan Perempuan Kewenangan Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(196, 375, '1', 'Pencegahan Kekerasan terhadap Perempuan Lingkup Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(197, 375, '2', 'Penyediaan Layanan Rujukan Lanjutan bagi Perempuan Korban Kekerasan yang Memerlukan Koordinasi Kewenangan Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(198, 375, '3', 'Penguatan dan Pengembangan Lembaga Penyedia Layanan Perlindungan Perempuan Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(199, 376, '1', 'Pelembagaan PHA pada Lembaga Pemerintah, Nonpemerintah, dan Dunia Usaha Kewenangan Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(200, 376, '2', 'Penguatan dan Pengembangan Lembaga Penyedia Layanan Peningkatan Kualitas Hidup Anak Kewenangan Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(201, 520, '1', 'Pencegahan Kekerasan terhadap Anak yang Melibatkan para Pihak Lingkup Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(202, 520, '2', 'Penyediaan Layanan bagi Anak yang Memerlukan Perlindungan Khusus yang Memerlukan Koordinasi Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(203, 520, '3', 'Penguatan dan Pengembangan Lembaga Penyedia Layanan bagi Anak yang Memerlukan Perlindungan Khusus Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(204, 104, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(205, 104, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(206, 104, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(207, 104, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(208, 104, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(209, 104, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(210, 104, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(211, 104, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(212, 104, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(213, 377, '1', 'Penyediaan dan Penyaluran Pangan Pokok atau Pangan Lainnya sesuai dengan Kebutuhan Daerah Kabupaten/Kota dalam rangka Stabilisasi Pasokan dan Harga Pangan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(214, 377, '2', 'Pengelolaan dan Keseimbangan Cadangan Pangan Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(215, 377, '3', 'Penentuan Harga Minimum Daerah untuk Pangan Lokal yang Tidak Ditetapkan oleh Pemerintah Pusat dan Pemerintah Provinsi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(216, 377, '4', 'Pelaksanaan Pencapaian Target Konsumsi Pangan Perkapita/Tahun sesuai dengan Angka Kecukupan Gizi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(217, 378, '1', 'Penyusunan Peta Kerentanan dan Ketahanan Pangan Kecamatan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(218, 378, '2', 'Penanganan Kerawanan Pangan Kewenangan Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(219, 379, '1', 'Pelaksanaan Pengawasan Keamanan Pangan Segar Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(220, 380, '1', 'Penyelesaian Masalah Ganti Kerugian dan Santunan Tanah untuk Pembangunan oleh Pemerintah Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(221, 381, '1', 'Penyelesaian Masalah Tanah Kosong', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(222, 381, '2', 'Inventarisasi dan Pemanfaatan Tanah Kosong', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(223, 382, '1', 'Penyelesaian Sengketa Tanah Garapan dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(224, 383, '1', 'Penggunaan Tanah yang Hamparannya dalam satu Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(225, 384, '1', 'Penerbitan Izin Membuka Tanah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(226, 441, '1', 'Penetapan Subjek dan Objek Redistribusi Tanah serta Ganti Kerugian Tanah Kelebihan Maksimum dan Tanah Absentee dalam 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(227, 441, '2', 'Penetapan Ganti Kerugian Tanah Kelebihan Maksimum dan Tanah Absentee Lintas Daerah Kabupaten/Kota dalam 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(228, 111, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(229, 111, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(230, 111, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(231, 111, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(232, 111, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(233, 111, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(234, 111, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(235, 111, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(236, 111, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(237, 112, '1', 'Rencana Perlindungan dan Pengelolaan Lingkungan Hidup (RPPLH) Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(238, 112, '2', 'Penyelenggaraan Kajian Lingkungan Hidup Strategis (KLHS) Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(239, 385, '1', 'Pencegahan Pencemaran dan/atau Kerusakan Lingkungan Hidup Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(240, 385, '2', 'Penanggulangan Pencemaran dan/atau Kerusakan Lingkungan Hidup Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(241, 385, '3', 'Pemulihan Pencemaran dan/atau Kerusakan Lingkungan Hidup Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(242, 386, '1', 'Pengelolaan Keanekaragaman Hayati Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(243, 387, '1', 'Penyimpanan Sementara Limbah B3', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(244, 387, '2', 'Pengumpulan Limbah B3 dalam 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(245, 113, '1', 'Pembinaan dan Pengawasan terhadap Usaha dan/atau Kegiatan yang Izin Lingkungan dan Izin PPLH diterbitkan oleh Pemerintah Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(246, 388, '1', 'Penyelenggaraan Pendidikan, Pelatihan, dan Penyuluhan Lingkungan Hidup untuk Lembaga Kemasyarakatan Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(247, 389, '1', 'Pemberian Penghargaan Lingkungan Hidup Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(248, 390, '1', 'Penyelesaian Pengaduan Masyarakat di Bidang Perlindungan dan Pengelolaan Lingkungan Hidup (PPLH) Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(249, 391, '1', 'Pengelolaan Sampah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(250, 391, '2', 'Penerbitan Izin Pendaurulangan Sampah/Pengelolaan Sampah, Pengangkutan Sampah dan Pemrosesan Akhir Sampah yang Diselenggarakan oleh Swasta', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(251, 391, '3', 'Pembinaan dan Pengawasan Pengelolaan Sampah yang Diselenggarakan oleh Pihak Swasta', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(252, 120, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(253, 120, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(254, 120, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(255, 120, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(256, 120, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(257, 120, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(258, 120, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(259, 120, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(260, 120, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(261, 121, '1', 'Pelayanan Pendaftaran Penduduk', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(262, 121, '2', 'Penataan Pendaftaran Penduduk', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(263, 121, '3', 'Penyelenggaraan Pendaftaran Penduduk', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(264, 121, '4', 'Pembinaan dan Pengawasan Penyelenggaraan Pendaftaran Penduduk', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(265, 122, '1', 'Pelayanan Pencatatan Sipil', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(266, 122, '2', 'Penyelenggaraan Pencatatan Sipil', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(267, 122, '3', 'Pembinaan dan Pengawasan Penyelenggaraan Pencatatan Sipil', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(268, 392, '1', 'Pengumpulan Data Kependudukan dan Pemanfaatan dan Penyajian Database Kependudukan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(269, 392, '2', 'Penataan Pengelolaan Informasi Administrasi Kependudukan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(270, 392, '3', 'Penyelenggaraan Pengelolaan Informasi Administrasi Kependudukan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(271, 392, '4', 'Pembinaan dan Pengawasan Pengelolaan Informasi Administrasi Kependudukan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(272, 128, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(273, 128, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(274, 128, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(275, 128, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(276, 128, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(277, 128, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(278, 128, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(279, 128, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(280, 128, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(281, 129, '1', 'Penyelenggaraan Penataan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(282, 393, '1', 'Fasilitasi Kerja Sama Antar Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(283, 394, '1', 'Pembinaan dan Pengawasan Penyelenggaraan Administrasi Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(284, 396, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(285, 396, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(286, 396, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(287, 396, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(288, 396, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(289, 396, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(290, 396, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(291, 396, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(292, 396, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(293, 397, '1', 'Pemaduan dan Sinkronisasi Kebijakan Pemerintah Daerah Provinsi dengan Pemerintah Daerah Kabupaten/Kota dalam rangka Pengendalian Kuantitas Penduduk', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(294, 397, '2', 'Pemetaan Perkiraan Pengendalian Penduduk Cakupan Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(295, 398, '1', 'Pelaksanaan Advokasi, Komunikasi, Informasi dan Edukasi (KIE) Pengendalian Penduduk dan KB sesuai Kearifan Budaya Lokal', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(296, 398, '2', 'Pendayagunaan Tenaga Penyuluh KB/Petugas Lapangan KB (PKB/PLKB)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(297, 398, '3', 'Pengendalian dan Pendistribusian Kebutuhan Alat dan Obat Kontrasepsi serta Pelaksanaan Pelayanan KB di Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(298, 398, '4', 'Pemberdayaan dan Peningkatan Peran serta Organisasi Kemasyarakatan Tingkat Daerah Kabupaten/Kota dalam Pelaksanaan Pelayanan dan Pembinaan Kesertaan Ber-KB', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(299, 399, '1', 'Pelaksanaan Pembangunan Keluarga melalui Pembinaan Ketahanan dan Kesejahteraan Keluarga', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(300, 399, '2', 'Pelaksanaan dan Peningkatan Peran Serta Organisasi Kemasyarakatan Tingkat Daerah Kabupaten/ Kota dalam Pembangunan Keluarga Melalui Pembinaan Ketahanan dan Kesejahteraan Keluarga', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18');
INSERT INTO `kegiatans` (`id`, `program_id`, `kode`, `deskripsi`, `tahun_perubahan`, `status_aturan`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(301, 134, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(302, 134, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(303, 134, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(304, 134, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(305, 134, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(306, 134, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(307, 134, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(308, 134, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(309, 134, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(310, 135, '1', 'Penetapan Rencana Induk Jaringan LLAJ Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(311, 135, '2', 'Penyediaan Perlengkapan Jalan di Jalan Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(312, 135, '3', 'Pengelolaan Terminal Penumpang Tipe C', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(313, 135, '4', 'Penerbitan Izin Penyelenggaraan dan Pembangunan Fasilitas Parkir', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(314, 135, '5', 'Pengujian Berkala Kendaraan Bermotor', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(315, 135, '6', 'Pelaksanaan Manajemen dan Rekayasa Lalu Lintas untuk Jaringan Jalan Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(316, 135, '7', 'Persetujuan Hasil Analisis Dampak Lalu Lintas (Andalalin) untuk Jalan Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(317, 135, '8', 'Audit dan Inspeksi Keselamatan LLAJ di Jalan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(318, 135, '9', 'Penyediaan Angkutan Umum untuk Jasa Angkutan Orang dan/atau Barang antar Kota dalam 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(319, 135, '10', 'Penetapan Kawasan Perkotaan untuk Pelayanan Angkutan Perkotaan yang Melampaui Batas 1 (satu) Daerah Kabupaten/Kota dalam 1 (Satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(320, 141, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(321, 141, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(322, 141, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(323, 141, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(324, 141, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(325, 141, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(326, 141, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(327, 141, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(328, 141, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(329, 142, '1', 'Pengelolaan Informasi dan Komunikasi Publik Pemerintah Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(330, 400, '1', 'Pengelolaan Nama Domain yang telah Ditetapkan oleh Pemerintah Pusat dan Sub Domain di Lingkup Pemerintah Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(331, 400, '2', 'Pengelolaan e-government Di Lingkup Pemerintah Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(332, 147, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(333, 147, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(334, 147, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(335, 147, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(336, 147, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(337, 147, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(338, 147, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(339, 147, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(340, 147, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(341, 401, '1', 'Pemeriksaan dan Pengawasan Koperasi, Koperasi Simpan Pinjam/Unit Simpan Pinjam Koperasi yang Wilayah Keanggotaannya dalam Daerah Kabupaten/ Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(342, 402, '1', 'Penilaian Kesehatan Koperasi Simpan Pinjam/Unit Simpan Pinjam Koperasi yang Wilayah Keanggotaannya dalam 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(343, 403, '1', 'Pendidikan dan Latihan Perkoperasian bagi Koperasi yang Wilayah Keanggotaan dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(344, 149, '1', 'Pemberdayaan dan Perlindungan Koperasi yang Keanggotaannya dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(345, 404, '1', 'Pemberdayaan Usaha Mikro yang Dilakukan melalui Pendataan, Kemitraan, Kemudahan Perizinan, Penguatan Kelembagaan dan Koordinasi dengan Para Pemangku Kepentingan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(346, 405, '1', 'Pengembangan Usaha Mikro dengan Orientasi Peningkatan Skala Usaha menjadi Usaha Kecil', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(347, 148, '1', 'Penerbitan Izin Usaha Simpan Pinjam untuk Koperasi dengan Wilayah Keanggotaan dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(348, 148, '2', 'Penerbitan Izin Pembukaan Kantor Cabang, Cabang Pembantu dan Kantor Kas Koperasi Simpan Pinjam untuk Koperasi dengan Wilayah Keanggotaan dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(349, 161, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(350, 161, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(351, 161, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(352, 161, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(353, 161, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(354, 161, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(355, 161, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(356, 161, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(357, 161, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(358, 406, '1', 'Penyelenggaraan Promosi Penanaman Modal yang menjadi Kewenangan Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(359, 407, '1', 'Pelayanan Perizinan dan Non Perizinan secara Terpadu Satu Pintu dibidang Penanaman Modal yang menjadi Kewenangan Daerah Kabupaten/ Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(360, 408, '1', 'Pengendalian Pelaksanaan Penanaman Modal yang menjadi Kewenangan Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(361, 162, '1', 'Penetapan Pemberian Fasilitas/Insentif Dibidang Penanaman Modal yang menjadi Kewenangan Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(362, 162, '2', 'Pembuatan Peta Potensi Investasi Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(363, 163, '1', 'Pengelolaan Data dan Informasi Perizinan dan Non Perizinan yang Terintegrasi pada Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(364, 167, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(365, 167, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(366, 167, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(367, 167, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(368, 167, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(369, 167, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(370, 167, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(371, 167, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(372, 167, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(373, 409, '1', 'Pembinaan dan Pengembangan Olahraga Pendidikan pada Jenjang Pendidikan yang menjadi Kewenangan Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(374, 409, '2', 'Penyelenggaraan Kejuaraan Olahraga Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(375, 409, '3', 'Pembinaan dan Pengembangan Olahraga Prestasi Tingkat Daerah Provinsi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(376, 409, '4', 'Pembinaan dan Pengembangan Organisasi Olahraga', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(377, 409, '5', 'Pembinaan dan Pengembangan Olahraga Rekreasi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(378, 168, '1', 'Penyadaran, Pemberdayaan, dan Pengembangan Pemuda dan Kepemudaan terhadap Pemuda Pelopor Kabupaten/Kota, Wirausaha Muda Pemula, dan Pemuda Kader Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(379, 168, '2', 'Pemberdayaan dan Pengembangan Organisasi Kepemudaan Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(380, 410, '1', 'Penyelenggaraan Statistik Sektoral di Lingkup Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(381, 411, '1', 'Penyelenggaraan Persandian untuk Pengamanan Informasi Pemerintah Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(382, 411, '2', 'Penetapan Pola Hubungan Komunikasi Sandi Antar Perangkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(383, 412, '1', 'Pengelolaan Kebudayaan yang Masyarakat Pelakunya dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(384, 412, '2', 'Pelestarian Kesenian Tradisional yang Masyarakat Pelakunya dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(385, 413, '1', 'Pembinaan Sejarah Lokal dalam 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(386, 414, '1', 'Penetapan Cagar Budaya Peringkat Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(387, 414, '2', 'Pengelolaan Cagar Budaya Peringkat Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(388, 414, '3', 'Penerbitan Izin membawa Cagar Budaya ke Luar Daerah Kabupaten/Kota dalam 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(389, 415, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(390, 415, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(391, 415, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(392, 415, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(393, 415, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(394, 415, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(395, 415, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(396, 415, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(397, 415, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(398, 416, '1', 'Pengelolaan Perpustakaan Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(399, 416, '2', 'Pembudayaan Gemar Membaca Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(400, 417, '1', 'Pengelolaan Arsip Dinamis Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(401, 417, '2', 'Pengelolaan Arsip Statis Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(402, 417, '3', 'Pengelolaan Simpul Jaringan Informasi Kearsipan Nasional Tingkat Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(403, 418, '1', 'Pemusnahan Arsip Dilingkungan Pemerintah Daerah Kabupaten/Kota yang Memiliki Retensi di Bawah 10 (sepuluh) Tahun', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(404, 418, '2', 'Perlindungan dan Penyelamatan Arsip Akibat Bencana yang Berskala Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(405, 418, '3', 'Penyelamatan Arsip Perangkat Daerah Kabupaten/Kota yang Digabung dan/atau Dibubarkan, dan Pemekaran Daerah Kecamatan dan Desa/Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(406, 418, '4', 'Autentikasi Arsip Statis dan Arsip Hasil Alih Media Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(407, 418, '5', 'Pencarian Arsip Statis Kabupaten/Kota yang Dinyatakan Hilang', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(408, 419, '1', 'Penerbitan Izin Usaha Perikanan di Bidang Pembudidayaan Ikan yang Usahanya dalam 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(409, 419, '2', 'Pemberdayaan Pembudi Daya Ikan Kecil', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(410, 419, '3', 'Penerbitan Tanda Daftar bagi Pembudi Daya Ikan Kecil (TDPIK) dalam 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(411, 419, '4', 'Pengelolaan Pembudidayaan Ikan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(412, 420, '1', 'Penerbitan Tanda Daftar Usaha Pengolahan Hasil Perikanan bagi Usaha Skala Mikro dan Kecil', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(413, 420, '2', 'Pembinaan Mutu dan Keamanan Hasil Perikanan bagi Usaha Pengolahan dan Pemasaran Skala Mikro dan Kecil', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(414, 420, '3', 'Penyediaan dan Penyaluran Bahan Baku Industri Pengolahan Ikan dalam 1 (satu) Daerah Kabupaten/ Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(415, 421, '1', 'Pengelolaan Penangkapan Ikan di Wilayah Sungai, Danau, Waduk, Rawa, dan Genangan Air Lainnya yang dapat Diusahakan dalam 1 (satu) Daerah Kabupaten/ Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(416, 421, '2', 'Pemberdayaan Nelayan Kecil dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(417, 421, '3', 'Pengelolaan dan Penyelenggaraan Tempat Pelelangan Ikan (TPI)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(418, 422, '1', 'Pengelolaan Daya Tarik Wisata Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(419, 422, '2', 'Pengelolaan Kawasan Strategis Pariwisata Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(420, 422, '3', 'Pengelolaan Destinasi Pariwisata Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(421, 422, '4', 'Penetapan Tanda Daftar Usaha Pariwisata Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(422, 423, '1', 'Pemasaran Pariwisata Dalam dan Luar Negeri Daya Tarik, Destinasi dan Kawasan Strategis Pariwisata Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(423, 424, '1', 'Pelaksanaan Peningkatan Kapasitas Sumber Daya Manusia Pariwisata dan Ekonomi Kreatif Tingkat Dasar', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(424, 424, '2', 'Pengembangan Kapasitas Pelaku Ekonomi Kreatif', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(425, 179, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(426, 179, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(427, 179, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(428, 179, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(429, 179, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(430, 179, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(431, 179, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(432, 179, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(433, 179, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(434, 425, '1', 'Pengawasan Penggunaan Sarana Pertanian', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(435, 425, '2', 'Pengelolaan Sumber Daya Genetik (SDG) Hewan, Tumbuhan, dan Mikro Organisme Kewenangan Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(436, 425, '3', 'Peningkatan Mutu dan Peredaran Benih/Bibit Ternak dan Tanaman Pakan Ternak serta Pakan dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(437, 425, '4', 'Pengawasan Obat Hewan di Tingkat Pengecer', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(438, 425, '5', 'Pengendalian dan Pengawasan Penyediaan dan Peredaran Benih/Bibit Ternak, dan Hijauan Pakan Ternak dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(439, 425, '6', 'Penyediaan Benih/Bibit Ternak dan Hijauan Pakan Ternak yang Sumbernya dalam 1 (satu) Daerah Kabupaten/Kota Lain', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(440, 426, '1', 'Penjaminan Kesehatan Hewan, Penutupan dan Pembukaan Daerah Wabah Penyakit Hewan Menular Dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(441, 426, '2', 'Pengawasan Pemasukan dan Pengeluaran Hewan dan Produk Hewan Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(442, 426, '3', 'Pengelolaan Pelayanan Jasa Laboratorium dan Jasa Medik Veteriner dalam Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(443, 426, '4', 'Penerapan dan Pengawasaan Persyaratan Teknis Kesehatan Masyarakat Veteriner', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(444, 426, '5', 'Penerapan dan Pengawasan Persyaratan Teknis Kesejahteraan Hewan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(445, 427, '1', 'Pengendalian dan Penanggulangan Bencana Pertanian Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(446, 428, '1', 'Pengembangan Prasarana Pertanian', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(447, 428, '2', 'Pembangunan Prasarana Pertanian', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(448, 428, '3', 'Pengelolaan Wilayah Sumber Bibit Ternak dan Rumpun/Galur Ternak dalam Daerah Kabupaten/ Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(449, 428, '4', 'Pengembangan Lahan Penggembalaan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(450, 429, '1', 'Pelaksanaan Penyuluhan Pertanian', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(451, 523, '1', 'Pengembangan Prasarana Pertanian', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(452, 523, '2', 'Pembangunan Prasarana Pertanian', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(453, 523, '3', 'Pengelolaan Wilayah Sumber Bibit Ternak dan Rumpun/Galur Ternak dalam Daerah Kabupaten/ Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(454, 523, '4', 'Pengembangan Lahan Penggembalaan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(455, 430, '1', 'Penerbitan Izin Pengelolaan Pasar Rakyat, Pusat Perbelanjaan, dan Izin Usaha Toko Swalayan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(456, 430, '2', 'Penerbitan Tanda Daftar Gudang', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(457, 430, '3', 'Penerbitan Surat Tanda Pendaftaran Waralaba (STPW) untuk Penerima Waralaba dari Waralaba Dalam Negeri', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(458, 430, '4', 'Penerbitan Surat Tanda Pendaftaran Waralaba (STPW) untuk Penerima Waralaba Lanjutan dari Waralaba Luar Negeri', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(459, 430, '5', 'Penerbitan Surat Izin Usaha Perdagangan Minuman Beralkohol Golongan B dan C untuk Pengecer dan Penjual Langsung Minum di Tempat', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(460, 430, '6', 'Pengendalian Fasilitas Penyimpanan Bahan Berbahaya dan Pengawasan Distribusi, Pengemasan dan Pelabelan Bahan Berbahaya di Tingkat Daerah Kabupaten/ Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(461, 430, '7', 'Penerbitan Surat Keterangan Asal (bagi Daerah Kabupaten/Kota yang Telah Ditetapkan Sebagai Instansi Penerbit Surat Keterangan Asal)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(462, 431, '1', 'Pembangunan dan Pengelolaan Sarana Distribusi Perdagangan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(463, 431, '2', 'Pembinaan terhadap Pengelola Sarana Distribusi Perdagangan Masyarakat di Wilayah Kerjanya', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(464, 432, '1', 'Menjamin Ketersediaan Barang Kebutuhan Pokok dan Barang Penting di Tingkat Daerah Kabupaten/ Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(465, 432, '2', 'Pengendalian Harga, dan Stok Barang Kebutuhan Pokok dan Barang Penting di Tingkat Pasar Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(466, 432, '3', 'Pengawasan Pupuk dan Pestisida Bersubsidi di Tingkat Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(467, 433, '1', 'Penyelenggaraan Promosi Dagang melalui Pameran Dagang dan Misi Dagang bagi Produk Ekspor Unggulan yang terdapat pada 1 (satu) Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(468, 434, '1', 'Pelaksanaan Metrologi Legal berupa, Tera, Tera Ulang, dan Pengawasan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(469, 435, '1', 'Pelaksanaan Promosi, Pemasaran dan Peningkatan Penggunaan Produk Dalam Negeri', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(470, 436, '1', 'Penyusunan, Penerapan dan Evaluasi Rencana Pembangunan Industri Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(471, 437, '1', 'Penerbitan Izin Usaha Industri (IUI), Izin Perluasan Usaha Industri (IPUI), Izin Usaha Kawasan Industri (IUKI) dan IPKI Kewenangan (SIINAS)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(472, 438, '1', 'Penyediaan Informasi Industri untuk Informasi Industri untuk IUI, IPUI, IUKI dan IPKI Kewenangan Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(473, 439, '1', 'Pengembangan Satuan Permukiman pada Tahap Kemandirian', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(474, 440, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(475, 440, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(476, 440, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(477, 440, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(478, 440, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(479, 440, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(480, 440, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(481, 440, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(482, 440, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(483, 440, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(484, 440, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(485, 442, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(486, 442, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(487, 442, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(488, 442, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(489, 442, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(490, 442, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(491, 442, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(492, 442, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(493, 442, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(494, 442, '13', 'Penataan Organisasi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(495, 443, '10', 'Peningkatan Pelayanan BLUD', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(496, 443, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(497, 443, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(498, 443, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(499, 443, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(500, 443, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(501, 443, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(502, 443, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(503, 443, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(504, 443, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(505, 444, '1', 'Administrasi Tata Pemerintahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(506, 444, '3', 'Fasilitasi dan Koordinasi Hukum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(507, 444, '4', 'Fasilitasi Kerjasama Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(508, 444, '2', 'Pelaksanaan Kebijakan Kesejahteraan Rakyat', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(509, 445, '1', 'Administrasi Tata Pemerintahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(510, 445, '2', 'Pelaksanaan Kebijakan Kesejahteraan Rakyat', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(511, 445, '3', 'Fasilitasi dan Koordinasi Hukum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(512, 445, '4', 'Fasilitasi Kerjasama Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(513, 446, '1', 'Administrasi Tata Pemerintahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(514, 446, '2', 'Pelaksanaan Kebijakan Kesejahteraan Rakyat', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(515, 446, '3', 'Fasilitasi dan Koordinasi Hukum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(516, 446, '4', 'Fasilitasi Kerjasama Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(517, 246, '1', 'Pelaksanaan Kebijakan Perekonomian', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(518, 246, '2', 'Pelaksanaan Administrasi Pembangunan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(519, 246, '3', 'Pengelolaan Pengadaan Barang dan Jasa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(520, 246, '4', 'Pemantauan Kebijakan Sumber Daya Alam', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(521, 447, '3', 'Pengelolaan Pengadaan Barang dan Jasa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(522, 448, '1', 'Pelaksanaan Kebijakan Perekonomian', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(523, 448, '2', 'Pelaksanaan Administrasi Pembangunan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(524, 448, '3', 'Pengelolaan Pengadaan Barang dan Jasa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(525, 448, '4', 'Pemantauan Kebijakan Sumber Daya Alam', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(526, 522, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(527, 522, '2', 'Administrasi Keuangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(528, 522, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(529, 522, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(530, 522, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(531, 522, '6', 'Administrasi Umum Perangkat Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(532, 522, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(533, 522, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(534, 522, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(535, 522, '13', 'Penataan Organisasi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(536, 524, '0', 'xxx', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(537, 1, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(538, 1, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah (Kegiatan dan Sub Kegiatan hanya digunakan oleh Sekretariat Daerah)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(539, 1, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah (Kegiatan dan Sub Kegiatan hanya digunakan oleh Sekretariat Daerah)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(540, 1, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(541, 1, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(542, 1, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(543, 1, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(544, 349, '2', 'Penerbitan Izin PAUD dan Pendidikan Nonformal yang Diselenggarakan oleh Masyarakat', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(545, 43, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(546, 43, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(547, 43, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(548, 43, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(549, 43, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(550, 43, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(551, 43, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(552, 64, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(553, 64, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(554, 64, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(555, 64, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(556, 64, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(557, 64, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(558, 64, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(559, 65, '4', 'Pemberdayaan Masyarakat dalam Pencegahan Kebakaran', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(560, 65, '5', 'Penyelenggaraan Operasi Pencarian dan Pertolongan terhadap Kondisi Membahayakan Manusia', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(561, 367, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(562, 367, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(563, 367, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(564, 367, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(565, 367, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(566, 367, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(567, 367, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(568, 10, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(569, 10, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(570, 10, '13', 'Penataan Organisas', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(571, 10, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(572, 10, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(573, 10, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(574, 351, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daera', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(575, 351, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(576, 351, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(577, 351, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(578, 351, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(579, 351, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(580, 332, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daera', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(581, 332, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(582, 332, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(583, 332, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(584, 332, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(585, 332, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(586, 58, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(587, 58, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(588, 58, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(589, 58, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(590, 58, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(591, 58, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(592, 58, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(593, 58, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(594, 88, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(595, 88, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(596, 88, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(597, 88, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(598, 88, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(599, 88, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(600, 88, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(601, 82, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(602, 82, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(603, 82, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(604, 82, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(605, 82, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(606, 82, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(607, 82, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(608, 383, '2', 'Koordinasi Perencanaan Penggunaan dan Pemanfaatan Tanah Pasca Reklamasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(609, 104, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(610, 104, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(611, 104, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(612, 104, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(613, 104, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(614, 104, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(615, 104, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(616, 120, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(617, 120, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18');
INSERT INTO `kegiatans` (`id`, `program_id`, `kode`, `deskripsi`, `tahun_perubahan`, `status_aturan`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(618, 120, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(619, 120, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(620, 120, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(621, 120, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(622, 120, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(623, 111, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(624, 111, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(625, 111, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(626, 111, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(627, 111, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(628, 111, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(629, 111, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(630, 396, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(631, 396, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daera', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(632, 396, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(633, 396, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(634, 396, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(635, 396, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(636, 396, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(637, 141, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(638, 141, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(639, 141, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(640, 141, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(641, 141, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(642, 141, '15', 'Layanan Keuangan dan Kesejahteraan DPR', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(643, 141, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(644, 128, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(645, 128, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(646, 128, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(647, 128, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(648, 128, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(649, 128, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(650, 128, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(651, 393, '2', 'Fasilitasi Kerja Sama Antar Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(652, 161, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(653, 161, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(654, 161, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(655, 161, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(656, 161, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(657, 161, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(658, 161, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(659, 412, '3', 'Pembinaan Lembaga Adat yang Penganutnya dalam Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(660, 134, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(661, 134, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(662, 134, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(663, 134, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(664, 134, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(665, 134, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(666, 134, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(667, 135, '11', 'Penetapan Rencana Umum Jaringan Trayek Perkotaan dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(668, 135, '12', 'Penetapan Rencana Umum Jaringan Trayek Pedesaan dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(669, 135, '13', 'Penetapan Wilayah Operasi Angkutan Orang dengan Menggunakan Taksi dalam Kawasan Perkotaan yang Wilayah Operasinya dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(670, 135, '14', 'Penerbitan Izin Penyelenggaraan Angkutan Orang dalam Trayek Lintas Daerah Kabupaten/Kota dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(671, 135, '15', 'Penerbitan Izin Penyelenggaraan Angkutan Taksi yang Wilayah Operasinya dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(672, 135, '16', 'Penetapan Tarif Kelas Ekonomi untuk Angkutan Orang yang Melayani Trayek serta Angkutan Perkotaan dan Perdesaan dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(673, 248, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(674, 248, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(675, 248, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(676, 248, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(677, 248, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(678, 248, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(679, 248, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(680, 248, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(681, 248, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(682, 248, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(683, 248, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(684, 248, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(685, 248, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(686, 248, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(687, 248, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(688, 248, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(689, 249, '1', 'Pembentukan Peraturan Daerah dan Peraturan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(690, 249, '2', 'Pembahasan Kebijakan Anggaran', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(691, 249, '3', 'Pengawasan Penyelenggaraan Pemerintahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(692, 249, '4', 'Peningkatan Kapasitas DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(693, 249, '5', 'Penyerapan dan Penghimpunan Aspirasi Masyarakat', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(694, 249, '6', 'Pelaksanaan dan Pengawasan Kode Etik DPRD0', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(695, 249, '7', 'Pembahasan Kerja Sama Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(696, 249, '8', 'Fasilitasi Tugas DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(697, 147, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(698, 147, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(699, 147, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(700, 147, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(701, 147, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(702, 147, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(703, 147, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(704, 167, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(705, 167, '11', 'Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(706, 167, '12', 'Fasilitasi Kerumahtanggaan Sekretariat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(707, 167, '13', 'Penataan Organisasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(708, 167, '14', 'Pelaksanaan Protokol dan Komunikasi Pimpinan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(709, 167, '15', 'Layanan Keuangan dan Kesejahteraan DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(710, 167, '16', 'Layanan Administrasi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kegiatan_indikator_kinerjas`
--

CREATE TABLE `kegiatan_indikator_kinerjas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kegiatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kegiatan_indikator_kinerjas`
--

INSERT INTO `kegiatan_indikator_kinerjas` (`id`, `kegiatan_id`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1128, 172, 'Baru 2', '2022-11-13 02:39:16', '2022-11-13 02:39:16'),
(1129, 10, 'test test test', '2022-11-16 10:28:09', '2022-11-16 10:28:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kegiatan_target_satuan_rp_realisasis`
--

CREATE TABLE `kegiatan_target_satuan_rp_realisasis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `opd_kegiatan_indikator_kinerja_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_rp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `realisasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `realisasi_rp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kegiatan_target_satuan_rp_realisasis`
--

INSERT INTO `kegiatan_target_satuan_rp_realisasis` (`id`, `opd_kegiatan_indikator_kinerja_id`, `target`, `satuan`, `target_rp`, `realisasi`, `realisasi_rp`, `tahun`, `created_at`, `updated_at`) VALUES
(1, 1, '10', 'buku', '100000', '5', '50000', '2018', '2022-11-16 10:52:55', '2022-11-16 11:05:29'),
(2, 1, '11', 'karung', '1000000', '11', '1000000', '2019', '2022-11-16 11:05:48', '2022-11-16 11:10:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelurahans`
--

CREATE TABLE `kelurahans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kecamatan_id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warna` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kelurahans`
--

INSERT INTO `kelurahans` (`id`, `kecamatan_id`, `nama`, `warna`, `created_at`, `updated_at`) VALUES
(1, 1, 'Babadan', '#a8a511', '2022-07-16 21:18:52', '2022-07-16 21:18:52'),
(2, 1, 'Balerejo', '#7d3e90', '2022-07-16 21:31:07', '2022-07-16 21:31:07'),
(3, 1, 'Banaran', '#672201', '2022-07-16 21:32:03', '2022-07-16 21:32:03'),
(4, 1, 'Bulakrejo', '#c05192', '2022-07-16 21:46:35', '2022-07-16 21:46:35'),
(5, 1, 'Gading', '#f37793', '2022-07-16 21:46:46', '2022-07-16 21:46:46'),
(6, 1, 'Garon', '#0dcf37', '2022-07-16 21:47:28', '2022-07-16 21:47:28'),
(7, 1, 'Glonggong', '#27e4a7', '2022-07-16 21:47:43', '2022-07-16 21:47:43'),
(8, 1, 'Jerukgulung', '#3abb38', '2022-07-16 21:47:57', '2022-07-16 21:47:57'),
(9, 1, 'Kebonagung', '#8927c9', '2022-07-16 21:48:11', '2022-07-16 21:48:11'),
(10, 1, 'Kedungjati', '#525308', '2022-07-16 21:48:25', '2022-07-16 21:48:25'),
(11, 1, 'Kedungrejo', '#edde1d', '2022-07-16 21:48:41', '2022-07-16 21:48:41'),
(12, 1, 'Kuwu', '#f56352', '2022-07-16 21:48:55', '2022-07-16 21:48:55'),
(13, 1, 'Pacinan', '#027bde', '2022-07-16 21:49:09', '2022-07-16 21:49:09'),
(14, 1, 'Simo', '#6294b4', '2022-07-16 21:49:18', '2022-07-16 21:49:18'),
(15, 1, 'Sogo', '#fa5073', '2022-07-16 21:49:42', '2022-07-16 21:49:42'),
(16, 1, 'Sumberbening', '#2facb1', '2022-07-16 21:49:54', '2022-07-16 21:49:54'),
(17, 1, 'Tapelan', '#f5a15e', '2022-07-16 21:50:06', '2022-07-16 21:50:06'),
(18, 1, 'Warurejo', '#1bd128', '2022-07-16 21:50:19', '2022-07-16 21:50:19'),
(19, 2, 'Banjarejo', '#aaa815', '2022-07-16 21:53:17', '2022-07-16 21:53:17'),
(20, 2, 'Banjarsari Kulon', '#0e6d75', '2022-07-16 21:54:24', '2022-07-16 21:54:24'),
(21, 2, 'Banjarsari Wetan', '#d81cca', '2022-07-16 21:58:26', '2022-07-16 21:58:26'),
(22, 2, 'Dagangan', '#043bef', '2022-07-16 21:59:46', '2022-07-16 21:59:46'),
(23, 2, 'Jetis', '#c0ef6f', '2022-07-16 22:09:07', '2022-07-16 22:09:07'),
(24, 1, 'Joho', '#bc91de', '2022-07-16 22:10:15', '2022-07-16 22:10:15'),
(25, 2, 'Kepet', '#59c44d', '2022-07-16 22:11:13', '2022-07-16 22:11:13'),
(26, 2, 'Ketandan', '#9699b5', '2022-07-16 22:12:15', '2022-07-16 22:12:15'),
(27, 2, 'Mendak', '#ea1a36', '2022-07-16 22:13:05', '2022-07-16 22:13:05'),
(28, 2, 'Mruwak', '#1ec180', '2022-07-16 22:14:26', '2022-07-16 22:14:26'),
(29, 2, 'Ngranget', '#fa05e4', '2022-07-16 22:15:21', '2022-07-16 22:15:21'),
(30, 2, 'Padas', '#dbd8bb', '2022-07-16 22:16:14', '2022-07-16 22:16:14'),
(31, 2, 'Prambon', '#441a03', '2022-07-16 22:16:59', '2022-07-16 22:16:59'),
(32, 2, 'Segulung', '#53273e', '2022-07-16 22:17:55', '2022-07-16 22:17:55'),
(33, 2, 'Sewulan', '#1c7c12', '2022-07-16 22:18:40', '2022-07-16 22:18:40'),
(34, 2, 'Sukosari', '#a56e4b', '2022-07-16 22:19:58', '2022-07-16 22:19:58'),
(35, 2, 'Tileng', '#8cd6ab', '2022-07-16 22:20:45', '2022-07-16 22:20:45'),
(36, 3, 'Bader', '#44319d', '2022-07-16 22:22:26', '2022-07-16 22:22:26'),
(37, 3, 'Blimbing', '#495c0e', '2022-07-16 22:23:17', '2022-07-16 22:23:17'),
(38, 3, 'Bangunsari', '#81785d', '2022-07-16 22:24:22', '2022-07-16 22:24:22'),
(39, 3, 'Candimulyo', '#353b3c', '2022-07-16 22:25:10', '2022-07-16 22:25:10'),
(40, 3, 'Doho', '#4b29f6', '2022-07-16 22:25:45', '2022-07-16 22:25:45'),
(41, 3, 'Dolopo', '#c63818', '2022-07-16 22:26:29', '2022-07-16 22:26:29'),
(42, 3, 'Glonggong', '#eb32cb', '2022-07-16 22:27:10', '2022-07-16 22:27:10'),
(43, 3, 'Ketawang', '#c6c3ca', '2022-07-16 22:27:59', '2022-07-16 22:27:59'),
(44, 3, 'Kradinan', '#ca3897', '2022-07-16 22:28:49', '2022-07-16 22:28:49'),
(45, 3, 'Lembah', '#bdb318', '2022-07-16 22:29:27', '2022-07-16 22:29:27'),
(46, 3, 'Milir', '#ba28de', '2022-07-16 22:30:30', '2022-07-16 22:30:30'),
(47, 3, 'Suluk', '#123348', '2022-07-16 22:31:26', '2022-07-16 22:31:26'),
(48, 3, 'Hutan', '#600ecd', '2022-07-16 22:32:54', '2022-07-16 22:32:54'),
(49, 4, 'Banaran', '#440562', '2022-07-16 22:34:17', '2022-07-16 22:34:17'),
(50, 4, 'Geger', '#d8bd11', '2022-07-16 22:34:50', '2022-07-16 22:34:50'),
(51, 4, 'Jatisari', '#64d1db', '2022-07-16 22:35:35', '2022-07-16 22:35:35'),
(52, 4, 'Jogodayuh', '#c57b56', '2022-07-16 22:36:15', '2022-07-16 22:36:15'),
(53, 4, 'Kaibon', '#e521e7', '2022-07-16 22:36:50', '2022-07-16 22:36:50'),
(54, 4, 'Kertobanyon', '#f5d07b', '2022-07-16 22:37:27', '2022-07-16 22:37:27'),
(55, 4, 'Kertosari', '#5194b7', '2022-07-16 22:38:42', '2022-07-16 22:38:42'),
(56, 4, 'Klorogan', '#86b81b', '2022-07-16 22:39:18', '2022-07-16 22:39:18'),
(57, 4, 'Kranggan', '#861000', '2022-07-16 22:40:00', '2022-07-16 22:40:00'),
(58, 4, 'Nglandung', '#39db1a', '2022-07-16 22:40:37', '2022-07-16 22:40:37'),
(59, 4, 'Pagotan', '#7d1dfe', '2022-07-16 22:41:30', '2022-07-16 22:41:30'),
(60, 4, 'Purworejo', '#8eaf1d', '2022-07-16 22:42:06', '2022-07-16 22:42:06'),
(61, 4, 'Putat', '#0b3225', '2022-07-16 22:42:47', '2022-07-16 22:42:47'),
(62, 4, 'Sambirejo', '#450812', '2022-07-16 22:43:29', '2022-07-16 22:43:29'),
(63, 4, 'Sangen', '#68279b', '2022-07-16 22:44:09', '2022-07-16 22:44:09'),
(64, 4, 'Sareng', '#356954', '2022-07-16 22:44:48', '2022-07-16 22:44:48'),
(65, 4, 'Slambur', '#5cb7d7', '2022-07-16 22:45:17', '2022-07-16 22:45:17'),
(66, 4, 'Sumberejo', '#1fff1d', '2022-07-16 22:45:53', '2022-07-16 22:45:53'),
(67, 4, 'Uteran', '#b06ad9', '2022-07-16 22:46:21', '2022-07-16 22:46:21'),
(68, 5, 'Batok', '#0149b4', '2022-07-16 22:47:53', '2022-07-16 22:47:53'),
(69, 5, 'Durenan', '#3167cd', '2022-07-16 22:48:25', '2022-07-16 22:48:25'),
(70, 5, 'Gemarang', '#638dc3', '2022-07-16 22:49:31', '2022-07-16 22:49:31'),
(71, 5, 'Hutan', '#b618f2', '2022-07-16 22:50:28', '2022-07-16 22:50:28'),
(72, 5, 'Nampu', '#f05c65', '2022-07-16 22:51:05', '2022-07-16 22:51:05'),
(73, 5, 'Sebayi', '#577e64', '2022-07-16 22:51:45', '2022-07-16 22:51:45'),
(74, 5, 'Tawangrejo', '#08e654', '2022-07-16 22:53:14', '2022-07-16 22:53:14'),
(75, 5, 'Winong', '#21cddf', '2022-07-16 22:54:03', '2022-07-16 22:54:03'),
(76, 6, 'Bedoho', '#56d501', '2022-07-16 22:57:07', '2022-07-16 22:57:07'),
(77, 6, 'Bibrik', '#d5adc2', '2022-07-16 22:57:59', '2022-07-16 22:57:59'),
(78, 6, 'Bukur', '#bf3839', '2022-07-16 23:22:54', '2022-07-16 23:22:54'),
(79, 6, 'Jiwan', '#7a0496', '2022-07-16 23:23:40', '2022-07-16 23:23:40'),
(80, 6, 'Grobogan', '#b097ea', '2022-07-16 23:24:16', '2022-07-16 23:24:16'),
(81, 6, 'Kincangwetan', '#bcff0a', '2022-07-16 23:25:10', '2022-07-16 23:25:10'),
(82, 6, 'Klagen Serut', '#574878', '2022-07-16 23:26:22', '2022-07-16 23:26:22'),
(83, 6, 'Kwangsen', '#8eeac0', '2022-07-16 23:27:08', '2022-07-16 23:27:08'),
(84, 6, 'Metesih', '#b69baa', '2022-07-16 23:27:49', '2022-07-16 23:27:49'),
(85, 6, 'Ngetrep', '#0e2168', '2022-07-16 23:28:26', '2022-07-16 23:28:26'),
(86, 6, 'Sambirejo', '#fb9a91', '2022-07-16 23:28:56', '2022-07-16 23:28:56'),
(88, 6, 'Teguhan', '#3b7290', '2022-07-16 23:31:01', '2022-07-16 23:31:01'),
(89, 6, 'Wayut', '#d5051e', '2022-07-16 23:32:33', '2022-07-16 23:32:33'),
(90, 7, 'Bodag', '#fc5c1b', '2022-07-16 23:35:35', '2022-07-16 23:35:35'),
(91, 7, 'Bolo', '#25a4f6', '2022-07-16 23:36:29', '2022-07-16 23:36:29'),
(92, 7, 'Cermo', '#7a0281', '2022-07-16 23:37:09', '2022-07-16 23:37:09'),
(93, 7, 'Kare', '#7c87e0', '2022-07-16 23:37:52', '2022-07-16 23:37:52'),
(94, 7, 'Kepel', '#6a4390', '2022-07-16 23:38:24', '2022-07-16 23:38:24'),
(95, 7, 'Kuwiran', '#fec4db', '2022-07-16 23:40:10', '2022-07-16 23:40:10'),
(96, 7, 'Morang', '#c97e31', '2022-07-16 23:41:49', '2022-07-16 23:41:49'),
(97, 7, 'Randualas', '#bc0a72', '2022-07-16 23:42:33', '2022-07-16 23:42:33'),
(98, 6, 'Sukolilo', '#53091d', '2022-07-16 23:51:09', '2022-07-16 23:51:09'),
(99, 8, 'Bacem', '#30acce', '2022-07-16 23:57:23', '2022-07-16 23:57:23'),
(100, 8, 'Balerejo', '#0906aa', '2022-07-16 23:57:56', '2022-07-16 23:57:56'),
(101, 8, 'Kebonsari', '#edeed8', '2022-07-16 23:58:34', '2022-07-16 23:58:34'),
(102, 8, 'Kedondong', '#4debce', '2022-07-16 23:59:12', '2022-07-16 23:59:12'),
(103, 8, 'Krandegan', '#a6067f', '2022-07-17 00:00:17', '2022-07-17 00:00:17'),
(104, 8, 'Mojorejo', '#664f01', '2022-07-17 00:00:54', '2022-07-17 00:00:54'),
(105, 8, 'Palur', '#76d6b2', '2022-07-17 00:01:32', '2022-07-17 00:01:32'),
(106, 8, 'Pucanganom', '#774c24', '2022-07-17 00:02:11', '2022-07-17 00:02:11'),
(107, 8, 'Rejosari', '#9786bd', '2022-07-17 00:02:50', '2022-07-17 00:02:50'),
(108, 8, 'Sidorejo', '#f1b01e', '2022-07-17 00:03:26', '2022-07-17 00:03:26'),
(109, 8, 'Singgahan', '#d11613', '2022-07-17 00:04:02', '2022-07-17 00:04:02'),
(110, 8, 'Sukorejo', '#7458e8', '2022-07-17 00:05:30', '2022-07-17 00:05:30'),
(111, 8, 'Tambakmas', '#3efb06', '2022-07-17 00:06:17', '2022-07-17 00:06:17'),
(112, 8, 'Tanjungrejo', '#0b43f7', '2022-07-17 00:06:55', '2022-07-17 00:06:55'),
(113, 9, 'Bagi', '#6c8bcf', '2022-07-17 00:08:30', '2022-07-17 00:08:30'),
(114, 9, 'Banjarsari', '#eaf9fa', '2022-07-17 00:09:14', '2022-07-17 00:09:14'),
(115, 9, 'Betek', '#e6e9da', '2022-07-17 00:09:47', '2022-07-17 00:09:47'),
(116, 9, 'Dempelan', '#a2dc4a', '2022-07-17 00:11:24', '2022-07-17 00:11:24'),
(117, 9, 'Dimong', '#4089f8', '2022-07-17 00:12:04', '2022-07-17 00:12:04'),
(118, 9, 'Gunung Sari', '#444d16', '2022-07-17 00:12:43', '2022-07-17 00:12:43'),
(119, 9, 'Hutan', '#22d408', '2022-07-17 00:14:29', '2022-07-17 00:14:29'),
(120, 9, 'Nglames', '#616a0b', '2022-07-17 00:15:40', '2022-07-17 00:15:40'),
(121, 9, 'Sendangrejo', '#686334', '2022-07-17 00:16:20', '2022-07-17 00:16:20'),
(122, 9, 'Sirapan', '#9d4079', '2022-07-17 00:16:54', '2022-07-17 00:16:54'),
(123, 9, 'Sumberejo', '#a51c3e', '2022-07-17 00:17:29', '2022-07-17 00:17:29'),
(124, 9, 'Tanjungrejo', '#fef5d0', '2022-07-17 00:18:08', '2022-07-17 00:18:08'),
(125, 9, 'Tiron', '#2f0573', '2022-07-17 00:18:51', '2022-07-17 00:18:51'),
(126, 9, 'Tulungrejo', '#94b875', '2022-07-17 00:19:28', '2022-07-17 00:19:28'),
(127, 10, 'Bangunsari', '#3192a9', '2022-07-17 00:21:07', '2022-07-17 00:21:07'),
(128, 10, 'Blabakan', '#73fbfa', '2022-07-17 00:21:48', '2022-07-17 00:21:48'),
(129, 10, 'Danau/Waduk', '#fd9814', '2022-07-17 00:23:43', '2022-07-17 00:23:43'),
(130, 10, 'Darmorejo', '#89aabc', '2022-07-17 00:24:53', '2022-07-17 00:24:53'),
(131, 10, 'Kaliabu', '#462586', '2022-07-17 00:25:40', '2022-07-17 00:25:40'),
(132, 10, 'Kaligunting', '#f5f5a2', '2022-07-17 00:28:42', '2022-07-17 00:28:42'),
(133, 10, 'Kebonagung', '#20b5db', '2022-07-17 00:29:29', '2022-07-17 00:29:29'),
(134, 10, 'Klecorejo', '#35726a', '2022-07-17 00:30:18', '2022-07-17 00:30:18'),
(135, 10, 'Krajan', '#8226b4', '2022-07-17 00:30:50', '2022-07-17 00:30:50'),
(136, 10, 'Kuncen', '#f8fe75', '2022-07-17 00:31:20', '2022-07-17 00:31:20'),
(137, 10, 'Mejayan', '#392b9c', '2022-07-17 00:31:53', '2022-07-17 00:31:53'),
(138, 10, 'Ngampel', '#3be034', '2022-07-17 00:32:27', '2022-07-17 00:32:27'),
(139, 10, 'Pandeyan', '#cbed5f', '2022-07-17 00:33:01', '2022-07-17 00:33:01'),
(140, 10, 'Sidodadi', '#a4089a', '2022-07-17 00:33:41', '2022-07-17 00:33:41'),
(141, 10, 'Wonorejo', '#3141a2', '2022-07-17 00:34:26', '2022-07-17 00:34:26'),
(143, 11, 'Bulu', '#1e21e8', '2022-07-17 00:40:14', '2022-07-17 00:40:14'),
(144, 11, 'Dawuhan', '#45bcbd', '2022-07-17 00:41:11', '2022-07-17 00:41:11'),
(145, 11, 'Duren', '#db6902', '2022-07-17 00:41:51', '2022-07-17 00:41:51'),
(146, 11, 'Gandul', '#0ef375', '2022-07-17 00:42:32', '2022-07-17 00:42:32'),
(147, 11, 'Kedung Banteng', '#f8bbe3', '2022-07-17 00:43:25', '2022-07-17 00:43:25'),
(148, 11, 'Kedungmaron', '#4450b3', '2022-07-17 00:44:10', '2022-07-17 00:44:10'),
(149, 11, 'Kedungrejo', '#3b92ee', '2022-07-17 00:45:02', '2022-07-17 00:45:02'),
(150, 11, 'Kenongorejo', '#de6aef', '2022-07-17 00:46:31', '2022-07-17 00:46:31'),
(151, 11, 'Krebet', '#44cdbd', '2022-07-17 00:47:27', '2022-07-17 00:47:27'),
(152, 11, 'Luworo', '#b99d16', '2022-07-17 00:48:18', '2022-07-17 00:48:18'),
(153, 11, 'Muneng', '#78f4d0', '2022-07-17 00:49:03', '2022-07-17 00:49:03'),
(154, 11, 'Ngale', '#3928fb', '2022-07-17 00:49:41', '2022-07-17 00:49:41'),
(155, 11, 'Ngengor', '#281afc', '2022-07-17 00:50:11', '2022-07-17 00:50:11'),
(156, 11, 'Pilangkenceng', '#17fec1', '2022-07-17 00:51:02', '2022-07-17 00:51:02'),
(157, 11, 'Pulerejo', '#e524e6', '2022-07-17 00:51:45', '2022-07-17 00:51:45'),
(158, 11, 'Purworejo', '#02e3e5', '2022-07-17 00:52:21', '2022-07-17 00:52:21'),
(159, 11, 'Sumbergandu', '#28ba6f', '2022-07-17 00:53:13', '2022-07-17 00:53:13'),
(160, 11, 'Wonoayu', '#4ad08b', '2022-07-17 00:54:08', '2022-07-17 00:54:08'),
(161, 12, 'Bajulan', '#4a22c6', '2022-07-17 00:56:24', '2022-07-17 00:56:24'),
(162, 12, 'Bandungan', '#230003', '2022-07-17 00:57:13', '2022-07-17 00:57:13'),
(163, 12, 'Bener', '#2cb659', '2022-07-17 00:57:59', '2022-07-17 00:57:59'),
(164, 12, 'Bongsopotro', '#2fe5cc', '2022-07-17 00:59:00', '2022-07-17 00:59:00'),
(165, 12, 'Hutan', '#458494', '2022-07-17 00:59:36', '2022-07-17 00:59:36'),
(166, 12, 'Klangon', '#19c7a4', '2022-07-17 01:00:57', '2022-07-17 01:00:57'),
(167, 12, 'Klumutan', '#05ad21', '2022-07-17 01:01:33', '2022-07-17 01:01:33'),
(168, 12, 'Ngepeh', '#ed1133', '2022-07-17 01:02:03', '2022-07-17 01:02:03'),
(169, 12, 'Pajaran', '#fe1183', '2022-07-17 01:02:50', '2022-07-17 01:02:50'),
(170, 12, 'Sambirejo', '#17e2fa', '2022-07-17 01:03:37', '2022-07-17 01:03:37'),
(171, 12, 'Sidorejo', '#a6ed3b', '2022-07-17 01:04:13', '2022-07-17 01:04:13'),
(172, 12, 'Sugihwaras', '#9b7460', '2022-07-17 01:04:59', '2022-07-17 01:04:59'),
(173, 12, 'Sukorejo', '#5d4dc7', '2022-07-17 01:05:38', '2022-07-17 01:05:38'),
(174, 12, 'Sumberbendo', '#cc42d2', '2022-07-17 01:06:18', '2022-07-17 01:06:18'),
(175, 12, 'Sumbersari', '#6ab9fa', '2022-07-17 01:06:51', '2022-07-17 01:06:51'),
(176, 12, 'Tulung', '#9683f7', '2022-07-17 01:07:20', '2022-07-17 01:07:20'),
(177, 13, 'Bakur', '#05ac5b', '2022-07-17 01:09:58', '2022-07-17 01:09:58'),
(178, 13, 'Cabean', '#5f89d7', '2022-07-17 01:10:35', '2022-07-17 01:10:35'),
(179, 13, 'Golan', '#c3e4dd', '2022-07-17 01:11:16', '2022-07-17 01:11:16'),
(180, 13, 'Kajang', '#45c3cd', '2022-07-17 01:12:42', '2022-07-17 01:12:42'),
(181, 13, 'Kanung', '#13f155', '2022-07-17 01:13:33', '2022-07-17 01:13:33'),
(182, 13, 'Klumpit', '#8c8bf2', '2022-07-17 01:14:47', '2022-07-17 01:14:47'),
(183, 13, 'Krokeh', '#de48b6', '2022-07-17 01:15:25', '2022-07-17 01:15:25'),
(184, 13, 'Lebakayu', '#270865', '2022-07-17 01:16:00', '2022-07-17 01:16:00'),
(185, 13, 'Pucangrejo', '#ee0b07', '2022-07-17 01:16:41', '2022-07-17 01:16:41'),
(186, 13, 'Pule', '#d49fe7', '2022-07-17 01:17:11', '2022-07-17 01:17:11'),
(187, 13, 'Rejosari', '#feacf2', '2022-07-17 01:17:51', '2022-07-17 01:17:51'),
(188, 13, 'Sawahan', '#37a125', '2022-07-17 01:18:23', '2022-07-17 01:18:23'),
(189, 13, 'Sidomulyo', '#752b07', '2022-07-17 01:19:49', '2022-07-17 01:19:49'),
(190, 14, 'Bancong', '#cf4f20', '2022-07-17 01:22:51', '2022-07-17 01:22:51'),
(191, 14, 'Banyukambang', '#c90a9d', '2022-07-17 01:23:26', '2022-07-17 01:23:26'),
(192, 14, 'Buduran', '#bbbe5c', '2022-07-17 01:23:57', '2022-07-17 01:23:57'),
(193, 14, 'Danau / Waduk', '#759491', '2022-07-17 01:24:38', '2022-07-17 01:24:38'),
(194, 14, 'Jatirejo', '#be9965', '2022-07-17 01:25:12', '2022-07-17 01:25:12'),
(195, 14, 'Klitik', '#556572', '2022-07-17 01:25:49', '2022-07-17 01:25:49'),
(196, 14, 'Ngadirejo', '#99bcb6', '2022-07-17 01:26:20', '2022-07-17 01:26:20'),
(197, 14, 'Plumpungrejo', '#ce2bfc', '2022-07-17 01:27:36', '2022-07-17 01:27:36'),
(198, 14, 'Purwosari', '#cb5cbe', '2022-07-17 01:28:19', '2022-07-17 01:28:19'),
(199, 14, 'Sidomulyo', '#26f2b3', '2022-07-17 01:28:57', '2022-07-17 01:28:57'),
(200, 14, 'Wonoasri', '#1d907f', '2022-07-17 01:29:52', '2022-07-17 01:29:52'),
(201, 15, 'Bantengan', '#d60f7e', '2022-07-17 01:33:47', '2022-07-17 01:33:47'),
(202, 15, 'Brumbun', '#175117', '2022-07-17 01:34:27', '2022-07-17 01:34:27'),
(203, 15, 'Hutan', '#a3813a', '2022-07-17 01:35:19', '2022-07-17 01:35:19'),
(204, 15, 'Karangrejo', '#49d783', '2022-07-17 01:35:58', '2022-07-17 01:35:58'),
(205, 15, 'Kresek', '#92a902', '2022-07-17 01:36:27', '2022-07-17 01:36:27'),
(206, 15, 'Mojopurno', '#7f85b7', '2022-07-17 01:37:03', '2022-07-17 01:37:03'),
(207, 15, 'Mojorayung', '#4e220e', '2022-07-17 01:37:35', '2022-07-17 01:37:35'),
(208, 15, 'Manggut', '#0a4c39', '2022-07-17 01:38:02', '2022-07-17 01:38:02'),
(209, 15, 'Nglambangan', '#6d74cb', '2022-07-17 01:38:34', '2022-07-17 01:38:34'),
(210, 15, 'Nglanduk', '#bf98f3', '2022-07-17 01:39:10', '2022-07-17 01:39:10'),
(211, 15, 'Pilangrejo', '#6b5407', '2022-07-17 01:39:42', '2022-07-17 01:39:42'),
(212, 15, 'Sidorejo', '#23a776', '2022-07-17 01:40:29', '2022-07-17 01:40:29'),
(213, 15, 'Sobrah', '#0821d6', '2022-07-17 01:41:06', '2022-07-17 01:41:06'),
(214, 15, 'Tempursari', '#396823', '2022-07-17 01:41:46', '2022-07-17 01:41:46'),
(215, 15, 'Wungu', '#b81588', '2022-07-17 01:43:00', '2022-07-17 01:43:00'),
(216, 2, 'Joho', '#e40971', '2022-07-17 03:38:40', '2022-07-17 03:38:40'),
(217, 2, 'Hutan', '#4a9761', '2022-07-17 03:41:01', '2022-07-17 03:41:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_opds`
--

CREATE TABLE `master_opds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jenis_opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_opds`
--

INSERT INTO `master_opds` (`id`, `jenis_opd_id`, `nama`, `created_at`, `updated_at`) VALUES
(2, 2, 'Biro Administrasi Pimpinan', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(3, 2, 'Biro Perekonomian dan Administrasi Pembangunan', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(4, 2, 'Biro Pemerintahan dan Kesejahteraan Rakyat', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(5, 2, 'Biro Hukum', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(6, 2, 'Biro Organisasi dan Reformasi Birokrasi', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(7, 2, 'Biro Umum', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(8, 2, 'Biro Pengadaan Barang / Jasa', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(9, 3, 'Dinas Perpustakaan dan Kearsipan', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(10, 3, 'Dinas Pemberdayaan Perempuan, Perlindungan Anak, Kependudukan dan Keluarga Berencana', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(11, 3, 'Dinas Lingkungan Hidup dan Kehutanan', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(12, 3, 'Dinas Ketahanan Pangan', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(13, 3, 'Dinas Perumahan Rakyat dan Kawasan Permukiman', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(14, 3, 'Dinas Pekerjaan Umum dan Penataan Ruang', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(15, 3, 'Dinas Kesehatan', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(16, 3, 'Dinas Pendidikan dan Kebudayaan', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(17, 3, 'Dinas Kepemudaan dan Olah Raga', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(18, 3, 'Dinas Pertanian', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(19, 3, 'Dinas Kelautan dan Perikanan', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(20, 3, 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(21, 3, 'Dinas Pemberdayaan Masyarakat dan Desa', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(22, 3, 'Dinas Komunikasi, Informatika, Statistik dan Persandian', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(23, 3, 'Dinas Perhubungan', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(24, 3, 'Dinas Energi dan Sumberdaya Mineral', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(25, 3, 'Dinas Perindustrian dan Perdagangan', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(26, 3, 'Dinas Pariwisata', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(27, 3, 'Dinas Sosial', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(28, 3, 'Dinas Tenaga Kerja dan Transmigrasi', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(29, 3, 'Dinas Koperasi, Usaha Kecil dan Menengah', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(30, 4, 'Sekretariat Dewan Perwakilan Rakyat Daerah (DPRD) Provinsi Banten', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(31, 5, 'Inspektorat', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(32, 5, 'Badan Pengelolaan Keuangan dan Aset Daerah', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(33, 5, 'Badan Kepegawaian Daerah', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(34, 5, 'Badan Pengembangan Sumber Daya Manusia', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(35, 5, 'Badan Kesatuan Bangsa dan Politik', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(36, 5, 'Badan Pendapatan Daerah', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(37, 5, 'Badan Perencanaan Pembangunan Daerah', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(38, 5, 'Badan Penanggulangan Bencana Daerah', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(39, 5, 'Badan Penghubung', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(40, 6, 'Satuan Polisi Pamong Praja (SATPOLPP)', '2022-09-30 09:05:34', '2022-09-30 09:05:34'),
(41, 7, 'Rumah Sakit Umum Daerah', '2022-09-30 09:05:34', '2022-09-30 09:05:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2022_09_26_122548_create_akun_opds_table', 1),
(5, '2022_09_27_045958_create_opds_table', 1),
(6, '2022_09_27_133910_create_jenis_opds_table', 1),
(7, '2022_09_30_143811_create_master_opds_table', 1),
(8, '2022_10_03_041043_create_tahun_periodes_table', 1),
(9, '2022_10_10_104201_create_urusans_table', 1),
(10, '2022_10_10_104516_create_pivot_perubahan_urusans_table', 1),
(11, '2022_10_10_105120_create_programs_table', 1),
(12, '2022_10_10_105509_create_pivot_perubahan_programs_table', 1),
(13, '2022_10_10_105819_create_kegiatans_table', 1),
(14, '2022_10_10_110401_create_pivot_perubahan_kegiatans_table', 1),
(15, '2022_10_10_111031_create_sub_kegiatans_table', 1),
(16, '2022_10_10_111825_create_pivot_perubahan_sub_kegiatans_table', 1),
(41, '2022_10_13_040726_create_visis_table', 2),
(42, '2022_10_13_041117_create_pivot_perubahan_visis_table', 2),
(43, '2022_10_13_041342_create_misis_table', 2),
(44, '2022_10_13_041649_create_pivot_perubahan_misis_table', 2),
(45, '2022_10_13_041901_create_tujuans_table', 2),
(46, '2022_10_13_042303_create_pivot_perubahan_tujuans_table', 2),
(47, '2022_10_13_042432_create_sasarans_table', 2),
(48, '2022_10_13_043559_create_pivot_perubahan_sasarans_table', 2),
(54, '2022_10_18_185914_create_pivot_program_kegiatan_renstras_table', 4),
(55, '2022_10_19_125429_create_target_rp_pertahun_programs_table', 5),
(56, '2022_10_30_081155_create_renstra_kegiatans_table', 6),
(57, '2022_10_30_091041_create_pivot_opd_rentra_kegiatans_table', 7),
(58, '2022_10_30_154522_create_target_rp_pertahun_renstra_kegiatans_table', 8),
(59, '2022_11_13_054641_create_program_indikator_kinerjas_table', 9),
(60, '2022_11_13_075947_create_kegiatan_indikator_kinerjas_table', 10),
(61, '2022_11_13_080217_create_sub_kegiatan_indikator_kinerjas_table', 10),
(62, '2022_11_13_142447_create_opd_program_indikator_kinerjas_table', 11),
(63, '2022_11_13_143339_create_program_target_satuan_rp_realisasis_table', 11),
(64, '2022_11_14_182021_create_tujuan_indikator_kinerjas_table', 12),
(66, '2022_11_14_185349_create_tujuan_target_satuan_rp_realisasis_table', 13),
(67, '2022_11_15_063514_create_sasaran_indikator_kinerjas_table', 14),
(68, '2022_11_15_063825_create_sasaran_target_satuan_rp_realisasis_table', 14),
(69, '2022_10_13_044742_create_program_rpjmds_table', 15),
(70, '2022_10_13_045336_create_pivot_opd_program_rpjmds_table', 16),
(71, '2022_10_16_190130_create_pivot_sasaran_indikator_program_rpjmds_table', 17),
(72, '2022_11_15_210719_create_tujuan_pds_table', 18),
(73, '2022_11_15_211244_create_pivot_perubahan_tujuan_pds_table', 18),
(74, '2022_11_15_211553_create_tujuan_pd_indikator_kinerjas_table', 18),
(75, '2022_11_15_212156_create_tujuan_pd_target_satuan_rp_realisasis_table', 18),
(76, '2022_11_15_212441_create_sasaran_pds_table', 18),
(77, '2022_11_15_212958_create_pivot_perubahan_sasaran_pds_table', 18),
(78, '2022_11_15_213257_create_sasaran_pd_indikator_kinerjas_table', 18),
(79, '2022_11_15_213638_create_sasaran_pd_target_satuan_rp_realisasis_table', 18),
(80, '2022_11_16_150502_create_opd_kegiatan_indikator_kinerjas_table', 19),
(81, '2022_11_16_151230_create_kegiatan_target_satuan_rp_realisasis_table', 19);

-- --------------------------------------------------------

--
-- Struktur dari tabel `misis`
--

CREATE TABLE `misis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `kabupaten_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `misis`
--

INSERT INTO `misis` (`id`, `visi_id`, `kode`, `deskripsi`, `kabupaten_id`, `tahun_perubahan`, `created_at`, `updated_at`) VALUES
(1, 1, '1', 'Mewujudkan rasa aman bagi seluruh Masyarakat dan aparatur pemerintah Kabupaten Madiun', 62, '2020', '2022-10-13 21:32:24', '2022-10-13 21:32:24'),
(2, 1, '2', 'Mewujudkan aparatur pemerintah yang profesional untuk meningkatkan pelayanan publik', 62, '2020', '2022-10-13 21:35:27', '2022-10-13 21:35:27'),
(3, 1, '3', 'Meningkatkan pembangunan ekonomi yang mandiri berbasis agrobisnis, agroindustri dan pariwisata yang berkelanjutan', 62, '2020', '2022-10-13 21:36:34', '2022-10-13 21:36:34'),
(4, 1, '4', 'Meningkatkan kesejahteraan yang berkeadilan', 62, '2020', '2022-10-13 21:37:11', '2022-10-13 21:37:11'),
(5, 1, '5', 'Mewujudkan masyarakat berakhlak mulia dengan menin...', 62, '2020', '2022-10-13 21:38:12', '2022-10-13 21:38:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `negaras`
--

CREATE TABLE `negaras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `negaras`
--

INSERT INTO `negaras` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(62, 'Indonesia', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `opds`
--

CREATE TABLE `opds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` longtext COLLATE utf8mb4_unicode_ci,
  `negara_id` bigint(20) UNSIGNED DEFAULT NULL,
  `provinsi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kabupaten_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kecamatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kelurahan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opd_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `opds`
--

INSERT INTO `opds` (`id`, `nama`, `no_hp`, `alamat`, `negara_id`, `provinsi_id`, `kabupaten_id`, `kecamatan_id`, `kelurahan_id`, `foto`, `opd_id`, `created_at`, `updated_at`) VALUES
(1, 'Dinas Kebudayaan, Pariwisata, Pemuda, Dan Olahraga Kota Madiun', '123456789012', 'Jl. Udowo, Kartoharjo, Kec. Kartoharjo, Kota Madiun, Jawa Timur 63117', 62, 5, 62, NULL, NULL, '635a28685adf2-221027.jpg', 16, '2022-10-27 06:42:48', '2022-10-27 06:42:48'),
(2, 'Biro Administrasi Pimpinan Madiun', '123456789012', 'Jl. Pahlawan No. 110 Surabaya Jawa Timur', 62, 5, 62, NULL, NULL, '635c5a20198ea-221029.jpg', 2, '2022-10-28 22:39:28', '2022-10-28 22:39:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `opd_kegiatan_indikator_kinerjas`
--

CREATE TABLE `opd_kegiatan_indikator_kinerjas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kegiatan_indikator_kinerja_id` bigint(20) UNSIGNED DEFAULT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `opd_kegiatan_indikator_kinerjas`
--

INSERT INTO `opd_kegiatan_indikator_kinerjas` (`id`, `kegiatan_indikator_kinerja_id`, `opd_id`, `created_at`, `updated_at`) VALUES
(1, 1129, 16, '2022-11-16 10:28:09', '2022-11-16 10:28:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `opd_program_indikator_kinerjas`
--

CREATE TABLE `opd_program_indikator_kinerjas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_indikator_kinerja_id` bigint(20) UNSIGNED DEFAULT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `opd_program_indikator_kinerjas`
--

INSERT INTO `opd_program_indikator_kinerjas` (`id`, `program_indikator_kinerja_id`, `opd_id`, `created_at`, `updated_at`) VALUES
(1, 743, 16, '2022-11-15 09:52:25', '2022-11-15 09:52:25'),
(2, 1345, 16, '2022-11-16 04:14:18', '2022-11-16 04:14:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_opd_rentra_kegiatans`
--

CREATE TABLE `pivot_opd_rentra_kegiatans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rentra_kegiatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pagu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_kegiatans`
--

CREATE TABLE `pivot_perubahan_kegiatans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kegiatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_aturan` enum('Sebelum Perubahan','Sesudah Perubahan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_perubahan_kegiatans`
--

INSERT INTO `pivot_perubahan_kegiatans` (`id`, `kegiatan_id`, `program_id`, `kode`, `deskripsi`, `tahun_perubahan`, `status_aturan`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, 110, 58, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:22:17', '2022-11-12 12:22:17'),
(2, 1, 1, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(3, 2, 1, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(4, 3, 1, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(5, 4, 1, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(6, 5, 1, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(7, 6, 1, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(8, 7, 1, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(9, 8, 1, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(10, 9, 1, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(11, 10, 2, '1', 'Pengelolaan Pendidikan Sekolah Dasar', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(12, 11, 2, '2', 'Pengelolaan Pendidikan Sekolah Menengah Pertama', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(13, 12, 2, '3', 'Pengelolaan Pendidikan Anak Usia Dini (PAUD)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(14, 13, 2, '4', 'Pengelolaan Pendidikan Nonformal/Kesetaraan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(15, 14, 348, '1', 'Penetapan Kurikulum Muatan Lokal Pendidikan Dasar', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(16, 15, 348, '2', 'Penetapan Kurikulum Muatan Lokal Pendidikan Anak Usia Dini dan Pendidikan Nonforma', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(17, 16, 349, '1', 'Pemerataan Kuantitas dan Kualitas Pendidik dan Tenaga Kependidikan bagi Satuan Pendidikan Dasar, PAUD, dan Pendidikan Nonformal/Kesetaraan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(18, 77, 43, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(19, 78, 43, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(20, 79, 43, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(21, 80, 43, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(22, 81, 43, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(23, 82, 43, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(24, 83, 43, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(25, 84, 43, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(26, 85, 43, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(27, 86, 44, '1', 'Pengelolaan dan Pengembangan Sistem Penyediaan Air Minum (SPAM) di Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(28, 88, 356, '1', 'Pengelolaan dan Pengembangan Sistem Penyediaan Air Minum (SPAM) di Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(29, 89, 357, '1', 'Pengelolaan dan Pengembangan Sistem Air Limbah Domestik dalam Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(30, 90, 45, '1', 'Pengelolaan dan Pengembangan Sistem Drainase yang Terhubung Langsung dengan Sungai dalam Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(31, 91, 358, '1', 'Penyelenggaraan Infrastruktur pada Permukiman di Kawasan Strategis Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(32, 92, 341, '1', 'Penyelenggaraan Bangunan Gedung di Wilayah Daerah Kabupaten/Kota, Pemberian Izin Mendirikan Bangunan (IMB) dan Sertifikat Laik Fungsi Bangunan Gedung', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(33, 93, 359, '1', 'Penyelenggaraan Penataan Bangunan dan Lingkungannya di Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(34, 94, 360, '1', 'Penyelenggaraan Jalan Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(35, 95, 361, '1', 'Penyelenggaraan Pelatihan Tenaga Terampil Konstruksi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(36, 96, 361, '2', 'Penyelenggaraan Sistem Informasi Jasa Konstruksi Cakupan Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(37, 97, 361, '3', 'Penerbitan Izin Usaha Jasa Konstruksi Nasional (Non Kecil dan Kecil', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(38, 98, 361, '4', 'Pengawasan Tertib Usaha, Tertib Penyelenggaraan dan Tertib Pemanfaatan Jasa Konstruksi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(39, 99, 362, '1', 'Penetapan Rencana Tata Ruang Wilayah (RTRW) dan Rencana Rinci Tata Ruang (RRTR) Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(40, 100, 362, '2', 'Koordinasi dan Sinkronisasi Perencanaan Tata Ruang Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(41, 101, 362, '3', 'Koordinasi dan Sinkronisasi Pemanfaatan Ruang Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(42, 102, 362, '4', 'Koordinasi dan Sinkronisasi Pengendalian Pemanfaatan Ruang Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(43, 193, 98, '1', 'Pelembagaan Pengarusutamaan Gender (PUG) pada Lembaga Pemerintah Kewenangan Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(44, 194, 98, '2', 'Pemberdayaan Perempuan Bidang Politik, Hukum, Sosial, dan Ekonomi pada Organisasi Kemasyarakatan Kewenangan Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(45, 195, 98, '3', 'Penguatan dan Pengembangan Lembaga Penyedia Layanan Pemberdayaan Perempuan Kewenangan Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(46, 196, 375, '1', 'Pencegahan Kekerasan terhadap Perempuan Lingkup Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(47, 197, 375, '2', 'Penyediaan Layanan Rujukan Lanjutan bagi Perempuan Korban Kekerasan yang Memerlukan Koordinasi Kewenangan Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(48, 198, 375, '3', 'Penguatan dan Pengembangan Lembaga Penyedia Layanan Perlindungan Perempuan Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(49, 199, 376, '1', 'Pelembagaan PHA pada Lembaga Pemerintah, Nonpemerintah, dan Dunia Usaha Kewenangan Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(50, 200, 376, '2', 'Penguatan dan Pengembangan Lembaga Penyedia Layanan Peningkatan Kualitas Hidup Anak Kewenangan Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(51, 201, 520, '1', 'Pencegahan Kekerasan terhadap Anak yang Melibatkan para Pihak Lingkup Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(52, 202, 520, '2', 'Penyediaan Layanan bagi Anak yang Memerlukan Perlindungan Khusus yang Memerlukan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(53, 203, 520, '3', 'Penguatan dan Pengembangan Lembaga Penyedia Layanan bagi Anak yang Memerlukan Perlindungan Khusus Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(54, 124, 64, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(55, 125, 64, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(56, 126, 64, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(57, 127, 64, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(58, 128, 64, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(59, 129, 64, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(60, 130, 64, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(61, 131, 64, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(62, 132, 64, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(63, 133, 65, '1', 'Penanganan Gangguan Ketenteraman dan Ketertiban Umum dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(64, 134, 65, '2', 'Penegakan Peraturan Daerah Kabupaten/Kota dan Peraturan Bupati/Wali Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(65, 135, 65, '3', 'Pembinaan Penyidik Pegawai Negeri Sipil (PPNS) Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(66, 136, 366, '1', 'Pencegahan, Pengendalian, Pemadaman, Penyelamatan, dan Penanganan Bahan Berbahaya dan Beracun Kebakaran dalam Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(67, 137, 366, '2', 'Inspeksi Peralatan Proteksi Kebakaran', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(68, 141, 367, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(69, 142, 367, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(70, 143, 367, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(71, 144, 367, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(72, 145, 367, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(73, 146, 367, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(74, 147, 367, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(75, 148, 367, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(76, 149, 367, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(77, 150, 368, '1', 'Pelayanan Informasi Rawan Bencana Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(78, 151, 368, '2', 'Pelayanan Pencegahan dan Kesiapsiagaan terhadap Bencana', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(79, 152, 368, '3', 'Pelayanan Penyelamatan dan Evakuasi Korban Bencana', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(80, 153, 368, '4', 'Penataan Sistem Dasar Penanggulangan Bencana', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(81, 17, 10, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(82, 18, 10, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(83, 19, 10, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(84, 20, 10, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(85, 21, 10, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(86, 22, 10, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(87, 23, 10, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(88, 24, 10, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(89, 25, 10, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(90, 26, 10, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(91, 27, 11, '1', 'Penyediaan Fasilitas Pelayanan Kesehatan untuk UKM dan UKP Kewenangan Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(92, 28, 11, '2', 'Penyediaan Layanan Kesehatan untuk UKM dan UKP Rujukan Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(93, 29, 11, '3', 'Penyelenggaraan Sistem Informasi Kesehatan secara Terintegrasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(94, 30, 11, '4', 'Penerbitan Izin Rumah Sakit Kelas C dan D serta Fasilitas Pelayanan Kesehatan Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(95, 31, 26, '1', 'Pemberian Izin Praktik Tenaga Kesehatan di Wilayah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(96, 32, 26, '2', 'Perencanaan Kebutuhan dan Pendayagunaan Sumberdaya Manusia Kesehatan untuk UKP dan UKM di Wilayah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(97, 33, 26, '3', 'Pengembangan Mutu dan Peningkatan Kompetensi Teknis Sumber Daya Manusia Kesehatan Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(98, 34, 350, '1', 'Pemberian Izin Apotek, Toko Obat, Toko Alat Kesehatan dan Optikal, Usaha Mikro Obat Tradisional (UMOT)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(99, 35, 350, '2', 'Pemberian Sertifikat Produksi untuk Sarana Produksi Alat Kesehatan Kelas 1 tertentu dan Perbekalan Kesehatan Rumah Tangga Kelas 1 Tertentu Perusahaan Rumah Tangga', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(100, 36, 350, '3', 'Penerbitan Sertifikat Produksi Pangan Industri Rumah Tangga dan Nomor P-IRT sebagai Izin Produksi, untuk Produk Makanan Minuman Tertentu yang dapat Diproduksi oleh Industri Rumah Tangga', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(101, 37, 350, '4', 'Penerbitan Sertifikat Laik Higiene Sanitasi Tempat Pengelolaan Makanan (TPM) antara lain Jasa Boga, Rumah Makan/Restoran dan Depot Air Minum (DAM)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(102, 38, 350, '5', 'Penerbitan Stiker Pembinaan pada Makanan Jajanan dan Sentra Makanan Jajanan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(103, 39, 350, '6', 'Pemeriksaan dan Tindak Lanjut Hasil Pemeriksaan Post Market pada Produksi dan Produk Makanan Minuman Industri Rumah Tangga', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(104, 40, 12, '1', 'Advokasi, Pemberdayaan, Kemitraan, Peningkatan Peran serta Masyarakat dan Lintas Sektor Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(105, 41, 12, '2', 'Pelaksanaan Sehat dalam rangka Promotif Preventif Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(106, 42, 12, '3', 'Pelaksanaan Sehat dalam rangka Promotif Preventif Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(107, 43, 351, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(108, 44, 351, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(109, 45, 351, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(110, 46, 351, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(111, 47, 351, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(112, 48, 351, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(113, 49, 351, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(114, 50, 351, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(115, 51, 351, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(116, 52, 351, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(117, 53, 352, '1', 'Penyediaan Fasilitas Pelayanan Kesehatan untuk UKM dan UKP Kewenangan Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(118, 54, 352, '2', 'Penyediaan Layanan Kesehatan untuk UKM dan UKP Rujukan Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(119, 55, 352, '3', 'Penyelenggaraan Sistem Informasi Kesehatan secara Terintegrasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(120, 56, 352, '4', 'Penerbitan Izin Rumah Sakit Kelas C dan D serta Fasilitas Pelayanan Kesehatan Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(121, 57, 353, '1', 'Pemberian Izin Praktik Tenaga Kesehatan di Wilayah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(122, 58, 353, '2', 'Perencanaan Kebutuhan dan Pendayagunaan Sumberdaya Manusia Kesehatan untuk UKP dan UKM di Wilayah Kabupaten/Kot', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(123, 59, 353, '3', 'Pengembangan Mutu dan Peningkatan Kompetensi Teknis Sumber Daya Manusia Kesehatan Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(124, 60, 332, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(125, 61, 332, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(126, 62, 332, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(127, 63, 332, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(128, 64, 332, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(129, 65, 332, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(130, 66, 332, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(131, 67, 332, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(132, 68, 332, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(133, 69, 332, '10', 'Peningkatan Pelayanan BLUD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(134, 70, 354, '1', 'Penyediaan Fasilitas Pelayanan Kesehatan untuk UKM dan UKP Kewenangan Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(135, 71, 354, '2', 'Penyediaan Layanan Kesehatan untuk UKM dan UKP Rujukan Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(136, 72, 354, '3', 'Penyelenggaraan Sistem Informasi Kesehatan secara Terintegrasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(137, 73, 354, '4', 'Penerbitan Izin Rumah Sakit Kelas C dan D serta Fasilitas Pelayanan Kesehatan Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(138, 74, 355, '1', 'Pemberian Izin Praktik Tenaga Kesehatan di Wilayah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(139, 75, 355, '2', 'Perencanaan Kebutuhan dan Pendayagunaan Sumberdaya Manusia Kesehatan untuk UKP dan UKM di Wilayah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(140, 76, 355, '3', 'Pengembangan Mutu dan Peningkatan Kompetensi Teknis Sumber Daya Manusia Kesehatan Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(141, 103, 58, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(142, 104, 58, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(143, 105, 58, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(144, 106, 58, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(145, 107, 58, '5', 'Administrasi Kepegawaian Perangkat Daera', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(146, 108, 58, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(147, 109, 58, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(148, 110, 58, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daera', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(149, 111, 59, '1', 'Pendataan Penyediaan dan Rehabilitasi Rumah Korban Bencana atau Relokasi Program Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(150, 112, 59, '2', 'Sosialisasi dan Persiapan Penyediaan dan Rehabilitasi Rumah Korban Bencana atau Relokasi Program Kabupaten/Kot', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(151, 113, 59, '3', 'Pembangunan dan Rehabilitasi Rumah Korban Bencana atau Relokasi Program Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(152, 114, 59, '4', 'Pendistribusian dan Serah Terima Rumah bagi Korban Bencana atau Relokasi Program Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(153, 115, 59, '5', 'Pembinaan Pengelolaan Rumah Susun Umum dan/atau Rumah Khusus', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(154, 116, 59, '6', 'Penerbitan Izin Pembangunan dan Pengembangan Perumahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(155, 117, 59, '7', 'Penerbitan Sertifikat Kepemilikan Bangunan Gedung (SKGB)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(156, 118, 363, '1', 'Penerbitan Izin Pembangunan dan Pengembangan Kawasan Permukiman', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(157, 119, 363, '2', 'Penataan dan Peningkatan Kualitas Kawasan Permukiman Kumuh dengan Luas di Bawah 10 (sepuluh) Ha', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(158, 120, 363, '3', 'Peningkatan Kualitas Kawasan Permukiman Kumuh dengan Luas di Bawah 10 (sepuluh) Ha', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(159, 121, 364, '1', 'Pencegahan Perumahan dan Kawasan Permukiman Kumuh pada Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(160, 122, 365, '1', 'Urusan Penyelenggaraan PSU Perumahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(161, 123, 60, '1', 'Sertifikasi dan Registrasi bagi Orang atau Badan Hukum yang Melaksanakan Perancangan dan Perencanaan Rumah serta Perencanaan Prasarana, Sarana dan Utilitas Umum PSU Tingkat Kemampuan Kecil', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(162, 172, 88, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(163, 173, 88, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(164, 174, 88, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(165, 175, 88, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(166, 176, 88, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(167, 177, 88, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(168, 178, 88, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(169, 179, 88, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(170, 180, 88, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(171, 181, 372, '1', 'Pelaksanaan Pelatihan berdasarkan Unit Kompetensi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(172, 182, 372, '2', 'Pembinaan Lembaga Pelatihan Kerja Swasta', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(173, 183, 372, '3', 'Perizinan dan Pendaftaran Lembaga Pelatihan Kerja', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(174, 184, 372, '4', 'Konsultansi Produktivitas pada Perusahaan Keci', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(175, 185, 372, '5', 'Pengukuran Produktivitas Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(176, 186, 373, '1', 'Pelayanan Antarkerja di Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(177, 187, 373, '2', 'Penerbitan Izin Lembaga Penempatan Tenaga Kerja Swasta (LPTKS) dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(178, 188, 373, '3', 'Pengelolaan Informasi Pasar Kerja', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(179, 189, 373, '4', 'Pelindungan PMI (Pra dan Purna Penempatan) di Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(180, 190, 373, '5', 'Penerbitan Perpanjangan IMTA yang Lokasi Kerja dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(181, 191, 374, '1', 'Pengesahan Peraturan Perusahaan dan Pendaftaran Perjanjian Kerja Bersama untuk Perusahaan yang hanya Beroperasi dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(182, 192, 374, '2', 'Pencegahan dan Penyelesaian Perselisihan Hubungan Industrial, Mogok Kerja dan Penutupan Perusahaan di Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(183, 154, 82, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(185, 155, 82, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(186, 156, 82, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(187, 157, 82, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daera', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(188, 158, 82, '5', 'Administrasi Kepegawaian Perangkat Daera', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(189, 159, 82, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(190, 160, 82, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(191, 161, 82, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(192, 162, 82, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(193, 163, 83, '1', 'Pemberdayaan Sosial Komunitas Adat Terpencil (KAT)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(194, 164, 83, '2', 'Pengumpulan Sumbangan dalam Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(195, 165, 83, '3', 'Pengembangan Potensi Sumber Kesejahteraan Sosial Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(196, 166, 369, '1', 'Rehabilitasi Sosial Dasar Penyandang Disabilitas Terlantar, Anak Terlantar, Lanjut Usia Terlantar, serta Gelandangan Pengemis di Luar Panti Sosial', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(197, 167, 369, '2', 'Rehabilitasi Sosial Penyandang Masalah Kesejahteraan Sosial (PMKS) Lainnya Bukan Korban HIV/AIDS dan NAPZA di Luar Panti Sosi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(198, 168, 370, '1', 'Pemeliharaan Anak-Anak Terlantar', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(199, 169, 370, '2', 'Pengelolaan Data Fakir Miskin Cakupan Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(200, 170, 371, '1', 'Perlindungan Sosial Korban Bencana Alam dan Sosial Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(201, 171, 371, '2', 'Penyelenggaraan Pemberdayaan Masyarakat terhadap Kesiapsiagaan Bencana Kabupaten/Kot', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(202, 220, 380, '1', 'Penyelesaian Masalah Ganti Kerugian dan Santunan Tanah untuk Pembangunan oleh Pemerintah Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(203, 221, 381, '1', 'Penyelesaian Masalah Tanah Kosong', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(204, 223, 382, '1', 'Penyelesaian Sengketa Tanah Garapan dalam Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(205, 224, 383, '1', 'Penggunaan Tanah yang Hamparannya dalam satu Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(206, 225, 384, '1', 'Penerbitan Izin Membuka Tanah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(207, 226, 441, '1', 'Penetapan Subjek dan Objek Redistribusi Tanah serta Ganti Kerugian Tanah Kelebihan Maksimum dan Tanah Absentee dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(208, 227, 441, '2', 'Penetapan Ganti Kerugian Tanah Kelebihan Maksimum dan Tanah Absentee Lintas Daerah Kabupaten/Kota dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(209, 204, 104, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(210, 205, 104, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(211, 206, 104, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(212, 207, 104, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(213, 208, 104, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(214, 209, 104, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(215, 210, 104, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(216, 211, 104, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(217, 212, 104, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(218, 213, 377, '1', 'Penyediaan dan Penyaluran Pangan Pokok atau Pangan Lainnya sesuai dengan Kebutuhan Daerah Kabupaten/Kota dalam rangka Stabilisasi Pasokan dan Harga Pangan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(219, 214, 377, '2', 'Pengelolaan dan Keseimbangan Cadangan Pangan Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(220, 215, 377, '3', 'Penentuan Harga Minimum Daerah untuk Pangan Lokal yang Tidak Ditetapkan oleh Pemerintah Pusat dan Pemerintah Provinsi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(221, 216, 377, '4', 'Pelaksanaan Pencapaian Target Konsumsi Pangan Perkapita/Tahun sesuai dengan Angka Kecukupan Gizi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(222, 217, 378, '1', 'Penyusunan Peta Kerentanan dan Ketahanan Pangan Kecamatan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(223, 218, 378, '2', 'Penanganan Kerawanan Pangan Kewenangan Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(224, 219, 379, '1', 'Pelaksanaan Pengawasan Keamanan Pangan Segar Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(225, 252, 120, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(226, 253, 120, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(227, 254, 120, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(228, 255, 120, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(229, 256, 120, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(230, 257, 120, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(231, 258, 120, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(232, 259, 120, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(233, 260, 120, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(234, 261, 121, '1', 'Pelayanan Pendaftaran Penduduk', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(235, 262, 121, '2', 'Penataan Pendaftaran Penduduk', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(236, 263, 121, '3', 'Penyelenggaraan Pendaftaran Penduduk', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(237, 264, 121, '4', 'Pembinaan dan Pengawasan Penyelenggaraan Pendaftaran Penduduk', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(238, 265, 122, '1', 'Pelayanan Pencatatan Sipil', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(239, 266, 122, '2', 'Penyelenggaraan Pencatatan Sipil', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(240, 267, 122, '3', 'Pembinaan dan Pengawasan Penyelenggaraan Pencatatan Sipil', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(241, 268, 392, '1', 'Pengumpulan Data Kependudukan dan Pemanfaatan dan Penyajian Database Kependudukan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(242, 269, 392, '2', 'Penataan Pengelolaan Informasi Administrasi Kependudukan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(243, 270, 392, '3', 'Penyelenggaraan Pengelolaan Informasi Administrasi Kependudukan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(244, 271, 392, '4', 'Pembinaan dan Pengawasan Pengelolaan Informasi Administrasi Kependudukan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(245, 228, 111, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(246, 229, 111, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(247, 230, 111, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(248, 231, 111, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(249, 232, 111, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(250, 233, 111, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(251, 234, 111, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(252, 235, 111, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(253, 236, 111, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:18', '2022-11-12 12:22:18'),
(254, 237, 112, '1', 'Rencana Perlindungan dan Pengelolaan Lingkungan Hidup (RPPLH) Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(255, 238, 112, '2', 'Penyelenggaraan Kajian Lingkungan Hidup Strategis (KLHS) Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(256, 239, 385, '1', 'Pencegahan Pencemaran dan/atau Kerusakan Lingkungan Hidup Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(257, 240, 385, '2', 'Penanggulangan Pencemaran dan/atau Kerusakan Lingkungan Hidup Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(258, 241, 385, '3', 'Pemulihan Pencemaran dan/atau Kerusakan Lingkungan Hidup Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(259, 242, 386, '1', 'Pengelolaan Keanekaragaman Hayati Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(260, 243, 387, '1', 'Penyimpanan Sementara Limbah B3', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(261, 244, 387, '2', 'Pengumpulan Limbah B3 dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(262, 245, 113, '1', 'Pembinaan dan Pengawasan terhadap Usaha dan/atau Kegiatan yang Izin Lingkungan dan Izin PPLH diterbitkan oleh Pemerintah Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(263, 246, 388, '1', 'Penyelenggaraan Pendidikan, Pelatihan, dan Penyuluhan Lingkungan Hidup untuk Lembaga Kemasyarakatan Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(264, 247, 389, '1', 'Pemberian Penghargaan Lingkungan Hidup Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(265, 248, 390, '1', 'Penyelesaian Pengaduan Masyarakat di Bidang Perlindungan dan Pengelolaan Lingkungan Hidup (PPLH) Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(266, 249, 391, '1', 'Pengelolaan Sampah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(267, 250, 391, '2', 'Penerbitan Izin Pendaurulangan Sampah/Pengelolaan Sampah, Pengangkutan Sampah dan Pemrosesan Akhir Sampah yang Diselenggarakan oleh Swasta', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(268, 251, 391, '3', 'Pembinaan dan Pengawasan Pengelolaan Sampah yang Diselenggarakan oleh Pihak Swasta', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(269, 284, 396, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(270, 285, 396, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(271, 286, 396, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(272, 287, 396, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(273, 288, 396, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(274, 289, 396, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(275, 290, 396, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(276, 291, 396, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(277, 292, 396, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(278, 293, 397, '1', 'Pemaduan dan Sinkronisasi Kebijakan Pemerintah Daerah Provinsi dengan Pemerintah Daerah Kabupaten/Kota dalam rangka Pengendalian Kuantitas Penduduk', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(279, 294, 397, '2', 'Pemetaan Perkiraan Pengendalian Penduduk Cakupan Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(280, 295, 398, '1', 'Pelaksanaan Advokasi, Komunikasi, Informasi dan Edukasi (KIE) Pengendalian Penduduk dan KB sesuai Kearifan Budaya Loka', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(281, 296, 398, '2', 'Pendayagunaan Tenaga Penyuluh KB/Petugas Lapangan KB (PKB/PLKB)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(282, 297, 398, '3', 'Pengendalian dan Pendistribusian Kebutuhan Alat dan Obat Kontrasepsi serta Pelaksanaan Pelayanan KB di Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(283, 298, 398, '4', 'Pemberdayaan dan Peningkatan Peran serta Organisasi Kemasyarakatan Tingkat Daerah Kabupaten/Kota dalam Pelaksanaan Pelayanan dan Pembinaan Kesertaan Ber-KB', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(284, 299, 399, '1', 'Pelaksanaan Pembangunan Keluarga melalui Pembinaan Ketahanan dan Kesejahteraan Keluarga', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(285, 300, 399, '2', 'Pelaksanaan dan Peningkatan Peran Serta Organisasi Kemasyarakatan Tingkat Daerah Kabupaten/ Kota dalam Pembangunan Keluarga Melalui Pembinaan Ketahanan dan Kesejahteraan Keluarga', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(286, 320, 141, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daera', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(287, 321, 141, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(288, 322, 141, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(289, 323, 141, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(290, 324, 141, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(291, 325, 141, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(292, 326, 141, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(293, 327, 141, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19');
INSERT INTO `pivot_perubahan_kegiatans` (`id`, `kegiatan_id`, `program_id`, `kode`, `deskripsi`, `tahun_perubahan`, `status_aturan`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(294, 328, 141, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(295, 329, 142, '1', 'Pengelolaan Informasi dan Komunikasi Publik Pemerintah Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(296, 330, 400, '1', 'Pengelolaan Nama Domain yang telah Ditetapkan oleh Pemerintah Pusat dan Sub Domain di Lingkup Pemerintah Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(297, 331, 400, '2', 'Pengelolaan e-government Di Lingkup Pemerintah Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(298, 272, 128, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(299, 273, 128, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(300, 274, 128, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(301, 275, 128, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(302, 276, 128, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(303, 277, 128, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(304, 278, 128, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(305, 279, 128, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(306, 280, 128, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(307, 281, 129, '1', 'Penyelenggaraan Penataan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(308, 283, 394, '1', 'Pembinaan dan Pengawasan Penyelenggaraan Administrasi Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(309, 349, 161, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(310, 350, 161, '2', 'Administrasi Keuangan Perangkat Daera', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(311, 351, 161, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(312, 352, 161, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(313, 353, 161, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(314, 354, 161, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(315, 355, 161, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(316, 356, 161, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(317, 357, 161, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(318, 358, 406, '1', 'Penyelenggaraan Promosi Penanaman Modal yang menjadi Kewenangan Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(319, 359, 407, '1', 'Pelayanan Perizinan dan Non Perizinan secara Terpadu Satu Pintu dibidang Penanaman Modal yang menjadi Kewenangan Daerah Kabupaten/ Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(320, 360, 408, '1', 'Pengendalian Pelaksanaan Penanaman Modal yang menjadi Kewenangan Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(321, 361, 162, '1', 'Penetapan Pemberian Fasilitas/Insentif Dibidang Penanaman Modal yang menjadi Kewenangan Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(322, 362, 162, '2', 'Pembuatan Peta Potensi Investasi Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(323, 363, 163, '1', 'Pengelolaan Data dan Informasi Perizinan dan Non Perizinan yang Terintegrasi pada Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(324, 380, 410, '1', 'Penyelenggaraan Statistik Sektoral di Lingkup Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(325, 383, 412, '1', 'Penyelenggaraan Statistik Sektoral di Lingkup Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(326, 384, 412, '2', 'Pelestarian Kesenian Tradisional yang Masyarakat Pelakunya dalam Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(327, 385, 413, '1', 'Pembinaan Sejarah Lokal dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(328, 386, 414, '1', 'Penetapan Cagar Budaya Peringkat Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(329, 387, 414, '2', 'Pengelolaan Cagar Budaya Peringkat Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(330, 388, 414, '3', 'Penerbitan Izin membawa Cagar Budaya ke Luar Daerah Kabupaten/Kota dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(331, 400, 417, '1', 'Pengelolaan Arsip Dinamis Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(332, 401, 417, '2', 'Pengelolaan Arsip Statis Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(333, 402, 417, '3', 'Pengelolaan Simpul Jaringan Informasi Kearsipan Nasional Tingkat Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(334, 403, 418, '1', 'Pemusnahan Arsip Dilingkungan Pemerintah Daerah Kabupaten/Kota yang Memiliki Retensi di Bawah 10 (sepuluh) Tahun', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(335, 404, 418, '2', 'Perlindungan dan Penyelamatan Arsip Akibat Bencana yang Berskala Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(336, 405, 418, '3', 'Penyelamatan Arsip Perangkat Daerah Kabupaten/Kota yang Digabung dan/atau Dibubarkan, dan Pemekaran Daerah Kecamatan dan Desa/Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(337, 406, 418, '4', 'Autentikasi Arsip Statis dan Arsip Hasil Alih Media Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(338, 407, 418, '5', 'Pencarian Arsip Statis Kabupaten/Kota yang Dinyatakan Hilang', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(339, 418, 422, '1', 'Pengelolaan Daya Tarik Wisata Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(340, 419, 422, '2', 'Pengelolaan Kawasan Strategis Pariwisata Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(341, 420, 422, '3', 'Pengelolaan Destinasi Pariwisata Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(342, 421, 422, '4', 'Penetapan Tanda Daftar Usaha Pariwisata Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(343, 422, 423, '1', 'Pemasaran Pariwisata Dalam dan Luar Negeri Daya Tarik, Destinasi dan Kawasan Strategis Pariwisata Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(344, 423, 424, '1', 'Pelaksanaan Peningkatan Kapasitas Sumber Daya Manusia Pariwisata dan Ekonomi Kreatif Tingkat Dasar', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(345, 424, 424, '2', 'Pengembangan Kapasitas Pelaku Ekonomi Kreatif', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(346, 455, 430, '1', 'Penerbitan Izin Pengelolaan Pasar Rakyat, Pusat Perbelanjaan, dan Izin Usaha Toko Swalayan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(347, 456, 430, '2', 'Penerbitan Tanda Daftar Gudang', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(348, 457, 430, '3', 'Penerbitan Surat Tanda Pendaftaran Waralaba (STPW) untuk Penerima Waralaba dari Waralaba Dalam Negeri', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(349, 458, 430, '4', 'Penerbitan Surat Tanda Pendaftaran Waralaba (STPW) untuk Penerima Waralaba Lanjutan dari Waralaba Luar Negeri', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(350, 459, 430, '5', 'Penerbitan Surat Izin Usaha Perdagangan Minuman Beralkohol Golongan B dan C untuk Pengecer dan Penjual Langsung Minum di Tempat', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(351, 460, 430, '6', 'Pengendalian Fasilitas Penyimpanan Bahan Berbahaya dan Pengawasan Distribusi, Pengemasan dan Pelabelan Bahan Berbahaya di Tingkat Daerah Kabupaten/ Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(352, 461, 430, '7', 'Penerbitan Surat Keterangan Asal (bagi Daerah Kabupaten/Kota yang Telah Ditetapkan Sebagai Instansi Penerbit Surat Keterangan Asal)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(353, 462, 431, '1', 'Pembangunan dan Pengelolaan Sarana Distribusi Perdagangan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(354, 463, 431, '2', 'Pembinaan terhadap Pengelola Sarana Distribusi Perdagangan Masyarakat di Wilayah Kerjanya', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(355, 464, 432, '1', 'Menjamin Ketersediaan Barang Kebutuhan Pokok dan Barang Penting di Tingkat Daerah Kabupaten/ Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(356, 465, 432, '2', 'Pengendalian Harga, dan Stok Barang Kebutuhan Pokok dan Barang Penting di Tingkat Pasar Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(357, 466, 432, '3', 'Pengawasan Pupuk dan Pestisida Bersubsidi di Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(358, 467, 433, '1', 'Penyelenggaraan Promosi Dagang melalui Pameran Dagang dan Misi Dagang bagi Produk Ekspor Unggulan yang terdapat pada 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(359, 468, 434, '1', 'Pelaksanaan Metrologi Legal berupa, Tera, Tera Ulang, dan Pengawasan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(360, 469, 435, '1', 'Pelaksanaan Promosi, Pemasaran dan Peningkatan Penggunaan Produk Dalam Negeri', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(361, 301, 134, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(362, 302, 134, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(363, 303, 134, '3', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(364, 304, 134, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(365, 305, 134, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(366, 306, 134, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(367, 307, 134, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(368, 308, 134, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(369, 309, 134, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(370, 310, 135, '1', 'Penetapan Rencana Induk Jaringan LLAJ Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(371, 311, 135, '2', 'Penyediaan Perlengkapan Jalan di Jalan Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(372, 312, 135, '3', 'Pengelolaan Terminal Penumpang Tipe C', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(373, 313, 135, '4', 'Penerbitan Izin Penyelenggaraan dan Pembangunan Fasilitas Parkir', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(374, 314, 135, '5', 'Pengujian Berkala Kendaraan Bermotor', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(375, 315, 135, '6', 'Pelaksanaan Manajemen dan Rekayasa Lalu Lintas untuk Jaringan Jalan Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(376, 316, 135, '7', 'Persetujuan Hasil Analisis Dampak Lalu Lintas (Andalalin) untuk Jalan Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(377, 317, 135, '8', 'Audit dan Inspeksi Keselamatan LLAJ di Jalan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(378, 318, 135, '9', 'Penyediaan Angkutan Umum untuk Jasa Angkutan Orang dan/atau Barang antar Kota dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(379, 319, 135, '10', 'Penetapan Kawasan Perkotaan untuk Pelayanan Angkutan Perkotaan yang Melampaui Batas 1 (satu) Daerah Kabupaten/Kota dalam 1 (Satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(380, 473, 439, '1', 'Pengembangan Satuan Permukiman pada Tahap Kemandirian', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(381, 332, 147, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(382, 333, 147, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(383, 334, 147, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(384, 335, 147, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(385, 336, 147, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(386, 337, 147, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(387, 338, 147, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(388, 339, 147, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(389, 340, 147, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(390, 341, 401, '1', 'Pemeriksaan dan Pengawasan Koperasi, Koperasi Simpan Pinjam/Unit Simpan Pinjam Koperasi yang Wilayah Keanggotaannya dalam Daerah Kabupaten/ Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(391, 342, 402, '1', 'Penilaian Kesehatan Koperasi Simpan Pinjam/Unit Simpan Pinjam Koperasi yang Wilayah Keanggotaannya dalam 1 (satu) Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(392, 343, 403, '1', 'Pendidikan dan Latihan Perkoperasian bagi Koperasi yang Wilayah Keanggotaan dalam Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(393, 344, 149, '1', 'Pemberdayaan dan Perlindungan Koperasi yang Keanggotaannya dalam Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(394, 345, 404, '1', 'Pemberdayaan Usaha Mikro yang Dilakukan melalui Pendataan, Kemitraan, Kemudahan Perizinan, Penguatan Kelembagaan dan Koordinasi dengan Para Pemangku Kepentingan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(395, 346, 405, '1', 'Pengembangan Usaha Mikro dengan Orientasi Peningkatan Skala Usaha menjadi Usaha Kecil', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(396, 347, 148, '1', 'Pengelolaan Informasi dan Komunikasi Publik Pemerintah Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(397, 364, 167, '1', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(398, 365, 167, '2', 'Administrasi Keuangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(399, 366, 167, '3', 'Administrasi Barang Milik Daerah pada Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(400, 367, 167, '4', 'Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(401, 368, 167, '5', 'Administrasi Kepegawaian Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(402, 369, 167, '6', 'Administrasi Umum Perangkat Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(403, 370, 167, '7', 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(404, 371, 167, '8', 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(405, 372, 167, '9', 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(406, 373, 409, '1', 'Pembinaan dan Pengembangan Olahraga Pendidikan pada Jenjang Pendidikan yang menjadi Kewenangan Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(407, 374, 409, '2', 'Penyelenggaraan Kejuaraan Olahraga Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(408, 375, 409, '3', 'Pembinaan dan Pengembangan Olahraga Prestasi Tingkat Daerah Provinsi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(409, 376, 409, '4', 'Pembinaan dan Pengembangan Organisasi Olahraga', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(410, 377, 409, '5', 'Pembinaan dan Pengembangan Olahraga Rekreasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(411, 378, 168, '1', 'Penyadaran, Pemberdayaan, dan Pengembangan Pemuda dan Kepemudaan terhadap Pemuda Pelopor Kabupaten/Kota, Wirausaha Muda Pemula, dan Pemuda Kader Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(412, 379, 168, '2', 'Pemberdayaan dan Pengembangan Organisasi Kepemudaan Tingkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(413, 381, 411, '1', 'Penyelenggaraan Persandian untuk Pengamanan Informasi Pemerintah Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19'),
(414, 382, 411, '2', 'Penetapan Pola Hubungan Komunikasi Sandi Antar Perangkat Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:22:19', '2022-11-12 12:22:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_misis`
--

CREATE TABLE `pivot_perubahan_misis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `misi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kabupaten_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_perubahan_misis`
--

INSERT INTO `pivot_perubahan_misis` (`id`, `misi_id`, `kabupaten_id`, `visi_id`, `kode`, `deskripsi`, `tahun_perubahan`, `created_at`, `updated_at`) VALUES
(1, 1, 62, 1, '1', 'Mewujudkan rasa aman bagi seluruh masyarakat dan aparatur pemerintah Kabupaten Madiun', '2021', '2022-10-13 22:03:06', '2022-10-13 22:03:06'),
(2, 2, 62, 1, '2', 'Mewujudkan aparatur pemerintah yang profesional untuk meningkatkan pelayanan publik', '2021', '2022-10-13 22:06:08', '2022-10-13 22:06:08'),
(3, 3, 62, 1, '3', 'Meningkatkan pembangunan ekonomi yang mandiri berbasis agrobisnis, agroindustri dan pariwisata yang berkelanjutan', '2021', '2022-10-13 22:08:25', '2022-10-13 22:08:25'),
(4, 4, 62, 1, '4', 'Meningkatkan kesejahteraan yang berkeadilan', '2021', '2022-10-13 22:08:36', '2022-10-13 22:08:36'),
(5, 5, 62, 1, '5', 'Mewujudkan masyarakat berakhlak mulia dengan meningkatkan kehidupan beragama, menguatkan budaya dan mengedepankan kearifan lokal', '2020', '2022-10-13 22:09:02', '2022-10-13 22:09:02'),
(6, 5, 62, 1, '5', 'Mewujudkan masyarakat berakhlak mulia dengan meningkatkan kehidupan beragama, menguatkan budaya dan mengedepankan kearifan lokal', '2021', '2022-10-13 22:09:18', '2022-10-13 22:09:18'),
(7, 1, 62, 1, '1', 'Mewujudkan rasa aman bagi seluruh masyarakat dan aparatur pemerintah Kabupaten Madiun', '2022', '2022-10-13 22:09:35', '2022-10-13 22:09:35'),
(8, 2, 62, 1, '2', 'Mewujudkan aparatur pemerintah yang profesional untuk meningkatkan pelayanan publik', '2022', '2022-10-13 22:09:41', '2022-10-13 22:09:41'),
(9, 3, 62, 1, '3', 'Meningkatkan pembangunan ekonomi yang mandiri berbasis agrobisnis, agroindustri dan pariwisata yang berkelanjutan', '2022', '2022-10-13 22:09:47', '2022-10-13 22:09:47'),
(10, 4, 62, 1, '4', 'Meningkatkan kesejahteraan yang berkeadilan', '2022', '2022-10-13 22:10:01', '2022-10-13 22:10:01'),
(11, 5, 62, 1, '5', 'Mewujudkan masyarakat berakhlak mulia dengan meningkatkan kehidupan beragama, menguatkan budaya dan mengedepankan kearifan lokal', '2022', '2022-10-13 22:10:06', '2022-10-13 22:10:06'),
(12, 1, 62, 1, '1', 'Mewujudkan rasa aman bagi seluruh masyarakat dan aparatur pemerintah Kabupaten Madiun', '2023', '2022-10-13 22:10:23', '2022-10-13 22:10:23'),
(13, 2, 62, 1, '2', 'Mewujudkan aparatur pemerintah yang profesional untuk meningkatkan pelayanan publik', '2023', '2022-10-13 22:10:29', '2022-10-13 22:10:29'),
(14, 3, 62, 1, '3', 'Meningkatkan pembangunan ekonomi yang mandiri berbasis agrobisnis, agroindustri dan pariwisata yang berkelanjutan', '2023', '2022-10-13 22:10:34', '2022-10-13 22:10:34'),
(15, 4, 62, 1, '4', 'Meningkatkan kesejahteraan yang berkeadilan', '2023', '2022-10-13 22:10:40', '2022-10-13 22:10:40'),
(16, 5, 62, 1, '5', 'Mewujudkan masyarakat berakhlak mulia dengan meningkatkan kehidupan beragama, menguatkan budaya dan mengedepankan kearifan lokal', '2023', '2022-10-13 22:10:46', '2022-10-13 22:10:46'),
(17, 5, 62, 1, '5', 'Mewujudkan masyarakat berakhlak mulia dengan meningkatkan kehidupan beragama, menguatkan budaya dan mengedepankan kearifan lokal', '2023', '2022-11-14 07:01:14', '2022-11-14 07:01:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_programs`
--

CREATE TABLE `pivot_perubahan_programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `urusan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_aturan` enum('Sebelum Perubahan','Sesudah Perubahan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_perubahan_programs`
--

INSERT INTO `pivot_perubahan_programs` (`id`, `program_id`, `urusan_id`, `kode`, `deskripsi`, `tahun_perubahan`, `status_aturan`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, 256, 37, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(2, 1, 39, '1', 'Program Pelayanan Administrasi Perkantoran', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(3, 2, 39, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(4, 3, 39, '15', 'Program Pendidikan Anak Usia Dini', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(5, 4, 39, '18', 'Program Pendidikan Non Formal', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(6, 5, 39, '20', 'Program Peningkatan Mutu Pendidik dan Tenaga Kependidikan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(7, 7, 39, '24', 'Program Pendidikan SD', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(8, 8, 39, '25', 'Program Pendidikan SMP', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(9, 6, 39, '22', 'Program Manajemen Pelayanan Pendidikan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(10, 10, 40, '1', 'Program Pelayanan Administrasi Perkantoran', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(11, 11, 40, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(12, 12, 40, '5', 'Program Peningkatan Kapasitas Sumber Daya Aparatur', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(13, 13, 40, '6', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(14, 14, 40, '16', 'Program Upaya Kesehatan Masyarakat', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(15, 15, 40, '25', 'Program pengadaan, peningkatan dan perbaikan sarana dan prasarana puskesmas/ puskemas pembantu dan jaringannya', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(16, 19, 40, '39', 'Program Peningkatan Sumber Daya Kesehatan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(17, 18, 40, '38', 'Program pelayanan Kesehatan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(18, 22, 40, '42', 'Program Penyediaan Bantuan Operasional Kesehatan (BOK)', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(19, 23, 40, '48', 'Program Pembinaan Lingkungan Sosial bidang Kesehatan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(20, 24, 40, '01car', 'Program Pelayanan Administrasi Perkantoran', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(21, 25, 40, '02car', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(22, 26, 40, '03car', 'Program peningkatan disiplin aparatur', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(23, 27, 40, '05car', 'Program Peningkatan Kapasitas Sumber Daya Aparatur', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(24, 28, 40, '33car', 'Program Peningkatan Pelayanan Rumah Sakit', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(25, 29, 40, '44car', 'Program Penatalaksanaan Keuangan dan Akuntansi', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(26, 30, 40, '45car', 'Program Penyelenggaraan Pelayanan Medis dan Keperawatan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(27, 31, 40, '46car', 'Program Penyelenggaraan Penunjang Medis dan Non Medis', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(28, 43, 1, '1', 'Program Pelayanan Administrasi Perkantoran', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(29, 44, 1, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(30, 46, 1, '15', 'Program Pembangunan Jalan dan Jembatan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(31, 48, 1, '23', 'Program Peningkatan Sarana dan Prasarana Kebinamargaan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(32, 49, 1, '24', 'Program Pengembangan dan Pengelolaan Jaringan Irigasi, Rawa dan Jaringan Pengairan lainnya', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(33, 51, 1, '31', 'Program Peningkatan Sarana dan Prasarana Pemerintah', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(34, 52, 1, '37', 'Program Jasa Konstruksi', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(35, 50, 1, '28', 'Program Pengendalian Banjir', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(36, 53, 1, '38', 'Program Integrated Participatory Development and Management Irrigation Program (IPDMIP)', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(37, 54, 1, '39', 'Program Pembangunan Infrastruktur Perdesaan/kelurahan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(38, 55, 1, '42', 'Program Peningkatan Jalan dan Jembatan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(39, 58, 2, '1', 'Program Pelayanan Administrasi Perkantoran', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(40, 1, 39, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(41, 2, 39, '2', 'Program Pengelolaan Pendidikan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(42, 10, 40, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(43, 11, 40, '2', 'Program Pemenuhan Upaya Kesehatan Perorangan dan Upaya Kesehatan Masyarakat', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(44, 26, 40, '3', 'Program Peningkatan Kapasitas Sumber Daya Manusia Kesehatan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(45, 12, 40, '5', 'Program Pemberdayaan Masyarakat Bidang Kesehatan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(46, 332, 40, '01b', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(47, 43, 1, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(48, 44, 1, '2', 'Program Pengelolaan Sumber Daya Air (SDA)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(49, 45, 1, '6', 'Program Pengelolaan dan Pengembangan Sistem Drainase', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(50, 341, 1, '8', 'Program Penataan Bangunan Gedung', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(51, 58, 2, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(52, 59, 2, '2', 'Program Pengembangan Perumahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(53, 60, 2, '6', 'Program Peningkatan Pelayanan Sertifikasi, Kualifikasi, Klasifikasi, Dan Registrasi Bidang Perumahan Dan Kawasan Permukiman', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(54, 64, 3, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(55, 65, 3, '2', 'Program Peningkatan Ketenteraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(56, 82, 4, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(57, 83, 4, '2', 'Program Pemberdayaan Sosial', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(58, 88, 5, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(59, 98, 6, '2', 'Program Pengarusutamaan Gender Dan Pemberdayaan Perempuan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(60, 104, 7, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(61, 111, 9, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(62, 112, 9, '2', 'Program Perencanaan Lingkungan Hidup', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(63, 113, 9, '6', 'Program Pembinaan dan Pengawasan Terhadap Izin Lingkungan dan Izin Perlindungan dan Pengelolaan Lingkungan Hidup (PPLH)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(64, 120, 10, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(65, 121, 10, '2', 'Program Pendaftaran Penduduk', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(66, 122, 10, '3', 'Program Pencatatan Sipil', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(67, 128, 11, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(68, 129, 11, '2', 'Program Penataan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(69, 134, 13, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(70, 135, 13, '2', 'Program Penyelenggaraan Lalu Lintas dan Angkutan Jalan (LLAJ)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(71, 141, 14, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(72, 142, 14, '2', 'Program Informasi dan Komunikasi Publik', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(73, 147, 15, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(74, 149, 15, '6', 'Program Pemberdayaan dan Perlindungan Koperasi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(75, 148, 15, '2', 'Program Pelayanan Izin Usaha Simpan Pinjam', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(76, 161, 16, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(77, 162, 16, '2', 'Program Pengembangan Iklim Penanaman Modal', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(78, 163, 16, '6', 'Program Pengelolaan Data dan Sistem Informasi Penanaman Modal', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(79, 167, 17, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(80, 168, 17, '2', 'Program Pengembangan Kapasitas Daya Saing Kepemudaan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(81, 179, 25, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(82, 246, 30, '3', 'Program Perekonomian dan Pembangunan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(83, 248, 31, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(84, 249, 31, '2', 'Program Dukungan Pelaksanaan Tugas dan Fungsi DPRD', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(85, 191, 32, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(86, 192, 32, '2', 'Program Perencanaan, Pengendalian, dan Evaluasi Pembangunan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(87, 199, 33, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(88, 200, 33, '2', 'Program Pengelolaan Keuangan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(89, 211, 34, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(90, 212, 34, '2', 'Program Kepegawaian Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(91, 217, 36, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(92, 255, 37, '01bal', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(93, 256, 37, '02bal', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(94, 257, 37, '06bal', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(95, 259, 37, '01dag', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(96, 261, 37, '01dol', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(97, 262, 37, '02dol', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(98, 264, 37, '01gem', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(99, 265, 37, '02gem', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(100, 267, 37, '01jiw', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(101, 268, 37, '02jiw', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(102, 269, 37, '03jiw', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(103, 270, 37, '01keb', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(104, 271, 37, '02keb', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(105, 273, 37, '01kar', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(106, 274, 37, '02kar', 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(107, 276, 37, '01mad', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(108, 277, 37, '02mad', 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(109, 279, 37, '01mej', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(110, 280, 37, '02mej', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(111, 282, 37, '01pil', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(112, 283, 37, '02pil', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(113, 284, 37, '03pil', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(114, 288, 37, '01sar', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(115, 289, 37, '02sar', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(116, 290, 37, '03sar', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(117, 285, 37, '01saw', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(118, 286, 37, '02saw', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(119, 291, 37, '01wun', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(120, 292, 37, '02wun', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(121, 294, 37, '01won', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(122, 295, 37, '02won', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(123, 296, 37, '06won', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(124, 1, 39, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(125, 2, 39, '2', 'Program Pengelolaan Pendidikan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(126, 348, 39, '3', 'Program Pengembangan Kurikulum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(127, 349, 39, '4', 'Program Pendidik dan Tenaga Kependidikan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(128, 10, 40, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(129, 11, 40, '2', 'Program Pemenuhan Upaya Kesehatan Perorangan dan Upaya Kesehatan Masyarakat', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(130, 26, 40, '3', 'Program Peningkatan Kapasitas Sumber Daya Manusia Kesehatan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(131, 350, 40, '4', 'Program Sediaan Farmasi, Alat Kesehatan dan Makanan Minuman', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(132, 12, 40, '5', 'Program Pemberdayaan Masyarakat Bidang Kesehatan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(133, 351, 40, '01a', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(134, 352, 40, '02a', 'Program Pemenuhan Upaya Kesehatan Perorangan dan Upaya Kesehatan Masyarakat', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(135, 353, 40, '03a', 'Program Peningkatan Kapasitas Sumber Daya Manusia Kesehatan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(136, 332, 40, '01b', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(137, 354, 40, '02b', 'Program Pemenuhan Upaya Kesehatan Perorangan dan Upaya Kesehatan Masyarakat', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(138, 355, 40, '03b', 'Program Peningkatan Kapasitas Sumber Daya Manusia Kesehatan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(139, 43, 1, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(140, 44, 1, '2', 'Program Pengelolaan Sumber Daya Air (SDA)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(141, 356, 1, '3', 'Program Pengelolaan dan Pengembangan Sistem Penyediaan Air Minum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(142, 357, 1, '5', 'Program Pengelolaan dan Pengembangan Sistem Air Limbah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(143, 45, 1, '6', 'Program Pengelolaan dan Pengembangan Sistem Drainase', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(144, 358, 1, '7', 'Program Pengembangan Permukiman', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(145, 341, 1, '8', 'Program Penataan Bangunan Gedung', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(146, 359, 1, '9', 'Program Penataan Bangunan dan Lingkungannya', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(147, 360, 1, '10', 'Program Penyelenggaraan Jalan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(148, 361, 1, '11', 'Program Pengembangan Jasa Konstruksi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(149, 362, 1, '12', 'Program Penyelenggaraan Penataan Ruang', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(150, 58, 2, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(151, 59, 2, '2', 'Program Pengembangan Perumahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(152, 363, 2, '3', 'Program Kawasan Permukiman', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(153, 364, 2, '4', 'Program Perumahan Dan Kawasan Permukiman Kumuh', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(154, 365, 2, '5', 'Program Peningkatan Prasarana, Sarana dan Utilitas Umum (PSU)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(155, 60, 2, '6', 'Program Peningkatan Pelayanan Sertifikasi, Kualifikasi, Klasifikasi, Dan Registrasi Bidang Perumahan Dan Kawasan Permukiman', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(156, 64, 3, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(157, 65, 3, '2', 'Program Peningkatan Ketenteraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(158, 366, 3, '4', 'Program Pencegahan, Penanggulangan, Penyelamatan Kebakaran dan Penyelamatan Non Kebakaran', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(159, 367, 3, '01a', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(160, 368, 3, '3', 'Program Penanggulangan Bencana', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(161, 82, 4, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(162, 83, 4, '2', 'Program Pemberdayaan Sosial', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(163, 369, 4, '4', 'Program Rehabilitasi Sosial', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(164, 370, 4, '5', 'Program Perlindungan dan Jaminan Sosial', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(165, 371, 4, '6', 'Program Penanganan Bencana', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(166, 88, 5, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(167, 372, 5, '3', 'Program Pelatihan Kerja dan Produktivitas Tenaga Kerja', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(168, 373, 5, '4', 'Program Penempatan Tenaga Kerja', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(169, 374, 5, '5', 'Program Hubungan Industrial', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(170, 98, 6, '2', 'Program Pengarusutamaan Gender Dan Pemberdayaan Perempuan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(171, 375, 6, '3', 'Program Perlindungan Perempuan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(172, 376, 6, '6', 'Program Pemenuhan Hak Anak (PHA)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(173, 104, 7, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(174, 377, 7, '3', 'Program Peningkatan Diversifikasi dan Ketahanan Pangan Masyarakat', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(175, 378, 7, '4', 'Program Penanganan Kerawanan Pangan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(176, 379, 7, '5', 'Program Pengawasan Keamanan Pangan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(177, 380, 8, '5', 'ProgramPenyelesaian Ganti Kerugian dan Santunan Tanah Untuk Pembangunan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(178, 381, 8, '8', 'Program Pengelolaan Tanah Kosong', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(179, 382, 8, '4', 'Program Penyelesaian Sengketa Tanah Garapan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(180, 383, 8, '10', 'Program Penatagunaan Tanah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(181, 384, 8, '9', 'Program Pengelolaan Izin Membuka Tanah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(182, 111, 9, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(183, 112, 9, '2', 'Program Perencanaan Lingkungan Hidup', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(184, 385, 9, '3', 'Program Pengendalian Pencemaran dan/atau Kerusakan Lingkungan Hidup', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(185, 386, 9, '4', 'Program Pengelolaan Keanekaragaman Hayati (KEHATI)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(186, 387, 9, '5', 'Program Pengendalian Bahan Berbahaya dan Beracun (B3) dan Limbah Bahan Berbahaya dan Beracun (Limbah B3)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(187, 113, 9, '6', 'Program Pembinaan dan Pengawasan Terhadap Izin Lingkungan dan Izin Perlindungan dan Pengelolaan Lingkungan Hidup (PPLH)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(188, 388, 9, '8', 'Program Peningkatan Pendidikan, Pelatihan dan Penyuluhan Lingkungan Hidup Untuk Masyarakat', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(189, 389, 9, '9', 'Program Penghargaan Lingkungan Hidup Untuk Masyarakat', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(190, 390, 9, '10', 'Program Penanganan Pengaduan Lingkungan Hidup', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(191, 391, 9, '11', 'Program Pengelolaan Persampahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(192, 120, 10, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(193, 121, 10, '2', 'Program Pendaftaran Penduduk', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(194, 122, 10, '3', 'Program Pencatatan Sipil', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(195, 392, 10, '4', 'Program Pengelolaan Informasi Administrasi Kependudukan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(196, 128, 11, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(197, 129, 11, '2', 'Program Penataan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(198, 393, 11, '3', 'Program Peningkatan Kerjasama Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(199, 394, 11, '4', 'Program Administrasi Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(200, 395, 11, '5', 'Program Pemberdayaan Lembaga Kemasyarakatan, Lembaga Adat dan Masyarakat Hukum Adat', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(201, 396, 12, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(202, 397, 12, '2', 'Program Pengendalian Penduduk', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(203, 398, 12, '3', 'Program Pembinaan Keluarga Berencana (KB)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(204, 399, 12, '4', 'Program Pemberdayaan dan Peningkatan Keluarga Sejahtera (KS)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(205, 134, 13, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(206, 135, 13, '2', 'Program Penyelenggaraan Lalu Lintas dan Angkutan Jalan (LLAJ)', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(207, 141, 14, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(208, 142, 14, '2', 'Program Informasi dan Komunikasi Publik', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(209, 400, 14, '3', 'Program Aplikasi Informatika', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(210, 147, 15, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(211, 401, 15, '3', 'Program Pengawasan dan Pemeriksaan Koperasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(212, 402, 15, '4', 'Program Penilaian Kesehatan KSP/USP Koperasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(213, 403, 15, '5', 'Program Pendidikan dan Latihan Perkoperasian', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(214, 149, 15, '6', 'Program Pemberdayaan dan Perlindungan Koperasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(215, 404, 15, '7', 'Program Pemberdayaan Usaha Menengah, Usaha Kecil, dan Usaha Mikro', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(216, 405, 15, '8', 'Program Pengembangan UMKM', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(217, 148, 15, '2', 'Program Pelayanan Izin Usaha Simpan Pinjam', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(218, 161, 16, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(219, 406, 16, '3', 'Program Promosi Penanaman Modal', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(220, 407, 16, '4', 'Program Pelayanan Penanaman Modal', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(221, 408, 16, '5', 'Program Pengendalian Pelaksanaan Penanaman Modal', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(222, 162, 16, '2', 'Program Pengembangan Iklim Penanaman Modal', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(223, 163, 16, '6', 'Program Pengelolaan Data dan Sistem Informasi Penanaman Modal', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(224, 167, 17, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(225, 409, 17, '3', 'Program Pengembangan Kapasitas Daya Saing Keolahragaan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(226, 168, 17, '2', 'Program Pengembangan Kapasitas Daya Saing Kepemudaan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(227, 410, 18, '2', 'Program Penyelenggaraan Statistik Sektoral', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(228, 411, 19, '2', 'Program Penyelenggaraan Persandian untuk Pengamanan Informasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(229, 412, 20, '2', 'Program Pengembangan Kebudayaan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(230, 413, 20, '4', 'Program Pembinaan Sejarah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(231, 414, 20, '5', 'Program Pelestarian dan Pengelolaan Cagar Budaya', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(232, 415, 21, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(233, 416, 21, '2', 'Program Pembinaan Perpustakaan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(234, 417, 22, '2', 'Program Pengelolaan Arsip', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(235, 418, 22, '3', 'Program Perlindungan Dan Penyelamatan Arsip', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(236, 419, 23, '4', 'Program Pengelolaan Perikanan Budidaya', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(237, 420, 23, '6', 'Program Pengolahan dan Pemasaran Hasil Perikanan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(238, 421, 23, '3', 'Program Pengelolaan Perikanan Tangkap', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(239, 422, 24, '2', 'Program Peningkatan Daya Tarik Destinasi Pariwisata', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(240, 423, 24, '3', 'Program Pemasaran Pariwisata', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(241, 424, 24, '5', 'Program Pengembangan Sumber Daya Pariwisata dan Ekonomi Kreatif', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(242, 179, 25, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(243, 425, 25, '02a', 'Program Penyediaan dan Pengembangan Sarana Pertanian', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(244, 426, 25, '4', 'Program Pengendalian Kesehatan Hewan dan Kesehatan Masyarakat Veteriner', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(245, 427, 25, '5', 'Program Pengendalian dan Penanggulangan Bencana Pertanian', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(246, 428, 25, '3', 'Program Penyediaan dan Pengembangan Prasarana Pertanian', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(247, 429, 25, '7', 'Program Penyuluhan Pertanian', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(248, 430, 27, '2', 'Program Perizinan dan Pendaftaran Perusahaan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(249, 431, 27, '3', 'Program Peningkatan Sarana Distribusi Perdagangan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(250, 432, 27, '4', 'Program Stabilitasi Harga Barang Kebutuhan Pokok dan Barang Penting', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(251, 433, 27, '5', 'Program Pengembangan Ekspor', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(252, 434, 27, '6', 'Program Standarisasi dan Perlindungan Konsumen', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(253, 435, 27, '7', 'Program Penggunaan Dan Pemasaran Produk Dalam Negeri', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(254, 436, 28, '2', 'Program Perencanaan dan Pembangunan Industri', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(255, 437, 28, '3', 'Program Pengendalian Izin Usaha Industri', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(256, 438, 28, '4', 'Program Pengelolaan Sistem Informasi Industri Nasional', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(257, 439, 29, '4', 'Program Pengembangan Kawasan Transmigrasi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(258, 440, 30, '01um', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(259, 441, 8, '6', 'Program Redistribusi Tanah, Serta Ganti Kerugian Program Tanah Kelebihan Maksimum Dan Tanah Absentee', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(260, 442, 30, '01bo1', 'Program Penunjang Urusan Pemerintahan Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(261, 443, 30, '01bp', 'Program Penunjang Urusan Pemerintahan Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(262, 444, 30, '02bh', 'Program Pemerintahan dan Kesejahteraan Rakyat', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(263, 524, 30, '02bk1', 'Program Pemerintahan dan Kesejahteraan Rakyat', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(264, 446, 30, '02bape', 'Program Pemerintahan dan Kesejahteraan Rakyat', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(265, 246, 30, '3', 'Program Perekonomian dan Pembangunan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(266, 447, 30, '02jas', 'Program Perekonomian dan Pembangunan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(267, 448, 30, '03Per', 'Program Perekonomian Dan Pembangunan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(268, 248, 31, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(269, 249, 31, '2', 'Program Dukungan Pelaksanaan Tugas dan Fungsi DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(270, 191, 32, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(271, 192, 32, '2', 'Program Perencanaan, Pengendalian, dan Evaluasi Pembangunan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(272, 449, 32, '3', 'Program Koordinasi dan Sinkronisasi Perencanaan Pembangunan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(273, 450, 35, '2', 'Program Penelitian dan Pengembangan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(274, 199, 33, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(275, 200, 33, '2', 'Program Pengelolaan Keuangan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(276, 451, 33, '3', 'Program Pengelolaan Barang Milik Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(277, 452, 33, '01pen', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(278, 453, 33, '4', 'Program Pengelolaan Pendapatan Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(279, 211, 34, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(280, 212, 34, '2', 'Program Kepegawaian Daerah', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(281, 217, 36, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(282, 454, 36, '02a', 'Program Penyelenggaraan Pengawasan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(283, 455, 36, '3', 'Program Perumusan Kebijakan, Pendampingan dan Asistensi', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(284, 456, 38, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(285, 457, 38, '2', 'Program Penguatan Ideologi Pancasila dan Karakter Kebangsaan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(286, 458, 38, '3', 'Program Peningkatan Peran Partai Politik dan Lembaga Pendidikan Melalui Pendidikan Politik dan Pengembangan Etika Serta Budaya Politik', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(287, 459, 38, '4', 'Program Pemberdayaan dan Pengawasan Organisasi Kemasyarakatan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(288, 460, 38, '6', 'Program Peningkatan Kewaspadaan Nasional dan Peningkatan Kualitas dan Fasilitasi Penanganan Konflik Sosial', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(289, 461, 38, '5', 'Program Pembinaan dan Pengembangan Ketahanan Ekonomi, Sosial, dan Budaya', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(290, 255, 37, '01bal', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(291, 256, 37, '02bal', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(292, 462, 37, '03bal', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(293, 463, 37, '04bal', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(294, 464, 37, '05bal', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(295, 257, 37, '06bal', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(296, 259, 37, '01dag', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(297, 465, 37, '02dag', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(298, 466, 37, '03dag', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(299, 467, 37, '04dag', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(300, 468, 37, '05dag', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(301, 469, 37, '06dag', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(302, 261, 37, '01dol', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(303, 262, 37, '02dol', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(304, 470, 37, '03dol', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(305, 471, 37, '04dol', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(306, 472, 37, '05dol', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(307, 473, 37, '06dol', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(308, 474, 37, '01geg', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(309, 475, 37, '02geg', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(310, 476, 37, '03geg', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(311, 477, 37, '04geg', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(312, 478, 37, '05geg', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(313, 479, 37, '06geg', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(314, 264, 37, '01gem', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(315, 265, 37, '02gem', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(316, 480, 37, '03gem', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(317, 481, 37, '04gem', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(318, 482, 37, '05gem', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(319, 483, 37, '06gem', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(320, 267, 37, '01jiw', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(321, 268, 37, '02jiw', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(322, 269, 37, '03jiw', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(323, 484, 37, '04jiw', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03');
INSERT INTO `pivot_perubahan_programs` (`id`, `program_id`, `urusan_id`, `kode`, `deskripsi`, `tahun_perubahan`, `status_aturan`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(324, 485, 37, '05jiw', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(325, 486, 37, '06jiw', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(326, 270, 37, '01keb', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(327, 271, 37, '02keb', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(328, 487, 37, '03keb', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(329, 488, 37, '04keb', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(330, 489, 37, '05keb', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(331, 490, 37, '06keb', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(332, 273, 37, '01kar', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(333, 274, 37, '02kar', 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(334, 491, 37, '03kar', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(335, 492, 37, '04kar', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(336, 493, 37, '05kar', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(337, 494, 37, '06kar', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(338, 276, 37, '01mad', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(339, 277, 37, '02mad', 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(340, 495, 37, '03mad', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(341, 496, 37, '04mad', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(342, 497, 37, '05mad', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(343, 498, 37, '06mad', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(344, 279, 37, '01mej', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(345, 280, 37, '02mej', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(346, 499, 37, '03mej', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(347, 500, 37, '04mej', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(348, 501, 37, '05mej', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(349, 502, 37, '06mej', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(350, 282, 37, '01pil', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(351, 283, 37, '02pil', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(352, 284, 37, '03pil', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(353, 503, 37, '04pil', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(354, 504, 37, '05pil', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(355, 505, 37, '06pil', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(356, 288, 37, '01sar', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(357, 289, 37, '02sar', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(358, 290, 37, '03sar', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(359, 506, 37, '04sar', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(360, 507, 37, '05sar', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(361, 508, 37, '06sar', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(362, 285, 37, '01saw', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(363, 286, 37, '02saw', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(364, 509, 37, '03saw', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(365, 510, 37, '04saw', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(366, 511, 37, '05saw', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(367, 512, 37, '06saw', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(368, 291, 37, '01wun', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(369, 292, 37, '02wun', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(370, 513, 37, '03wun', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(371, 514, 37, '04wun', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(372, 515, 37, '05wun', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(373, 516, 37, '06wun', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(374, 294, 37, '01won', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(375, 295, 37, '02won', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(376, 517, 37, '03won', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(377, 518, 37, '04won', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(378, 519, 37, '05won', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(379, 296, 37, '06won', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(380, 520, 6, '7', 'Program Perlindungan Khusus Anak', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(381, 521, 36, '02b', 'Program Penyelenggaraan Pengawasan', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(382, 445, 30, '02bk2', 'Program Pemerintahan dan Kesejahteraan Rakyat', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(383, 523, 25, '02b', 'Program Penyediaan dan Pengembangan Sarana Pertanian', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(384, 1, 39, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(385, 2, 39, '2', 'Program Pengelolaan Pendidikan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(386, 348, 39, '3', 'Program Pengembangan Kurikulum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(387, 349, 39, '4', 'Program Pendidik dan Tenaga Kependidikan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(388, 10, 40, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(389, 11, 40, '2', 'Program Pemenuhan Upaya Kesehatan Perorangan dan Upaya Kesehatan Masyarakat', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(390, 26, 40, '3', 'Program Peningkatan Kapasitas Sumber Daya Manusia Kesehatan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(391, 350, 40, '4', 'Program Sediaan Farmasi, Alat Kesehatan dan Makanan Minuman', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(392, 12, 40, '5', 'Program Pemberdayaan Masyarakat Bidang Kesehatan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(393, 351, 40, '01a', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(394, 352, 40, '02a', 'Program Pemenuhan Upaya Kesehatan Perorangan dan Upaya Kesehatan Masyarakat', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(395, 353, 40, '03a', 'Program Peningkatan Kapasitas Sumber Daya Manusia Kesehatan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(396, 332, 40, '01b', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(397, 354, 40, '02b', 'Program Pemenuhan Upaya Kesehatan Perorangan dan Upaya Kesehatan Masyarakat', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(398, 355, 40, '03b', 'Program Peningkatan Kapasitas Sumber Daya Manusia Kesehatan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(399, 43, 1, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(400, 44, 1, '2', 'Program Pengelolaan Sumber Daya Air (SDA)', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(401, 356, 1, '3', 'Program Pengelolaan dan Pengembangan Sistem Penyediaan Air Minum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(402, 357, 1, '5', 'Program Pengelolaan dan Pengembangan Sistem Air Limbah', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(403, 45, 1, '6', 'Program Pengelolaan dan Pengembangan Sistem Drainase', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(404, 358, 1, '7', 'Program Pengembangan Permukiman', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(405, 341, 1, '8', 'Program Penataan Bangunan Gedung', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(406, 359, 1, '9', 'Program Penataan Bangunan dan Lingkungannya', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(407, 360, 1, '10', 'Program Penyelenggaraan Jalan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(408, 361, 1, '11', 'Program Pengembangan Jasa Konstruksi', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(409, 362, 1, '12', 'Program Penyelenggaraan Penataan Ruang', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(410, 58, 2, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(411, 59, 2, '2', 'Program Pengembangan Perumahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(412, 363, 2, '3', 'Program Kawasan Permukiman', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(413, 364, 2, '4', 'Program Perumahan Dan Kawasan Permukiman Kumuh', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(414, 365, 2, '5', 'Program Peningkatan Prasarana, Sarana dan Utilitas Umum (PSU)', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(415, 60, 2, '6', 'Program Peningkatan Pelayanan Sertifikasi, Kualifikasi, Klasifikasi, Dan Registrasi Bidang Perumahan Dan Kawasan Permukiman', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(416, 64, 3, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(417, 65, 3, '2', 'Program Peningkatan Ketenteraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(418, 366, 3, '4', 'Program Pencegahan, Penanggulangan, Penyelamatan Kebakaran dan Penyelamatan Non Kebakaran', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(419, 367, 3, '01a', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(420, 368, 3, '3', 'Program Penanggulangan Bencana', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(421, 82, 4, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(422, 83, 4, '2', 'Program Pemberdayaan Sosial', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(423, 369, 4, '4', 'Program Rehabilitasi Sosial', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(424, 370, 4, '5', 'Program Perlindungan dan Jaminan Sosial', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(425, 371, 4, '6', 'Program Penanganan Bencana', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(426, 88, 5, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(427, 372, 5, '3', 'Program Pelatihan Kerja dan Produktivitas Tenaga Kerja', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(428, 373, 5, '4', 'Program Penempatan Tenaga Kerja', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(429, 374, 5, '5', 'Program Hubungan Industrial', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(430, 98, 6, '2', 'Program Pengarusutamaan Gender Dan Pemberdayaan Perempuan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(431, 375, 6, '3', 'Program Perlindungan Perempuan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(432, 376, 6, '6', 'Program Pemenuhan Hak Anak (PHA)', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(433, 104, 7, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(434, 377, 7, '3', 'Program Peningkatan Diversifikasi dan Ketahanan Pangan Masyarakat', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(435, 378, 7, '4', 'Program Penanganan Kerawanan Pangan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(436, 379, 7, '5', 'Program Pengawasan Keamanan Pangan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(437, 380, 8, '5', 'ProgramPenyelesaian Ganti Kerugian dan Santunan Tanah Untuk Pembangunan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(438, 381, 8, '8', 'Program Pengelolaan Tanah Kosong', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(439, 382, 8, '4', 'Program Penyelesaian Sengketa Tanah Garapan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(440, 383, 8, '10', 'Program Penatagunaan Tanah', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(441, 384, 8, '9', 'Program Pengelolaan Izin Membuka Tanah', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(442, 111, 9, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(443, 112, 9, '2', 'Program Perencanaan Lingkungan Hidup', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(444, 385, 9, '3', 'Program Pengendalian Pencemaran dan/atau Kerusakan Lingkungan Hidup', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(445, 386, 9, '4', 'Program Pengelolaan Keanekaragaman Hayati (KEHATI)', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(446, 387, 9, '5', 'Program Pengendalian Bahan Berbahaya dan Beracun (B3) dan Limbah Bahan Berbahaya dan Beracun (Limbah B3)', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(447, 113, 9, '6', 'Program Pembinaan dan Pengawasan Terhadap Izin Lingkungan dan Izin Perlindungan dan Pengelolaan Lingkungan Hidup (PPLH)', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(448, 388, 9, '8', 'Program Peningkatan Pendidikan, Pelatihan dan Penyuluhan Lingkungan Hidup Untuk Masyarakat', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(449, 389, 9, '9', 'Program Penghargaan Lingkungan Hidup Untuk Masyarakat', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(450, 390, 9, '10', 'Program Penanganan Pengaduan Lingkungan Hidup', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(451, 391, 9, '11', 'Program Pengelolaan Persampahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(452, 120, 10, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(453, 121, 10, '2', 'Program Pendaftaran Penduduk', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(454, 122, 10, '3', 'Program Pencatatan Sipil', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(455, 392, 10, '4', 'Program Pengelolaan Informasi Administrasi Kependudukan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(456, 128, 11, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(457, 129, 11, '2', 'Program Penataan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(458, 393, 11, '3', 'Program Peningkatan Kerjasama Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(459, 394, 11, '4', 'Program Administrasi Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(460, 395, 11, '5', 'Program Pemberdayaan Lembaga Kemasyarakatan, Lembaga Adat dan Masyarakat Hukum Adat', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(461, 396, 12, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(462, 397, 12, '2', 'Program Pengendalian Penduduk', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(463, 398, 12, '3', 'Program Pembinaan Keluarga Berencana (KB)', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(464, 399, 12, '4', 'Program Pemberdayaan dan Peningkatan Keluarga Sejahtera (KS)', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(465, 134, 13, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(466, 135, 13, '2', 'Program Penyelenggaraan Lalu Lintas dan Angkutan Jalan (LLAJ)', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(467, 141, 14, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(468, 142, 14, '2', 'Program Informasi dan Komunikasi Publik', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(469, 400, 14, '3', 'Program Aplikasi Informatika', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(470, 147, 15, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(471, 401, 15, '3', 'Program Pengawasan dan Pemeriksaan Koperasi', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(472, 402, 15, '4', 'Program Penilaian Kesehatan KSP/USP Koperasi', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(473, 403, 15, '5', 'Program Pendidikan dan Latihan Perkoperasian', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(474, 149, 15, '6', 'Program Pemberdayaan dan Perlindungan Koperasi', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(475, 404, 15, '7', 'Program Pemberdayaan Usaha Menengah, Usaha Kecil, dan Usaha Mikro', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(476, 405, 15, '8', 'Program Pengembangan UMKM', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(477, 148, 15, '2', 'Program Pelayanan Izin Usaha Simpan Pinjam', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(478, 161, 16, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(479, 406, 16, '3', 'Program Promosi Penanaman Modal', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(480, 407, 16, '4', 'Program Pelayanan Penanaman Modal', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(481, 408, 16, '5', 'Program Pengendalian Pelaksanaan Penanaman Modal', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(482, 162, 16, '2', 'Program Pengembangan Iklim Penanaman Modal', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(483, 163, 16, '6', 'Program Pengelolaan Data dan Sistem Informasi Penanaman Modal', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(484, 167, 17, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(485, 409, 17, '3', 'Program Pengembangan Kapasitas Daya Saing Keolahragaan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(486, 168, 17, '2', 'Program Pengembangan Kapasitas Daya Saing Kepemudaan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(487, 410, 18, '2', 'Program Penyelenggaraan Statistik Sektoral', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(488, 411, 19, '2', 'Program Penyelenggaraan Persandian untuk Pengamanan Informasi', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(489, 412, 20, '2', 'Program Pengembangan Kebudayaan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(490, 413, 20, '4', 'Program Pembinaan Sejarah', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(491, 414, 20, '5', 'Program Pelestarian dan Pengelolaan Cagar Budaya', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(492, 415, 21, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(493, 416, 21, '2', 'Program Pembinaan Perpustakaan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(494, 417, 22, '2', 'Program Pengelolaan Arsip', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(495, 418, 22, '3', 'Program Perlindungan Dan Penyelamatan Arsip', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(496, 419, 23, '4', 'Program Pengelolaan Perikanan Budidaya', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(497, 420, 23, '6', 'Program Pengolahan dan Pemasaran Hasil Perikanan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(498, 421, 23, '3', 'Program Pengelolaan Perikanan Tangkap', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(499, 422, 24, '2', 'Program Peningkatan Daya Tarik Destinasi Pariwisata', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(500, 423, 24, '3', 'Program Pemasaran Pariwisata', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(501, 424, 24, '5', 'Program Pengembangan Sumber Daya Pariwisata dan Ekonomi Kreatif', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(502, 179, 25, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(503, 425, 25, '02a', 'Program Penyediaan dan Pengembangan Sarana Pertanian', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(504, 426, 25, '4', 'Program Pengendalian Kesehatan Hewan dan Kesehatan Masyarakat Veteriner', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(505, 427, 25, '5', 'Program Pengendalian dan Penanggulangan Bencana Pertanian', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(506, 428, 25, '3', 'Program Penyediaan dan Pengembangan Prasarana Pertanian', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(507, 429, 25, '7', 'Program Penyuluhan Pertanian', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(508, 430, 27, '2', 'Program Perizinan dan Pendaftaran Perusahaan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(509, 431, 27, '3', 'Program Peningkatan Sarana Distribusi Perdagangan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(510, 432, 27, '4', 'Program Stabilitasi Harga Barang Kebutuhan Pokok dan Barang Penting', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(511, 433, 27, '5', 'Program Pengembangan Ekspor', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(512, 434, 27, '6', 'Program Standarisasi dan Perlindungan Konsumen', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(513, 435, 27, '7', 'Program Penggunaan Dan Pemasaran Produk Dalam Negeri', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(514, 436, 28, '2', 'Program Perencanaan dan Pembangunan Industri', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(515, 437, 28, '3', 'Program Pengendalian Izin Usaha Industri', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(516, 438, 28, '4', 'Program Pengelolaan Sistem Informasi Industri Nasional', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(517, 439, 29, '4', 'Program Pengembangan Kawasan Transmigrasi', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(518, 440, 30, '01um', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(519, 441, 8, '6', 'Program Redistribusi Tanah, Serta Ganti Kerugian Program Tanah Kelebihan Maksimum Dan Tanah Absentee', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(520, 442, 30, '01bo1', 'Program Penunjang Urusan Pemerintahan Daerah Kabupaten/Kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(521, 443, 30, '01bp', 'Program Penunjang Urusan Pemerintahan Daerah Kabupaten/Kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(522, 444, 30, '02bh', 'Program Pemerintahan dan Kesejahteraan Rakyat', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(523, 524, 30, '02bk1', 'Program Pemerintahan dan Kesejahteraan Rakyat', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(524, 446, 30, '02bape', 'Program Pemerintahan dan Kesejahteraan Rakyat', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(525, 246, 30, '3', 'Program Perekonomian dan Pembangunan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(526, 447, 30, '02jas', 'Program Perekonomian dan Pembangunan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(527, 448, 30, '03Per', 'Program Perekonomian Dan Pembangunan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(528, 248, 31, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(529, 249, 31, '2', 'Program Dukungan Pelaksanaan Tugas dan Fungsi DPRD', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(530, 191, 32, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(531, 192, 32, '2', 'Program Perencanaan, Pengendalian, dan Evaluasi Pembangunan Daerah', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(532, 449, 32, '3', 'Program Koordinasi dan Sinkronisasi Perencanaan Pembangunan Daerah', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(533, 450, 35, '2', 'Program Penelitian dan Pengembangan Daerah', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(534, 199, 33, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(535, 200, 33, '2', 'Program Pengelolaan Keuangan Daerah', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(536, 451, 33, '3', 'Program Pengelolaan Barang Milik Daerah', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(537, 452, 33, '01pen', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(538, 453, 33, '4', 'Program Pengelolaan Pendapatan Daerah', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(539, 211, 34, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(540, 212, 34, '2', 'Program Kepegawaian Daerah', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(541, 217, 36, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(542, 454, 36, '02a', 'Program Penyelenggaraan Pengawasan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(543, 455, 36, '3', 'Program Perumusan Kebijakan, Pendampingan dan Asistensi', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(544, 456, 38, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(545, 457, 38, '2', 'Program Penguatan Ideologi Pancasila dan Karakter Kebangsaan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(546, 458, 38, '3', 'Program Peningkatan Peran Partai Politik dan Lembaga Pendidikan Melalui Pendidikan Politik dan Pengembangan Etika Serta Budaya Politik', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(547, 459, 38, '4', 'Program Pemberdayaan dan Pengawasan Organisasi Kemasyarakatan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(548, 460, 38, '6', 'Program Peningkatan Kewaspadaan Nasional dan Peningkatan Kualitas dan Fasilitasi Penanganan Konflik Sosial', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(549, 461, 38, '5', 'Program Pembinaan dan Pengembangan Ketahanan Ekonomi, Sosial, dan Budaya', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(550, 255, 37, '01bal', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(551, 256, 37, '02bal', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(552, 462, 37, '03bal', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(553, 463, 37, '04bal', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(554, 464, 37, '05bal', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(555, 257, 37, '06bal', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(556, 259, 37, '01dag', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(557, 465, 37, '02dag', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(558, 466, 37, '03dag', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(559, 467, 37, '04dag', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(560, 468, 37, '05dag', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(561, 469, 37, '06dag', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(562, 261, 37, '01dol', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(563, 262, 37, '02dol', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(564, 470, 37, '03dol', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(565, 471, 37, '04dol', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(566, 472, 37, '05dol', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(567, 473, 37, '06dol', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(568, 474, 37, '01geg', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(569, 475, 37, '02geg', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(570, 476, 37, '03geg', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(571, 477, 37, '04geg', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(572, 478, 37, '05geg', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(573, 479, 37, '06geg', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(574, 264, 37, '01gem', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(575, 265, 37, '02gem', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(576, 480, 37, '03gem', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(577, 481, 37, '04gem', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(578, 482, 37, '05gem', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(579, 483, 37, '06gem', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(580, 267, 37, '01jiw', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(581, 268, 37, '02jiw', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(582, 269, 37, '03jiw', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(583, 484, 37, '04jiw', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(584, 485, 37, '05jiw', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(585, 486, 37, '06jiw', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(586, 270, 37, '01keb', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(587, 271, 37, '02keb', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(588, 487, 37, '03keb', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(589, 488, 37, '04keb', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(590, 489, 37, '05keb', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(591, 490, 37, '06keb', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(592, 273, 37, '01kar', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(593, 274, 37, '02kar', 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(594, 491, 37, '03kar', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(595, 492, 37, '04kar', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(596, 493, 37, '05kar', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(597, 494, 37, '06kar', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(598, 276, 37, '01mad', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(599, 277, 37, '02mad', 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(600, 495, 37, '03mad', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(601, 496, 37, '04mad', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(602, 497, 37, '05mad', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(603, 498, 37, '06mad', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(604, 279, 37, '01mej', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(605, 280, 37, '02mej', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(606, 499, 37, '03mej', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(607, 500, 37, '04mej', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(608, 501, 37, '05mej', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(609, 502, 37, '06mej', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(610, 282, 37, '01pil', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(611, 283, 37, '02pil', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(612, 284, 37, '03pil', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(613, 503, 37, '04pil', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(614, 504, 37, '05pil', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(615, 505, 37, '06pil', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(616, 288, 37, '01sar', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(617, 289, 37, '02sar', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(618, 290, 37, '03sar', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(619, 506, 37, '04sar', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(620, 507, 37, '05sar', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(621, 508, 37, '06sar', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(622, 285, 37, '01saw', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(623, 286, 37, '02saw', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(624, 509, 37, '03saw', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(625, 510, 37, '04saw', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(626, 511, 37, '05saw', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(627, 512, 37, '06saw', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(628, 291, 37, '01wun', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(629, 292, 37, '02wun', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(630, 513, 37, '03wun', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(631, 514, 37, '04wun', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(632, 515, 37, '05wun', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(633, 516, 37, '06wun', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(634, 294, 37, '01won', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(635, 295, 37, '02won', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(636, 517, 37, '03won', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(637, 518, 37, '04won', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(638, 519, 37, '05won', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(639, 296, 37, '06won', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(640, 520, 6, '7', 'Program Perlindungan Khusus Anak', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(641, 525, 30, '01bo2', 'Program Penunjang Urusan Pemerintahan Daerah Kabupaten/Kota', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(642, 521, 36, '02b', 'Program Penyelenggaraan Pengawasan', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(643, 523, 25, '02b', 'Program Penyediaan dan Pengembangan Sarana Pertanian', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03'),
(644, 445, 30, '02bk2', 'Program Pemerintahan dan Kesejahteraan Rakyat', '2023', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_sasarans`
--

CREATE TABLE `pivot_perubahan_sasarans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sasaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tujuan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `kabupaten_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_perubahan_sasarans`
--

INSERT INTO `pivot_perubahan_sasarans` (`id`, `sasaran_id`, `tujuan_id`, `kode`, `deskripsi`, `kabupaten_id`, `tahun_perubahan`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '1', 'Terciptanya ketenteraman dan ketertiban masyarakat', 62, '2021', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(2, 2, 2, '1', 'Mewujudkan Pemerintahan yang Akuntable', 62, '2021', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(3, 3, 2, '2', 'Pengembangan Kapasitas Aparatur Sipil Negara (ASN) Pemerintah Daerah', 62, '2021', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(4, 4, 2, '3', 'Meningkatnya Inovasi Layanan Publik berbasis Transformasi Digital', 62, '2021', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(5, 5, 3, '1', 'MeningkatnyaPertumbuhan Ekonomi yang Inklusif dan mandiri', 62, '2021', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(6, 6, 3, '2', 'Meningkatnya sarana dan prasarana infrastruktur perekonomian', 62, '2021', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(7, 7, 3, '3', 'Terjaganya Keseimbangan Kualitas Lingkungan Hidup', 62, '2021', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(8, 9, 5, '1', 'Terciptanya pemerataan distribusi pendapatan masyarakat', 62, '2021', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(9, 10, 5, '2', 'Meningkatnya Kualitas dan Aksesibilitas pelayanan pendidikan dan kesehatan', 62, '2021', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(10, 1, 1, '1', 'Terciptanya ketenteraman dan ketertiban masyarakat', 62, '2022', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(11, 1, 1, '1', 'Terciptanya ketenteraman dan ketertiban masyarakat', 62, '2023', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(12, 2, 2, '1', 'Mewujudkan Pemerintahan yang Akuntable', 62, '2023', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(13, 3, 2, '2', 'Pengembangan Kapasitas Aparatur Sipil Negara (ASN) Pemerintah Daerah', 62, '2023', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(14, 4, 2, '3', 'Meningkatnya Inovasi Layanan Publik berbasis Transformasi Digital', 62, '2023', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(15, 5, 3, '1', 'Meningkatnya Pertumbuhan Ekonomi yang Inklusif dan mandiri', 62, '2023', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(16, 6, 3, '2', 'Meningkatnya sarana dan prasarana infrastruktur perekonomian', 62, '2023', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(17, 7, 3, '3', 'Terjaganya Keseimbangan Kualitas Lingkungan Hidup', 62, '2023', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(18, 12, 3, '4', 'Meningkatnya Ketahanan Bencana Daerah', 62, '2023', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(19, 13, 3, '5', 'Meningkatnya Penyerapan Tenaga kerja Lokal', 62, '2023', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(20, 9, 5, '1', 'Terciptanya pemerataan distribusi pendapatan masyarakat', 62, '2023', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(21, 10, 5, '2', 'Meningkatnya Kualitas dan Aksesibilitas pelayanan pendidikan dan kesehatan', 62, '2023', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(22, 14, 6, '1', 'Menguatkan karakteristik kebudayaan', 62, '2023', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(23, 15, 6, '2', 'Terwujudnya nilai â€“ nilai keagamaan dan gotong royong dalam kehidupan masyarakat', 62, '2023', '2022-11-14 23:26:26', '2022-11-14 23:26:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_sasaran_pds`
--

CREATE TABLE `pivot_perubahan_sasaran_pds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sasaran_pd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sasaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_sub_kegiatans`
--

CREATE TABLE `pivot_perubahan_sub_kegiatans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sub_kegiatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kegiatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_aturan` enum('Sebelum Perubahan','Sesudah Perubahan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_perubahan_sub_kegiatans`
--

INSERT INTO `pivot_perubahan_sub_kegiatans` (`id`, `sub_kegiatan_id`, `kegiatan_id`, `kode`, `deskripsi`, `tahun_perubahan`, `status_aturan`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, 5, 1, '5', 'Koordinasi dan Penyusunan Perubahan DPA-SKPD', '2021', 'Sesudah Perubahan', 62, '2022-11-13 01:48:58', '2022-11-13 01:48:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_tujuans`
--

CREATE TABLE `pivot_perubahan_tujuans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tujuan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `misi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `kabupaten_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_perubahan_tujuans`
--

INSERT INTO `pivot_perubahan_tujuans` (`id`, `tujuan_id`, `misi_id`, `kode`, `deskripsi`, `kabupaten_id`, `tahun_perubahan`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '1', 'Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal', 62, '2021', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(2, 2, 2, '1', 'Meningkatkan Tata kelola Pemerintah yang Baik (Good Governance) untuk Pelayanan Publik', 62, '2021', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(3, 3, 3, '1', 'Meningkatnya Daya Saing Ekonomi Inklusif, Mandiri dan Berkelanjutan', 62, '2021', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(4, 5, 4, '1', 'Meningkatkan kualitas dan aksesibilitas pelayanan pendidikan dan kesehatan', 62, '2021', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(5, 6, 5, '1', 'Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal', 62, '2021', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(6, 1, 1, '1', 'Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal', 62, '2022', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(7, 1, 1, '1', 'Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal', 62, '2023', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(8, 2, 2, '1', 'Meningkatkan Tata kelola Pemerintah yang Baik (Good Governance) untuk Pelayanan Publik', 62, '2023', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(9, 3, 3, '1', 'Meningkatnya Daya Saing Ekonomi Inklusif, Mandiri dan Berkelanjutan', 62, '2023', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(10, 5, 4, '1', 'Meningkatkan kualitas dan aksesibilitas pelayanan pendidikan dan kesehatan', 62, '2023', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(11, 6, 5, '1', 'Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal', 62, '2023', '2022-11-14 06:39:39', '2022-11-14 06:39:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_tujuan_pds`
--

CREATE TABLE `pivot_perubahan_tujuan_pds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tujuan_pd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tujuan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_urusans`
--

CREATE TABLE `pivot_perubahan_urusans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `urusan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_aturan` enum('Sebelum Perubahan','Sesudah Perubahan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_perubahan_urusans`
--

INSERT INTO `pivot_perubahan_urusans` (`id`, `urusan_id`, `kode`, `deskripsi`, `tahun_perubahan`, `status_aturan`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, 1, '1.03', 'urusan pemerintahan bidang pekerjaan umum dan penataan ruang', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(2, 2, '1.04', 'urusan pemerintahan bidang perumahan rakyat dan kawasan permukiman', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(3, 3, '1.05', 'urusan pemerintahan bidang ketentraman dan ketertiban umum serta perlindungan masyarakat', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(4, 4, '1.06', 'urusan pemerintahan bidang sosial', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(5, 5, '2.07', 'urusan pemerintahan bidang tenaga kerja', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(6, 6, '2.08', 'urusan pemerintahan bidang pemberdayaan perempuan dan perlindungan anak', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(7, 7, '2.09', 'urusan pemerintahan bidang pangan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(8, 8, '2.10', 'urusan pemerintahan bidang pertanahan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(9, 9, '2.11', 'urusan pemerintahan bidang lingkungan hidup', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(10, 10, '2.12', 'urusan pemerintahan bidang administrasi kependudukan dan pencatatan sipil', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(11, 11, '2.13', 'urusan pemerintahan bidang pemberdayaan masyarakat dan desa', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(12, 12, '2.14', 'urusan pemerintahan bidang pengendalian penduduk dan keluarga berencana', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(13, 13, '2.15', 'urusan pemerintahan bidang perhubungan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(14, 14, '2.16', 'urusan pemerintahan bidang komunikasi dan informatika', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(15, 15, '2.17', 'urusan pemerintahan bidang koperasi, usaha kecil dan menengah', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(16, 16, '2.18', 'urusan pemerintahan bidang penanaman modal', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(17, 17, '2.19', 'urusan pemerintahan bidang kepemudaan dan olahraga', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(18, 18, '2.20', 'urusan pemerintahan bidang statistik', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(19, 19, '2.21', 'urusan pemerintahan bidang persandian', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(20, 20, '2.22', 'urusan pemerintahan bidang kebudayaan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(21, 21, '2.23', 'urusan pemerintahan bidang perpustakaan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(22, 22, '2.24', 'urusan pemerintahan bidang kearsipan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(23, 23, '3.25', 'urusan pemerintahan bidang kelautan dan perikanan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(24, 24, '3.26', 'urusan pemerintahan bidang pariwisata', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(25, 25, '3.27', 'urusan pemerintahan bidang pertanian', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(26, 26, '3.29', 'urusan pemerintahan bidang energi dan sumber daya mineral', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(27, 27, '3.30', 'urusan pemerintahan bidang perdagangan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(28, 28, '3.31', 'urusan pemerintahan bidang perindustrian', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(29, 29, '3.32', 'urusan pemerintahan bidang transmigrasi', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(30, 30, '4.01', 'unsur Sekretariat daerah', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(31, 31, '4.02', 'unsur Sekretariat DPRD', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(32, 32, '5.01', 'unsur perencanaan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(33, 33, '5.02', 'unsur keuangan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(34, 34, '5.03', 'unsur kepegawaian', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(35, 35, '5.05', 'unsur penelitian dan pengembangan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(36, 36, '6.01', 'Unsur pengawasan urusan pemerintahan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(37, 37, '7.01', 'unsur kewilayahan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(38, 38, '8.01', 'urusan pemerintahan umum', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(39, 39, '1.01', 'urusan pemerintahan bidang pendidikan', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(40, 40, '1.02', 'urusan pemerintahan bidang kesehatan', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(41, 1, '1.03', 'urusan pemerintahan bidang pekerjaan umum dan penataan ruang', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(42, 2, '1.04', 'urusan pemerintahan bidang perumahan rakyat dan kawasan permukiman', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(43, 3, '1.05', 'urusan pemerintahan bidang ketentraman dan ketertiban umum serta perlindungan masyarakat', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(44, 4, '1.06', 'urusan pemerintahan bidang sosial', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(45, 5, '2.07', 'urusan pemerintahan bidang tenaga kerja', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(46, 6, '2.08', 'urusan pemerintahan bidang pemberdayaan perempuan dan perlindungan anak', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(47, 7, '2.09', 'urusan pemerintahan bidang pangan', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(48, 8, '2.10', 'urusan pemerintahan bidang pertanahan', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(49, 9, '2.11', 'urusan pemerintahan bidang lingkungan hidup', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(50, 10, '2.12', 'urusan pemerintahan bidang administrasi kependudukan dan pencatatan sipil', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(51, 11, '2.13', 'urusan pemerintahan bidang pemberdayaan masyarakat dan desa', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(52, 12, '2.14', 'urusan pemerintahan bidang pengendalian penduduk dan keluarga berencana', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(53, 13, '2.15', 'urusan pemerintahan bidang perhubungan', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(54, 14, '2.16', 'urusan pemerintahan bidang komunikasi dan informatika', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(55, 15, '2.17', 'urusan pemerintahan bidang koperasi, usaha kecil dan menengah', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(56, 16, '2.18', 'urusan pemerintahan bidang penanaman modal', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(57, 17, '2.19', 'urusan pemerintahan bidang kepemudaan dan olahraga', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(58, 18, '2.20', 'urusan pemerintahan bidang statistik', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(59, 19, '2.21', 'urusan pemerintahan bidang persandian', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(60, 20, '2.22', 'urusan pemerintahan bidang kebudayaan', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(61, 21, '2.23', 'urusan pemerintahan bidang perpustakaan', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(62, 22, '2.24', 'urusan pemerintahan bidang kearsipan', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(63, 23, '3.25', 'urusan pemerintahan bidang kelautan dan perikanan', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(64, 24, '3.26', 'urusan pemerintahan bidang pariwisata', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(65, 25, '3.27', 'urusan pemerintahan bidang pertanian', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(66, 27, '3.30', 'urusan pemerintahan bidang perdagangan', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(67, 28, '3.31', 'urusan pemerintahan bidang perindustrian', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(68, 29, '3.32', 'urusan pemerintahan bidang transmigrasi', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(69, 30, '4.01', 'unsur Sekretariat daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(70, 31, '4.02', 'unsur Sekretariat DPRD', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(71, 32, '5.01', 'unsur perencanaan', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(72, 33, '5.02', 'unsur keuangan', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(73, 34, '5.03', 'unsur kepegawaian', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(74, 35, '5.05', 'unsur penelitian dan pengembangan', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(75, 36, '6.01', 'Unsur pengawasan urusan pemerintahan', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(76, 37, '7.01', 'unsur kewilayahan', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(77, 38, '8.01', 'urusan pemerintahan umum', '2021', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(78, 39, '1.01', 'urusan pemerintah bidang pendidikan', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(79, 40, '1.02', 'urusan pemerintah bidang kesehatan', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(119, 39, '1.01', 'urusan pemerintahan bidang pendidikan', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(120, 40, '1.02', 'urusan pemerintahan bidang kesehatan', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(121, 1, '1.03', 'urusan pemerintahan bidang pekerjaan umum dan penataan ruang', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(122, 2, '1.04', 'urusan pemerintahan bidang perumahan rakyat dan kawasan permukiman', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(123, 3, '1.05', 'urusan pemerintahan bidang ketentraman dan ketertiban umum serta perlindungan masyarakat', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(124, 4, '1.06', 'urusan pemerintahan bidang sosial', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(125, 5, '2.07', 'urusan pemerintahan bidang tenaga kerja', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(126, 6, '2.08', 'urusan pemerintahan bidang pemberdayaan perempuan dan perlindungan anak', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(127, 7, '2.09', 'urusan pemerintahan bidang pangan', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(128, 8, '2.10', 'urusan pemerintahan bidang pertanahan', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(129, 9, '2.11', 'urusan pemerintahan bidang lingkungan hidup', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(130, 10, '2.12', 'urusan pemerintahan bidang administrasi kependudukan dan pencatatan sipil', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(131, 11, '2.13', 'urusan pemerintahan bidang pemberdayaan masyarakat dan desa', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(132, 12, '2.14', 'urusan pemerintahan bidang pengendalian penduduk dan keluarga berencana', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(133, 13, '2.15', 'urusan pemerintahan bidang perhubungan', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(134, 14, '2.16', 'urusan pemerintahan bidang komunikasi dan informatika', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(135, 15, '2.17', 'urusan pemerintahan bidang koperasi, usaha kecil dan menengah', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(136, 16, '2.18', 'urusan pemerintahan bidang penanaman modal', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(137, 17, '2.19', 'urusan pemerintahan bidang kepemudaan dan olahraga', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(138, 18, '2.20', 'urusan pemerintahan bidang statistik', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(139, 19, '2.21', 'urusan pemerintahan bidang persandian', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(140, 20, '2.22', 'urusan pemerintahan bidang kebudayaan', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(141, 21, '2.23', 'urusan pemerintahan bidang perpustakaan', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(142, 22, '2.24', 'urusan pemerintahan bidang kearsipan', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(143, 23, '3.25', 'urusan pemerintahan bidang kelautan dan perikanan', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(144, 24, '3.26', 'urusan pemerintahan bidang pariwisata', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(145, 25, '3.27', 'urusan pemerintahan bidang pertanian', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(146, 27, '3.30', 'urusan pemerintahan bidang perdagangan', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(147, 28, '3.31', 'urusan pemerintahan bidang perindustrian', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(148, 29, '3.32', 'urusan pemerintahan bidang transmigrasi', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(149, 30, '4.01', 'unsur Sekretariat daerah', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(150, 31, '4.02', 'unsur Sekretariat DPRD', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(151, 32, '5.01', 'unsur perencanaan', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(152, 33, '5.02', 'unsur keuangan', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(153, 34, '5.03', 'unsur kepegawaian', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(154, 35, '5.05', 'unsur penelitian dan pengembangan', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(155, 36, '6.01', 'Unsur pengawasan urusan pemerintahan', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(156, 37, '7.01', 'unsur kewilayahan', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(157, 38, '8.01', 'urusan pemerintahan umum', '2023', 'Sesudah Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(158, 39, '1.01', 'URUSAN PEMERINTAHAN BIDANG PENDIDIKAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(159, 40, '1.02', 'URUSAN PEMERINTAHAN BIDANG KESEHATAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(160, 1, '1.03', 'URUSAN PEMERINTAHAN BIDANG PEKERJAAN UMUM DAN PENATAAN RUANG', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(161, 2, '1.04', 'URUSAN PEMERINTAHAN BIDANG PERUMAHAN DAN KAWASAN PERMUKIMAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(162, 3, '1.05', 'URUSAN PEMERINTAHAN BIDANG KETENTERAMAN DAN KETERTIBAN UMUM SERTA PERLINDUNGAN MASYARAKAT', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(163, 4, '1.06', 'URUSAN PEMERINTAHAN BIDANG SOSIAL', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(164, 5, '2.07', 'URUSAN PEMERINTAHAN BIDANG TENAGA KERJA', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(165, 6, '2.08', 'URUSAN PEMERINTAHAN BIDANG PEMBERDAYAAN PEREMPUAN DAN PERLINDUNGAN ANAK', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(166, 7, '2.09', 'URUSAN PEMERINTAHAN BIDANG PANGAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(167, 8, '2.10', 'URUSAN PEMERINTAHAN BIDANG PERTANAHAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(168, 9, '2.11', 'URUSAN PEMERINTAHAN BIDANG LINGKUNGAN HIDUP', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(169, 10, '2.12', 'URUSAN PEMERINTAHAN BIDANG ADMINISTRASI KEPENDUDUKAN DAN PENCATATAN SIPIL', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(170, 11, '2.13', 'URUSAN PEMERINTAHAN BIDANG PEMBERDAYAAN MASYARAKAT DAN DESA', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(171, 12, '2.14', 'URUSAN PEMERINTAHAN BIDANG PENGENDALIAN PENDUDUK DAN KELUARGA BERENCANA', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(172, 13, '2.15', 'URUSAN PEMERINTAHAN BIDANG PERHUBUNGAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(173, 14, '2.16', 'URUSAN PEMERINTAHAN BIDANG KOMUNIKASI DAN INFORMATIKA', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:34', '2022-11-11 22:54:34'),
(174, 15, '2.17', 'URUSAN PEMERINTAHAN BIDANG KOPERASI, USAHA KECIL, DAN MENENGAH', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(175, 16, '2.18', 'URUSAN PEMERINTAHAN BIDANG PENANAMAN MODAL', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(176, 17, '2.19', 'URUSAN PEMERINTAHAN BIDANG KEPEMUDAAN DAN OLAHRAGA', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(177, 18, '2.20', 'URUSAN PEMERINTAHAN BIDANG STATISTIK', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(178, 19, '2.21', 'URUSAN PEMERINTAHAN BIDANG PERSANDIAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(179, 20, '2.22', 'URUSAN PEMERINTAHAN BIDANG KEBUDAYAAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(180, 21, '2.23', 'URUSAN PEMERINTAHAN BIDANG PERPUSTAKAAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(181, 22, '2.24', 'URUSAN PEMERINTAHAN BIDANG KEARSIPAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(182, 23, '3.25', 'URUSAN PEMERINTAHAN BIDANG KELAUTAN DAN PERIKANAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(183, 24, '3.26', 'URUSAN PEMERINTAHAN BIDANG PARIWISATA', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(184, 25, '3.27', 'URUSAN PEMERINTAHAN BIDANG PERTANIAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(185, 41, '3.28', 'URUSAN PEMERINTAHAN BIDANG KEHUTANAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(186, 26, '3.29', 'URUSAN PEMERINTAHAN BIDANG ENERGI DAN SUMBER DAYA MINERAL', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(187, 27, '3.30', 'URUSAN PEMERINTAHAN BIDANG PERDAGANGAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(188, 28, '3.31', 'URUSAN PEMERINTAHAN BIDANG PERINDUSTRIAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(189, 29, '3.32', 'URUSAN PEMERINTAHAN BIDANG TRANSMIGRASI', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(190, 30, '4.01', 'SEKRETARIAT DAERAH', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(191, 31, '4.02', 'SEKRETARIAT DPRD', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(192, 32, '5.01', 'PERENCANAAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(193, 33, '5.02', 'KEUANGAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(194, 34, '5.03', 'KEPEGAWAIAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(195, 35, '5.05', 'PENELITIAN DAN PENGEMBANGAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(196, 36, '6.01', 'INSPEKTORAT DAERAH', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(197, 37, '7.01', 'KECAMATAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(198, 38, '8.01', 'KESATUAN BANGSA DAN POLITIK', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_visis`
--

CREATE TABLE `pivot_perubahan_visis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `kabupaten_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_perubahan_visis`
--

INSERT INTO `pivot_perubahan_visis` (`id`, `visi_id`, `deskripsi`, `kabupaten_id`, `tahun_perubahan`, `created_at`, `updated_at`) VALUES
(1, 1, 'TERWUJUDNYA KABUPATEN MADIUN AMAN, MANDIRI, SEJAHTERA DAN BERAKHLAK', 62, '2020', '2022-10-13 12:09:54', '2022-10-13 12:09:54'),
(2, 1, 'TERWUJUDNYA KABUPATEN MADIUN AMAN, MANDIRI, SEJAHTERA DAN BERAKHLAK', 62, '2021', '2022-10-13 12:09:59', '2022-10-13 12:09:59'),
(3, 1, 'TERWUJUDNYA KABUPATEN MADIUN AMAN, MANDIRI, SEJAHTERA DAN BERAKHLAK', 62, '2022', '2022-10-13 12:10:03', '2022-10-13 12:10:03'),
(4, 1, 'TERWUJUDNYA KABUPATEN MADIUN AMAN, MANDIRI, SEJAHTERA DAN BERAKHLAK', 62, '2023', '2022-10-13 12:10:07', '2022-10-13 12:10:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_program_kegiatan_renstras`
--

CREATE TABLE `pivot_program_kegiatan_renstras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_rpjmd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kegiatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_program_kegiatan_renstras`
--

INSERT INTO `pivot_program_kegiatan_renstras` (`id`, `program_rpjmd_id`, `program_id`, `kegiatan_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2022-10-18 21:30:13', '2022-10-18 21:30:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_sasaran_indikator_program_rpjmds`
--

CREATE TABLE `pivot_sasaran_indikator_program_rpjmds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_rpjmd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sasaran_indikator_kinerja_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_sasaran_indikator_program_rpjmds`
--

INSERT INTO `pivot_sasaran_indikator_program_rpjmds` (`id`, `program_rpjmd_id`, `sasaran_indikator_kinerja_id`, `created_at`, `updated_at`) VALUES
(6, 4, 102, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(7, 5, 103, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(8, 6, 104, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(9, 7, 104, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(10, 8, 106, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(11, 9, 107, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(12, 10, 108, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(13, 11, 109, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(14, 12, 109, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(15, 13, 109, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(16, 14, 109, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(17, 15, 110, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(18, 16, 111, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(19, 17, 111, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(20, 18, 113, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(21, 19, 114, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(22, 20, 115, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(23, 21, 116, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(24, 22, 116, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(25, 23, 116, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(26, 24, 116, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(27, 25, 117, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(28, 26, 117, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(29, 27, 117, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(30, 28, 118, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(31, 29, 118, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(32, 30, 119, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(33, 31, 119, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(34, 32, 120, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(35, 33, 121, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(36, 34, 122, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(37, 5, 59, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(38, 6, 60, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(39, 7, 60, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(40, 35, 61, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(41, 8, 62, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(42, 9, 63, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(43, 10, 64, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(44, 11, 65, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(45, 12, 65, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(46, 13, 65, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(47, 14, 65, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(48, 15, 66, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(49, 16, 67, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(50, 17, 67, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(51, 36, 68, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(52, 37, 68, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(53, 38, 68, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(54, 39, 68, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(55, 40, 68, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(56, 41, 68, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(57, 42, 68, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(58, 43, 68, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(59, 18, 69, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(60, 44, 69, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(61, 19, 70, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(62, 20, 71, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(63, 45, 71, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(64, 46, 71, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(65, 21, 72, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(66, 22, 72, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(67, 23, 72, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(68, 24, 72, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(69, 25, 96, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(70, 27, 96, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(71, 28, 97, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(72, 29, 97, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(73, 30, 98, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(74, 31, 98, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(75, 32, 99, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(76, 26, 96, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(77, 33, 100, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(78, 34, 101, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(79, 35, 105, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(80, 36, 112, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(81, 37, 112, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(82, 38, 112, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(83, 39, 112, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(84, 40, 112, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(85, 41, 112, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(86, 42, 112, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(87, 43, 112, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(88, 44, 113, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(89, 45, 115, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(90, 46, 115, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(91, 28, 103, '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(92, 47, 118, '2022-11-16 02:42:22', '2022-11-16 02:42:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `programs`
--

CREATE TABLE `programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `urusan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_aturan` enum('Sebelum Perubahan','Sesudah Perubahan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `programs`
--

INSERT INTO `programs` (`id`, `urusan_id`, `kode`, `deskripsi`, `tahun_perubahan`, `status_aturan`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, 39, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(2, 39, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(3, 39, '15', 'Program Pendidikan Anak Usia Dini', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(4, 39, '18', 'Program Pendidikan Non Formal', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(5, 39, '20', 'Program Peningkatan Mutu Pendidik dan Tenaga Kependidikan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(6, 39, '22', 'Program Manajemen Pelayanan Pendidikan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(7, 39, '24', 'Program Pendidikan SD', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(8, 39, '25', 'Program Pendidikan SMP', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(9, 20, '15', 'Program Pengembangan Nilai Budaya', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(10, 40, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(11, 40, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(12, 40, '5', 'Program Peningkatan Kapasitas Sumber Daya Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(13, 40, '6', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(14, 40, '16', 'Program Upaya Kesehatan Masyarakat', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(15, 40, '25', 'Program pengadaan, peningkatan dan perbaikan sarana dan prasarana puskesmas/ puskemas pembantu dan jaringannya', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(16, 40, '34', 'Program Peningkatan Pelayanan Publik', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(17, 40, '35', 'Program Peningkatan Pelayanan Jaminan Kesehatan Nasional (JKN)', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(18, 40, '38', 'Program Pelayanan Kesehatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(19, 40, '39', 'Program Peningkatan Sumber Daya Kesehatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(20, 40, '40', 'Program Pencegahan dan Penanggulangan Penyakit Menular dan Tidak Menular', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(21, 40, '41', 'Program Pembinaan Lingkungan Sosial', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(22, 40, '42', 'Program Penyediaan Bantuan Operasional Kesehatan (BOK)', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(23, 40, '48', 'Program Pembinaan Lingkungan Sosial bidang Kesehatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(24, 40, '01car', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(25, 40, '02car', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(26, 40, '03car', 'Program peningkatan disiplin aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(27, 40, '05car', 'Program Peningkatan Kapasitas Sumber Daya Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(28, 40, '33car', 'Program Peningkatan Pelayanan Rumah Sakit', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(29, 40, '44car', 'Program Penatalaksanaan Keuangan dan Akuntansi', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(30, 40, '45car', 'Program Penyelenggaraan Pelayanan Medis dan Keperawatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(31, 40, '46car', 'Program Penyelenggaraan Penunjang Medis dan Non Medis', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(32, 40, '47car', 'Program pelayanan kesehatan Rujukan (DAK)', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(33, 40, '48car', 'Program Pembinaan Lingkungan Sosial bidang Kesehatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(34, 40, '01dol', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(35, 40, '02dol', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(36, 40, '05dol', 'Program Peningkatan Kapasitas Sumber Daya Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(37, 40, '06dol', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(38, 40, '26dol', 'Program pengadaan, peningkatan sarana dan prasarana rumah sakit/ rumah sakit jiwa/ rumah sakit paru-paru/ rumah sakit mata', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(39, 40, '27dol', 'Program pemeliharaan sarana dan prasarana rumah sakit/ rumah sakit jiwa/ rumah sakit paru-paru/ rumah sakit mata', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(40, 40, '28dol', 'Program kemitraan peningkatan pelayanan kesehatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(41, 40, '33dol', 'Program Peningkatan Pelayanan Rumah Sakit', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(42, 40, '48dol', 'Program Pembinaan Lingkungan Sosial bidang Kesehatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(43, 1, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(44, 1, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(45, 1, '6', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(46, 1, '15', 'Program Pembangunan Jalan dan Jembatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(47, 1, '16', 'Program Pembangunan Saluran Drainase/Gorong-gorong', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(48, 1, '23', 'Program Peningkatan Sarana dan Prasarana Kebinamargaan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(49, 1, '24', 'Program Pengembangan dan Pengelolaan Jaringan Irigasi, Rawa dan Jaringan Pengairan lainnya', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(50, 1, '28', 'Program Pengendalian Banjir', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(51, 1, '31', 'Program Peningkatan Sarana dan Prasarana Pemerintah', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(52, 1, '37', 'Program Jasa Konstruksi', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(53, 1, '38', 'Program Integrated Participatory Development and Management Irrigation Program (IPDMIP)', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(54, 1, '39', 'Program Pembangunan Infrastruktur Perdesaan/kelurahan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(55, 1, '42', 'Program Peningkatan Jalan dan Jembatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(56, 1, '43', 'Program Rehabilitasi/pemeliharaan Jalan dan Jembatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(57, 1, '44', 'Program Pembinaan Lingkungan Sosial Bidang Infrastruktur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(58, 2, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(59, 2, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(60, 2, '6', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(61, 2, '23', 'Program Pengembangan Perumahan dan permukiman', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(62, 2, '24', 'Program Pemberdayaan Komunitas Perumahan dan Pemukiman', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(63, 2, '25', 'Program Penataan, Penguasaan, Pemilikan, Penggunaan, dan Pemfaatan Tanah', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(64, 3, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(65, 3, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(66, 3, '5', 'Program Peningkatan Kapasitas Sumber Daya Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(67, 3, '16', 'Program pemeliharaan kantrantibmas dan pencegahan tindak kriminal', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(68, 3, '23', 'Program Penegakan Produk Hukum Daerah', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(69, 3, '01bpbd', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(70, 3, '02bpbd', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(71, 3, '24bpbd', 'Program Kedaruratan dan Logistik Penanggulangan Bencana', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(72, 3, '25bpbd', 'Program Rehabilitasi dan Rekonstruksi Penanggulangan Bencana', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(73, 3, '26bpbd', 'Program Pencegahan dan Kesiapsiagaan Penanggulangan Bencana', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(74, 3, '01kesb', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(75, 3, '02kesb', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(76, 3, '06kesb', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(77, 3, '15kesb', 'Program peningkatan keamanan dan kenyamanan lingkungan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(78, 3, '21kesb', 'Program pendidikan politik masyarakat', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(79, 3, '27kesb', 'Program Kemitraan Pengembangan Wawasan Kebangsaan dan Kehidupan Beragama', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(80, 3, '28kesb', 'Program Pencegahan Penanganan Konflik', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(81, 3, '29kesb', 'Program Peningkatan Wawasan Kebangsaan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(82, 4, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(83, 4, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(84, 4, '15', 'Program Pemberdayaan Fakir Miskin, Komunitas Adat Terpencil (KAT) dan Penyandang Masalah Kesejahteraan Sosial (PMKS) Lainnya', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(85, 4, '16', 'Program Pelayanan dan Rehabilitasi Kesejahteraan Sosial', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(86, 4, '21', 'Program Pemberdayaan Kelembagaan Kesejahteraan Sosial', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(87, 4, '24', 'Program Perlindungan dan Jaminan Sosial', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(88, 5, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(89, 5, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(90, 5, '6', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(91, 5, '15', 'Program Peningkatan Kualitas dan Produktivitas Tenaga Kerja', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(92, 5, '16', 'Program Peningkatan Kesempatan Kerja', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(93, 5, '17', 'Program Perlindungan dan Pengembangan Lembaga Ketenagakerjaan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(94, 5, '18', 'Program Pembinaan Lingkungan Sosial', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(95, 5, '20', 'Program Pembinaan Lingkungan Sosial bidang Ketenagakerjaan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(96, 29, '18', 'Program Transmigrasi', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(97, 6, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(98, 6, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(99, 6, '20', 'Program Peningkatan Kualitas Hidup Perempuan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(100, 6, '21', 'Program Peningkatan Hak, Perlindungan Perempuan dan Anak', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(101, 12, '26', 'Program Peningkatan Kualitas Ketahanan Keluarga dan Remaja', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(102, 12, '25', 'Program Pengendalian Penduduk', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(103, 12, '15', 'Program Keluarga Berencana', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(104, 7, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(105, 7, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(106, 7, '6', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(107, 7, '15', 'Program Peningkatan Ketahanan Pangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(108, 7, '16', 'Program Penganekaragaman Konsumsi Pangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(109, 7, '17', 'Program Pembinaan Lingkungan Sosial bidang Pangan (CUKAI)', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(110, 7, '18', 'Program Pembinaan Lingkungan Sosial Bidang Ketenagakerjaan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(111, 9, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(112, 9, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(113, 9, '6', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(114, 9, '16', 'Program Pengendalian Pencemaran dan Perusakan Lingkungan Hidup', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(115, 9, '17', 'Program Perlindungan dan Konservasi Sumber Daya Alam', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(116, 9, '24', 'Program Pengelolaan Ruang Terbuka Hijau (RTH)', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(117, 9, '25', 'Program Pengembangan Kinerja Pengolahan Sampah dan Limbah Domestik', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(118, 9, '26', 'Program Perlindungan Fungsi, Pengendalian Pencemaran Lingkungan Hidup, Keanekaragaman Sumber daya Hayati dan Adaptasi serta Mitigasi Perubahan Iklim', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(119, 9, '27', 'Program Pembinaan Lingkungan Sosial bidang Lingkungan Hidup', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(120, 10, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(121, 10, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(122, 10, '3', 'Program peningkatan disiplin aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(123, 10, '6', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(124, 10, '16', 'Program Penataan Administrasi Pendaftaran Penduduk', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(125, 10, '17', 'Program Penataan Administrasi Pencatatan Sipil', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(126, 10, '18', 'Program Pengelolaan Informasi Administrasi Kependudukan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(127, 10, '19', 'Program Pemanfaatan Data dan Inovasi Pelayanan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(128, 11, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(129, 11, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(130, 11, '21', 'Program Peningkatan Usaha Ekonomi Desa / Kelurahan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(131, 11, '22', 'Program Peningkatan Pembangunan dan Kapasitas Lembaga Kemasyarakatan Desa/Kelurahan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(132, 11, '23', 'Program Penataan dan Peningkatan Kapasitas Pemerintahan Desa', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(133, 11, '24', 'Program Pembinaan Lingkungan Sosial Bidang Ketenagakerjaan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(134, 13, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(135, 13, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(136, 13, '21', 'Program Peningkatan Pelayanan, Kelaikan Kendaraan Dan Sarana Prasarana Mutimoda Angkutan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(137, 13, '22', 'Program Manajemen Rekayasa dan Pengendalian Lalu Lintas', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(138, 13, '23', 'Program Keselamatan dan Perlengkapan Sarana Jalan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(139, 13, '15', 'Program Peningkatan Pelayanan angkutan, keselamatan jalan dan keselamatan perkeretaapian', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(140, 13, '16', 'Program Sarana dan Prasarana Jalan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(141, 14, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(142, 14, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(143, 14, '19', 'Program Tata Kelola E-Government dan Infrastruktur Jaringan Tl dan Komunikasi', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(144, 14, '20', 'Pengelolaan Informasi dan Komunikasi Publik', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(145, 19, '7', 'Program Penyelenggaraan Persandian dan Pengamanan Informasi', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(146, 18, '15', 'Program Pengembangan Data/ Informasi/ Statistik Daerah', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(147, 15, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(148, 15, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(149, 15, '6', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(150, 28, '16', 'Program pengembangan industri kecil dan menengah', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(151, 27, '18', 'Program peningkatan efisiensi perdagangan dalam negeri', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(152, 27, '19', 'Pembinaan Lingkungan Sosial', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(153, 28, '20', 'Program Pembinaan Lingkungan Sosial Bidang Ketenagakerjaan Lingkup Industri', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(154, 15, '20', 'Program Pengembangan Usaha Mikro', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(155, 15, '21', 'Program Pengembangan Koperasi', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(156, 27, '23', 'Program Pengelolaan Pasar Daerah', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(157, 27, '24', 'Program Pemberantasan Barang Kena Cukai 1 Legal', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(158, 27, '12', 'Program pembinaan dan peningkatan pedagang formal', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(159, 27, '13', 'Program Operasi Pasar', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(160, 15, '14', 'Program Pembinaan Lingkungan Sosial Bidang Ketenagakerjaan Lingkup Koperasi dan Usaha Mikro (CUKAI)', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(161, 16, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(162, 16, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(163, 16, '6', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(164, 16, '16', 'Program Peningkatan Iklim Investasi dan Realisasi Investasi', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(165, 16, '18', 'Program Peningkatan Kualitas Pelayanan Publik', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(166, 26, '18', 'Program Pengembangan, Pembinaan dan Pengawasan Bidang Energi dan Sumber Daya Mineral', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(167, 17, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(168, 17, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(169, 17, '6', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(170, 17, '16', 'Program peningkatan peran serta kepemudaan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(171, 24, '18', 'Program Pengembangan Destinasi dan Industri Pariwisata', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(172, 17, '19', 'Program Pengembangan Kebijakan dan Manajemen Olah Raga', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(173, 24, '19', 'Program Pengembangan Kelembagaan dan Pemasaran Pariwisata', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(174, 17, '20', 'Program Pembinaan dan Pemasyarakatan Olah Raga', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(175, 24, '20', 'Program Pembinaan Lingkungan Sosial bidang Pariwisata', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(176, 24, '21', 'Pembinaan Lingkungan Sosial Bidang Infrastruktur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(177, 17, '22', 'Pembinaan Lingkungan Sosial Bidang Ketenagakerjaan Lingkup Pemuda dan Olahraga', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(178, 24, '23', 'Pembinaan Lingkungan Sosial Bidang Ketenagakerjaan Lingkup Pariwisata', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(179, 25, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(180, 25, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(181, 25, '24', 'Program Peningkatan Kualitas Bahan Baku', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(182, 25, '26', 'Program Pembinaan Lingkungan Sosial bidang Pertanian', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(183, 25, '27', 'Program Pemberdayaan Penyuluh dan Lembaga Petani', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(184, 25, '30', 'Program Pengembangan Tanaman Pangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(185, 25, '31', 'Program Pengembangan Peternakan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(186, 25, '32', 'Program Integrated Participatory Development and Management of Irrigation Program (IPDMIP)', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(187, 25, '33', 'Program Tanaman Hortikultura', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(188, 25, '34', 'Program Pengembangan Perkebunan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(189, 25, '35', 'Program Pembinaan Lingkungan Sosial bidang Pemberdayaan Ekonomi', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(190, 25, '36', 'Program Pembinaan Lingkungan Sosial Bidang Ketenagakerjaan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(191, 32, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(192, 32, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(193, 32, '6', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(194, 35, '21', 'Program Penelitian dan Pengembangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(195, 32, '21', 'Program perencanaan pembangunan daerah', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(196, 32, '26', 'Program Perencanaan Bidang Infrastruktur dan Pengembangan Wilayah', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(197, 32, '27', 'Program Perencanaan Bidang Ekonomi dan SDA', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(198, 32, '28', 'Program Perencanaan Bidang Sosbud dan Penmas', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(199, 33, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(200, 33, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(201, 33, '5', 'Program Peningkatan Kapasitas Sumber Daya Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(202, 33, '6', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(203, 33, '17', 'Program peningkatan dan pengembangan pengelolaan keuangan daerah', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(204, 33, '18', 'Program pembinaan dan fasilitasi pengelolaan keuangan kabupaten/kota', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(205, 33, '19', 'Program Penatausahaan Aset dan Akuntansi', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(206, 33, '22', 'Program Peningkatan Pengelolaan Belanja Tidak Langsung', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(207, 33, '01bapenda', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(208, 33, '02bapenda', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(209, 33, '20bapenda', 'Program Pengembangan dan Penetapan Pendapatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(210, 33, '21bapenda', 'Program Penatausahaan, Verifikasi dan Penagihan Pendapatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(211, 34, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(212, 34, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(213, 34, '6', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(214, 34, '29', 'Program Penyusunan, Penetapan, Kebutuhan dan Pengadaan serta Pengembangan Karier dan Kompetensi Pegawai', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(215, 34, '30', 'Program Mutasi Pegawai', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(216, 34, '31', 'Program Pembinaan, Data, dan Kesejahteraan Pegawai', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(217, 36, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(218, 36, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(219, 36, '5', 'Program Peningkatan Kapasitas Sumber Daya Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(220, 36, '6', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(221, 36, '23', 'Program Pembinaan dan Pengawasan Penyelenggaraan Pemerintahan Daerah', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(222, 30, '01umum', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(223, 30, '02umum', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(224, 30, '16umum', 'Program peningkatan pelayanan kedinasan kepala daerah/wakil kepala daerah', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(225, 30, '01hukum', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(226, 30, '02hukum', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(227, 30, '06hukum', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(228, 30, '07hukum', 'Program Penataan Peraturan Perundang-Undangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(229, 30, '01bagor', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(230, 30, '02bagor', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(231, 30, '18bagor', 'Program Peningkatan Kualitas Pelayanan Publik', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(232, 30, '32', 'Program Tatalaksana dan Kelembagaan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(233, 30, '33bagor', 'Program Kinerja dan Reformasi Birokrasi', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(234, 30, '0adbang', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(235, 30, '02adbang', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(236, 30, '06adbang', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(237, 30, '42adbang', 'Program Penunjang Administrasi Pemerintahan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(238, 30, '01perek', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(239, 30, '02perek', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(240, 30, '35perek', 'Program Penunjang Perekonomian dan Sumber Daya Alam', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(241, 30, '01kesra', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(242, 30, '01humas', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(243, 30, '02humas', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(244, 30, '01adpem', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(245, 30, '02adpem', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(246, 30, '03adpem', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(247, 30, '42adpem', 'Program Penunjang Administrasi Pemerintahan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(248, 31, '1', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(249, 31, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(250, 31, '3', 'Program peningkatan disiplin aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(251, 31, '20', 'Program peningkatan sistem pengawasan internal dan pengendalian pelaksanaan kebijakan KDH', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(252, 31, '39', 'Program Penyelenggaraan Lembaga Perwakilan Rakyat Daerah', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(253, 31, '40', 'Program Hubungan Masyarakat dan Hubungan Antar Lembaga', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(254, 31, '41', 'Program Perencanaan dan Keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(255, 37, '01bal', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(256, 37, '02bal', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(257, 37, '06bal', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(258, 37, '29bal', 'Program Penyelenggaraan Pemerintahan di Kecamatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(259, 37, '01dag', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(260, 37, '29dag', 'Program Penyelenggaraan Pemerintahan di Kecamatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(261, 37, '01dol', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(262, 37, '02dol', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(263, 37, '29dol', 'Program Penyelenggaraan Pemerintahan di Kecamatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(264, 37, '01gem', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(265, 37, '02gem', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(266, 37, '29gem', 'Program Penyelenggaraan Pemerintahan di Kecamatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(267, 37, '01jiw', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(268, 37, '02jiw', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(269, 37, '03jiw', 'Program Penyelenggaraan Pemerintahan di Kecamatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(270, 37, '01keb', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(271, 37, '02keb', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(272, 37, '29keb', 'Program Penyelenggaraan Pemerintahan di Kecamatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(273, 37, '01kar', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(274, 37, '02kar', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(275, 37, '29kar', 'Program Penyelenggaraan Pemerintahan di Kecamatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(276, 37, '01mad', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(277, 37, '02mad', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(278, 37, '29mad', 'Program Penyelenggaraan Pemerintahan di Kecamatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(279, 37, '01mej', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(280, 37, '02mej', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(281, 37, '29mej', 'Program Penyelenggaraan Pemerintahan di Kecamatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(282, 37, '01pil', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(283, 37, '02pil', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(284, 37, '03pil', 'Program Penyelenggaraan Pemerintahan di Kecamatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(285, 37, '01saw', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(286, 37, '02saw', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(287, 37, '29saw', 'Program Penyelenggaraan Pemerintahan di Kecamatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(288, 37, '01sar', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(289, 37, '02sar', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(290, 37, '03sar', 'Program Penyelenggaraan Pemerintahan di Kecamatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(291, 37, '01wun', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(292, 37, '02wun', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(293, 37, '29wun', 'Program Penyelenggaraan Pemerintahan di Kecamatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(294, 37, '01won', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(295, 37, '02won', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(296, 37, '06won', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(297, 37, '29won', 'Program Penyelenggaraan Pemerintahan di Kecamatan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(298, 37, '01kelbd', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(299, 37, '02kelbd', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(300, 37, '30kelbd', 'Program Penyelenggaraan Pemerintahan di Kelurahan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(301, 37, '01kelml', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(302, 37, '02kelml', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(303, 37, '30kelml', 'Program Penyelenggaraan Pemerintahan di Kelurahan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(304, 37, '01kelng', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(305, 37, '02kelng', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(306, 37, '30kelng', 'Program Penyelenggaraan Pemerintahan di Kelurahan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(307, 37, '01kelbm', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(308, 37, '02kelbm', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(309, 37, '30kelbm', 'Program Penyelenggaraan Pemerintahan di Kelurahan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(310, 37, '01kelkr', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(311, 37, '02kelkr', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(312, 37, '30kelkr', 'Program Penyelenggaraan Pemerintahan di Kelurahan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(313, 37, '01kelpa', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(314, 37, '02kelpa', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(315, 37, '30kelpa', 'Program Penyelenggaraan Pemerintahan di Kelurahan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(316, 37, '01kelwu', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(317, 37, '02kelwu', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(318, 37, '06kelwu', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(319, 37, '30kelwu', 'Program Penyelenggaraan Pemerintahan di Kelurahan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(320, 37, '01kelmu', 'Program Pelayanan Administrasi Perkantoran', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(321, 37, '02kelmu', 'Program Peningkatan Sarana dan Prasarana Aparatur', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(322, 37, '30kelmu', 'Program Penyelenggaraan Pemerintahan di Kelurahan', '2019', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(323, 39, '8', 'Program Pelayanan Kesekretariatan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(324, 39, '23', 'Program Bantuan Operasional Sekolah', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(325, 40, '8', 'Program Pelayanan Kesekretariatan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(326, 40, '22', 'Program Pencegahan dan Pengendalian Penyakit', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(327, 40, '43', 'Program Penyelenggaraan BLUD Puskesmas', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(328, 40, '06car', 'Program peningkatan pengembangan sistem pelaporan capaian kinerja dan keuangan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(329, 40, '26car', 'Program pengadaan, peningkatan sarana dan prasarana rumah sakit/ rumah sakit jiwa/ rumah sakit paru-paru/ rumah sakit mata', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(330, 40, '47a', 'Program pelayanan kesehatan Rujukan (DAK)', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(331, 40, '48a', 'Program Pembinaan Lingkungan Sosial bidang Kesehatan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02');
INSERT INTO `programs` (`id`, `urusan_id`, `kode`, `deskripsi`, `tahun_perubahan`, `status_aturan`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(332, 40, '01b', 'Program Pelayanan Administrasi Perkantoran', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(333, 40, '26b', 'Program pengadaan, peningkatan sarana dan prasarana rumah sakit/ rumah sakit jiwa/ rumah sakit paru-paru/ rumah sakit mata', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(334, 40, '33b', 'Program Peningkatan Pelayanan Rumah Sakit', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(335, 40, '44b', 'Program Penatalaksanaan Keuangan dan Akuntansi', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(336, 40, '45b', 'Program Penyelenggaraan Pelayanan Medis dan Keperawatan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(337, 40, '46b', 'Program Penyelenggaraan Penunjang Medis dan Non Medis', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(338, 40, '47b', 'Program Pelayanan Kesehatan Rujukan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(339, 40, '48b', 'Program Pembinaan Lingkungan Sosial bidang Kesehatan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(340, 40, 'xx', 'Program Penataan Penguasaan, Pemilikan, Penggunaan dan Pemanaatan Tanah', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(341, 1, '8', 'Program Pelayanan Kesekretariatan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(342, 1, '18', 'Program Rehabilitasi/pemeliharaan Jalan dan Jembatan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(343, 1, '26', 'Program Pengembangan, Pengelolaan, dan Konservasi Sungai, Danau dan Sumber Daya Air Lainnya', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(344, 1, '49', 'Program Pembinaan Lingkungan Sosial bidang Infrastruktur', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(345, 1, '45', 'Program Pembangunan dan Peningkatan Jalan dan Jembatan', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(346, 1, '47', 'Program Pengembangan dan Pengelolaan Jaringan Irigasi dan Jaringan Pengairan Lainnya', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(347, 1, 'xx', 'Program Perencanaan, Pemanfaatan dan Pengendalian Tata Ruang', '2020', 'Sebelum Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(348, 39, '3', 'Program Pengembangan Kurikulum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(349, 39, '4', 'Program Pendidik dan Tenaga Kependidikan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(350, 40, '4', 'Program Sediaan Farmasi, Alat Kesehatan dan Makanan Minuman', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(351, 40, '01a', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(352, 40, '02a', 'Program Pemenuhan Upaya Kesehatan Perorangan dan Upaya Kesehatan Masyarakat', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(353, 40, '03a', 'Program Peningkatan Kapasitas Sumber Daya Manusia Kesehatan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(354, 40, '02b', 'Program Pemenuhan Upaya Kesehatan Perorangan dan Upaya Kesehatan Masyarakat', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(355, 40, '03b', 'Program Peningkatan Kapasitas Sumber Daya Manusia Kesehatan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(356, 1, '3', 'Program Pengelolaan dan Pengembangan Sistem Penyediaan Air Minum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(357, 1, '5', 'Program Pengelolaan dan Pengembangan Sistem Air Limbah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(358, 1, '7', 'Program Pengembangan Permukiman', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(359, 1, '9', 'Program Penataan Bangunan dan Lingkungannya', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(360, 1, '10', 'Program Penyelenggaraan Jalan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(361, 1, '11', 'Program Pengembangan Jasa Konstruksi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(362, 1, '12', 'Program Penyelenggaraan Penataan Ruang', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(363, 2, '3', 'Program Kawasan Permukiman', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(364, 2, '4', 'Program Perumahan Dan Kawasan Permukiman Kumuh', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(365, 2, '5', 'Program Peningkatan Prasarana, Sarana dan Utilitas Umum (PSU)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(366, 3, '4', 'Program Pencegahan, Penanggulangan, Penyelamatan Kebakaran dan Penyelamatan Non Kebakaran', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(367, 3, '01a', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(368, 3, '3', 'Program Penanggulangan Bencana', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(369, 4, '4', 'Program Rehabilitasi Sosial', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(370, 4, '5', 'Program Perlindungan dan Jaminan Sosial', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(371, 4, '6', 'Program Penanganan Bencana', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(372, 5, '3', 'Program Pelatihan Kerja dan Produktivitas Tenaga Kerja', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(373, 5, '4', 'Program Penempatan Tenaga Kerja', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(374, 5, '5', 'Program Hubungan Industrial', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(375, 6, '3', 'Program Perlindungan Perempuan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(376, 6, '6', 'Program Pemenuhan Hak Anak (PHA)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(377, 7, '3', 'Program Peningkatan Diversifikasi dan Ketahanan Pangan Masyarakat', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(378, 7, '4', 'Program Penanganan Kerawanan Pangan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(379, 7, '5', 'Program Pengawasan Keamanan Pangan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(380, 8, '5', 'ProgramPenyelesaian Ganti Kerugian dan Santunan Tanah Untuk Pembangunan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(381, 8, '8', 'Program Pengelolaan Tanah Kosong', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(382, 8, '4', 'Program Penyelesaian Sengketa Tanah Garapan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(383, 8, '10', 'Program Penatagunaan Tanah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(384, 8, '9', 'Program Pengelolaan Izin Membuka Tanah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(385, 9, '3', 'Program Pengendalian Pencemaran dan/atau Kerusakan Lingkungan Hidup', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(386, 9, '4', 'Program Pengelolaan Keanekaragaman Hayati (KEHATI)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(387, 9, '5', 'Program Pengendalian Bahan Berbahaya dan Beracun (B3) dan Limbah Bahan Berbahaya dan Beracun (Limbah B3)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(388, 9, '8', 'Program Peningkatan Pendidikan, Pelatihan dan Penyuluhan Lingkungan Hidup Untuk Masyarakat', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(389, 9, '9', 'Program Penghargaan Lingkungan Hidup Untuk Masyarakat', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(390, 9, '10', 'Program Penanganan Pengaduan Lingkungan Hidup', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(391, 9, '11', 'Program Pengelolaan Persampahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(392, 10, '4', 'Program Pengelolaan Informasi Administrasi Kependudukan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(393, 11, '3', 'Program Peningkatan Kerjasama Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(394, 11, '4', 'Program Administrasi Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(395, 11, '5', 'Program Pemberdayaan Lembaga Kemasyarakatan, Lembaga Adat dan Masyarakat Hukum Adat', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(396, 12, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(397, 12, '2', 'Program Pengendalian Penduduk', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(398, 12, '3', 'Program Pembinaan Keluarga Berencana (KB)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(399, 12, '4', 'Program Pemberdayaan dan Peningkatan Keluarga Sejahtera (KS)', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(400, 14, '3', 'Program Aplikasi Informatika', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(401, 15, '3', 'Program Pengawasan dan Pemeriksaan Koperasi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(402, 15, '4', 'Program Penilaian Kesehatan KSP/USP Koperasi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(403, 15, '5', 'Program Pendidikan dan Latihan Perkoperasian', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(404, 15, '7', 'Program Pemberdayaan Usaha Menengah, Usaha Kecil, dan Usaha Mikro', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(405, 15, '8', 'Program Pengembangan UMKM', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(406, 16, '3', 'Program Promosi Penanaman Modal', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(407, 16, '4', 'Program Pelayanan Penanaman Modal', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(408, 16, '5', 'Program Pengendalian Pelaksanaan Penanaman Modal', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(409, 17, '3', 'Program Pengembangan Kapasitas Daya Saing Keolahragaan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(410, 18, '2', 'Program Penyelenggaraan Statistik Sektoral', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(411, 19, '2', 'Program Penyelenggaraan Persandian untuk Pengamanan Informasi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(412, 20, '2', 'Program Pengembangan Kebudayaan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(413, 20, '4', 'Program Pembinaan Sejarah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(414, 20, '5', 'Program Pelestarian dan Pengelolaan Cagar Budaya', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(415, 21, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(416, 21, '2', 'Program Pembinaan Perpustakaan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(417, 22, '2', 'Program Pengelolaan Arsip', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(418, 22, '3', 'Program Perlindungan Dan Penyelamatan Arsip', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(419, 23, '4', 'Program Pengelolaan Perikanan Budidaya', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(420, 23, '6', 'Program Pengolahan dan Pemasaran Hasil Perikanan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(421, 23, '3', 'Program Pengelolaan Perikanan Tangkap', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(422, 24, '2', 'Program Peningkatan Daya Tarik Destinasi Pariwisata', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(423, 24, '3', 'Program Pemasaran Pariwisata', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(424, 24, '5', 'Program Pengembangan Sumber Daya Pariwisata dan Ekonomi Kreatif', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(425, 25, '02a', 'Program Penyediaan dan Pengembangan Sarana Pertanian', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(426, 25, '4', 'Program Pengendalian Kesehatan Hewan dan Kesehatan Masyarakat Veteriner', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(427, 25, '5', 'Program Pengendalian dan Penanggulangan Bencana Pertanian', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(428, 25, '3', 'Program Penyediaan dan Pengembangan Prasarana Pertanian', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(429, 25, '7', 'Program Penyuluhan Pertanian', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(430, 27, '2', 'Program Perizinan dan Pendaftaran Perusahaan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(431, 27, '3', 'Program Peningkatan Sarana Distribusi Perdagangan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(432, 27, '4', 'Program Stabilitasi Harga Barang Kebutuhan Pokok dan Barang Penting', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(433, 27, '5', 'Program Pengembangan Ekspor', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(434, 27, '6', 'Program Standarisasi dan Perlindungan Konsumen', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(435, 27, '7', 'Program Penggunaan Dan Pemasaran Produk Dalam Negeri', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(436, 28, '2', 'Program Perencanaan dan Pembangunan Industri', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(437, 28, '3', 'Program Pengendalian Izin Usaha Industri', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(438, 28, '4', 'Program Pengelolaan Sistem Informasi Industri Nasional', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(439, 29, '4', 'Program Pengembangan Kawasan Transmigrasi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(440, 30, '01um', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(441, 8, '6', 'Program Redistribusi Tanah, Serta Ganti Kerugian Program Tanah Kelebihan Maksimum Dan Tanah Absentee', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(442, 30, '01bo1', 'Program Penunjang Urusan Pemerintahan Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(443, 30, '01bp', 'Program Penunjang Urusan Pemerintahan Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(444, 30, '02bh', 'Program Pemerintahan dan Kesejahteraan Rakyat', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(445, 30, '02bk2', 'Program Pemerintahan dan Kesejahteraan Rakyat', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(446, 30, '02bape', 'Program Pemerintahan dan Kesejahteraan Rakyat', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(447, 30, '02jas', 'Program Perekonomian dan Pembangunan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(448, 30, '03Per', 'Program Perekonomian Dan Pembangunan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(449, 32, '3', 'Program Koordinasi dan Sinkronisasi Perencanaan Pembangunan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(450, 35, '2', 'Program Penelitian dan Pengembangan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(451, 33, '3', 'Program Pengelolaan Barang Milik Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(452, 33, '01pen', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(453, 33, '4', 'Program Pengelolaan Pendapatan Daerah', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(454, 36, '02a', 'Program Penyelenggaraan Pengawasan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(455, 36, '3', 'Program Perumusan Kebijakan, Pendampingan dan Asistensi', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(456, 38, '1', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(457, 38, '2', 'Program Penguatan Ideologi Pancasila dan Karakter Kebangsaan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(458, 38, '3', 'Program Peningkatan Peran Partai Politik dan Lembaga Pendidikan Melalui Pendidikan Politik dan Pengembangan Etika Serta Budaya Politik', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(459, 38, '4', 'Program Pemberdayaan dan Pengawasan Organisasi Kemasyarakatan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(460, 38, '6', 'Program Peningkatan Kewaspadaan Nasional dan Peningkatan Kualitas dan Fasilitasi Penanganan Konflik Sosial', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(461, 38, '5', 'Program Pembinaan dan Pengembangan Ketahanan Ekonomi, Sosial, dan Budaya', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(462, 37, '03bal', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(463, 37, '04bal', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(464, 37, '05bal', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(465, 37, '02dag', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(466, 37, '03dag', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(467, 37, '04dag', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(468, 37, '05dag', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(469, 37, '06dag', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(470, 37, '03dol', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(471, 37, '04dol', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(472, 37, '05dol', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(473, 37, '06dol', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(474, 37, '01geg', 'Program penunjang urusan pemerintahan daerah kabupaten/kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(475, 37, '02geg', 'Program Penyelenggaraan Pemerintahan dan Pelayanan Publik', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(476, 37, '03geg', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(477, 37, '04geg', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(478, 37, '05geg', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(479, 37, '06geg', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(480, 37, '03gem', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(481, 37, '04gem', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(482, 37, '05gem', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(483, 37, '06gem', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(484, 37, '04jiw', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(485, 37, '05jiw', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(486, 37, '06jiw', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(487, 37, '03keb', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(488, 37, '04keb', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(489, 37, '05keb', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(490, 37, '06keb', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(491, 37, '03kar', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(492, 37, '04kar', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(493, 37, '05kar', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(494, 37, '06kar', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(495, 37, '03mad', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(496, 37, '04mad', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(497, 37, '05mad', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(498, 37, '06mad', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(499, 37, '03mej', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(500, 37, '04mej', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(501, 37, '05mej', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(502, 37, '06mej', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(503, 37, '04pil', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(504, 37, '05pil', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(505, 37, '06pil', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(506, 37, '04sar', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(507, 37, '05sar', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(508, 37, '06sar', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(509, 37, '03saw', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(510, 37, '04saw', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(511, 37, '05saw', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(512, 37, '06saw', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(513, 37, '03wun', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(514, 37, '04wun', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(515, 37, '05wun', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(516, 37, '06wun', 'Program Pembinaan Dan Pengawasan Pemerintahan Desa', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(517, 37, '03won', 'Program Pemberdayaan Masyarakat Desa Dan Kelurahan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(518, 37, '04won', 'Program Koordinasi Ketentraman dan Ketertiban Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(519, 37, '05won', 'Program Penyelenggaraan Urusan Pemerintahan Umum', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(520, 6, '7', 'Program Perlindungan Khusus Anak', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(521, 36, '02b', 'Program Penyelenggaraan Pengawasan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(522, 30, '01b02', 'Program Penunjang Urusan Pemerintahan Daerah Kabupaten/Kota', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(523, 25, '02b', 'Program Penyediaan dan Pengembangan Sarana Pertanian', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(524, 30, '02bk1', 'Persentase Rumusan Kebijakan Bidang Kesejahteraan Rakyat yang ditetapkan sesuai kebutuhan', '2021', 'Sesudah Perubahan', 62, '2022-11-12 12:18:02', '2022-11-12 12:18:02'),
(525, 30, '01bo2', 'Program Penunjang Urusan Pemerintahan Daerah Kabupaten/Kota', '2022', 'Sesudah Perubahan', 62, '2022-11-12 12:18:03', '2022-11-12 12:18:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `program_indikator_kinerjas`
--

CREATE TABLE `program_indikator_kinerjas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `program_indikator_kinerjas`
--

INSERT INTO `program_indikator_kinerjas` (`id`, `program_id`, `deskripsi`, `created_at`, `updated_at`) VALUES
(93, 371, 'Persentase personil Tagana yang dibina', '2022-11-13 02:22:08', '2022-11-13 02:22:08'),
(144, 398, 'Persentase masyarakat yang memahami program Bangga kencana (Pembangunan Keluarga, Kependudukan dan Keluarga Berencana)', '2022-11-13 02:22:08', '2022-11-13 02:22:08'),
(220, 246, 'Jumlah Rumusan Kebijakan Pengelolaan Barang dan Jasa yang ditindaklanjuti', '2022-11-13 02:22:08', '2022-11-13 02:22:08'),
(305, 484, 'Persentase Layanan Ketenteraman dan Ketertiban Umum', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(387, 10, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(415, 354, 'Persentase indikator SPM bidang penunjang yang memenuhi standar', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(418, 43, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(432, 58, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(441, 64, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(447, 367, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(452, 82, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(453, 83, 'Persentase PSKS yang berpartisipasi aktif dalam penyelenggaraan kesejahteraan sosial', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(460, 88, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(469, 104, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(478, 111, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(490, 120, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(497, 128, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(506, 396, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(509, 398, 'Persentase masyarakat yang memahami program Banggakencana (Pembangunan Keluarga, Kependudukan dan Keluarga Berencana)', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(512, 134, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(513, 135, 'persentase sarana prasarana dan perlengkapan jalan yang berfungsi baik', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(517, 141, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(521, 147, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(525, 403, 'Persentase Koperasi yang telah mengikuti Pendidikan dan Pelatihan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(531, 161, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(538, 167, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(547, 415, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(559, 179, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(578, 440, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(612, 453, 'Program Pengelolaan Pendapatan Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(645, 522, 'Persentase PD Pengampu pelayanan masyarakat yang sesuai dengan mutu pelayanan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(672, 268, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(741, 1, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(742, 1, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(743, 2, 'Angka partisipasi pendidikan kesetaraan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(744, 2, 'Persentase lembaga pendidikan kesetaraan yang terakreditasi', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(745, 2, 'APS SD', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(746, 2, 'Persentase lembaga SD terakreditasi A', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(747, 2, 'APS SMP', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(748, 2, 'Persentase lembaga SMP terakreditasi A', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(749, 2, 'APS PAUD', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(750, 2, 'Persentase lembaga pendidikan PAUD yang terakreditasi', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(751, 348, 'Prosentase dokumen kurikulum SD yang dilaksanakan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(752, 348, 'Prosentase dokumen kurikulum PAUD yang dilaksanakan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(753, 349, 'Persentase tenaga pendidik yang tersertifikasi', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(754, 349, 'Persentase guru yang memenuhi kualifikasi S1/DIV', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(755, 10, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(756, 10, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(757, 11, 'Angka Kematian Ibu', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(758, 11, 'Angka Kematian Bayi', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(759, 11, 'Prevalensi Balita Stunting', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(760, 11, 'Persentase pemenuhan sarana, prasarana, dan peralatan puskesmas', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(761, 11, 'Persentase pemenuhan sarana, prasarana, dan peralatan rumah sakit', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(762, 11, 'Persentase masyarakat yang mendapat pelayanan kesehatan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(763, 26, 'Persentase sumber daya manusia kesehatan yang memenuhi standar kompetensi', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(764, 26, 'Persentase peningkatan kompetensi sumber daya manusia kesehatan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(765, 350, 'Persentase indikator SPM bidang pelayanan yang memenuhi standar', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(766, 12, 'Persentase Desa Siaga Aktif Purnama Mandiri', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(767, 351, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(768, 351, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(769, 351, 'Persentase capaian indikator SPM bidang keuangan sesuai dengan standar', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(770, 351, 'Persentase Capaian indikator SPM bidang tata usaha sesuai dengan standar', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(771, 351, 'Persentase capaian indikator SPM bidang pelayanan yang memenuhi standar', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(772, 351, 'Persentase capaian indikator SPM bidang penunjang yang memenuhi standar', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(773, 352, 'Persentase Capaian indikator SPM bidang tata usaha sesuai dengan standar', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(774, 352, 'Persentase capaian indikator SPM bidang pelayanan yang memenuhi standar', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(775, 352, 'Persentase capaian indikator SPM bidang penunjang yang memenuhi standar', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(776, 353, 'Persentase sumber daya manusia kesehatan yang memenuhi standar kompetensi', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(777, 332, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(778, 332, 'Kepuasan ASN terhadap pelayanan sekretariat perangkat daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(779, 332, 'Persentase capaian indikator SPM bidang keuangan sesuai dengan standar', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(780, 332, 'Persentase Capaian indikator SPM bidang tata usaha sesuai dengan standar', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(781, 332, 'Persentase indikator SPM bidang pelayanan yang memenuhi standar', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(782, 332, 'Persentase indikator SPM bidang penunjang yang memenuhi standar', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(783, 354, 'Persentase indikator SPM bidang pelayanan yang memenuhi standar', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(786, 355, 'Persentase sumber daya manusia kesehatan yang memenuhi standar kompetensi', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(787, 43, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(788, 43, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(789, 44, 'Persentase luas baku sawah yang terlayani air irigasi', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(790, 356, 'Persentase perluasan akses pelayanan air bersih (SR)', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(791, 357, 'Persentase sarana limbah domestik setempat yang terbangun', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(792, 45, 'Persentase saluran drainase kondisi baik', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(793, 45, 'Persentase trotoar kondisi baik', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(794, 358, 'Persentase panjang jalan lingkungan kondisi baik', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(795, 341, 'Persentase penyelenggaraan bangunan gedung pemerintah yang terpenuhi', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(796, 359, 'Persentase bangunan dan lingkungan yang ditata', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(797, 360, 'Persentase panjang jalan kabupaten kondisi mantap', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(798, 360, 'Persentase jumlah jembatan kondisi baik', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(799, 361, 'Persentase peningkatan jumlah SDM jasa konstruksi yang bersertifikat', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(800, 362, 'Persentase dokumen rencana umum tata ruang dan rencana rinci tata ruang yang tersusun', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(801, 58, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(802, 58, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(803, 59, 'Persentase rumah tidak layak huni yang tertangani', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(804, 59, 'Prosentase rumah layak huni yang terbangun bagi korban bencana', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(805, 59, 'Persentase rumah layak huni yang terbangun bagi masyarakat yang terkena relokasi program Pemerintah Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(806, 363, 'Cakupan kawasan kumuh yang tertangani', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(807, 364, 'Prosentase kawasan kumuh baru yang tertangani', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(808, 365, 'Cakupan perumahan yang telah ditingkatkan prasarana, sarana, dan utilitas umumnya', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(809, 60, 'Persentase Orang/Badan Hukum yang Melaksanakan Perancangan dan Perencanaan Rumah serta Perencanaan PSU Tingkat Kemampuan Kecil', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(810, 64, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(811, 64, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(812, 65, 'Persentase kasus ketenteraman dan ketertiban umum yang diselesaikan sesuai ketentuan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(813, 65, 'Persentase kasus Pelanggaran Perda dan Perkada yang diselesaikan sesuai ketentuan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(814, 366, 'Persentase kasus kebakaran yang tertangani', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(815, 366, 'Persentase kasus non kebakaran yang tertangani', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(816, 367, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(817, 367, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(818, 368, 'Persentase desa/kelurahan tangguh bencana terbentuk', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(819, 368, 'Persentase korban terdampak bencana yang ditangani', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(820, 368, 'Persentase pemulihan pasca bencana yang direalisasikan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(821, 82, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(822, 82, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(823, 83, 'Persentase PSKS yang berpartisipasi aktif dalam penyelenggaraan kesejahteraansosial', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(824, 83, 'Persentase desa / kelurahan yang diberdayakan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(825, 369, 'Persentase PPKS yang tertangani', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(826, 370, 'Persentase kepesertaan jaminan perlindungan sosial untuk masyarakat miskin dan rentan miskin', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(827, 371, 'Persentase Korban Bencana Alam yang menerima bantuan dan bantuan khusus pasca Bencana', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(828, 371, 'Persentase personil Tanaga yang dibina', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(829, 88, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(830, 88, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(831, 372, 'Persentase lulusan pelatihan kerja yang bekerja', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(832, 373, 'Persentase pencari kerja yang ditempatkan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(833, 374, 'Angka sengketa Perusahaan Pekerja Per tahun', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(834, 98, 'Persentase lembaga penyedia layanan pemberdayaan perempuan yang aktif', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(835, 375, 'Rasio kekerasan terhadap perempuan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(836, 376, 'Persentase lembaga penyedia layanan peningkatan kualitas hidup anak yang aktif', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(837, 104, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(838, 377, 'Tingkat Capaian Angka Kecukupan Energi dan protein', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(839, 378, 'Persentase Daerah Rawan Pangan yang Tertangani', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(840, 379, 'Persentase pangan segar asal tanaman (PSAT) yang aman', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(841, 380, 'Jumlah Ganti Kerugian dan Santunan Tanah untuk Kepentingan Umum yang Terselesaikan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(842, 381, 'Jumlah dokumen pengelolaan tanah kosong', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(843, 382, 'Persentase Sengketa Tanah Yang Tertangani', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(844, 383, 'Jumlah dokumen penatagunaan tanah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(845, 384, 'Persentase Penyelenggaraan Perizinan membuka tanah yang dikelola', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(846, 104, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(847, 111, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(848, 111, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(849, 112, 'Tersusunnya dan terlaksananya dokumen perencanaan lingkungan hidup', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(850, 385, 'Persentase layanan pelaku usaha dan kegiatan yang menerapkan dokumen lingkungan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(851, 386, 'Cakupan penghijauan wilayah potensi longsor dan sumber mata air', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(852, 386, 'Luas Ruang Terbuka Hijau (RTH) yang dikelola', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(853, 387, 'Persentase industri yang menerapkan sistem pengolahan limbah B3', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(854, 520, 'Rasio kekerasan terhadap anak', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(855, 113, 'Persentase izin lingkungan dan izin perlindungan dan pengelolaan lingkungan hidup yang diterbitkan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(856, 388, 'Cakupan masyarakat yang mendapatkan pendidikan, pelatihan, dan penyuluhan lingkungan hidup', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(857, 389, 'Jumlah penerima penghargaan lingkungan hidup', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(858, 390, 'Persentase pengaduan lingkungan hidup yang tertangani', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(859, 391, 'Persentase sampah yang tertangani', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(860, 120, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(861, 120, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(862, 121, 'Persentase penduduk yang sudah menerima dokumen kependudukan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(863, 121, 'Persentase penduduk yang sudah memiliki dokumen kependudukan', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(864, 122, 'Persentase penduduk yang memiliki dokumen pencatatan sipil', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(865, 392, 'Persentase database kependudukan yang valid', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(866, 392, 'Persentase penyajian data kependudukan skala Kabupaten dalam satu tahun', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(867, 128, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(868, 128, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(869, 129, 'Persentase Fasilitasi Penyelenggaraan Penataan Desa', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(870, 393, 'Jumlah kerja sama Desa yang terbentuk', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(871, 394, 'Persentase desa dengan tata kelola pemerintahan desa yang baik', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(872, 394, 'Persentase Desa dengan dokumen perencanaan pembangunan yang baik', '2022-11-13 02:22:09', '2022-11-13 02:22:09'),
(873, 395, 'Persentase Lembaga Kemasyarakatan Desa/Kelurahan yang aktif', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(874, 395, 'Persentase BUMDesa yang aktif', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(875, 395, 'Persentase Lembaga Ekonomi yang aktif', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(876, 396, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(877, 396, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(878, 397, 'Persentase dokumen data informasi kependudukan yang tersusun', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(879, 398, 'Persentase pasangan usia subur yang tidak ber KB karena Unmeet Need', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(880, 398, '\"Persentase masyarakat yang memahami program Banggakencana (Pembangunan Keluarga, Kependudukan dan Keluarga Berencana) \"', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(881, 399, 'Persentase perkawinan dengan usia istri dibawah 20 Tahun', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(882, 134, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(883, 134, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(884, 135, 'Persentase sarana prasarana dan perlengkapan jalan yang berkeselamatan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(885, 135, 'Persentase kendaraan laik jalan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(886, 135, 'Persentase angka tertib lalu lintas', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(887, 141, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(888, 141, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(889, 142, 'Presentase Desiminasi layanan informasi publik yang dilaksanakan sesuai dengan strategi komunikasi (STRAKOM) dan SOP yang ditetapkan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(890, 400, 'Persentase Perangkat Daerah yang menerapkan aplikasi layanan SPBE', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(891, 147, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(892, 147, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(893, 401, 'Persentase koperasi yang berkualitas', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(894, 402, 'Persentase koperasi yang sehat', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(895, 403, 'Persentase Koperasi yang telah mengikuti Pendidikan dan Pelatihan.', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(896, 149, 'Persentase koperasi yang telah diberdayakan dan dilindungi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(897, 404, 'Persentase peningkatan Pemberdayaan Usaha Mikro, Kecil, dan Menengah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(898, 405, 'Persentase Peningkatan pengembangan UMKM', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(899, 148, 'Persentase Perijinan Koperasi yang diterbitkan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(900, 161, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(901, 161, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(902, 406, 'Minat Investasi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(903, 407, 'Rata-rata waktu penyelesaian perijinan dan Non Perijinan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(904, 408, 'Persentase perusahaan yang tertib menyampaikan LKPM', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(905, 162, 'Persentase investor yang difasilitasi dalam kegiatan penanaman modal', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(906, 163, 'Persentase peningkatan jumlah masyarakat yang memanfaatkan layanan perizinan dan non perizinan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(907, 167, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(908, 167, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(909, 409, 'Persentase atlet yang berprestasi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(910, 168, 'Persentase pemuda yang berprestasi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(911, 410, 'Persentase data statistik sektoral yang tersedia dan valid', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(912, 411, 'Persentase pengamanan informasi pemerintah daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(913, 412, 'Persentase budaya lokal yang dilestarikan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(914, 413, 'Cakupan pembinaan sejarah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(915, 414, 'Persentase cagar budaya yang ditetapkan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(916, 415, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(917, 415, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(918, 416, 'Persentase Perpustakaan Terakreditasi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(919, 417, 'Indeks Ketersediaan Arsip', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(920, 418, 'Indeks Keberadaan dan Keutuhan Arsip', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(921, 419, 'Persentase Peningkatan Produksi Perikanan Budidaya', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(922, 420, 'Persentase peningkatan hasil Produk Olahan Asal Ikan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(923, 421, 'Persentase Peningkatan Produksi Perikanan Tangkap', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(924, 422, 'Persentase pengembangan daya tarik yang dilaksanakan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(925, 423, 'Persentase pemasaran pariwisata yang dilaksanakan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(926, 424, 'Persentase peningkatan pelaku industri pariwisata', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(927, 424, 'Persentase peningkatan pelaku ekonomi kreatif', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(928, 179, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(929, 179, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(930, 425, 'Persentase kelompok tani yang mendapatkan sarana pertanian', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(931, 426, 'Persentase kasus kesehatan hewan yang tertangani', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(932, 427, 'Persentase lahan pertanian yang bebas dari bencana pertanian', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(935, 428, 'Persentase peningkatan jumlah sarana prasarana pertanian dalam kondisi baik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(936, 428, 'Persentase terpeliharanya prasarana peternakan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(937, 429, 'Persentase peningkatan kelas kelompok tani', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(938, 429, 'Presentase peningkatan kualitas peternak dan pelaku usaha ternak', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(939, 430, 'Persentase peningkatan rekomendasi perizinan yang diterbitkan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(940, 431, 'Persentase peningkatan sarana distribusi perdagangan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(941, 432, 'Persentase Ketersediaan Barang Kebutuhan Pokok dan Barang Penting Lainnya', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(942, 433, 'Persentase peningkatan fasilitasi Produk Ekspor Unggulan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(943, 434, 'Persentase peningkatan pelaksanaan metrologi legal', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(944, 435, 'Persentase peningkatan penjualan produk dalam negeri', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(945, 436, 'Jumlah rencana pembangunan industri', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(946, 437, 'Persentase IKM yang mendapatkan ijin usaha', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(947, 438, 'Persentase IKM yang memanfaatkan SIINas', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(948, 439, 'Persentase transmigran umum yang berhasil', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(949, 440, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(950, 440, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(951, 440, 'Persentase Kegiatan Kepala Daerah dan Wakil Kepala Daerah yang di Fasilitasi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(952, 441, 'Jumlah Tanah, serta Ganti Kerugian Program Tanah Kelebihan Maksimum dan Tanah Absentee yang Teredistribusi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(953, 442, 'Persentase Rumusan Kebijakan KetataLaksanaan Organisasi yang ditetapkan sesuai kebutuhan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(954, 443, 'Persentase Kegiatan Keprotokolan dan Komunikasi Pimpinan Daerah dan Sekretaris Daerah yang di fasilitasi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(955, 444, 'Jumlah Rumusan Kebijakan Penyelenggaraan Bidang hukum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(956, 524, 'Persentase Rumusan Kebijakan Bidang Kesejahteraan Rakyat yang ditetapkan sesuai kebutuhan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(957, 446, 'Persentase Rumusan Kebijakan Bidang Pemerintahan yang ditetapkan sesuai kebutuhan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(958, 246, 'Persentase Rumusan Kebijakan Pembangunan daerah yang ditetapkan sesuai kebutuhan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(959, 447, 'Jumlah Rumusan Kebijakan Pengelolaan Barang dan Jasa yang ditindaklanjuti', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(960, 448, 'Persentase Rumusan Kebijakan Bidang Perekonomian yang ditetapkan sesuai kebutuhan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(961, 525, 'Persentase PD Pengampu pelayanan masyarakat yang sesuai dengan mutu pelayanan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(962, 248, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(963, 248, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(964, 248, 'Persentase penyelenggaraan administrasi DPRD', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(965, 248, 'Persentase layanan keuangan DPRD', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(966, 248, 'Persentase layanan kesejahteraan DPRD', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(967, 249, 'Persentase fasilitasi pembahasan Peraturan Daerah APBD', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(968, 249, 'Persentase fasilitasi pembahasan Peraturan Daerah Non APBD', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(969, 249, 'Persentase fasilitasi penganggaran dan pengawasan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(970, 249, 'Persentase fasilitasi tugas dan fungsi DPRD', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(971, 191, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(972, 191, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(973, 192, 'Persentase perencanaan, pengendalian, dan evaluasi pembangunan daerah yang sesuai dengan ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(974, 449, 'Persentase PD Bidang PPM dengan capaian hasil outcome minimal 75%', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(975, 449, 'Persentase PD Bidang Ekonomi dan SDA dengan capaian hasil outcome minimal 75%', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(976, 449, 'Persentase PD Bidang IPW dengan capaian hasil outcome minimal 75%', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(977, 450, 'Persentase Perangkat Daerahyang difasilitasi dalam penerapan Inovasi Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(978, 450, 'Persentase pemanfaatan hasil kelitbangan yang ditindaklanjuti /diterbitkan /dipublikasikan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(979, 199, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(980, 199, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(981, 200, 'Persentase OPD yang tertib penyusunan laporan keuangan daerah yang sesuai SAP', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(982, 451, 'Persentase OPD yang tertib tata kelola barang milik daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(983, 452, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(984, 452, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(985, 453, 'Persentase peningkatan PAD', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(986, 211, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(987, 211, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(988, 212, 'Persentase penetapan kebutuhan ASN', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(989, 212, 'Persentase mutasi jabatan sesuai kualifikasi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(990, 212, 'Persentase kedisiplinan ASN', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(991, 212, 'Persentase Penilaian Kinerja ASN', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(992, 212, 'Persentase ASN yang mengikuti pengembangan kompetensi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(993, 217, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(994, 217, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(995, 454, 'Persentase OPD yang mendapatkan Nilai hasil Evaluasi SAKIP Memuaskan (A)', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(996, 455, 'Level kapabilitas APIP atau jumlah rumusan kebijakan teknis pengawasan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(997, 455, 'Persentase pendampingan, asistensi, dan verifikasi kepada OPD yang sesuai peraturan berlaku', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(998, 456, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(999, 456, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1000, 457, 'Persentase Penyelenggaraan penguatan ideologi Pancasila dan Karakter Kebangsaan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1001, 458, 'Persentase Penyelenggaraan penguatan ideologi Pancasila dan Karakter Kebangsaan (indikator sama?)', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1002, 459, 'Persentase Organisasi Kemasyarakatan yang dibina', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1003, 460, 'Indeks Keamanan Manusia', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1004, 461, 'Persentase penyelenggaraan pembinaan dan pengembangan ketahanan ekonomi, sosial dan budaya', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1005, 255, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1006, 255, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1007, 256, 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1008, 462, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1009, 463, 'Persentase Layanan Ketenteraman dan Ketertiban Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1010, 464, 'Persentase Layanan Penyelenggaraan Urusan Pemerintahan Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1011, 257, 'Persentase Layanan Pembinaan Dan Pengawasan Pemerintahan Desa', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1012, 259, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1013, 259, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1014, 465, 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1015, 466, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1016, 467, 'Persentase Layanan Ketenteraman dan Ketertiban Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1017, 468, 'Persentase Layanan Penyelenggaraan Urusan Pemerintahan Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1018, 469, 'Persentase Layanan Pembinaan Dan Pengawasan Pemerintahan Desa', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1019, 261, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1020, 262, 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1021, 261, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1022, 470, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1023, 471, 'Persentase Layanan Ketenteraman dan Ketertiban Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1024, 472, 'Persentase Layanan Penyelenggaraan Urusan Pemerintahan Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1025, 473, 'Persentase Layanan Pembinaan Dan Pengawasan Pemerintahan Desa', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1026, 474, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1027, 474, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1028, 475, 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1029, 476, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1030, 477, 'Persentase Layanan Ketenteraman dan Ketertiban Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1031, 478, 'Persentase Layanan Penyelenggaraan Urusan Pemerintahan Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1032, 479, 'Persentase Layanan Pembinaan Dan Pengawasan Pemerintahan Desa', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1033, 264, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1034, 264, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1035, 265, 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1036, 480, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1037, 481, 'Persentase Layanan Ketenteraman dan Ketertiban Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1038, 482, 'Persentase Layanan Penyelenggaraan Urusan Pemerintahan Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1039, 483, 'Persentase Layanan Pembinaan Dan Pengawasan Pemerintahan Desa', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1040, 267, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1041, 267, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1042, 268, 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1043, 269, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1044, 484, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1045, 485, 'Persentase Layanan Penyelenggaraan Urusan Pemerintahan Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1046, 486, 'Persentase Layanan Pembinaan Dan Pengawasan Pemerintahan Desa', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1047, 270, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1048, 270, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1049, 271, 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1050, 487, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1051, 488, 'Persentase Layanan Ketenteraman dan Ketertiban Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1052, 489, 'Persentase Layanan Penyelenggaraan Urusan Pemerintahan Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1053, 490, 'Persentase Layanan Pembinaan Dan Pengawasan Pemerintahan Desa', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1054, 273, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1055, 273, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1056, 274, 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1057, 491, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1058, 492, 'Persentase Layanan Ketenteraman dan Ketertiban Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1059, 493, 'Persentase Layanan Penyelenggaraan Urusan Pemerintahan Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1060, 494, 'Persentase Layanan Pembinaan Dan Pengawasan Pemerintahan Desa', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1061, 276, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1062, 276, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1063, 277, 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1064, 495, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1065, 496, 'Persentase Layanan Ketenteraman dan Ketertiban Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1066, 497, 'Persentase Layanan Penyelenggaraan Urusan Pemerintahan Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1067, 498, 'Persentase Layanan Pembinaan Dan Pengawasan Pemerintahan Desa', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1068, 279, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1069, 279, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1070, 280, 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1071, 499, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1072, 500, 'Persentase Layanan Ketenteraman dan Ketertiban Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1073, 501, 'Persentase Layanan Penyelenggaraan Urusan Pemerintahan Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1074, 502, 'Persentase Layanan Pembinaan Dan Pengawasan Pemerintahan Desa', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1075, 282, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1076, 282, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1077, 283, 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1078, 284, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1079, 503, 'Persentase Layanan Ketenteraman dan Ketertiban Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1080, 504, 'Persentase Layanan Penyelenggaraan Urusan Pemerintahan Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1081, 505, 'Persentase Layanan Pembinaan Dan Pengawasan Pemerintahan Desa', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1082, 288, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1083, 288, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1084, 289, 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1085, 290, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1086, 506, 'Persentase Layanan Ketenteraman dan Ketertiban Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1087, 507, 'Persentase Layanan Penyelenggaraan Urusan Pemerintahan Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1088, 508, 'Persentase Layanan Pembinaan Dan Pengawasan Pemerintahan Desa', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1089, 285, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1090, 285, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1091, 286, 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1092, 509, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1093, 510, 'Persentase Layanan Ketenteraman dan Ketertiban Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1094, 511, 'Persentase Layanan Penyelenggaraan Urusan Pemerintahan Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1095, 512, 'Persentase Layanan Pembinaan Dan Pengawasan Pemerintahan Desa', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1096, 291, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1097, 291, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1098, 292, 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1099, 513, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1100, 514, 'Persentase Layanan Ketenteraman dan Ketertiban Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1101, 515, 'Persentase Layanan Penyelenggaraan Urusan Pemerintahan Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1102, 516, 'Persentase Layanan Pembinaan Dan Pengawasan Pemerintahan Desa', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1103, 294, 'Nilai SAKIP Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1104, 294, 'Kepuasan ASN terhadap pelayanan kesekretariatan Perangkat Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1105, 295, 'Persentase Layanan Penyelenggaraan Pemerintahan Dan Pelayanan Publik Sesuai Ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1106, 517, 'Persentase Layanan Pemberdayaan Masyarakat Desa Dan Kelurahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1107, 518, 'Persentase Layanan Ketenteraman dan Ketertiban Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1108, 519, 'Persentase Layanan Penyelenggaraan Urusan Pemerintahan Umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1109, 296, 'Persentase Layanan Pembinaan Dan Pengawasan Pemerintahan Desa', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1110, 521, 'Persentase rekomendasi hasil pemeriksaan BPK dan Inspektorat yang ditindaklanjuti', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1111, 523, 'Persentase ternak bunting dari pemeriksaan kebuntingan (PKb)', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1121, 7, 'Persentase lembaga SD yang terakreditasi A', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1126, 9, 'Persentase kesenian daerah yang dilestarikan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1127, 9, 'Persentase Benda, situs dan kawasan Cagar Budaya yang dilestarikan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1136, 16, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1137, 17, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1141, 20, 'Prevalensi HIV', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1142, 20, 'Persentase ODGJ berat yang mendapatkanpelayanan kesehatan jiwa sesuai standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1143, 21, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1147, 25, 'Persentase kebutuhan sarana dan prasarana aparatur yang tersedia dengan kondisi baik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1159, 32, 'Persentase indikator SPM bidang penunjang yang memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1160, 32, 'Persentase indikator SPM bidang pelayanan yang memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1161, 33, 'Persentase indikator SPM bidang penunjang yang memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1162, 33, 'Persentase indikator SPM bidang pelayanan yang memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1163, 34, 'Prosentase SPM Bidang Tata Usaha yang dicapai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1164, 35, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1165, 36, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1166, 37, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1167, 38, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1168, 39, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1169, 40, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1170, 41, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1171, 42, 'Prosentase bangunan rumah sakit yang dibangun', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1174, 45, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1176, 47, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1179, 49, 'Cakupan luas sawah yang terairi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1186, 56, 'Persentase Panjang Jalan Kondisi Sedang', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1187, 57, 'Jumlah sarana prasarana wisata yang terbangun', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1189, 59, 'Prosentase Sarana dan prasarana dalam kondisi baik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1190, 60, 'Jumlah Laporan Capaian Kinerja yang terselesaikan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1191, 61, 'Prosentase Rumah Tidak Layak Huni yang tertangani', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1192, 62, 'Prosentase Pemberdayaan Perumahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1193, 63, 'Prosentase Tanah yang bersertifikat', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1194, 64, 'Persentase administrasi perkantoran yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1195, 65, 'Prosentase Sarana dan prasarana dalam kondisi baik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1196, 66, 'Persentase peningkatan kompetensi sumber daya aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1197, 67, 'Presentase Penanganan Gangguan Ketertiban Umum dan Keterntraman Masyarakat', '2022-11-13 02:22:10', '2022-11-13 02:22:10');
INSERT INTO `program_indikator_kinerjas` (`id`, `program_id`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1198, 68, 'Presentase Penurunan Pelanggaran Peraturan Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1199, 69, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1200, 70, 'Persentase sarana dan prasarana kerja yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1201, 71, 'Persentase Kejadian Bencana yang Tertangani', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1202, 72, 'Persentase pemulihan rumah yang rusak akibat bencana alam', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1203, 73, 'Jumlah Desa tangguh bencana yang terbentuk tingkat pratama', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1204, 74, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1205, 75, 'Persentase sarana dan prasarana kerja yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1206, 76, 'Persentase Laporan Kinerja SKPD yang tercapai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1207, 77, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1208, 445, 'Persentase Kegiatan Keagamaan Yang Difasilitasi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1209, 78, 'Prosentase Peran Serta Pemilih Dalam Pengembangan Etika Dan Budaya Politik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1210, 79, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1211, 80, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1212, 81, 'Prosentase Kelembagaan Yang Melaksakan 4 Pilar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1213, 78, 'Prosentase Peran Ormas / LSM', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1214, 82, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1215, 83, 'Persentase sarana dan prasarana kerja yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1216, 84, 'Persentase PMKS yang terpenuhi kebutuhan dasarnya', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1217, 85, 'Persentase PMKS yang tertangani', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1218, 86, 'Persentase PSKS yang berpartisipasi aktif dalam penyelenggaraan kesejahteraan sosial', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1219, 86, 'Persentase penerima jaminan dan perlindungan sosial', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1220, 87, 'Jumlah PMKS yang memperoleh bantuan sosial', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1221, 88, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1222, 89, 'Persentase sarana dan prasarana kerja yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1223, 90, 'Persentase Laporan Kinerja SKPD yang tercapai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1224, 91, 'Persentase lulusan pelatihan yang telah bekerja', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1225, 92, 'Persentase pencari kerja yang ditempatkan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1226, 92, 'Persentase pencari kerja yang dilatih kewirausahaan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1227, 93, 'Angka Sengketa perusahaan-pekerja per tahun', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1228, 94, 'Jumlah peserta pelatihan yang terseleksi sesuai ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1229, 95, 'Jumlah peserta pelatihan yang terseleksi sesuai ketentuan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1230, 96, 'Jumlah transmigran umum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1231, 97, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1232, 98, 'Persentase sarana dan prasarana kerja yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1233, 99, 'Persentase lembaga perempuan yang aktif', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1234, 100, 'Rasio kekerasan terhadap perempuan dan anak', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1235, 101, 'Persentase perkawinan dengan usia istri dibawah 20 tahun', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1236, 102, 'Persentase pasangan usia subur yang tidak ber KB karena Unmeet Need', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1237, 103, 'Persentase penggunaan kontrasepsi jangka panjang (MKJP)', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1238, 104, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1239, 105, 'Persentase sarana dan prasarana kerja yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1240, 106, 'Persentase Laporan Kinerja SKPD yang tercapai (%)', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1241, 107, 'Jumlah Ketersediaan Pangan Utama (Beras)', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1242, 108, 'Skor Pola Pangan Harapan (PPH) Konsumsi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1243, 109, 'Produk unggulan olahan pangan lokal', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1244, 110, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1245, 111, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1246, 112, 'Persentase sarana dan prasarana kerja yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1247, 113, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1248, 114, 'Persentase pelaku usaha dan kegiatan yang menerapkan dokumen lingkungan hidup', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1249, 115, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1250, 116, 'Luas Ruang Terbuka Hijau (RTH) yang dikelola', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1251, 117, 'Persentase pelayanan persampahan di perkotaan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1252, 118, 'Cakupan penghijauan wilayah longsor dan sumber mata air', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1253, 119, 'Persentase ketersediaan sarana prasarana persampahan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1254, 120, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1255, 121, 'Persentase sarana dan prasarana kerja yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1256, 122, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1257, 123, 'Persentase Laporan Kinerja SKPD yang tercapai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1258, 124, 'Cakupan Penerbitan Kartu Tanda Penduduk', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1259, 124, 'Cakupan Penerbitan KIA', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1260, 124, 'Cakupan Penerbitan Kartu Keluarga', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1261, 125, 'Cakupan Penerbitan Akte Kelahiran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1262, 125, 'Cakupan Penerbitan Akte Kelahiran Anak usia 0 -18 Tahun', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1263, 125, 'Cakupan Penerbitan Akte Kematian', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1264, 126, 'Persentase database kependudukan yang valid dan update', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1265, 127, 'Persentase data kependudukan yg dimanfaatkan oleh lembaga pengguna', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1266, 128, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1267, 129, 'Persentase sarana dan prasarana kerja yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1268, 130, 'Persentase BUMDes yang tumbuh', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1269, 131, 'Persentase Lembaga Kemasyarakatan Desa yang aktif', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1270, 132, 'Persentase Desa yang memiliki kapasitas Pemerintah Desa yang baik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1271, 133, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1272, 134, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1273, 135, 'Persentase sarana dan prasarana kerja yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1274, 136, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1275, 137, 'Persentase kesadaran tertib lalu lintas', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1276, 138, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1277, 139, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1278, 140, 'Persentase penyelenggaraan PJU yang berkeselamatan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1279, 140, 'Persentase rambu Kondisi baik:', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1280, 140, 'Persentase warning light Kondisi baik:', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1281, 140, 'Persentase marka Kondisi baik:', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1282, 140, 'Persentase guard rail Kondisi baik:', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1283, 141, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1284, 142, 'Persentase sarana dan prasarana kerja yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1285, 143, 'Persentase aplikasi yang terintegrasi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1286, 144, 'Persentase Akses Masyarakat Terhadap Informasi (KIM)', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1287, 145, 'Persentase informasi OPD yang telah diklasifikasikan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1288, 146, 'Persentase Data Statistik Sektoral yang tersedia', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1289, 147, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1290, 148, 'Persentase sarana dan prasarana kerja yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1291, 149, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1292, 150, 'Persentase Peningkatan IKM', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1293, 151, 'Jumlah sarana perdagangan yang memadai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1294, 152, 'Jumlah PKL/Asongan yang dibina', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1295, 153, 'Jumlah IKM yang dibina', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1296, 154, 'Jumlah UM', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1297, 155, 'Jumlah Koperasi Aktif', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1298, 156, 'Jumlah pasar berkriteria SNI', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1299, 157, 'Persentase barang kena cukai ilegal', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1300, 158, 'Jumlah pedagang formal', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1301, 159, 'Persentase subsidi harga', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1302, 160, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1303, 161, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1304, 162, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1305, 163, 'Persentase kegiatan pelaporan capaian kinerja dan keuangan berjalan lancar dan tepat waktu', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1306, 164, 'Jumlah investor skala besar yang berinvestasi di Kab. Madiun', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1307, 165, 'Persentase masyarakat yang puas terhadap kualitas pelayanan perizinan dan non perizinan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1308, 166, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1309, 167, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1310, 168, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1311, 169, 'Persentase Laporan Kinerja SKPD yang tercapai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1312, 170, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1313, 171, 'Jumlah destinasi wisata yang dikembangkan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1314, 172, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1315, 173, 'Jumlah even pariwisata yang dilaksanakan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1316, 173, 'Prosentase kelembagaan pariwisata yang dikembangkan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1317, 174, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1318, 175, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1319, 176, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1320, 177, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1321, 178, 'Persentase SDM pariwisata dan ekonomi kreatif yang dibina dengan dana cukai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1322, 179, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1323, 180, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1324, 181, 'Produksi tembakau', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1325, 182, 'Populasi Sapi potong', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1326, 1, 'Persentase administrasi perkantoran yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1327, 182, 'Populasi Sapi Perah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1328, 182, 'Populasi Kambing', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1329, 182, 'Populasi Domba', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1330, 2, 'Persentase sarana prasarana perkantoran yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1331, 182, 'Populasi Ayam Buras', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1332, 182, 'Populasi Ayam Petelur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1333, 182, 'Populasi Ayam Pedaging', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1334, 182, 'Populasi Itik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1335, 323, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1336, 183, 'Prosentase bina kelompok tani', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1337, 183, 'Prosentase bina kelompok ternak', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1338, 183, 'Prosentase bina kelompok ikan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1339, 184, 'Produksi padi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1340, 184, 'Produksi jagung', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1341, 184, 'Produksi kedelai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1342, 184, 'Produktivitas Padi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1343, 184, 'Produktivitas Jagung', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1344, 184, 'Produktivitas Kedelai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1345, 3, 'APS PAUD', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1346, 185, 'Produksi daging', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1347, 185, 'Produksi telur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1348, 185, 'Produksi susu', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1349, 4, 'Angka kelulusan paket A/B/C', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1350, 186, 'Jumlah kelompok tani, Gapoktan, P3A, GP3A', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1351, 187, 'Produksi mangga', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1352, 187, 'Produksi durian', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1353, 187, 'Produksi jambu air', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1354, 187, 'Produksi cabe', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1355, 187, 'Produksi bawang merah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1356, 5, 'Persentase guru yang memenuhi kualifikasi S1/DIV', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1357, 188, 'Produksi tebu', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1358, 188, 'Produksi kakao (biji kering)', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1359, 188, 'Produksi cengkeh (bunga kering)', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1360, 189, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1361, 324, 'Persentase Operasional sekolah yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1362, 190, 'Persentase kelompok ternak yang dibina', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1363, 7, 'APS SD', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1364, 7, 'Angka Kelulusan SD/MI', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1365, 7, 'Angka Melanjutkan SD/MI ke SMP/MTs', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1366, 7, 'Persentase lembaga SD yang terakreditasi A (%)', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1367, 8, 'APS SMP', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1368, 8, 'Angka Kelulusan SMP', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1369, 8, 'Angka Melanjutkan SMP/MTs ke SMA/SMK/MA', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1370, 8, 'Persentase lembaga SMP yang terakreditasi minimal A', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1371, 191, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1372, 192, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1373, 193, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1374, 6, 'Nilai IKM Dinas Pendidikan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1375, 194, 'Persentase Perangkat Daerah yang difasilitasi dalam penerapan Inovasi Daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1376, 194, 'Persentase pemanfaatan hasil kelitbangan yang ditindaklanjuti / diterbitkan / dipublikasikan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1377, 195, 'Persentase kesesuaian Program dalam dokumen perencanaan pembangunan daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1378, 196, 'Persentase kesesuaian Program dalam dokumen perencanaan pembangunan bidang Infrastruktur dan pengembangan wilayah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1379, 197, 'Persentase kesesuaian program dalam dokumen perencanaan pembangunan bidang ekonomi dan SDA', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1380, 10, 'Persentase kebutuhan pelayanan administrasi perkantoran yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1381, 11, 'Persentase kebutuhan sarana dan prasarana aparatur yang tersedia dengan kondisi baik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1382, 12, 'Persentase peningkatan kompetensi sumber daya aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1383, 13, 'Persentase kegiatan pelaporan capaian kinerja dan keuangan berjalan lancar dan tepat waktu', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1384, 325, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1385, 14, 'Angka Kematian Ibu (AKI)', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1386, 14, 'Angka Kematian Bayi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1387, 14, 'Prevalensi Balita Stunting', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1388, 326, 'Prevalensi HIV', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1389, 326, 'Persentase ODGJ berat yang mendapatkanpelayanan kesehatan jiwa sesuai standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1390, 15, 'Persentase sarana dan prasarana pelayanan kesehatan dasar memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1391, 19, 'Persentase tenaga kesehatan yang memiliki ijin', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1392, 19, 'Persentase puskesmas dengan alat kesehatan memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1393, 19, 'Persentase ketersediaan obat', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1394, 18, 'Persentase kepesertaan JKN', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1395, 22, 'Persentase kegiatan bantuan operasional kesehatan pada puskesmas (DAK Non fisik) beerjalan lancar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1396, 327, 'Cakupan masyarakat yang mendapat pelayanan kesehatan di puskesmas BLUD', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1397, 23, 'Persentase puskesmas memberikan pelayanan JKN sesuai standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1398, 24, 'Persentase Capaian indikator SPM bidang tata usaha sesuai dengan standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1399, 25, 'Persentase Capaian indikator SPM bidang tata usaha sesuai dengan standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1400, 26, 'Persentase Capaian indikator SPM bidang tata usaha sesuai dengan standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1401, 27, 'Persentase Capaian indikator SPM bidang tata usaha sesuai dengan standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1402, 328, 'Persentase Capaian indikator SPM bidang tata usaha sesuai dengan standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1403, 329, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1404, 28, 'Persentase Capaian indikator SPM bidang keuangan sesuai dengan standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1405, 28, 'Persentase Capaian indikator SPM bidang tata usaha sesuai dengan standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1406, 28, 'Persentase indikator SPM bidang pelayanan yang memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1407, 28, 'Persentase indikator SPM bidang penunjang yang memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1408, 29, 'Persentase Capaian indikator SPM bidang keuangan sesuai dengan standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1409, 30, 'Persentase indikator SPM bidang pelayanan yang memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1410, 30, 'Persentase elemen penilaian akreditasi rumah sakit yang memenuhi standar akreditasi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1411, 31, 'Persentase indikator SPM bidang pelayanan yang memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1412, 31, 'Persentase elemen penilaian akreditasi rumah sakit yang memenuhi standar akreditasi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1413, 330, 'Persentase indikator SPM bidang penunjang yang memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1414, 330, 'Persentase indikator SPM bidang pelayanan yang memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1415, 331, 'Persentase indikator SPM bidang penunjang yang memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1416, 331, 'Persentase indikator SPM bidang pelayanan yang memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1417, 332, 'Prosentase SPM Bidang Tata Usaha yang dicapai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1418, 333, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1419, 334, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1420, 335, 'Prosentase indikator SPM bidang Keuangan sesuai standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1421, 336, 'Prosentase SPM Bidang Pelayanan yang dicapai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1422, 336, 'Prosentase elemen penilaian akreditasi rumah sakit yang memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1423, 337, 'Prosentase SPM Bidang Penunjang yang dicapai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1424, 337, 'Prosentase elemen penilaian akreditasi rumah sakit yang memenuhi standar', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1425, 338, 'Prosentase bangunan rumah sakit yang dibangun', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1426, 339, 'Prosentase bangunan rumah sakit yang dibangun', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1427, 340, 'Jumlah lahan yang dibebaskan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1428, 198, 'Persentase kesesuaian program dalam dokumen perencanaan pembangunan bidang sosial budaya dan pembangunan manusia', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1429, 199, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1430, 200, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1431, 201, 'Persentase Laporan Kinerja SKPD yang tercapai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1432, 202, 'Persentase penyusunan Raperda APBD dan Raperda P.APBD tepat waktu', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1433, 203, 'Persentase penyusunan Raperda APBD dan Raperda P.APBD tepat waktu', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1434, 204, 'Persentase pelayanan perbendaharaan dan kas daerah tepat waktu', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1435, 205, 'Persentase pelayanan perbendaharaan dan kas daerah tepat waktu', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1436, 206, 'Persentase realisasi pencairan belanja tidak langsung non gaji', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1437, 207, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1438, 208, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1439, 209, 'Peningkatan Target PAD (Milyar)', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1440, 210, 'Pencapaian Target PAD setiap Tahunnya', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1441, 211, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1442, 212, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1443, 213, 'Persentase Laporan Kinerja SKPD yang tercapai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1444, 214, 'Jumlah ASN yang lulus uji Kompetensi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1445, 214, 'Prosentase ASN yang lulus Pendidikan dan Pelatihan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1446, 215, 'Prosentase Mutasi Jabatan sesuai Kompetensi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1447, 216, 'Prosentase ASN yang tidak melanggar aturan disiplin', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1448, 216, 'Prosentase ASN yang mempunyai Nilai SKP â‰¥ 75', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1449, 217, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1450, 218, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1451, 219, 'Persentase peningkatan kompetensi sumber daya aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1452, 220, 'Persentase Laporan Kinerja SKPD yang tercapai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1453, 221, 'Persentase kasus pengaduan yang selesai ditindaklanjuti', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1454, 221, 'Persentase penyelesaian tindaklanjuti temuan hasil pemeriksaan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1455, 221, 'Level maturitas SPIP', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1456, 221, 'Prosentase nilai evaluasi SAKIP OPD minimal BB', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1457, 221, 'Opini BPK terhadap LKPD', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1458, 222, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1459, 223, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1460, 224, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1461, 225, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1462, 226, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1463, 227, 'Persentase kegiatan pelaporan capaian kinerja dan keuangan berjalan lancar dan tepat waktu', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1464, 228, 'Persentase kasus yang tertangani', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1465, 228, 'Persentase pelaksanaan penyuluhan hukum terpadu', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1466, 228, 'Persentase pembinaan desa sadar hukum', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1467, 228, 'Persentase pelaksanaan sosialisasi produk hukum yang berkaitan dengan HAM', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1468, 228, 'Jumlah produk hukum daerah (Perda dan Perbup) yang diterbitkan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1469, 228, 'Prosentase produk hukum daerah yang dipublikasikan melalui JDIH', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1470, 229, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1471, 230, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1472, 231, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1473, 232, 'Persentase evaluasi tusi perangkat daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1474, 232, 'Prosentase Terwujudnya penentuan nama jabatan dan persyaratan jabatan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1475, 232, 'Persentase terwujudnya insrumen persyaratan jabatan struktural', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1476, 232, 'Prosentase Terwujudnya tertib pemakaian atribut PNS', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1477, 232, 'Ditetapkan ISO pada perangkat daerah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1478, 232, 'Persentase SOP OPD yang dievaluasi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1479, 232, 'Nilai rata-rata survey kepuasan masyarakat OPD', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1480, 233, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1481, 43, 'Persentase administrasi perkantoran yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1482, 44, 'Persentase sarana prasarana perkantoran yang terpenuhi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1483, 341, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1484, 46, 'Persentase panjang saluran drainase kondisi baik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1485, 48, 'Presentase alat-alat penunjang infrastruktur yang tersedia kondisi baik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1486, 342, 'Persentase Panjang Jalan Kondisi Sedang', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1487, 49, 'Panjang jaringan irigasi kondisi baik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1488, 343, 'Presentases kapasitas daya tampung air embung yang terbangun', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1489, 51, 'Presentase sarana prasana gedung pemerintah kondisi baik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1490, 52, 'Jumlah SDM Jasa Konstruksi yang memenuhi kualifikasi', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1491, 50, 'Panjang saluran pembuangan kondisi baik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1492, 53, 'Panjang daerah irigasi kondisi baik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1493, 54, 'Persentase panjang jalan lingkungan dan jaringan air bersih kondisi baik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1494, 55, 'Persentase Panjang Jalan Kondisi Baik', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1495, 344, 'Jumlah sarana prasarana wisata yang terbangun', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1496, 345, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1497, 346, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1498, 347, 'Jumlah dokumen tata ruang dan rencana detail tata ruang', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1499, 234, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1500, 235, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1501, 236, 'Persentase Laporan Kinerja SKPD yang tercapai', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1502, 237, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1503, 238, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1504, 239, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1505, 240, 'x', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1506, 241, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1507, 241, 'Persentase peserta yang mengikuti kegiatan keagamaan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1508, 241, 'Persentase peserta yang mengikuti peringatan hari besar agama', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1509, 241, 'Persentase dukungan terhadap anggota / lembaga keagamaan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1510, 241, 'Persentase masyarakat yang melaksanakan ibadah haji (Reguler melalui Kemenag Kabupaten Madiun)', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1511, 241, 'Persentase bantuan peralatan dan perengkapan tempat ibadah', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1512, 241, 'Persentase koordinasi bidang transmigrasi, pengendalian penduduk dan KB, kesehatan, sosial tenaga kerja dan penanggulangan bencana', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1513, 241, 'Persentase koordinasi bidang pemberdayaan perempuan dan perlindungan anak, pemuda dan olahraga, pemberdayaan masyarakat desa, pendidikan dan kebudayaan', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1514, 242, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:10', '2022-11-13 02:22:10'),
(1515, 243, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1516, 244, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1517, 245, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1518, 246, 'Persentase kegiatan pelaporan capaian kinerja dan keuangan berjalan lancar dan tepat waktu', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1519, 247, 'Penegasan Batas Administrasi Wilayah (Kecamatan dan Desa)', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1520, 247, 'Meningkatnya Peringkat LPPD Nasional', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1521, 247, 'Meningkatnya Pelayanan Publik kepada masyarakat di kecamatan', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1522, 247, 'Terselenggaranya peringatan hari jadi Provinsi', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1523, 248, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1524, 249, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1525, 250, 'Persentase ASN yang berseragam sesuai standar', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1526, 251, 'Persentase rekomendasi DPRD yang ditindaklanjuti', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1527, 252, 'Presentase rancangan Peraturan Daerah yang disahkan', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1528, 253, 'Persentase jumlah aduan masyarakat yang ditindaklanjuti', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1529, 254, 'Persentase dokumen perencanaan dan pelaporan sekretariat DPRD yang tersusun', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1530, 255, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1532, 257, 'Persentase kegiatan pelaporan capaian kinerja dan keuangan berjalan lancar dan tepat waktu', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1533, 258, 'Presentase rekomendasi dan realisasi hasil pembinaan yang ditindaklanjuti dalam satu tahun bidang pemerintahan pemberdayaan dan pembangunan, ketentraman dan ketertiban umum serta kesejahteraan sosial', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1534, 259, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1535, 256, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1536, 260, 'Presentase rekomendasi dan realisasi hasil pembinaan yang ditindaklanjuti dalam satu tahun bidang pemerintahan pemberdayaan dan pembangunan, ketentraman dan ketertiban umum serta kesejahteraan sosial', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1537, 261, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1538, 262, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1539, 263, 'Presentase rekomendasi dan realisasi hasil pembinaan yang ditindaklanjuti dalam satu tahun bidang pemerintahan pemberdayaan dan pembangunan, ketentraman dan ketertiban umum serta kesejahteraan sosial', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1540, 264, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1541, 265, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1542, 266, 'Presentase rekomendasi dan realisasi hasil pembinaan yang ditindaklanjuti dalam satu tahun bidang pemerintahan pemberdayaan dan pembangunan, ketentraman dan ketertiban umum serta kesejahteraan sosial', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1543, 267, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1544, 268, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1545, 269, 'Presentase rekomendasi dan realisasi hasil pembinaan yang ditindaklanjuti dalam satu tahun bidang pemerintahan pemberdayaan dan pembangunan, ketentraman dan ketertiban umum serta kesejahteraan sosial', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1546, 270, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1547, 271, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1548, 272, 'Presentase rekomendasi dan realisasi hasil pembinaan yang ditindaklanjuti dalam satu tahun bidang pemerintahan pemberdayaan dan pembangunan, ketentraman dan ketertiban umum serta kesejahteraan sosial', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1549, 273, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1550, 274, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1551, 275, 'Presentase rekomendasi dan realisasi hasil pembinaan yang ditindaklanjuti dalam satu tahun bidang pemerintahan pemberdayaan dan pembangunan, ketentraman dan ketertiban umum serta kesejahteraan sosial', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1552, 276, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1553, 277, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1554, 278, 'Presentase rekomendasi dan realisasi hasil pembinaan yang ditindaklanjuti dalam satu tahun bidang pemerintahan pemberdayaan dan pembangunan, ketentraman dan ketertiban umum serta kesejahteraan sosial', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1555, 279, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1556, 280, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1557, 281, 'Presentase rekomendasi dan realisasi hasil pembinaan yang ditindaklanjuti dalam satu tahun bidang pemerintahan pemberdayaan dan pembangunan, ketentraman dan ketertiban umum serta kesejahteraan sosial', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1558, 282, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1559, 283, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1560, 284, 'Presentase rekomendasi dan realisasi hasil pembinaan yang ditindaklanjuti dalam satu tahun bidang pemerintahan pemberdayaan dan pembangunan, ketentraman dan ketertiban umum serta kesejahteraan sosial', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1561, 285, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1562, 286, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1563, 287, 'Presentase rekomendasi dan realisasi hasil pembinaan yang ditindaklanjuti dalam satu tahun bidang pemerintahan pemberdayaan dan pembangunan, ketentraman dan ketertiban umum serta kesejahteraan sosial', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1564, 288, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1565, 289, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1566, 290, 'Presentase rekomendasi dan realisasi hasil pembinaan yang ditindaklanjuti dalam satu tahun bidang pemerintahan pemberdayaan dan pembangunan, ketentraman dan ketertiban umum serta kesejahteraan sosial', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1567, 291, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1568, 292, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1569, 293, 'Presentase rekomendasi dan realisasi hasil pembinaan yang ditindaklanjuti dalam satu tahun bidang pemerintahan pemberdayaan dan pembangunan, ketentraman dan ketertiban umum serta kesejahteraan sosial', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1570, 294, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1571, 295, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1572, 296, 'Nilai Survey Kepuasan Masyarakat', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1573, 297, 'Presentase rekomendasi dan realisasi hasil pembinaan yang ditindaklanjuti dalam satu tahun bidang pemerintahan pemberdayaan dan pembangunan, ketentraman dan ketertiban umum serta kesejahteraan sosial', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1574, 298, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1575, 299, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1576, 300, 'Persentase pelayanan masyarakat yang terpenuhi', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1577, 301, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1578, 302, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1579, 303, 'Persentase pelayanan masyarakat yang terpenuhi', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1580, 304, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1581, 305, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1582, 306, 'Persentase pelayanan masyarakat yang terpenuhi', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1583, 307, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1584, 308, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1585, 309, 'Persentase pelayanan masyarakat yang terpenuhi', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1586, 310, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1587, 311, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1588, 312, 'Persentase pelayanan masyarakat yang terpenuhi', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1589, 313, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1590, 314, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1591, 315, 'Persentase pelayanan masyarakat yang terpenuhi', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1592, 316, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1593, 317, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1594, 318, 'x', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1595, 319, 'Persentase pelayanan masayarakat yang terpenuhi', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1596, 320, 'Persentase Terpenuhinya kebutuhan administrasi dan pendukung operasi perkantoran', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1597, 321, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1598, 322, 'Prosentase Pemenuhan Kebutuhan Sarana dan Prasarana Aparatur', '2022-11-13 02:22:11', '2022-11-13 02:22:11'),
(1599, 58, 'Persentase administrasi perkantoran yang terpenuhi', '2022-11-13 02:22:11', '2022-11-13 02:22:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `program_rpjmds`
--

CREATE TABLE `program_rpjmds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status_program` enum('Prioritas','Pendukung') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `program_rpjmds`
--

INSERT INTO `program_rpjmds` (`id`, `program_id`, `status_program`, `created_at`, `updated_at`) VALUES
(4, 65, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(5, 192, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(6, 200, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(7, 451, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(8, 212, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(9, 400, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(10, 407, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(11, 428, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(12, 436, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(13, 435, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(14, 404, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(15, 422, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(16, 394, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(17, 395, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(18, 385, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(19, 368, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(20, 372, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(21, 377, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(22, 432, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(23, 370, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(24, 369, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(25, 11, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(26, 26, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(27, 12, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(28, 2, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(29, 349, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(30, 98, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(31, 399, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(32, 412, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(33, 461, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(34, 457, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(35, 455, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(36, 44, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(37, 356, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(38, 360, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(39, 357, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(40, 365, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(41, 59, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(42, 363, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(43, 135, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(44, 391, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(45, 373, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(46, 374, 'Prioritas', '2022-11-15 09:41:46', '2022-11-15 09:41:46'),
(47, 3, 'Pendukung', '2022-11-16 02:42:22', '2022-11-16 02:42:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `program_target_satuan_rp_realisasis`
--

CREATE TABLE `program_target_satuan_rp_realisasis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `opd_program_indikator_kinerja_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_rp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `realisasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `realisasi_rp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `program_target_satuan_rp_realisasis`
--

INSERT INTO `program_target_satuan_rp_realisasis` (`id`, `opd_program_indikator_kinerja_id`, `target`, `satuan`, `target_rp`, `realisasi`, `realisasi_rp`, `tahun`, `created_at`, `updated_at`) VALUES
(1, 1, '100', 'buku', '1000000', '12', '50000', '2018', '2022-11-15 09:53:12', '2022-11-16 11:19:04'),
(2, 1, '20', 'buku', '50000', '20', '500000', '2019', '2022-11-16 05:57:05', '2022-11-16 11:19:16'),
(3, 1, '50', 'buku', '100000', '50', '100000', '2020', '2022-11-16 22:53:15', '2022-11-16 23:14:25'),
(4, 1, '100', 'buku', '100000', NULL, NULL, '2021', '2022-11-16 23:12:59', '2022-11-16 23:12:59'),
(5, 1, '90', 'buku', '100000', NULL, NULL, '2022', '2022-11-16 23:13:25', '2022-11-16 23:13:25'),
(6, 1, '80', 'buku', '100000', NULL, NULL, '2023', '2022-11-16 23:13:53', '2022-11-16 23:13:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `provinsis`
--

CREATE TABLE `provinsis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `negara_id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `provinsis`
--

INSERT INTO `provinsis` (`id`, `negara_id`, `nama`, `created_at`, `updated_at`) VALUES
(5, 62, 'Jawa Timur', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `renstra_kegiatans`
--

CREATE TABLE `renstra_kegiatans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_rpjmd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kegiatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sasarans`
--

CREATE TABLE `sasarans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tujuan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `kabupaten_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sasarans`
--

INSERT INTO `sasarans` (`id`, `tujuan_id`, `kode`, `deskripsi`, `kabupaten_id`, `tahun_perubahan`, `created_at`, `updated_at`) VALUES
(1, 1, '1', 'Menciptakan ketentraman dan ketertiban masyarakat', 62, '2020', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(2, 2, '1', 'Meningkatnya Akuntabilitas Kinerja Pemerintah', 62, '2020', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(3, 2, '2', 'Meningkatnya Kualitas dan Kapasitas Aparatur Sipil Negara (ASN) Pemerintah Daerah', 62, '2020', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(4, 2, '3', 'Meningkatnya Kinerja Pelayanan Publik', 62, '2020', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(5, 3, '1', 'Meningkatnya Perekonomian Masyarakat', 62, '2020', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(6, 3, '2', 'Meningkatnya Sarana dan Prasarana Infrastruktur Perekonomian', 62, '2020', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(7, 3, '3', 'Terkendalinya Inflasi Daerah', 62, '2020', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(8, 4, '1', 'Meningkatnya Kualitas Ligkungan Hidup', 62, '2020', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(9, 5, '1', 'Meningkatnya Kualitas Pendidikan', 62, '2020', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(10, 5, '2', 'Meningkatnya Derajad Kesehatan Masyarakat', 62, '2020', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(11, 5, '3', 'Meningkatnya Kesejahteraan Sosial Bagi Masyarakat', 62, '2020', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(12, 3, '4', 'Meningkatnya Ketahanan Bencana Daerah', 62, '2021', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(13, 3, '5', 'Meningkatnya Penyerapan Tenaga kerja Lokal', 62, '2021', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(14, 6, '1', 'Menguatkan karakteristik kebudayaan', 62, '2021', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(15, 6, '2', 'Terwujudnya nilai â€“ nilai keagamaan dan gotong royong dalam kehidupan masyarakat', 62, '2021', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(16, 7, '1', 'Mewujudkan Pemerintahan yang Akuntable', 62, '2022', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(17, 7, '2', 'Pengembangan Kapasitas Aparatur Sipil Negara (ASN) Pemerintah Daerah', 62, '2022', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(18, 7, '3', 'Meningkatnya Inovasi Layanan Publik berbasis Transformasi Digital', 62, '2022', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(19, 8, '1', 'Meningkatnya Pertumbuhan Ekonomi yang Inklusif dan mandiri', 62, '2022', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(20, 8, '2', 'Meningkatnya sarana dan prasarana infrastruktur perekonomian', 62, '2022', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(21, 8, '3', 'Terjaganya Keseimbangan Kualitas Lingkungan Hidup', 62, '2022', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(22, 8, '4', 'Meningkatnya Ketahanan Bencana Daerah', 62, '2022', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(23, 8, '5', 'Meningkatnya Penyerapan Tenaga kerja Lokal', 62, '2022', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(24, 9, '1', 'Terciptanya pemerataan distribusi pendapatan masyarakat', 62, '2022', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(25, 9, '2', 'Meningkatnya Kualitas dan Aksesibilitas pelayanan pendidikan dan kesehatan', 62, '2022', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(26, 10, '1', 'Menguatkan karakteristik kebudayaan', 62, '2022', '2022-11-14 23:26:26', '2022-11-14 23:26:26'),
(27, 10, '2', 'Terwujudnya nilai â€“ nilai keagamaan dan gotong royong dalam kehidupan masyarakat', 62, '2022', '2022-11-14 23:26:26', '2022-11-14 23:26:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sasaran_indikator_kinerjas`
--

CREATE TABLE `sasaran_indikator_kinerjas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sasaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sasaran_indikator_kinerjas`
--

INSERT INTO `sasaran_indikator_kinerjas` (`id`, `sasaran_id`, `deskripsi`, `created_at`, `updated_at`) VALUES
(59, 16, 'Nilai SAKIP', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(60, 16, 'Opini atas Audit BPK', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(61, 16, 'Tingkat Maturitas SPIP', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(62, 17, 'Indeks Profesionalitas Aparatur', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(63, 18, 'Indeks SPBE', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(64, 18, 'Nilai Indeks Kepuasan Masyarakat (IKM)', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(65, 19, 'Pertumbuhan PDRB Unggulan (Pertanian, Industri, Perdagangan)', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(66, 19, 'Pengeluaran Wisatawan', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(67, 19, 'Persentase Desa Mandiri', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(68, 20, 'Indeks Kepuasan Layanan Infrastruktur (IKLI)', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(69, 21, 'Indeks Kualitas Lingkungan Hidup (IKLH)', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(70, 22, 'Indeks Risiko Bencana', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(71, 23, 'Tingkat Pengangguran Terbuka (TPT)', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(72, 24, 'Pengeluaran Perkapita makanan', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(73, 1, 'Indeks Keamanan Manusia', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(74, 1, 'Indeks Ketertiban Umum', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(75, 1, 'Tingkat Maturitas Sistem Pengendalian Intern Pemerintahan (SPIP)', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(76, 2, 'Nilai Sistem Akuntabilitas Kinerja Pemerintahan Daerah', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(78, 3, 'Indeks Profesionali ASN', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(79, 4, 'Kategori Indeks Kepuasan Masyarakat', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(80, 5, 'Nilai PDRB', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(81, 5, 'Jumlah nilai investasi (PMA/PMDN)', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(82, 6, 'Persentase Jalan dan Jembatan Kondisi Mantap', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(83, 6, 'Persentase Jaringan Irigasi Kondisi Baik', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(84, 6, 'Persentase Jalan yang Berkeselamatan', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(85, 7, 'Nilai Inflasi', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(86, 8, 'Indeks Kualitas Air', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(87, 8, 'Indeks Kualitas Udara', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(88, 8, 'Indeks Tutupan Lahan', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(89, 9, 'Indeks Pendidikan', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(91, 11, 'Persentase Penduduk Miskin', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(92, 11, 'Persentase Desa/Kelurahan Cepat Berkembang', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(93, 11, 'Tingkat Pengangguran Terbuka (TPT)', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(94, 11, 'Laju Pertumbuhan Penduduk (LPP)', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(95, 11, 'Indeks Pembangunan Gender (IPG)', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(96, 25, 'Indeks Kesehatan', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(97, 25, 'Indeks Pendidikan', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(98, 25, 'Indeks Pembangunan Gender', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(99, 26, 'Persentase budaya daerah yang dilestarikan', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(100, 27, 'Indeks Toleransi', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(101, 27, 'Indeks Solidaritas', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(102, 1, 'Indeks Stabilitas', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(103, 2, 'Nilai SAKIP', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(104, 2, 'Opini atas Audit BPK', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(105, 2, 'Tingkat Maturitas SPIP', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(106, 3, 'Indeks Profesionalitas Aparatur', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(107, 4, 'Indeks SPBE', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(108, 4, 'Nilai Indeks Kepuasan Masyarakat (IKM)', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(109, 5, 'Pertumbuhan PDRB Unggulan (Pertanian, Industri, Perdagangan)', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(110, 5, 'Pengeluaran Wisatawan', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(111, 5, 'Persentase Desa Mandiri', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(112, 6, 'Indeks Kepuasan Layanan Infrastruktur', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(113, 7, 'Indeks Kualitas Lingkungan Hidup (IKLH)', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(114, 12, 'Indeks Risiko Bencana', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(115, 13, 'Tingkat Pengangguran Terbuka (TPT)', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(116, 9, 'Pengeluaran Perkapita makanan', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(117, 10, 'Indeks Kesehatan', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(118, 10, 'Indeks Pendidikan', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(119, 10, 'Indeks Pembangunan Gender', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(120, 14, 'Persentase budaya daerah yang dilestarikan', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(121, 15, 'Indeks Toleransi', '2022-11-14 23:48:49', '2022-11-14 23:48:49'),
(122, 15, 'Indeks Solidaritas', '2022-11-14 23:48:49', '2022-11-14 23:48:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sasaran_pds`
--

CREATE TABLE `sasaran_pds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sasaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sasaran_pds`
--

INSERT INTO `sasaran_pds` (`id`, `sasaran_id`, `kode`, `deskripsi`, `opd_id`, `tahun_perubahan`, `created_at`, `updated_at`) VALUES
(2, 1, '1', 'Sasaran test 1', 16, '2020', '2022-11-16 02:00:16', '2022-11-16 02:00:16'),
(3, 2, '1', 'test', 16, '2021', '2022-11-16 08:44:14', '2022-11-16 08:44:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sasaran_pd_indikator_kinerjas`
--

CREATE TABLE `sasaran_pd_indikator_kinerjas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sasaran_pd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sasaran_pd_indikator_kinerjas`
--

INSERT INTO `sasaran_pd_indikator_kinerjas` (`id`, `sasaran_pd_id`, `deskripsi`, `created_at`, `updated_at`) VALUES
(2, 2, 'Kerja Kerja Kerja', '2022-11-16 02:05:23', '2022-11-16 02:05:23'),
(3, 3, 'kerja kerja kerja', '2022-11-16 08:44:25', '2022-11-16 08:44:25');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sasaran_pd_target_satuan_rp_realisasis`
--

CREATE TABLE `sasaran_pd_target_satuan_rp_realisasis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sasaran_pd_indikator_kinerja_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `realisasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sasaran_pd_target_satuan_rp_realisasis`
--

INSERT INTO `sasaran_pd_target_satuan_rp_realisasis` (`id`, `sasaran_pd_indikator_kinerja_id`, `target`, `satuan`, `realisasi`, `tahun`, `created_at`, `updated_at`) VALUES
(1, 2, '1', 'buku', '1', '2018', '2022-11-16 02:11:24', '2022-11-16 02:11:51'),
(2, 3, '1', 'buku', '1', '2018', '2022-11-16 14:33:27', '2022-11-16 14:33:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sasaran_target_satuan_rp_realisasis`
--

CREATE TABLE `sasaran_target_satuan_rp_realisasis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sasaran_indikator_kinerja_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `realisasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sasaran_target_satuan_rp_realisasis`
--

INSERT INTO `sasaran_target_satuan_rp_realisasis` (`id`, `sasaran_indikator_kinerja_id`, `target`, `satuan`, `realisasi`, `tahun`, `created_at`, `updated_at`) VALUES
(1, 73, '100', 'buku', '100', '2018', '2022-11-16 23:07:45', '2022-11-16 23:07:45'),
(2, 73, '100', 'buku', '100', '2019', '2022-11-16 23:07:57', '2022-11-16 23:07:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sub_kegiatans`
--

CREATE TABLE `sub_kegiatans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kegiatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `status_aturan` enum('Sebelum Perubahan','Sesudah Perubahan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sub_kegiatans`
--

INSERT INTO `sub_kegiatans` (`id`, `kegiatan_id`, `kode`, `deskripsi`, `tahun_perubahan`, `kabupaten_id`, `status_aturan`, `created_at`, `updated_at`) VALUES
(1, 1, '1', 'Penyusunan Dokumen Perencanaan Perangkat Daerah', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(2, 1, '2', 'Koordinasi dan Penyusunan Dokumen RKA-SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(3, 1, '3', 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(4, 1, '4', 'Koordinasi dan Penyusunan DPA-SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(5, 1, '5', 'Koordinasi dan Penyusunan Perubahan DPASKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(6, 1, '6', 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(7, 1, '7', 'Evaluasi Kinerja Perangkat Daerah', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(8, 2, '1', 'Penyediaan Gaji dan Tunjangan ASN', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(9, 2, '2', 'Penyediaan Administrasi Pelaksanaan Tugas ASN', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(10, 2, '3', 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(11, 2, '4', 'Koordinasi dan Pelaksanaan Akuntansi SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(12, 2, '5', 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(13, 2, '6', 'Pengelolaan dan Penyiapan Bahan Tanggapan Pemeriksaan', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(14, 2, '7', 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/Triwulanan/Semesteran SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(15, 2, '8', 'Penyusunan Pelaporan dan Analisis Prognosis Realisasi Anggaran', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(16, 3, '1', 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(17, 3, '2', 'Pengamanan Barang Milik Daerah SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(18, 3, '3', 'Koordinasi dan Penilaian Barang Milik Daerah SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(19, 3, '4', 'Pembinaan, Pengawasan, dan Pengendalian Barang Milik Daerah pada SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(20, 3, '5', 'Rekonsiliasi dan Penyusunan Laporan Barang Milik Daerah pada SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(21, 3, '6', 'Penatausahaan Barang Milik Daerah pada SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(22, 3, '7', 'Pemanfaatan Barang Milik Daerah SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(23, 4, '1', 'Perencanaan Pengelolaan Retribusi Daerah', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(24, 4, '2', 'Analisa dan Pengembangan Retribusi Daerah, serta Penyusunan Kebijakan Retribusi Daerah', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(25, 4, '3', 'Penyuluhan dan Penyebarluasan Kebijakan Retribusi Daerah', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(26, 4, '4', 'Pendataan dan Pendaftaran Objek Retribusi Daerah', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(27, 4, '5', 'Pengolahan Data Retribusi Daerah', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(28, 4, '6', 'Penetapan Wajib Retribusi Daerah', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(29, 4, '7', 'Pelaporan Pengelolaan Retribusi Daerah', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(30, 5, '1', 'Peningkatan Sarana dan Prasarana Disiplin Pegawai', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(31, 5, '2', 'Pengadaan Pakaian Dinas Beserta Atribut Kelengkapannya', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(32, 5, '3', 'Pendataan dan Pengolahan Administrasi Kepegawaian', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(33, 5, '4', 'Koordinasi dan Pelaksanaan Sistem Informasi Kepegawaian', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(34, 5, '5', 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(35, 5, '9', 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(36, 5, '10', 'Sosialisasi Peraturan Perundang-Undangan', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(37, 6, '1', 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(38, 6, '2', 'Penyediaan Peralatan dan Perlengkapan Kantor', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(39, 6, '3', 'Penyediaan Peralatan Rumah Tangga', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(40, 6, '4', 'Penyediaan Bahan Logistik Kantor', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(41, 6, '5', 'Penyediaan Barang Cetakan dan Penggandaan', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(42, 6, '6', 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(43, 6, '7', 'Penyediaan Bahan/Material', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(44, 6, '8', 'Fasilitasi Kunjungan Tamu', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(45, 6, '9', 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(46, 7, '1', 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(47, 7, '2', 'Pengadaan Kendaraan Dinas Operasional atau Lapangan', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(48, 7, '5', 'Pengadaan Mebel', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(49, 7, '6', 'Pengadaan Peralatan dan Mesin Lainnya', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(50, 7, '7', 'Pengadaan Aset Tetap Lainnya', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(51, 7, '8', 'Pengadaan Aset Tak Berwujud', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(52, 8, '1', 'Penyediaan Jasa Surat Menyurat', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(53, 8, '2', 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(54, 8, '3', 'Penyediaan Jasa Peralatan dan Perlengkapan Kantor', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(55, 8, '4', 'Penyediaan Jasa Pelayanan Umum Kantor', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(56, 9, '1', 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(57, 9, '2', 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(58, 9, '5', 'Pemeliharaan Mebel', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(59, 9, '7', 'Pemeliharaan Aset Tetap Lainnya', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(60, 9, '9', 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(61, 9, '10', 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(62, 9, '11', 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02'),
(63, 172, '1', 'Test Buat Renja', '2021', 62, 'Sesudah Perubahan', '2022-11-12 16:37:02', '2022-11-12 16:37:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sub_kegiatan_indikator_kinerjas`
--

CREATE TABLE `sub_kegiatan_indikator_kinerjas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sub_kegiatan_id` bigint(20) UNSIGNED NOT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tahun_periodes`
--

CREATE TABLE `tahun_periodes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tahun_awal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_akhir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Aktif','Tidak Aktif') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tahun_periodes`
--

INSERT INTO `tahun_periodes` (`id`, `tahun_awal`, `tahun_akhir`, `status`, `created_at`, `updated_at`) VALUES
(1, '2019', '2023', 'Aktif', '2022-10-10 06:29:14', '2022-10-10 06:29:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `target_rp_pertahun_programs`
--

CREATE TABLE `target_rp_pertahun_programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_rpjmd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `target_rp_pertahun_renstra_kegiatans`
--

CREATE TABLE `target_rp_pertahun_renstra_kegiatans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `renstra_kegiatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tujuans`
--

CREATE TABLE `tujuans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `misi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `kabupaten_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tujuans`
--

INSERT INTO `tujuans` (`id`, `misi_id`, `kode`, `deskripsi`, `kabupaten_id`, `tahun_perubahan`, `created_at`, `updated_at`) VALUES
(1, 1, '1', 'Menciptakan rasa aman bagi masyarakat dan ASN', 62, '2020', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(2, 2, '1', 'Meningkatkan tata kelola pemerintah yang baik (good goverment) untuk meningkatkan pelayanan publik', 62, '2020', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(3, 3, '1', 'Mewujudkan pembangunan ekonomi berkelanjutan', 62, '2020', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(4, 3, '2', 'Meningkatkan Pengelolaan Lingkungan Hidup', 62, '2020', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(5, 4, '1', 'Meningkatkan Kualitas Pembangunan Masyarakat', 62, '2020', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(6, 5, '1', 'Mewujudkan Kehidupan Masyarakat yang Berakhlak Mulia dan Berbudaya', 62, '2020', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(7, 2, '2', 'Meningkatkan Tata kelola Pemerintah yang Baik (Good Governance) untuk Pelayanan Publik', 62, '2022', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(8, 3, '3', 'Meningkatnya Daya Saing Ekonomi Inklusif, Mandiri dan Berkelanjutan', 62, '2022', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(9, 4, '4', 'Meningkatkan kualitas dan aksesibilitas pelayanan pendidikan dan kesehatan', 62, '2022', '2022-11-14 06:39:39', '2022-11-14 06:39:39'),
(10, 5, '5', 'Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal', 62, '2022', '2022-11-14 06:39:39', '2022-11-14 06:39:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tujuan_indikator_kinerjas`
--

CREATE TABLE `tujuan_indikator_kinerjas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tujuan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tujuan_indikator_kinerjas`
--

INSERT INTO `tujuan_indikator_kinerjas` (`id`, `tujuan_id`, `deskripsi`, `created_at`, `updated_at`) VALUES
(2, 2, 'Indeks Reformasi Birokras', '2022-11-15 02:27:38', '2022-11-15 02:27:38'),
(8, 7, 'Indeks Reformasi Birokrasi', '2022-11-15 02:27:38', '2022-11-15 02:27:38'),
(9, 8, 'Angka Pertumbuhan Ekonomi', '2022-11-15 02:27:38', '2022-11-15 02:27:38'),
(10, 8, 'Angka Kemiskinan', '2022-11-15 02:27:38', '2022-11-15 02:27:38'),
(11, 9, 'Indeks Pembangunan Manusia (IPM)', '2022-11-15 02:27:38', '2022-11-15 02:27:38'),
(12, 10, 'Indeks Kesalehan Sosial', '2022-11-15 02:27:38', '2022-11-15 02:27:38'),
(13, 1, 'Indeks rasa aman', '2022-11-15 02:27:38', '2022-11-15 02:27:38'),
(16, 4, 'Indeks Kualitas Lingkungan Hidup', '2022-11-15 02:27:38', '2022-11-15 02:27:38'),
(17, 5, 'Indeks Pembangunan Manusia', '2022-11-15 02:27:38', '2022-11-15 02:27:38'),
(19, 1, 'Indeks Kesalehan Sosial', '2022-11-15 02:27:38', '2022-11-15 02:27:38'),
(20, 2, 'Indeks Reformasi Birokrasi', '2022-11-15 02:27:38', '2022-11-15 02:27:38'),
(21, 3, 'Angka Pertumbuhan Ekonomi', '2022-11-15 02:27:38', '2022-11-15 02:27:38'),
(22, 3, 'Angka Kemiskinan', '2022-11-15 02:27:38', '2022-11-15 02:27:38'),
(23, 5, 'Indeks Pembangunan Manusia (IPM)', '2022-11-15 02:27:38', '2022-11-15 02:27:38'),
(24, 6, 'Indeks Kesalehan Sosial', '2022-11-15 02:27:38', '2022-11-15 02:27:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tujuan_pds`
--

CREATE TABLE `tujuan_pds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tujuan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tujuan_pds`
--

INSERT INTO `tujuan_pds` (`id`, `tujuan_id`, `kode`, `deskripsi`, `opd_id`, `tahun_perubahan`, `created_at`, `updated_at`) VALUES
(2, 1, '1', 'Test 1', 16, '2019', '2022-11-15 23:18:04', '2022-11-15 23:18:04'),
(3, 2, '1', 'tes', 16, '2021', '2022-11-16 08:43:43', '2022-11-16 08:43:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tujuan_pd_indikator_kinerjas`
--

CREATE TABLE `tujuan_pd_indikator_kinerjas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tujuan_pd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tujuan_pd_indikator_kinerjas`
--

INSERT INTO `tujuan_pd_indikator_kinerjas` (`id`, `tujuan_pd_id`, `deskripsi`, `created_at`, `updated_at`) VALUES
(2, 2, 'kerja kerja kerja', '2022-11-15 23:31:28', '2022-11-15 23:31:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tujuan_pd_target_satuan_rp_realisasis`
--

CREATE TABLE `tujuan_pd_target_satuan_rp_realisasis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tujuan_pd_indikator_kinerja_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `realisasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tujuan_pd_target_satuan_rp_realisasis`
--

INSERT INTO `tujuan_pd_target_satuan_rp_realisasis` (`id`, `tujuan_pd_indikator_kinerja_id`, `target`, `satuan`, `realisasi`, `tahun`, `created_at`, `updated_at`) VALUES
(1, 2, '11', 'buku', '11', '2018', '2022-11-15 23:46:36', '2022-11-15 23:50:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tujuan_target_satuan_rp_realisasis`
--

CREATE TABLE `tujuan_target_satuan_rp_realisasis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tujuan_indikator_kinerja_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `realisasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tujuan_target_satuan_rp_realisasis`
--

INSERT INTO `tujuan_target_satuan_rp_realisasis` (`id`, `tujuan_indikator_kinerja_id`, `target`, `satuan`, `realisasi`, `tahun`, `created_at`, `updated_at`) VALUES
(1, 13, '100', 'buku', '100', '2018', '2022-11-16 17:55:49', '2022-11-16 17:55:49'),
(2, 13, '50', 'buku', '50', '2019', '2022-11-16 17:56:04', '2022-11-16 17:56:04'),
(4, 19, '100', 'buku', '100', '2020', '2022-11-16 17:56:54', '2022-11-16 17:56:54'),
(5, 2, '100', 'buku', '100', '2018', '2022-11-16 17:58:26', '2022-11-16 17:58:26'),
(6, 2, '100', 'buku', '100', '2019', '2022-11-16 17:58:41', '2022-11-16 17:58:41'),
(7, 20, '100', 'buku', '99', '2021', '2022-11-16 23:01:33', '2022-11-16 23:01:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `urusans`
--

CREATE TABLE `urusans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_aturan` enum('Sebelum Perubahan','Sesudah Perubahan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `urusans`
--

INSERT INTO `urusans` (`id`, `kode`, `deskripsi`, `tahun_perubahan`, `status_aturan`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, '1.03', 'urusan pemerintahan bidang pekerjaan umum dan penataan ruang', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(2, '1.04', 'urusan pemerintahan bidang perumahan rakyat dan kawasan permukiman', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(3, '1.05', 'urusan pemerintahan bidang ketentraman dan ketertiban umum serta perlindungan masyarakat', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(4, '1.06', 'urusan pemerintahan bidang sosial', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(5, '2.07', 'urusan pemerintahan bidang tenaga kerja', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(6, '2.08', 'urusan pemerintahan bidang pemberdayaan perempuan dan perlindungan anak', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(7, '2.09', 'urusan pemerintahan bidang pangan', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(8, '2.10', 'urusan pemerintahan bidang pertanahan', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(9, '2.11', 'urusan pemerintahan bidang lingkungan hidup', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(10, '2.12', 'urusan pemerintahan bidang administrasi kependudukan dan pencatatan sipil', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(11, '2.13', 'urusan pemerintahan bidang pemberdayaan masyarakat dan desa', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(12, '2.14', 'urusan pemerintahan bidang pengendalian penduduk dan keluarga berencana', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(13, '2.15', 'urusan pemerintahan bidang perhubungan', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(14, '2.16', 'urusan pemerintahan bidang komunikasi dan informatika', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(15, '2.17', 'urusan pemerintahan bidang koperasi, usaha kecil dan menengah', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(16, '2.18', 'urusan pemerintahan bidang penanaman modal', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(17, '2.19', 'urusan pemerintahan bidang kepemudaan dan olahraga', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(18, '2.20', 'urusan pemerintahan bidang statistik', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(19, '2.21', 'urusan pemerintahan bidang persandian', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(20, '2.22', 'urusan pemerintahan bidang kebudayaan', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(21, '2.23', 'urusan pemerintahan bidang perpustakaan', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(22, '2.24', 'urusan pemerintahan bidang kearsipan', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(23, '3.25', 'urusan pemerintahan bidang kelautan dan perikanan', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(24, '3.26', 'urusan pemerintahan bidang pariwisata', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(25, '3.27', 'urusan pemerintahan bidang pertanian', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(26, '3.29', 'urusan pemerintahan bidang energi dan sumber daya mineral', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(27, '3.30', 'urusan pemerintahan bidang perdagangan', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(28, '3.31', 'urusan pemerintahan bidang perindustrian', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(29, '3.32', 'urusan pemerintahan bidang transmigrasi', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(30, '4.01', 'unsur Sekretariat daerah', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(31, '4.02', 'unsur Sekretariat DPRD', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(32, '5.01', 'unsur perencanaan', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(33, '5.02', 'unsur keuangan', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(34, '5.03', 'unsur kepegawaian', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(35, '5.05', 'unsur penelitian dan pengembangan', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(36, '6.01', 'unsur pengawasan urusan pemerintahan', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(37, '7.01', 'unsur kewilayahan', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(38, '8.01', 'urusan pemerintahan umum', '2019', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(39, '1.01', 'urusan pemerintahan bidang pendidikan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(40, '1.02', 'urusan pemerintahan bidang kesehatan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(41, '3.28', 'urusan pemerintahan bidang kehutanan', '2020', 'Sebelum Perubahan', 62, '2022-11-11 22:52:56', '2022-11-11 22:52:56'),
(42, '5.04', 'PENDIDIKAN DAN PELATIHAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35'),
(43, '5.06', 'PENGELOLAAN PERBATASAN', '2022', 'Sesudah Perubahan', 62, '2022-11-11 22:54:35', '2022-11-11 22:54:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `color_layout` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nav_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `placement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `behaviour` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `layout` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `radius` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `color_layout`, `nav_color`, `placement`, `behaviour`, `layout`, `radius`, `kabupaten_id`) VALUES
(1, 'Bappeda', 'bappeda@emonev.madiun', NULL, '$2y$10$i1XQWqM7gdTg8clhJmnRj.qdWm2nFcG514aRQVCCrW/7H/FfvndMa', NULL, NULL, '2022-11-14 08:12:24', 'light-blue', 'default', 'horizontal', 'pinned', 'fluid', 'standard', 62);

-- --------------------------------------------------------

--
-- Struktur dari tabel `visis`
--

CREATE TABLE `visis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `kabupaten_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tahun_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `visis`
--

INSERT INTO `visis` (`id`, `deskripsi`, `kabupaten_id`, `tahun_perubahan`, `created_at`, `updated_at`) VALUES
(1, 'TERWUJUDNYA KABUPATEN MADIUN AMAN, MANDIRI, SEJAHTERA DAN BERAKHLAK', 62, '2019', '2022-10-13 12:09:47', '2022-10-13 12:09:47');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `akun_opds`
--
ALTER TABLE `akun_opds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `akun_opds_email_unique` (`email`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jenis_opds`
--
ALTER TABLE `jenis_opds`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kabupatens`
--
ALTER TABLE `kabupatens`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kecamatans`
--
ALTER TABLE `kecamatans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kegiatans`
--
ALTER TABLE `kegiatans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kegiatan_indikator_kinerjas`
--
ALTER TABLE `kegiatan_indikator_kinerjas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kegiatan_target_satuan_rp_realisasis`
--
ALTER TABLE `kegiatan_target_satuan_rp_realisasis`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kelurahans`
--
ALTER TABLE `kelurahans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_opds`
--
ALTER TABLE `master_opds`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `misis`
--
ALTER TABLE `misis`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `negaras`
--
ALTER TABLE `negaras`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `opds`
--
ALTER TABLE `opds`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `opd_kegiatan_indikator_kinerjas`
--
ALTER TABLE `opd_kegiatan_indikator_kinerjas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `opd_program_indikator_kinerjas`
--
ALTER TABLE `opd_program_indikator_kinerjas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `pivot_opd_rentra_kegiatans`
--
ALTER TABLE `pivot_opd_rentra_kegiatans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_perubahan_kegiatans`
--
ALTER TABLE `pivot_perubahan_kegiatans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_perubahan_misis`
--
ALTER TABLE `pivot_perubahan_misis`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_perubahan_programs`
--
ALTER TABLE `pivot_perubahan_programs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_perubahan_sasarans`
--
ALTER TABLE `pivot_perubahan_sasarans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_perubahan_sasaran_pds`
--
ALTER TABLE `pivot_perubahan_sasaran_pds`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_perubahan_sub_kegiatans`
--
ALTER TABLE `pivot_perubahan_sub_kegiatans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_perubahan_tujuans`
--
ALTER TABLE `pivot_perubahan_tujuans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_perubahan_tujuan_pds`
--
ALTER TABLE `pivot_perubahan_tujuan_pds`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_perubahan_urusans`
--
ALTER TABLE `pivot_perubahan_urusans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_perubahan_visis`
--
ALTER TABLE `pivot_perubahan_visis`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_program_kegiatan_renstras`
--
ALTER TABLE `pivot_program_kegiatan_renstras`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_sasaran_indikator_program_rpjmds`
--
ALTER TABLE `pivot_sasaran_indikator_program_rpjmds`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `program_indikator_kinerjas`
--
ALTER TABLE `program_indikator_kinerjas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `program_rpjmds`
--
ALTER TABLE `program_rpjmds`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `program_target_satuan_rp_realisasis`
--
ALTER TABLE `program_target_satuan_rp_realisasis`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `provinsis`
--
ALTER TABLE `provinsis`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `renstra_kegiatans`
--
ALTER TABLE `renstra_kegiatans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sasarans`
--
ALTER TABLE `sasarans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sasaran_indikator_kinerjas`
--
ALTER TABLE `sasaran_indikator_kinerjas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sasaran_pds`
--
ALTER TABLE `sasaran_pds`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sasaran_pd_indikator_kinerjas`
--
ALTER TABLE `sasaran_pd_indikator_kinerjas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sasaran_pd_target_satuan_rp_realisasis`
--
ALTER TABLE `sasaran_pd_target_satuan_rp_realisasis`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sasaran_target_satuan_rp_realisasis`
--
ALTER TABLE `sasaran_target_satuan_rp_realisasis`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sub_kegiatans`
--
ALTER TABLE `sub_kegiatans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sub_kegiatan_indikator_kinerjas`
--
ALTER TABLE `sub_kegiatan_indikator_kinerjas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tahun_periodes`
--
ALTER TABLE `tahun_periodes`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `target_rp_pertahun_programs`
--
ALTER TABLE `target_rp_pertahun_programs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `target_rp_pertahun_renstra_kegiatans`
--
ALTER TABLE `target_rp_pertahun_renstra_kegiatans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tujuans`
--
ALTER TABLE `tujuans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tujuan_indikator_kinerjas`
--
ALTER TABLE `tujuan_indikator_kinerjas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tujuan_pds`
--
ALTER TABLE `tujuan_pds`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tujuan_pd_indikator_kinerjas`
--
ALTER TABLE `tujuan_pd_indikator_kinerjas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tujuan_pd_target_satuan_rp_realisasis`
--
ALTER TABLE `tujuan_pd_target_satuan_rp_realisasis`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tujuan_target_satuan_rp_realisasis`
--
ALTER TABLE `tujuan_target_satuan_rp_realisasis`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `urusans`
--
ALTER TABLE `urusans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indeks untuk tabel `visis`
--
ALTER TABLE `visis`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `akun_opds`
--
ALTER TABLE `akun_opds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jenis_opds`
--
ALTER TABLE `jenis_opds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `kabupatens`
--
ALTER TABLE `kabupatens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT untuk tabel `kecamatans`
--
ALTER TABLE `kecamatans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `kegiatans`
--
ALTER TABLE `kegiatans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=711;

--
-- AUTO_INCREMENT untuk tabel `kegiatan_indikator_kinerjas`
--
ALTER TABLE `kegiatan_indikator_kinerjas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1130;

--
-- AUTO_INCREMENT untuk tabel `kegiatan_target_satuan_rp_realisasis`
--
ALTER TABLE `kegiatan_target_satuan_rp_realisasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `kelurahans`
--
ALTER TABLE `kelurahans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;

--
-- AUTO_INCREMENT untuk tabel `master_opds`
--
ALTER TABLE `master_opds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT untuk tabel `misis`
--
ALTER TABLE `misis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `negaras`
--
ALTER TABLE `negaras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT untuk tabel `opds`
--
ALTER TABLE `opds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `opd_kegiatan_indikator_kinerjas`
--
ALTER TABLE `opd_kegiatan_indikator_kinerjas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `opd_program_indikator_kinerjas`
--
ALTER TABLE `opd_program_indikator_kinerjas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pivot_opd_rentra_kegiatans`
--
ALTER TABLE `pivot_opd_rentra_kegiatans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_kegiatans`
--
ALTER TABLE `pivot_perubahan_kegiatans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=415;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_misis`
--
ALTER TABLE `pivot_perubahan_misis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_programs`
--
ALTER TABLE `pivot_perubahan_programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=645;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_sasarans`
--
ALTER TABLE `pivot_perubahan_sasarans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_sasaran_pds`
--
ALTER TABLE `pivot_perubahan_sasaran_pds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_sub_kegiatans`
--
ALTER TABLE `pivot_perubahan_sub_kegiatans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_tujuans`
--
ALTER TABLE `pivot_perubahan_tujuans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_tujuan_pds`
--
ALTER TABLE `pivot_perubahan_tujuan_pds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_urusans`
--
ALTER TABLE `pivot_perubahan_urusans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_visis`
--
ALTER TABLE `pivot_perubahan_visis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pivot_program_kegiatan_renstras`
--
ALTER TABLE `pivot_program_kegiatan_renstras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pivot_sasaran_indikator_program_rpjmds`
--
ALTER TABLE `pivot_sasaran_indikator_program_rpjmds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT untuk tabel `programs`
--
ALTER TABLE `programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=526;

--
-- AUTO_INCREMENT untuk tabel `program_indikator_kinerjas`
--
ALTER TABLE `program_indikator_kinerjas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1600;

--
-- AUTO_INCREMENT untuk tabel `program_rpjmds`
--
ALTER TABLE `program_rpjmds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT untuk tabel `program_target_satuan_rp_realisasis`
--
ALTER TABLE `program_target_satuan_rp_realisasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `provinsis`
--
ALTER TABLE `provinsis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `renstra_kegiatans`
--
ALTER TABLE `renstra_kegiatans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `sasarans`
--
ALTER TABLE `sasarans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `sasaran_indikator_kinerjas`
--
ALTER TABLE `sasaran_indikator_kinerjas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT untuk tabel `sasaran_pds`
--
ALTER TABLE `sasaran_pds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `sasaran_pd_indikator_kinerjas`
--
ALTER TABLE `sasaran_pd_indikator_kinerjas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `sasaran_pd_target_satuan_rp_realisasis`
--
ALTER TABLE `sasaran_pd_target_satuan_rp_realisasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `sasaran_target_satuan_rp_realisasis`
--
ALTER TABLE `sasaran_target_satuan_rp_realisasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `sub_kegiatans`
--
ALTER TABLE `sub_kegiatans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT untuk tabel `sub_kegiatan_indikator_kinerjas`
--
ALTER TABLE `sub_kegiatan_indikator_kinerjas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tahun_periodes`
--
ALTER TABLE `tahun_periodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `target_rp_pertahun_programs`
--
ALTER TABLE `target_rp_pertahun_programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `target_rp_pertahun_renstra_kegiatans`
--
ALTER TABLE `target_rp_pertahun_renstra_kegiatans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tujuans`
--
ALTER TABLE `tujuans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `tujuan_indikator_kinerjas`
--
ALTER TABLE `tujuan_indikator_kinerjas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `tujuan_pds`
--
ALTER TABLE `tujuan_pds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tujuan_pd_indikator_kinerjas`
--
ALTER TABLE `tujuan_pd_indikator_kinerjas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tujuan_pd_target_satuan_rp_realisasis`
--
ALTER TABLE `tujuan_pd_target_satuan_rp_realisasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tujuan_target_satuan_rp_realisasis`
--
ALTER TABLE `tujuan_target_satuan_rp_realisasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `urusans`
--
ALTER TABLE `urusans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `visis`
--
ALTER TABLE `visis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
