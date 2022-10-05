-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 05 Okt 2022 pada 02.32
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
-- Database: `new_emonev`
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
(1, 'Dinas Pendidikan dan Kebudayaan', 'disdik_madiun@email.com', NULL, '$2y$10$Iht10.dzrzW.tHnlPdyCtOgjzoNvNB.NuajxeTKNl10MP2aPbMiBK', 1, NULL, '2022-09-27 09:56:20', '2022-10-04 08:32:29', 'light-purple', 'light', 'vertical', 'pinned', 'fluid', 'flat');

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
  `tanggal` date DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kegiatans`
--

INSERT INTO `kegiatans` (`id`, `program_id`, `kode`, `deskripsi`, `tanggal`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, 1, '01', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja  Perangkat Daerah', '2022-09-28', 62, '2022-09-28 09:23:02', '2022-09-28 09:23:02');

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
(6, '2022_09_26_122548_create_akun_opds_table', 2),
(7, '2022_09_27_045958_create_opds_table', 2),
(8, '2022_09_27_133910_create_jenis_opds_table', 3),
(9, '2022_09_27_185952_create_urusans_table', 4),
(10, '2022_09_27_190319_create_pivot_perubahan_urusans_table', 4),
(11, '2022_09_27_190603_create_programs_table', 4),
(12, '2022_09_27_191007_create_pivot_perubahan_programs_table', 4),
(13, '2022_09_27_191236_create_pivot_program_indikators_table', 4),
(14, '2022_09_27_191516_create_kegiatans_table', 4),
(15, '2022_09_27_192232_create_pivot_perubahan_kegiatans_table', 4),
(16, '2022_09_27_192449_create_pivot_kegiatan_indikators_table', 4),
(17, '2022_09_27_192745_create_sub_kegiatans_table', 4),
(18, '2022_09_27_193146_create_pivot_perubahan_sub_kegiatans_table', 4),
(19, '2022_09_27_193416_create_pivot_sub_kegiatan_indikators_table', 4),
(20, '2022_09_29_105646_create_visis_table', 5),
(21, '2022_09_29_110217_create_pivot_perubahan_visis_table', 5),
(26, '2022_09_29_132826_create_pivot_tujuan_indikators_table', 5),
(30, '2022_09_29_140452_create_pivot_sasaran_indikators_table', 5),
(36, '2022_09_29_112452_create_misis_table', 6),
(37, '2022_09_29_112829_create_pivot_perubahan_tujuans_table', 6),
(38, '2022_09_29_113051_create_pivot_perubahan_misis_table', 6),
(39, '2022_09_29_132153_create_tujuans_table', 6),
(40, '2022_09_29_135916_create_sasarans_table', 6),
(41, '2022_09_29_140302_create_pivot_perubahan_sasarans_table', 6),
(42, '2022_09_30_143811_create_master_opds_table', 7),
(43, '2022_09_29_141006_create_program_rpjmds_table', 8),
(44, '2022_09_29_142546_create_pivot_perubahan_program_rpjmds_table', 8),
(46, '2022_10_03_041043_create_tahun_periodes_table', 9),
(47, '2022_10_04_140759_create_renstras_table', 10),
(48, '2022_10_04_141402_create_target_rp_pertahun_programs_table', 10),
(49, '2022_10_04_142114_create_target_rp_pertahun_tujuans_table', 10),
(50, '2022_10_04_143049_create_target_rp_pertahun_sasarans_table', 10),
(51, '2022_10_05_085346_create_target_rp_pertahun_tujuans_table', 11),
(52, '2022_10_05_090901_create_target_rp_pertahun_sasarans_table', 11),
(53, '2022_10_05_091722_create_target_rp_pertahun_programs_table', 11);

-- --------------------------------------------------------

--
-- Struktur dari tabel `misis`
--

CREATE TABLE `misis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kabupaten_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `misis`
--

INSERT INTO `misis` (`id`, `visi_id`, `kabupaten_id`, `kode`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 1, 62, '1', 'Mewujudkan rasa aman bagi seluruh Masyarakat dan aparatur pemerintah Kabupaten Madiun', '2022-09-29 12:21:49', '2022-09-29 12:21:49');

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
(1, 'Dinas Pendidikan dan Kebudayaan', '123456789012', 'Jl. Raya Tiron No.87, Tiron, Kec. Madiun, Kabupaten Madiun, Jawa Timur 63151', 62, 5, 62, NULL, NULL, '6332c8c48503a-220927.png', NULL, '2022-09-27 09:56:20', '2022-09-27 09:56:20');

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
-- Struktur dari tabel `pivot_kegiatan_indikators`
--

CREATE TABLE `pivot_kegiatan_indikators` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kegiatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `indikator` longtext COLLATE utf8mb4_unicode_ci,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_kegiatan_indikators`
--

INSERT INTO `pivot_kegiatan_indikators` (`id`, `kegiatan_id`, `indikator`, `target`, `satuan`, `created_at`, `updated_at`) VALUES
(2, 1, 'x', '1', 'x', '2022-09-28 20:37:07', '2022-09-28 20:37:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_kegiatans`
--

CREATE TABLE `pivot_perubahan_kegiatans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kegiatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `tanggal` date DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_perubahan_kegiatans`
--

INSERT INTO `pivot_perubahan_kegiatans` (`id`, `kegiatan_id`, `program_id`, `kode`, `deskripsi`, `tanggal`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '01', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja  Perangkat Daerah1', '2022-09-28', 62, '2022-09-28 12:20:05', '2022-09-28 12:20:05'),
(2, 1, 1, '01', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja  Perangkat Daerah', '2022-09-28', 62, '2022-09-28 12:20:30', '2022-09-28 12:20:30'),
(3, 1, 1, '01', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja  Perangkat Daerah 1', '2022-09-29', 62, '2022-09-29 02:42:28', '2022-09-29 02:42:28'),
(4, 1, 1, '01', 'Perencanaan, Penganggaran, dan Evaluasi Kinerja  Perangkat Daerah', '2022-09-29', 62, '2022-09-29 02:42:40', '2022-09-29 02:42:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_misis`
--

CREATE TABLE `pivot_perubahan_misis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `misi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visi_id` int(11) DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `kabupaten_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_perubahan_misis`
--

INSERT INTO `pivot_perubahan_misis` (`id`, `misi_id`, `visi_id`, `kode`, `deskripsi`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '1', 'Mewujudkan rasa aman bagi seluruh Masyarakat dan aparatur pemerintah Kabupaten Madiun 1', 62, '2022-09-29 12:57:04', '2022-09-29 12:57:04'),
(2, 1, 1, '1', 'Mewujudkan rasa aman bagi seluruh Masyarakat dan aparatur pemerintah Kabupaten Madiun', 62, '2022-09-29 12:57:25', '2022-09-29 12:57:25');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_programs`
--

CREATE TABLE `pivot_perubahan_programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `urusan_id` int(11) DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `pagu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_perubahan_programs`
--

INSERT INTO `pivot_perubahan_programs` (`id`, `program_id`, `urusan_id`, `kode`, `deskripsi`, `pagu`, `tanggal`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(4, 1, 1, '01', 'Program Pelayanan Administrasi Perkantoran', '1037222336.35', '2022-09-28', 62, '2022-09-28 05:39:40', '2022-09-28 05:39:40'),
(5, 2, 1, '02', 'Program Peningkatan Sarana dan Prasarana Aparatur', '186924610.93', '2022-09-28', 62, '2022-09-28 06:13:06', '2022-09-28 06:13:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_program_rpjmds`
--

CREATE TABLE `pivot_perubahan_program_rpjmds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_rpjmd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sasaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status_program` enum('program_prioritas','program_pendukung') COLLATE utf8mb4_unicode_ci NOT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_sasarans`
--

CREATE TABLE `pivot_perubahan_sasarans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sasaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tujuan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kabupaten_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_perubahan_sasarans`
--

INSERT INTO `pivot_perubahan_sasarans` (`id`, `sasaran_id`, `tujuan_id`, `kabupaten_id`, `kode`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 62, '1', 'Terciptanya ketenteraman dan ketertiban masyarakat 1', '2022-09-30 06:22:13', '2022-09-30 06:22:13'),
(2, 1, 1, 62, '1 1', 'Terciptanya ketenteraman dan ketertiban masyarakat 1', '2022-09-30 06:22:25', '2022-09-30 06:22:25'),
(3, 1, 1, 62, '1', 'Terciptanya ketenteraman dan ketertiban masyarakat', '2022-09-30 06:22:43', '2022-09-30 06:22:43'),
(4, 2, 1, 62, '1 asdfdas', 'Mewujudkan Pemerintahan yang Akuntable asdfasdjkjalsd', '2022-09-30 06:31:03', '2022-09-30 06:31:03'),
(5, 2, 1, 62, '1', 'Mewujudkan Pemerintahan yang Akuntable', '2022-09-30 06:31:16', '2022-09-30 06:31:16'),
(6, 1, 1, 62, '2', 'Terciptanya ketenteraman dan ketertiban masyarakat', '2022-10-01 03:57:40', '2022-10-01 03:57:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_sub_kegiatans`
--

CREATE TABLE `pivot_perubahan_sub_kegiatans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sub_kegiatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kegiatan_id` int(11) DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `kabupaten_id` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_tujuans`
--

CREATE TABLE `pivot_perubahan_tujuans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tujuan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `misi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kabupaten_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_perubahan_tujuans`
--

INSERT INTO `pivot_perubahan_tujuans` (`id`, `tujuan_id`, `misi_id`, `kabupaten_id`, `kode`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 62, '1', 'Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal 1', '2022-09-29 22:40:15', '2022-09-29 22:40:15'),
(2, 1, 1, 62, '1asdfas', 'Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal 1asdfdas', '2022-09-29 22:40:30', '2022-09-29 22:40:30'),
(3, 1, 1, 62, '1', 'Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal 1', '2022-09-29 22:42:06', '2022-09-29 22:42:06'),
(4, 1, 1, 62, '1asdfasd', 'Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal 1asfdsad', '2022-09-30 01:40:27', '2022-09-30 01:40:27'),
(5, 1, 1, 62, '1', 'Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal 1', '2022-09-30 01:43:15', '2022-09-30 01:43:15'),
(6, 1, 1, 62, '1', 'Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal', '2022-09-30 01:43:34', '2022-09-30 01:43:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_urusans`
--

CREATE TABLE `pivot_perubahan_urusans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `urusan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `tanggal` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_perubahan_visis`
--

CREATE TABLE `pivot_perubahan_visis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_perubahan_visis`
--

INSERT INTO `pivot_perubahan_visis` (`id`, `visi_id`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 1, 'TERWUJUDNYA KABUPATEN MADIUN AMAN, MANDIRI, SEJAHTERA DAN BERAKHLAK 1', '2022-09-29 09:14:15', '2022-09-29 09:14:15'),
(2, 1, 'TERWUJUDNYA KABUPATEN MADIUN AMAN, MANDIRI, SEJAHTERA DAN BERAKHLAK', '2022-09-29 11:05:28', '2022-09-29 11:05:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_program_indikators`
--

CREATE TABLE `pivot_program_indikators` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `indikator` longtext COLLATE utf8mb4_unicode_ci,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_program_indikators`
--

INSERT INTO `pivot_program_indikators` (`id`, `program_id`, `indikator`, `target`, `satuan`, `created_at`, `updated_at`) VALUES
(3, 1, 'Persentase administrasi perkantoran yang terpenuhi', '100', 'persen', '2022-09-28 07:44:31', '2022-09-28 07:44:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_sasaran_indikators`
--

CREATE TABLE `pivot_sasaran_indikators` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sasaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `indikator` longtext COLLATE utf8mb4_unicode_ci,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_sasaran_indikators`
--

INSERT INTO `pivot_sasaran_indikators` (`id`, `sasaran_id`, `indikator`, `target`, `satuan`, `created_at`, `updated_at`) VALUES
(2, 2, 'Indeks Stabilitas', '77,09', 'Indeks', '2022-09-30 06:58:41', '2022-09-30 06:58:41'),
(3, 4, 'sasa', '100', 'persen', '2022-10-03 22:32:20', '2022-10-03 22:32:20'),
(4, 1, 'minum', '1', 'orang', '2022-10-03 22:33:49', '2022-10-03 22:33:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_sub_kegiatan_indikators`
--

CREATE TABLE `pivot_sub_kegiatan_indikators` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sub_kegiatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `indikator` longtext COLLATE utf8mb4_unicode_ci,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_sub_kegiatan_indikators`
--

INSERT INTO `pivot_sub_kegiatan_indikators` (`id`, `sub_kegiatan_id`, `indikator`, `target`, `satuan`, `created_at`, `updated_at`) VALUES
(1, 1, 'x', '1', 'x', '2022-09-29 03:22:33', '2022-09-29 03:22:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pivot_tujuan_indikators`
--

CREATE TABLE `pivot_tujuan_indikators` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tujuan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `indikator` longtext COLLATE utf8mb4_unicode_ci,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pivot_tujuan_indikators`
--

INSERT INTO `pivot_tujuan_indikators` (`id`, `tujuan_id`, `indikator`, `target`, `satuan`, `created_at`, `updated_at`) VALUES
(2, 2, 'Indeks Reformasi Birokrasi', '73,10', 'Indeks', '2022-09-30 01:48:54', '2022-09-30 01:48:54'),
(4, 1, 'Indeks Kesalehan Sosial', '65.69', 'Indeks', '2022-09-30 01:59:58', '2022-09-30 01:59:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `programs`
--

CREATE TABLE `programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `urusan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `pagu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `programs`
--

INSERT INTO `programs` (`id`, `urusan_id`, `kode`, `deskripsi`, `pagu`, `tanggal`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, 1, '1', 'Program Pelayanan Administrasi Perkantoran', '1037222336.35', '2022-09-28', 62, '2022-09-28 05:38:54', '2022-09-28 05:38:54'),
(2, 1, '2', 'Program Peningkatan Sarana dan Prasarana Aparatur', '186924610.93', '2022-09-28', 62, '2022-09-28 05:38:54', '2022-09-28 05:38:54'),
(3, 1, '15', 'Program Pendidikan Anak Usia Dini', '8106940947.96', '2022-09-28', 62, '2022-09-28 05:38:54', '2022-09-28 05:38:54'),
(4, 1, '18', 'Program Pendidikan Non Formal', '353179798.04', '2022-09-28', 62, '2022-09-28 05:38:54', '2022-09-28 05:38:54'),
(5, 1, '20', 'Program Peningkatan Mutu Pendidik dan Tenaga Kependidikan', '7766721180.6', '2022-09-28', 62, '2022-09-28 05:38:54', '2022-09-28 05:38:54'),
(6, 1, '22', 'Program Manajemen Pelayanan Pendidikan', '286465156.78', '2022-09-28', 62, '2022-09-28 05:38:54', '2022-09-28 05:38:54'),
(7, 1, '24', 'Program Pendidikan SD', '41642907194.78', '2022-09-28', 62, '2022-09-28 05:38:54', '2022-09-28 05:38:54'),
(8, 1, '25', 'Program Pendidikan SMP', '9635261160', '2022-09-28', 62, '2022-09-28 05:38:54', '2022-09-28 05:38:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `program_rpjmds`
--

CREATE TABLE `program_rpjmds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sasaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status_program` enum('Program Prioritas','Program Pendukung') COLLATE utf8mb4_unicode_ci NOT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `program_rpjmds`
--

INSERT INTO `program_rpjmds` (`id`, `program_id`, `sasaran_id`, `status_program`, `opd_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Program Prioritas', 2, '2022-10-03 21:51:16', '2022-10-03 21:51:16'),
(3, 1, 1, 'Program Pendukung', 2, '2022-10-03 21:51:44', '2022-10-03 21:51:44'),
(4, 2, 1, 'Program Pendukung', 5, '2022-10-03 22:12:42', '2022-10-03 22:12:42');

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
-- Struktur dari tabel `renstras`
--

CREATE TABLE `renstras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `misi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tujuan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sasaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `renstras`
--

INSERT INTO `renstras` (`id`, `misi_id`, `tujuan_id`, `sasaran_id`, `program_id`, `opd_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 2, 1, '2022-10-04 20:44:53', '2022-10-04 20:44:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sasarans`
--

CREATE TABLE `sasarans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tujuan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kabupaten_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sasarans`
--

INSERT INTO `sasarans` (`id`, `tujuan_id`, `kabupaten_id`, `kode`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 1, 62, '1', 'Terciptanya ketenteraman dan ketertiban masyarakat', '2022-09-30 03:26:48', '2022-09-30 03:26:48'),
(2, 1, 62, '1', 'Mewujudkan Pemerintahan yang Akuntable', '2022-09-30 06:30:41', '2022-09-30 06:30:41'),
(3, 1, 62, '1', 'adfasd', '2022-10-03 12:43:57', '2022-10-03 12:43:57'),
(4, 2, 62, '2', 'dafasd', '2022-10-03 12:44:22', '2022-10-03 12:44:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sub_kegiatans`
--

CREATE TABLE `sub_kegiatans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kegiatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `kabupaten_id` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sub_kegiatans`
--

INSERT INTO `sub_kegiatans` (`id`, `kegiatan_id`, `kode`, `deskripsi`, `kabupaten_id`, `tanggal`, `created_at`, `updated_at`) VALUES
(1, 1, '01', 'Penyusunan Dokumen Perencanaan Perangkat  Daerah', 62, '2022-09-29', '2022-09-28 21:35:52', '2022-09-28 21:35:52');

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
(2, '2019', '2023', 'Aktif', '2022-10-02 21:37:09', '2022-10-04 01:27:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `target_rp_pertahun_programs`
--

CREATE TABLE `target_rp_pertahun_programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pivot_program_indikator_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `target_rp_pertahun_sasarans`
--

CREATE TABLE `target_rp_pertahun_sasarans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pivot_sasaran_indikator_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `target_rp_pertahun_tujuans`
--

CREATE TABLE `target_rp_pertahun_tujuans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pivot_tujuan_indikator_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `kabupaten_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tujuans`
--

INSERT INTO `tujuans` (`id`, `misi_id`, `kabupaten_id`, `kode`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 1, 62, '1', 'Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal', '2022-09-29 22:20:46', '2022-09-29 22:20:46'),
(2, 1, 62, '2', 'Meningkatkan Tata kelola Pemerintah yang Baik (Good Governance) untuk Pelayanan Publik', '2022-09-30 01:48:31', '2022-09-30 01:48:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `urusans`
--

CREATE TABLE `urusans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `tanggal` date DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `urusans`
--

INSERT INTO `urusans` (`id`, `kode`, `deskripsi`, `tanggal`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, '1.01', 'urusan pemerintah bidang pendidikan', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(2, '1.02', 'urusan pemerintah bidang kesehatan', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(3, '1.03', 'urusan pemerintahan bidang pekerjaan umum dan penataan ruang', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(4, '1.04', 'urusan pemerintahan bidang perumahan rakyat dan kawasan permukiman', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(5, '1.05', 'urusan pemerintahan bidang ketentraman dan ketertiban umum serta perlindungan masyarakat', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(6, '1.06', 'urusan pemerintahan bidang sosial', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(7, '2.07', 'urusan pemerintahan bidang tenaga kerja', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(8, '2.08', 'urusan pemerintahan bidang pemberdayaan perempuan dan perlindungan anak', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(9, '2.09', 'urusan pemerintahan bidang pangan', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(10, '2.10', 'urusan pemerintahan bidang pertanahan', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(11, '2.11', 'urusan pemerintahan bidang lingkungan hidup', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(12, '2.12', 'urusan pemerintahan bidang administrasi kependudukan dan pencatatan sipil', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(13, '2.13', 'urusan pemerintahan bidang pemberdayaan masyarakat dan desa', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(14, '2.14', 'urusan pemerintahan bidang pengendalian penduduk dan keluarga berencana', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(15, '2.15', 'urusan pemerintahan bidang perhubungan', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(16, '2.16', 'urusan pemerintahan bidang komunikasi dan informatika', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(17, '2.17', 'urusan pemerintahan bidang koperasi, usaha kecil dan menengah', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(18, '2.18', 'urusan pemerintahan bidang penanaman modal', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(19, '2.19', 'urusan pemerintahan bidang kepemudaan dan olahraga', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(20, '2.20', 'urusan pemerintahan bidang statistik', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(21, '2.21', 'urusan pemerintahan bidang persandian', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(22, '2.22', 'urusan pemerintahan bidang kebudayaan', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(23, '2.23', 'urusan pemerintahan bidang perpustakaan', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(24, '2.24', 'urusan pemerintahan bidang kearsipan', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(25, '3.25', 'urusan pemerintahan bidang kelautan dan perikanan', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(26, '3.26', 'urusan pemerintahan bidang pariwisata', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(27, '3.27', 'urusan pemerintahan bidang pertanian', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(28, '3.29', 'urusan pemerintahan bidang energi dan sumber daya mineral', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(29, '3.30', 'urusan pemerintahan bidang perdagangan', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(30, '3.31', 'urusan pemerintahan bidang perindustrian', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(31, '3.32', 'urusan pemerintahan bidang transmigrasi', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(32, '4.01', 'unsur Sekretariat daerah', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(33, '4.02', 'unsur Sekretariat DPRD', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(34, '5.01', 'unsur perencanaan', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(35, '5.02', 'unsur keuangan', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(36, '5.03', 'unsur kepegawaian', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(37, '5.05', 'unsur penelitian dan pengembangan', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(38, '6.01', 'unsur pengawasan urusan pemerintahan', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(39, '7.01', 'unsur kewilayahan', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(40, '8.01', 'urusan pemerintahan umum', NULL, 62, '2022-09-27 21:41:09', '2022-09-27 21:41:09'),
(41, '1.01.01', 'Program Pelayanan Administrasi Perkantoran', NULL, 62, '2022-09-27 22:19:22', '2022-09-27 22:19:22');

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
  `color_layout` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nav_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `placement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `behaviour` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `layout` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `radius` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `color_layout`, `nav_color`, `placement`, `behaviour`, `layout`, `radius`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, 'Bappeda', 'bappeda@emonev.madiun', NULL, '$2y$10$i1XQWqM7gdTg8clhJmnRj.qdWm2nFcG514aRQVCCrW/7H/FfvndMa', NULL, 'light-blue', 'default', 'horizontal', 'pinned', 'fluid', 'standard', 62, '2022-09-26 05:33:25', '2022-10-02 22:21:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `visis`
--

CREATE TABLE `visis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `deskripsi` longtext COLLATE utf8mb4_unicode_ci,
  `kabupaten_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `visis`
--

INSERT INTO `visis` (`id`, `deskripsi`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, 'TERWUJUDNYA KABUPATEN MADIUN AMAN, MANDIRI, SEJAHTERA DAN BERAKHLAK', 62, '2022-09-29 09:02:03', '2022-09-29 09:02:03');

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
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `pivot_kegiatan_indikators`
--
ALTER TABLE `pivot_kegiatan_indikators`
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
-- Indeks untuk tabel `pivot_perubahan_program_rpjmds`
--
ALTER TABLE `pivot_perubahan_program_rpjmds`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_perubahan_sasarans`
--
ALTER TABLE `pivot_perubahan_sasarans`
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
-- Indeks untuk tabel `pivot_program_indikators`
--
ALTER TABLE `pivot_program_indikators`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_sasaran_indikators`
--
ALTER TABLE `pivot_sasaran_indikators`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_sub_kegiatan_indikators`
--
ALTER TABLE `pivot_sub_kegiatan_indikators`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pivot_tujuan_indikators`
--
ALTER TABLE `pivot_tujuan_indikators`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `program_rpjmds`
--
ALTER TABLE `program_rpjmds`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `provinsis`
--
ALTER TABLE `provinsis`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `renstras`
--
ALTER TABLE `renstras`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sasarans`
--
ALTER TABLE `sasarans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sub_kegiatans`
--
ALTER TABLE `sub_kegiatans`
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
-- Indeks untuk tabel `target_rp_pertahun_sasarans`
--
ALTER TABLE `target_rp_pertahun_sasarans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `target_rp_pertahun_tujuans`
--
ALTER TABLE `target_rp_pertahun_tujuans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tujuans`
--
ALTER TABLE `tujuans`
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT untuk tabel `misis`
--
ALTER TABLE `misis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `negaras`
--
ALTER TABLE `negaras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT untuk tabel `opds`
--
ALTER TABLE `opds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pivot_kegiatan_indikators`
--
ALTER TABLE `pivot_kegiatan_indikators`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_kegiatans`
--
ALTER TABLE `pivot_perubahan_kegiatans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_misis`
--
ALTER TABLE `pivot_perubahan_misis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_programs`
--
ALTER TABLE `pivot_perubahan_programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_program_rpjmds`
--
ALTER TABLE `pivot_perubahan_program_rpjmds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_sasarans`
--
ALTER TABLE `pivot_perubahan_sasarans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_sub_kegiatans`
--
ALTER TABLE `pivot_perubahan_sub_kegiatans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_tujuans`
--
ALTER TABLE `pivot_perubahan_tujuans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_urusans`
--
ALTER TABLE `pivot_perubahan_urusans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pivot_perubahan_visis`
--
ALTER TABLE `pivot_perubahan_visis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pivot_program_indikators`
--
ALTER TABLE `pivot_program_indikators`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pivot_sasaran_indikators`
--
ALTER TABLE `pivot_sasaran_indikators`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pivot_sub_kegiatan_indikators`
--
ALTER TABLE `pivot_sub_kegiatan_indikators`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pivot_tujuan_indikators`
--
ALTER TABLE `pivot_tujuan_indikators`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `programs`
--
ALTER TABLE `programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `program_rpjmds`
--
ALTER TABLE `program_rpjmds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `provinsis`
--
ALTER TABLE `provinsis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `renstras`
--
ALTER TABLE `renstras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `sasarans`
--
ALTER TABLE `sasarans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `sub_kegiatans`
--
ALTER TABLE `sub_kegiatans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tahun_periodes`
--
ALTER TABLE `tahun_periodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `target_rp_pertahun_programs`
--
ALTER TABLE `target_rp_pertahun_programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `target_rp_pertahun_sasarans`
--
ALTER TABLE `target_rp_pertahun_sasarans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `target_rp_pertahun_tujuans`
--
ALTER TABLE `target_rp_pertahun_tujuans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tujuans`
--
ALTER TABLE `tujuans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `urusans`
--
ALTER TABLE `urusans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

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
