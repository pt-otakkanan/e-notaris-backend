-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 28 Okt 2025 pada 04.34
-- Versi server: 10.4.24-MariaDB
-- Versi PHP: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `enotaris`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `logo` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favicon` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favicon_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_hero` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `desc_hero` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `desc_footer` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`id`, `logo`, `logo_path`, `favicon`, `favicon_path`, `telepon`, `facebook`, `instagram`, `twitter`, `linkedin`, `title_hero`, `desc_hero`, `desc_footer`, `created_at`, `updated_at`) VALUES
(1, 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761621911/enotaris/settings/logo_20251028032508.png', 'enotaris/settings/logo_20251028032508', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761621913/enotaris/settings/favicon_20251028032510.png', 'enotaris/settings/favicon_20251028032510', NULL, NULL, NULL, NULL, NULL, 'Praktik Kenotariatan Dalam Satu Platform', 'E-Notaris membantu Anda mengelola praktik notaris secara efisien. Mulai dari pembuatan akta, penyimpanan dokumen, hingga pelacakan aktivitas. Semua dalam satu platform digital yang aman dan terpercaya.', 'Platform digital untuk memudahkan notaris dalam mengelola proyek, akta, hingga pelacakan aktivitas secara aman dan terpercaya.', '2025-10-27 20:23:49', '2025-10-27 20:25:12');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
