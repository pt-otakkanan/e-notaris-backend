-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Nov 2025 pada 03.12
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
-- Struktur dari tabel `activity`
--

CREATE TABLE `activity` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `track_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deed_id` bigint(20) UNSIGNED NOT NULL,
  `user_notaris_id` bigint(20) UNSIGNED NOT NULL,
  `activity_notaris_id` bigint(20) UNSIGNED NOT NULL,
  `tracking_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `activity`
--

INSERT INTO `activity` (`id`, `track_id`, `deed_id`, `user_notaris_id`, `activity_notaris_id`, `tracking_code`, `created_at`, `updated_at`, `name`) VALUES
(1, 1, 4, 2, 2, 'ACT-UD2S5UZB', '2025-10-27 20:41:29', '2025-10-27 20:41:29', 'Pendirian CV'),
(2, 2, 4, 2, 2, 'ACT-TCANTWRG', '2025-11-02 18:25:05', '2025-11-02 18:25:05', 'Pendirian CV PT Kode Muda'),
(3, 3, 1, 1, 1, 'ACT-7BL2JDFP', '2025-11-03 19:02:54', '2025-11-03 19:02:54', 'Pendirian CV Kode Muda'),
(4, 4, 4, 2, 2, 'ACT-KU6CD88I', '2025-11-05 20:47:00', '2025-11-05 20:47:00', 'Pendirian CV ABC');

-- --------------------------------------------------------

--
-- Struktur dari tabel `blogs`
--

CREATE TABLE `blogs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `blogs`
--

INSERT INTO `blogs` (`id`, `user_id`, `image`, `image_path`, `title`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762411564/enotaris/blogs/blog_20251106064601.png', 'enotaris/blogs/blog_20251106064601', 'Dolores nihil volupt', '<p>Ut quod architecto n.</p>', '2025-11-05 23:46:04', '2025-11-05 23:46:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `blog_category`
--

CREATE TABLE `blog_category` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `blog_id` bigint(20) UNSIGNED NOT NULL,
  `category_blog_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `blog_category`
--

INSERT INTO `blog_category` (`id`, `blog_id`, `category_blog_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-11-05 23:46:04', '2025-11-05 23:46:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `category_blogs`
--

CREATE TABLE `category_blogs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `category_blogs`
--

INSERT INTO `category_blogs` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Tutorial', '2025-11-05 23:45:33', '2025-11-05 23:45:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `client_activities`
--

CREATE TABLE `client_activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `client_activity`
--

CREATE TABLE `client_activity` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status_approval` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `order` smallint(5) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `client_activity`
--

INSERT INTO `client_activity` (`id`, `user_id`, `activity_id`, `status_approval`, `order`, `created_at`, `updated_at`) VALUES
(1, 7, 1, 'approved', 1, '2025-10-27 20:41:29', '2025-10-27 20:42:18'),
(2, 3, 2, 'approved', 1, '2025-11-02 18:25:05', '2025-11-02 18:26:18'),
(3, 5, 2, 'approved', 2, '2025-11-02 18:25:05', '2025-11-02 18:27:15'),
(4, 5, 3, 'pending', 1, '2025-11-03 19:02:54', '2025-11-03 19:02:54'),
(5, 3, 4, 'approved', 1, '2025-11-05 20:47:00', '2025-11-05 20:49:07'),
(6, 7, 4, 'approved', 2, '2025-11-05 20:47:00', '2025-11-05 20:48:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `client_drafts`
--

CREATE TABLE `client_drafts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `draft_deed_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status_approval` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `client_drafts`
--

INSERT INTO `client_drafts` (`id`, `user_id`, `draft_deed_id`, `status_approval`, `created_at`, `updated_at`) VALUES
(1, 7, 1, 'pending', '2025-10-27 20:41:29', '2025-10-27 20:54:19'),
(2, 3, 2, 'approved', '2025-11-02 18:25:05', '2025-11-02 18:33:52'),
(3, 5, 2, 'approved', '2025-11-02 18:25:05', '2025-11-02 18:34:28'),
(4, 5, 3, 'pending', '2025-11-03 19:02:54', '2025-11-03 19:02:54'),
(5, 3, 4, 'approved', '2025-11-05 20:47:00', '2025-11-06 22:28:35'),
(6, 7, 4, 'approved', '2025-11-05 20:47:00', '2025-11-06 22:29:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `deeds`
--

CREATE TABLE `deeds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_notaris_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `deeds`
--

INSERT INTO `deeds` (`id`, `user_notaris_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Pendirian CV', 'Pembuatan akta pendirian Commanditaire Vennootschap (CV) untuk kegiatan usaha bersama.', '2025-10-27 19:26:09', '2025-10-27 19:26:09'),
(2, 1, 'Pendirian PT', 'Pembuatan akta pendirian Perseroan Terbatas (PT) untuk badan usaha berbadan hukum.', '2025-10-27 19:26:09', '2025-10-27 19:26:09'),
(3, 1, 'Jual Beli', 'Akta jual beli antara dua pihak dengan dasar hukum yang sah.', '2025-10-27 19:26:09', '2025-10-27 19:26:09'),
(4, 2, 'Pendirian CV', 'Pendirian CV digunakan untuk mendirikan CV', '2025-10-27 19:56:02', '2025-10-27 19:56:02'),
(9, 2, 'hanum', 'tes', '2025-11-09 20:33:43', '2025-11-09 20:33:43'),
(10, 2, 'tesss2', 'Jenis', '2025-11-09 21:37:02', '2025-11-09 21:37:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `deed_requirement_templates`
--

CREATE TABLE `deed_requirement_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `deed_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_file` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `default_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `deed_requirement_templates`
--

INSERT INTO `deed_requirement_templates` (`id`, `deed_id`, `name`, `is_file`, `is_active`, `default_value`, `created_at`, `updated_at`) VALUES
(5, 9, 'NPWP', 1, 1, NULL, '2025-11-09 20:56:56', '2025-11-09 20:56:56'),
(6, 9, 'KTP', 1, 1, NULL, '2025-11-09 20:56:56', '2025-11-09 20:56:56'),
(10, 10, 'KTP', 0, 1, NULL, '2025-11-09 22:21:44', '2025-11-09 22:21:44'),
(11, 10, 'KTP', 1, 1, NULL, '2025-11-09 22:21:44', '2025-11-09 22:21:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `document_requirements`
--

CREATE TABLE `document_requirements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `deed_requirement_template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activity_notaris_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `requirement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `requirement_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_file_snapshot` tinyint(1) NOT NULL DEFAULT 0,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_approval` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `document_requirements`
--

INSERT INTO `document_requirements` (`id`, `deed_requirement_template_id`, `activity_notaris_id`, `user_id`, `requirement_id`, `requirement_name`, `is_file_snapshot`, `value`, `file`, `file_path`, `status_approval`, `created_at`, `updated_at`) VALUES
(1, NULL, 2, 3, 1, 'NPWP', 0, NULL, NULL, NULL, 'pending', '2025-11-02 18:28:03', '2025-11-02 18:28:03'),
(2, NULL, 2, 5, 1, 'NPWP', 0, NULL, NULL, NULL, 'pending', '2025-11-02 18:28:03', '2025-11-02 18:28:03'),
(3, NULL, 2, 3, 2, 'Surat Kuasa', 1, NULL, NULL, NULL, 'pending', '2025-11-02 18:28:24', '2025-11-02 18:28:24'),
(4, NULL, 2, 5, 2, 'Surat Kuasa', 1, NULL, NULL, NULL, 'pending', '2025-11-02 18:28:24', '2025-11-02 18:28:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `draft_deeds`
--

CREATE TABLE `draft_deeds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `custom_value_template` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reading_schedule` timestamp NULL DEFAULT NULL,
  `status_approval` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_ttd` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_ttd_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `draft_deeds`
--

INSERT INTO `draft_deeds` (`id`, `reference_number`, `activity_id`, `custom_value_template`, `reading_schedule`, `status_approval`, `file`, `file_path`, `file_ttd`, `file_ttd_path`, `created_at`, `updated_at`) VALUES
(1, '1028/OK/ACT-UD2S5UZB/2025', 1, '<h2 class=\"ql-align-center\">PERJANJIAN SEWA MENYEWA</h2><p class=\"ql-align-center\">Nomor : {{reference_number}}</p><p>– Pada hari ini, {{today}}</p><p>– tanggal .............................................................</p><p>– Pukul .................................................................</p><p>– Berhadapan dengan saya, {{notaris_name}}, Notaris di {{schedule_place}}, dengan dihadiri oleh para saksi yang saya, Notaris, kenal dan akan disebutkan nama-namanya pada bahagian akhir akta ini:</p><p><strong>I. Tuan {{penghadap1_name}}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>– menurut keterangannya dalam hal ini bertindak dalam jabatannya selaku Presiden Direktur dari Perseroan Terbatas PT. .........., berkedudukan di Jakarta yang anggaran dasarnya beserta perubahannya telah mendapat persetujuan dari Menteri Kehakiman dan Hak Asasi Manusia berturut-turut:</p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>selanjutnya disebut: <strong>Pihak Kedua</strong> atau <strong>Penyewa</strong>.</p><p>– Para penghadap telah saya, Notaris, kenal.</p><p>– Para penghadap menerangkan terlebih dahulu:</p><p>– bahwa Pihak Pertama adalah pemilik dari bangunan Rumah Toko (Ruko) yang hendak disewakan kepada Pihak Kedua yang akan disebutkan di bawah ini dan Pihak Kedua menerangkan menyewa dari Pihak Pertama berupa:</p><p>– 1 (satu) unit bangunan Rumah Toko (Ruko) berlantai 3 (tiga) berikut turutannya, lantai keramik, dinding tembok, atap dak, aliran listrik sebesar 2.200 Watt, dilengkapi air dari jet pump, berdiri di atas sebidang tanah Sertifikat HGB Nomor: ............ seluas ...... m² (....................................), penerbitan sertifikat tanggal ..........................., tercantum atas nama .................. yang telah diuraikan dalam Gambar Situasi tanggal ............ nomor ............; Sertifikat tanah diterbitkan oleh Kantor Pertanahan Kabupaten Bekasi, terletak di Provinsi Jawa Barat, Kabupaten Bekasi, Kecamatan Cibitung, Desa Ganda Mekar, setempat dikenal sebagai Mega Mall MM.2100 Blok B Nomor 8.</p><p>– Berdasarkan keterangan-keterangan tersebut di atas, kedua belah pihak sepakat membuat perjanjian sewa-menyewa dengan syarat-syarat dan ketentuan-ketentuan sebagai berikut:</p><p><strong>----------------------- Pasal 1.</strong></p><p>Perjanjian sewa-menyewa ini berlangsung untuk jangka waktu 2 (dua) tahun terhitung sejak tanggal ............ sampai dengan tanggal ............</p><p>– Penyerahan Ruko akan dilakukan dalam keadaan kosong/tidak dihuni pada tanggal .................. dengan penyerahan semua kunci-kuncinya.</p><p><strong>----------------------- Pasal 2.</strong></p><p>– Uang kontrak sewa disepakati sebesar Rp. ............ (....................................) untuk 2 (dua) tahun masa sewa.</p><p>– Jumlah uang sewa sebesar Rp. ............ (....................................) tersebut dibayar oleh Pihak Kedua kepada Pihak Pertama pada saat penandatanganan akta ini atau pada tanggal .................. dengan kwitansi tersendiri, dan akta ini berlaku sebagai tanda penerimaan yang sah.</p><p><strong>----------------------- Pasal 3.</strong></p><p>– Pihak Kedua hanya akan menggunakan yang disewakan dalam akta ini sebagai tempat kegiatan perkantoran/usaha.</p><p>– Jika diperlukan, Pihak Pertama memberikan surat rekomendasi/keterangan yang diperlukan Pihak Kedua sepanjang tidak melanggar hukum.</p><p>– Pihak Kedua wajib mentaati peraturan-peraturan pihak yang berwajib dan menjamin Pihak Pertama tidak mendapat teguran/tuntutan apapun karenanya.</p><p><strong>----------------------- Pasal 4.</strong></p><p>– Hanya dengan persetujuan tertulis Pihak Pertama, Pihak Kedua boleh mengadakan perubahan/penambahan pada bangunan; seluruh biaya dan tanggung jawab pada Pihak Kedua, dan pada akhir masa kontrak menjadi hak Pihak Pertama.</p><p>– Penyerahan nyata dari yang disewakan oleh Pihak Pertama kepada Pihak Kedua dilakukan pada tanggal .................. dengan penyerahan semua kunci-kunci.</p><p><strong>----------------------- Pasal 5.</strong></p><p>Pihak Pertama memberi izin kepada Pihak Kedua untuk pemasangan/penambahan antara lain:</p><ol><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Sekat-sekat pada ruangan;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Antena radio/CD;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Line telepon;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Air Conditioner (AC);</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Penambahan daya listrik;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Saluran fax;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Internet;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>TV Kabel;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Shower;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Penggantian W/C;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Katrol pengangkut barang lantai 1–3;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Peralatan keamanan;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Peralatan pendukung usaha (rak/mesin) tanpa merusak struktur bangunan.</li></ol><p>– Setelah masa kontrak berakhir, Pihak Kedua mengembalikan seperti keadaan semula dengan biaya Pihak Kedua.</p><p>– Pihak Kedua boleh mengganti kunci ruangan di dalam bangunan (kecuali pintu utama); pada akhir masa kontrak, kunci-kunci diserahkan ke Pihak Pertama.</p><p>– Pihak Pertama menjamin yang disewakan adalah miliknya dan bebas dari tuntutan pihak lain.</p><p>– Selama masa sewa, Pihak Pertama boleh memeriksa bangunan sewaktu-waktu.</p><p><strong>----------------------- Pasal 6.</strong></p><p>– Selama masa kontrak, pembayaran langganan listrik/air/telepon dan kewajiban lain terkait pemakaian dibayar Pihak Kedua hingga bulan terakhir dengan bukti pembayaran setiap bulan.</p><p>– Pihak Pertama membayar Pajak Bumi dan Bangunan (PBB) untuk objek sewa.</p><p><strong>----------------------- Pasal 7.</strong></p><p>– Pihak Kedua wajib memelihara yang disewa dengan baik; kerusakan karena kelalaian diperbaiki atas biaya Pihak Kedua.</p><p>– Apabila terjadi force majeure (kebakaran—kecuali kelalaian Pihak Kedua—sabotase, badai, banjir, gempa) sehingga objek musnah, para pihak dibebaskan dari tuntutan.</p><p><strong>----------------------- Pasal 8.</strong></p><p>– Pihak Pertama menjamin tidak ada tuntutan atau gangguan dari pihak lain atas yang disewa selama kontrak.</p><p><strong>----------------------- Pasal 9.</strong></p><p>Pihak Kedua, dengan persetujuan tertulis Pihak Pertama, boleh mengalihkan/memindahkan hak kontrak pada pihak lain, sebagian maupun seluruhnya, selama masa kontrak berlaku.</p><p><strong>----------------------- Pasal 10.</strong></p><p>Pihak Kedua wajib memberi pemberitahuan mengenai berakhir/akan diperpanjangnya kontrak kepada Pihak Pertama selambat-lambatnya 2 (dua) bulan sebelum berakhir.</p><p><strong>----------------------- Pasal 11.</strong></p><p>Pada saat berakhirnya kontrak dan tidak ada perpanjangan, Pihak Kedua menyerahkan kembali objek sewa dalam keadaan kosong, terpelihara baik, dengan semua kunci pada tanggal ..................</p><p>Apabila terlambat, Pihak Kedua dikenakan denda sebesar Rp. 27.500,- per hari selama 7 (tujuh) hari pertama; jika masih tidak diserahkan, Pihak Kedua memberi kuasa kepada Pihak Pertama (dengan hak substitusi) untuk melakukan pengosongan dengan bantuan pihak berwajib, atas biaya dan risiko Pihak Kedua.</p><p><strong>----------------------- Pasal 12.</strong></p><p>Selama masa kontrak belum berakhir, perjanjian ini tidak berakhir karena:</p><ol><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Meninggalnya salah satu pihak;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Pihak Pertama mengalihkan hak milik atas objek sewa kepada pihak lain;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Dalam hal salah satu pihak meninggal dunia, ahli waris/penggantinya wajib melanjutkan perjanjian sampai berakhir; pemilik baru tunduk pada seluruh ketentuan akta ini.</li></ol><p><strong>----------------------- Pasal 13.</strong></p><p>Untuk menjamin pembayaran listrik, air, telepon, keamanan, dan kewajiban lain bulan terakhir, Pihak Kedua menyerahkan uang jaminan sebesar Rp. 2.000.000,- (dua juta rupiah) pada saat penyerahan kunci, dengan kwitansi tersendiri. Kelebihan dikembalikan Pihak Pertama; kekurangan ditambah oleh Pihak Kedua.</p><p><strong>----------------------- Pasal 14.</strong></p><p>Hal-hal yang belum cukup diatur akan dibicarakan kemudian secara musyawarah untuk mufakat.</p><p><strong>----------------------- Pasal 15.</strong></p><p>Pajak-pajak yang mungkin ada terkait akta ini dibayar oleh Pihak Kedua untuk dan atas nama Pihak Pertama.</p><p><strong>----------------------- Pasal 16.</strong></p><p>Biaya-biaya yang berkaitan dengan akta ini dibayar dan menjadi tanggungan Pihak Pertama.</p><p><strong>----------------------- Pasal 17.</strong></p><p>Kedua belah pihak memilih domisili hukum yang sah di Kepaniteraan Pengadilan Negeri Bekasi.</p><p><strong>DEMIKIAN AKTA INI</strong></p><p>– Dibuat dan diresmikan di Bekasi pada hari dan tanggal sebagaimana awal akta ini, dengan dihadiri oleh:</p><ol><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Nyonya ........................................</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Nyonya ........................................</li></ol><p>Keduanya Karyawan Kantor Notaris, sebagai saksi-saksi.</p><p>– Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p><p><br></p><p>{{signatures_block}}</p>', NULL, 'pending', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761623665/enotaris/activities/1/drafts/draft_pdf_20251028035424.pdf', 'enotaris/activities/1/drafts/draft_pdf_20251028035424', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762399507/enotaris/activities/1/signed/signed_pdf_20251106032505.pdf', 'enotaris/activities/1/signed/signed_pdf_20251106032505', '2025-10-27 20:41:29', '2025-11-05 20:25:07'),
(2, '1103/OK/ACT-TCANTWRG/2025', 2, '<h2 class=\"ql-align-center\">PERJANJIAN SEWA MENYEWA</h2><p class=\"ql-align-center\">Nomor : {{reference_number}}</p><p>– Pada hari ini, {{today}}</p><p>– tanggal .............................................................</p><p>– Pukul .................................................................</p><p>– Berhadapan dengan saya, {{notaris_name}}, Notaris di {{schedule_place}}, dengan dihadiri oleh para saksi yang saya, Notaris, kenal dan akan disebutkan nama-namanya pada bahagian akhir akta ini:</p><p><strong>I. Tuan {{penghadap1_name}}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p><strong>II. Tuan {{penghadap2_name}}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>– menurut keterangannya dalam hal ini bertindak dalam jabatannya selaku Presiden Direktur dari Perseroan Terbatas PT. .........., berkedudukan di Jakarta yang anggaran dasarnya beserta perubahannya telah mendapat persetujuan dari Menteri Kehakiman dan Hak Asasi Manusia berturut-turut:</p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>selanjutnya disebut: <strong>Pihak Kedua</strong> atau <strong>Penyewa</strong>.</p><p>– Para penghadap telah saya, Notaris, kenal.</p><p>– Para penghadap menerangkan terlebih dahulu:</p><p>– bahwa Pihak Pertama adalah pemilik dari bangunan Rumah Toko (Ruko) yang hendak disewakan kepada Pihak Kedua yang akan disebutkan di bawah ini dan Pihak Kedua menerangkan menyewa dari Pihak Pertama berupa:</p><p>– 1 (satu) unit bangunan Rumah Toko (Ruko) berlantai 3 (tiga) berikut turutannya, lantai keramik, dinding tembok, atap dak, aliran listrik sebesar 2.200 Watt, dilengkapi air dari jet pump, berdiri di atas sebidang tanah Sertifikat HGB Nomor: ............ seluas ...... m² (....................................), penerbitan sertifikat tanggal ..........................., tercantum atas nama .................. yang telah diuraikan dalam Gambar Situasi tanggal ............ nomor ............; Sertifikat tanah diterbitkan oleh Kantor Pertanahan Kabupaten Bekasi, terletak di Provinsi Jawa Barat, Kabupaten Bekasi, Kecamatan Cibitung, Desa Ganda Mekar, setempat dikenal sebagai Mega Mall MM.2100 Blok B Nomor 8.</p><p>– Berdasarkan keterangan-keterangan tersebut di atas, kedua belah pihak sepakat membuat perjanjian sewa-menyewa dengan syarat-syarat dan ketentuan-ketentuan sebagai berikut:</p><p><strong>----------------------- Pasal 1.</strong></p><p>Perjanjian sewa-menyewa ini berlangsung untuk jangka waktu 2 (dua) tahun terhitung sejak tanggal ............ sampai dengan tanggal ............</p><p>– Penyerahan Ruko akan dilakukan dalam keadaan kosong/tidak dihuni pada tanggal .................. dengan penyerahan semua kunci-kuncinya.</p><p><strong>----------------------- Pasal 2.</strong></p><p>– Uang kontrak sewa disepakati sebesar Rp. ............ (....................................) untuk 2 (dua) tahun masa sewa.</p><p>– Jumlah uang sewa sebesar Rp. ............ (....................................) tersebut dibayar oleh Pihak Kedua kepada Pihak Pertama pada saat penandatanganan akta ini atau pada tanggal .................. dengan kwitansi tersendiri, dan akta ini berlaku sebagai tanda penerimaan yang sah.</p><p><strong>----------------------- Pasal 3.</strong></p><p>– Pihak Kedua hanya akan menggunakan yang disewakan dalam akta ini sebagai tempat kegiatan perkantoran/usaha.</p><p>– Jika diperlukan, Pihak Pertama memberikan surat rekomendasi/keterangan yang diperlukan Pihak Kedua sepanjang tidak melanggar hukum.</p><p>– Pihak Kedua wajib mentaati peraturan-peraturan pihak yang berwajib dan menjamin Pihak Pertama tidak mendapat teguran/tuntutan apapun karenanya.</p><p><strong>----------------------- Pasal 4.</strong></p><p>– Hanya dengan persetujuan tertulis Pihak Pertama, Pihak Kedua boleh mengadakan perubahan/penambahan pada bangunan; seluruh biaya dan tanggung jawab pada Pihak Kedua, dan pada akhir masa kontrak menjadi hak Pihak Pertama.</p><p>– Penyerahan nyata dari yang disewakan oleh Pihak Pertama kepada Pihak Kedua dilakukan pada tanggal .................. dengan penyerahan semua kunci-kunci.</p><p><strong>----------------------- Pasal 5.</strong></p><p>Pihak Pertama memberi izin kepada Pihak Kedua untuk pemasangan/penambahan antara lain:</p><ol><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Sekat-sekat pada ruangan;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Antena radio/CD;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Line telepon;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Air Conditioner (AC);</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Penambahan daya listrik;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Saluran fax;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Internet;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>TV Kabel;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Shower;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Penggantian W/C;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Katrol pengangkut barang lantai 1–3;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Peralatan keamanan;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Peralatan pendukung usaha (rak/mesin) tanpa merusak struktur bangunan.</li></ol><p>– Setelah masa kontrak berakhir, Pihak Kedua mengembalikan seperti keadaan semula dengan biaya Pihak Kedua.</p><p>– Pihak Kedua boleh mengganti kunci ruangan di dalam bangunan (kecuali pintu utama); pada akhir masa kontrak, kunci-kunci diserahkan ke Pihak Pertama.</p><p>– Pihak Pertama menjamin yang disewakan adalah miliknya dan bebas dari tuntutan pihak lain.</p><p>– Selama masa sewa, Pihak Pertama boleh memeriksa bangunan sewaktu-waktu.</p><p><strong>----------------------- Pasal 6.</strong></p><p>– Selama masa kontrak, pembayaran langganan listrik/air/telepon dan kewajiban lain terkait pemakaian dibayar Pihak Kedua hingga bulan terakhir dengan bukti pembayaran setiap bulan.</p><p>– Pihak Pertama membayar Pajak Bumi dan Bangunan (PBB) untuk objek sewa.</p><p><strong>----------------------- Pasal 7.</strong></p><p>– Pihak Kedua wajib memelihara yang disewa dengan baik; kerusakan karena kelalaian diperbaiki atas biaya Pihak Kedua.</p><p>– Apabila terjadi force majeure (kebakaran—kecuali kelalaian Pihak Kedua—sabotase, badai, banjir, gempa) sehingga objek musnah, para pihak dibebaskan dari tuntutan.</p><p><strong>----------------------- Pasal 8.</strong></p><p>– Pihak Pertama menjamin tidak ada tuntutan atau gangguan dari pihak lain atas yang disewa selama kontrak.</p><p><strong>----------------------- Pasal 9.</strong></p><p>Pihak Kedua, dengan persetujuan tertulis Pihak Pertama, boleh mengalihkan/memindahkan hak kontrak pada pihak lain, sebagian maupun seluruhnya, selama masa kontrak berlaku.</p><p><strong>----------------------- Pasal 10.</strong></p><p>Pihak Kedua wajib memberi pemberitahuan mengenai berakhir/akan diperpanjangnya kontrak kepada Pihak Pertama selambat-lambatnya 2 (dua) bulan sebelum berakhir.</p><p><strong>----------------------- Pasal 11.</strong></p><p>Pada saat berakhirnya kontrak dan tidak ada perpanjangan, Pihak Kedua menyerahkan kembali objek sewa dalam keadaan kosong, terpelihara baik, dengan semua kunci pada tanggal ..................</p><p>Apabila terlambat, Pihak Kedua dikenakan denda sebesar Rp. 27.500,- per hari selama 7 (tujuh) hari pertama; jika masih tidak diserahkan, Pihak Kedua memberi kuasa kepada Pihak Pertama (dengan hak substitusi) untuk melakukan pengosongan dengan bantuan pihak berwajib, atas biaya dan risiko Pihak Kedua.</p><p><strong>----------------------- Pasal 12.</strong></p><p>Selama masa kontrak belum berakhir, perjanjian ini tidak berakhir karena:</p><ol><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Meninggalnya salah satu pihak;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Pihak Pertama mengalihkan hak milik atas objek sewa kepada pihak lain;</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Dalam hal salah satu pihak meninggal dunia, ahli waris/penggantinya wajib melanjutkan perjanjian sampai berakhir; pemilik baru tunduk pada seluruh ketentuan akta ini.</li></ol><p><strong>----------------------- Pasal 13.</strong></p><p>Untuk menjamin pembayaran listrik, air, telepon, keamanan, dan kewajiban lain bulan terakhir, Pihak Kedua menyerahkan uang jaminan sebesar Rp. 2.000.000,- (dua juta rupiah) pada saat penyerahan kunci, dengan kwitansi tersendiri. Kelebihan dikembalikan Pihak Pertama; kekurangan ditambah oleh Pihak Kedua.</p><p><strong>----------------------- Pasal 14.</strong></p><p>Hal-hal yang belum cukup diatur akan dibicarakan kemudian secara musyawarah untuk mufakat.</p><p><strong>----------------------- Pasal 15.</strong></p><p>Pajak-pajak yang mungkin ada terkait akta ini dibayar oleh Pihak Kedua untuk dan atas nama Pihak Pertama.</p><p><strong>----------------------- Pasal 16.</strong></p><p>Biaya-biaya yang berkaitan dengan akta ini dibayar dan menjadi tanggungan Pihak Pertama.</p><p><strong>----------------------- Pasal 17.</strong></p><p>Kedua belah pihak memilih domisili hukum yang sah di Kepaniteraan Pengadilan Negeri Bekasi.</p><p><strong>DEMIKIAN AKTA INI</strong></p><p>– Dibuat dan diresmikan di Bekasi pada hari dan tanggal sebagaimana awal akta ini, dengan dihadiri oleh:</p><ol><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Nyonya ........................................</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Nyonya ........................................</li></ol><p>Keduanya Karyawan Kantor Notaris, sebagai saksi-saksi.</p><p>– Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p><p><br></p><p>{{signatures_block}}</p>', NULL, 'pending', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762133399/enotaris/activities/2/drafts/draft_pdf_20251103012957.pdf', 'enotaris/activities/2/drafts/draft_pdf_20251103012957', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762227883/enotaris/activities/2/signed/signed_pdf_20251104034430.pdf', 'enotaris/activities/2/signed/signed_pdf_20251104034430', '2025-11-02 18:25:05', '2025-11-03 20:44:43'),
(3, '1104/OK/ACT-7BL2JDFP/2025', 3, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2025-11-03 19:02:54', '2025-11-03 19:02:54'),
(4, '1106/OK/ACT-KU6CD88I/2025', 4, '<h2 class=\"ql-align-center\">PERJANJIAN SEWA MENYEWA</h2><p class=\"ql-align-center\">Nomor : {{reference_number}}</p><p class=\"ql-indent-2\">– Pada hari ini, {{today}}</p><p class=\"ql-indent-2\">– tanggal .............................................................</p><p class=\"ql-indent-2\">– Pukul .................................................................</p><p class=\"ql-indent-2\">– Berhadapan dengan saya, {{notaris_name}}, Notaris di {{schedule_place}}, dengan dihadiri oleh para saksi yang saya, Notaris, kenal dan akan disebutkan nama-namanya pada bahagian akhir akta ini:</p><p class=\"ql-indent-2\"><strong>I. Tuan {{penghadap1_name}}</strong></p><p class=\"ql-indent-2\">..............................................................</p><p class=\"ql-indent-2\">..............................................................</p><p class=\"ql-indent-2\">..............................................................</p><p class=\"ql-indent-2\"><strong>II. Tuan {{penghadap2_name}}</strong></p><p class=\"ql-indent-2\">..............................................................</p><p class=\"ql-indent-2\">..............................................................</p><p class=\"ql-indent-2\">..............................................................</p><p class=\"ql-indent-2\">– menurut keterangannya dalam hal ini bertindak dalam jabatannya selaku Presiden Direktur dari Perseroan Terbatas PT. .........., berkedudukan di Jakarta yang anggaran dasarnya beserta perubahannya telah mendapat persetujuan dari Menteri Kehakiman dan Hak Asasi Manusia berturut-turut:</p><p class=\"ql-indent-2\"><br></p><p class=\"ql-indent-3\">selanjutnya disebut: <strong>Pihak Kedua</strong> atau <strong>Penyewa</strong>.</p><p class=\"ql-indent-3\">– Para penghadap telah saya, Notaris, kenal.</p><p class=\"ql-indent-3\">– Para penghadap menerangkan terlebih dahulu:</p><p class=\"ql-indent-3\">– bahwa Pihak Pertama adalah pemilik dari bangunan Rumah Toko (Ruko) yang hendak disewakan kepada Pihak Kedua yang akan disebutkan di bawah ini dan Pihak Kedua menerangkan menyewa dari Pihak Pertama berupa:</p><p class=\"ql-indent-3\">– 1 (satu) unit bangunan Rumah Toko (Ruko) berlantai 3 (tiga) berikut turutannya, lantai keramik, dinding tembok, atap dak, aliran listrik sebesar 2.200 Watt, dilengkapi air dari jet pump, berdiri di atas sebidang tanah Sertifikat HGB Nomor: ............ seluas ...... m² (....................................), penerbitan sertifikat tanggal ..........................., tercantum atas nama .................. yang telah diuraikan dalam Gambar Situasi tanggal ............ nomor ............; Sertifikat tanah diterbitkan oleh Kantor Pertanahan Kabupaten Bekasi, terletak di Provinsi Jawa Barat, Kabupaten Bekasi, Kecamatan Cibitung, Desa Ganda Mekar, setempat dikenal sebagai Mega Mall MM.2100 Blok B Nomor 8.</p><p class=\"ql-indent-3\">– Berdasarkan keterangan-keterangan tersebut di atas, kedua belah pihak sepakat membuat perjanjian sewa-menyewa dengan syarat-syarat dan ketentuan-ketentuan sebagai berikut:</p><p class=\"ql-indent-3\"><strong>----------------------- Pasal 1.</strong></p><p class=\"ql-indent-3\">Perjanjian sewa-menyewa ini berlangsung untuk jangka waktu 2 (dua) tahun terhitung sejak tanggal ............ sampai dengan tanggal ............</p><p class=\"ql-indent-3\">– Penyerahan Ruko akan dilakukan dalam keadaan kosong/tidak dihuni pada tanggal .................. dengan penyerahan semua kunci-kuncinya.</p><p class=\"ql-indent-3\"><strong>----------------------- Pasal 2.</strong></p><p class=\"ql-indent-3\">– Uang kontrak sewa disepakati sebesar Rp. ............ (....................................) untuk 2 (dua) tahun masa sewa.</p><p class=\"ql-indent-3\">– Jumlah uang sewa sebesar Rp. ............ (....................................) tersebut dibayar oleh Pihak Kedua kepada Pihak Pertama pada saat penandatanganan akta ini atau pada tanggal .................. dengan kwitansi tersendiri, dan akta ini berlaku sebagai tanda penerimaan yang sah.</p><p class=\"ql-indent-3\"><strong>----------------------- Pasal 3.</strong></p><p class=\"ql-indent-3\">– Pihak Kedua hanya akan menggunakan yang disewakan dalam akta ini sebagai tempat kegiatan perkantoran/usaha.</p><p class=\"ql-indent-3\">– Jika diperlukan, Pihak Pertama memberikan surat rekomendasi/keterangan yang diperlukan Pihak Kedua sepanjang tidak melanggar hukum.</p><p class=\"ql-indent-3\">– Pihak Kedua wajib mentaati peraturan-peraturan pihak yang berwajib dan menjamin Pihak Pertama tidak mendapat teguran/tuntutan apapun karenanya.</p><p class=\"ql-indent-3\"><strong>----------------------- Pasal 4.</strong></p><p class=\"ql-indent-3\">– Hanya dengan persetujuan tertulis Pihak Pertama, Pihak Kedua boleh mengadakan perubahan/penambahan pada bangunan; seluruh biaya dan tanggung jawab pada Pihak Kedua, dan pada akhir masa kontrak menjadi hak Pihak Pertama.</p><p class=\"ql-indent-3\">– Penyerahan nyata dari yang disewakan oleh Pihak Pertama kepada Pihak Kedua dilakukan pada tanggal .................. dengan penyerahan semua kunci-kunci.</p><p class=\"ql-indent-3\"><strong>----------------------- Pasal 5.</strong></p><p class=\"ql-indent-3\">Pihak Pertama memberi izin kepada Pihak Kedua untuk pemasangan/penambahan antara lain:</p><ol><li data-list=\"ordered\" class=\"ql-indent-3\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Sekat-sekat pada ruangan;</li><li data-list=\"ordered\" class=\"ql-indent-3\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Antena radio/CD;</li><li data-list=\"ordered\" class=\"ql-indent-3\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Line telepon;</li><li data-list=\"ordered\" class=\"ql-indent-3\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Air Conditioner (AC);</li><li data-list=\"ordered\" class=\"ql-indent-3\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Penambahan daya listrik;</li><li data-list=\"ordered\" class=\"ql-indent-3\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Saluran fax;</li><li data-list=\"ordered\" class=\"ql-indent-3\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Internet;</li><li data-list=\"ordered\" class=\"ql-indent-3\"><span class=\"ql-ui\" contenteditable=\"false\"></span>TV Kabel;</li><li data-list=\"ordered\" class=\"ql-indent-3\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Shower;</li><li data-list=\"ordered\" class=\"ql-indent-3\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Penggantian W/C;</li><li data-list=\"ordered\" class=\"ql-indent-3\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Katrol pengangkut barang lantai 1–3;</li><li data-list=\"ordered\" class=\"ql-indent-3\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Peralatan keamanan;</li><li data-list=\"ordered\" class=\"ql-indent-3\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Peralatan pendukung usaha (rak/mesin) tanpa merusak struktur bangunan.</li></ol><p class=\"ql-indent-3\">– Setelah masa kontrak berakhir, Pihak Kedua mengembalikan seperti keadaan semula dengan biaya Pihak Kedua.</p><p class=\"ql-indent-3\">– Pihak Kedua boleh mengganti kunci ruangan di dalam bangunan (kecuali pintu utama); pada akhir masa kontrak, kunci-kunci diserahkan ke Pihak Pertama.</p><p class=\"ql-indent-3\">– Pihak Pertama menjamin yang disewakan adalah miliknya dan bebas dari tuntutan pihak lain.</p><p class=\"ql-indent-3\">– Selama masa sewa, Pihak Pertama boleh memeriksa bangunan sewaktu-waktu.</p><p class=\"ql-indent-3\"><br></p><p class=\"ql-indent-2\"><strong>----------------------- Pasal 6.</strong></p><p class=\"ql-indent-2\">– Selama masa kontrak, pembayaran langganan listrik/air/telepon dan kewajiban lain terkait pemakaian dibayar Pihak Kedua hingga bulan terakhir dengan bukti pembayaran setiap bulan.</p><p class=\"ql-indent-2\">– Pihak Pertama membayar Pajak Bumi dan Bangunan (PBB) untuk objek sewa.</p><p class=\"ql-indent-2\"><strong>----------------------- Pasal 7.</strong></p><p class=\"ql-indent-2\">– Pihak Kedua wajib memelihara yang disewa dengan baik; kerusakan karena kelalaian diperbaiki atas biaya Pihak Kedua.</p><p class=\"ql-indent-2\">– Apabila terjadi force majeure (kebakaran—kecuali kelalaian Pihak Kedua—sabotase, badai, banjir, gempa) sehingga objek musnah, para pihak dibebaskan dari tuntutan.</p><p class=\"ql-indent-2\"><strong>----------------------- Pasal 8.</strong></p><p class=\"ql-indent-2\">– Pihak Pertama menjamin tidak ada tuntutan atau gangguan dari pihak lain atas yang disewa selama kontrak.</p><p class=\"ql-indent-2\"><strong>----------------------- Pasal 9.</strong></p><p class=\"ql-indent-2\">Pihak Kedua, dengan persetujuan tertulis Pihak Pertama, boleh mengalihkan/memindahkan hak kontrak pada pihak lain, sebagian maupun seluruhnya, selama masa kontrak berlaku.</p><p class=\"ql-indent-2\"><strong>----------------------- Pasal 10.</strong></p><p class=\"ql-indent-2\">Pihak Kedua wajib memberi pemberitahuan mengenai berakhir/akan diperpanjangnya kontrak kepada Pihak Pertama selambat-lambatnya 2 (dua) bulan sebelum berakhir.</p><p class=\"ql-indent-2\"><strong>----------------------- Pasal 11.</strong></p><p class=\"ql-indent-2\">Pada saat berakhirnya kontrak dan tidak ada perpanjangan, Pihak Kedua menyerahkan kembali objek sewa dalam keadaan kosong, terpelihara baik, dengan semua kunci pada tanggal ..................</p><p class=\"ql-indent-2\">Apabila terlambat, Pihak Kedua dikenakan denda sebesar Rp. 27.500,- per hari selama 7 (tujuh) hari pertama; jika masih tidak diserahkan, Pihak Kedua memberi kuasa kepada Pihak Pertama (dengan hak substitusi) untuk melakukan pengosongan dengan bantuan pihak berwajib, atas biaya dan risiko Pihak Kedua.</p><p class=\"ql-indent-2\"><strong>----------------------- Pasal 12.</strong></p><p class=\"ql-indent-2\">Selama masa kontrak belum berakhir, perjanjian ini tidak berakhir karena:</p><ol><li data-list=\"ordered\" class=\"ql-indent-2\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Meninggalnya salah satu pihak;</li><li data-list=\"ordered\" class=\"ql-indent-2\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Pihak Pertama mengalihkan hak milik atas objek sewa kepada pihak lain;</li><li data-list=\"ordered\" class=\"ql-indent-2\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Dalam hal salah satu pihak meninggal dunia, ahli waris/penggantinya wajib melanjutkan perjanjian sampai berakhir; pemilik baru tunduk pada seluruh ketentuan akta ini.</li></ol><p class=\"ql-indent-2\"><strong>----------------------- Pasal 13.</strong></p><p class=\"ql-indent-2\">Untuk menjamin pembayaran listrik, air, telepon, keamanan, dan kewajiban lain bulan terakhir, Pihak Kedua menyerahkan uang jaminan sebesar Rp. 2.000.000,- (dua juta rupiah) pada saat penyerahan kunci, dengan kwitansi tersendiri. Kelebihan dikembalikan Pihak Pertama; kekurangan ditambah oleh Pihak Kedua.</p><p class=\"ql-indent-2\"><strong>----------------------- Pasal 14.</strong></p><p class=\"ql-indent-2\">Hal-hal yang belum cukup diatur akan dibicarakan kemudian secara musyawarah untuk mufakat.</p><p class=\"ql-indent-2\"><strong>----------------------- Pasal 15.</strong></p><p class=\"ql-indent-2\">Pajak-pajak yang mungkin ada terkait akta ini dibayar oleh Pihak Kedua untuk dan atas nama Pihak Pertama.</p><p class=\"ql-indent-2\"><strong>----------------------- Pasal 16.</strong></p><p class=\"ql-indent-2\">Biaya-biaya yang berkaitan dengan akta ini dibayar dan menjadi tanggungan Pihak Pertama.</p><p class=\"ql-indent-2\"><strong>----------------------- Pasal 17.</strong></p><p class=\"ql-indent-2\">Kedua belah pihak memilih domisili hukum yang sah di Kepaniteraan Pengadilan Negeri Bekasi.</p><p class=\"ql-indent-2\"><strong>DEMIKIAN AKTA INI</strong></p><p class=\"ql-indent-2\">– Dibuat dan diresmikan di Bekasi pada hari dan tanggal sebagaimana awal akta ini, dengan dihadiri oleh:</p><ol><li data-list=\"ordered\" class=\"ql-indent-2\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Nyonya ........................................</li><li data-list=\"ordered\" class=\"ql-indent-2\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Nyonya ........................................</li></ol><p class=\"ql-indent-2\">Keduanya Karyawan Kantor Notaris, sebagai saksi-saksi.</p><p class=\"ql-indent-2\">– Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p>', NULL, 'pending', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762493227/enotaris/activities/4/drafts/draft_pdf_20251107052707.pdf', 'enotaris/activities/4/drafts/draft_pdf_20251107052707', NULL, NULL, '2025-11-05 20:47:00', '2025-11-09 21:35:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `identities`
--

CREATE TABLE `identities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `ktp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_ktp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_ktp_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_kk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_kk_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `npwp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_npwp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_npwp_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ktp_notaris` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_ktp_notaris` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_ktp_notaris_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_sign` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_sign_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_initial` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_initial_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `file_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `identities`
--

INSERT INTO `identities` (`id`, `user_id`, `ktp`, `file_ktp`, `file_ktp_path`, `file_kk`, `file_kk_path`, `npwp`, `file_npwp`, `file_npwp_path`, `ktp_notaris`, `file_ktp_notaris`, `file_ktp_notaris_path`, `file_sign`, `file_sign_path`, `file_initial`, `file_initial_path`, `created_at`, `updated_at`, `file_photo`, `file_photo_path`) VALUES
(1, 1, '5085532774292676', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138252/enotaris/users/3/identity/ktp/ktp_1762138246_bExv1Axz.jpg', 'seed/users/1/identity/ktp.jpg', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138255/enotaris/users/3/identity/kk/kk_1762138253_N5TPlJTR.jpg', 'seed/users/1/identity/kk.jpg', '258333056012237', 'https://via.placeholder.com/800x500.png?text=NPWP%20User%201', 'seed/users/1/identity/npwp.jpg', NULL, 'https://via.placeholder.com/800x500.png?text=KTP%20NOTARIS%20User%201', 'seed/users/1/identity/ktp_notaris.jpg', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761623495/enotaris/users/2/identity/sign/sign_1761623492_fy7OdBy5.png', 'enotaris/users/2/identity/sign/sign_1761623492_fy7OdBy5', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761623498/enotaris/users/2/identity/initial/initial_1761623497_sqZSieSh.png', 'seed/users/1/identity/initial.png', '2025-10-27 19:26:07', '2025-10-27 19:26:07', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138260/enotaris/users/3/identity/photo/photo_1762138257_2L0TPUEJ.png', 'seed/users/1/identity/photo.jpg'),
(2, 2, '5987880316139526', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138252/enotaris/users/3/identity/ktp/ktp_1762138246_bExv1Axz.jpg', 'seed/users/2/identity/ktp.jpg', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138255/enotaris/users/3/identity/kk/kk_1762138253_N5TPlJTR.jpg', 'seed/users/2/identity/kk.jpg', '595466855980791', 'https://via.placeholder.com/800x500.png?text=NPWP%20User%202', 'seed/users/2/identity/npwp.jpg', '0297082925122017', 'https://via.placeholder.com/800x500.png?text=KTP%20NOTARIS%20User%202', 'seed/users/2/identity/ktp_notaris.jpg', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761623495/enotaris/users/2/identity/sign/sign_1761623492_fy7OdBy5.png', 'enotaris/users/2/identity/sign/sign_1761623492_fy7OdBy5', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761623498/enotaris/users/2/identity/initial/initial_1761623497_sqZSieSh.png', 'enotaris/users/2/identity/initial/initial_1761623497_sqZSieSh', '2025-10-27 19:26:07', '2025-10-27 20:51:39', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138260/enotaris/users/3/identity/photo/photo_1762138257_2L0TPUEJ.png', 'seed/users/2/identity/photo.jpg'),
(3, 3, '9502358875183744', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138252/enotaris/users/3/identity/ktp/ktp_1762138246_bExv1Axz.jpg', 'enotaris/users/3/identity/ktp/ktp_1762138246_bExv1Axz', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138255/enotaris/users/3/identity/kk/kk_1762138253_N5TPlJTR.jpg', 'enotaris/users/3/identity/kk/kk_1762138253_N5TPlJTR', '393746329622730', 'https://via.placeholder.com/800x500.png?text=NPWP%20User%203', 'seed/users/3/identity/npwp.jpg', NULL, 'https://via.placeholder.com/800x500.png?text=KTP%20NOTARIS%20User%203', 'seed/users/3/identity/ktp_notaris.jpg', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761623495/enotaris/users/2/identity/sign/sign_1761623492_fy7OdBy5.png', 'enotaris/users/2/identity/sign/sign_1761623492_fy7OdBy5', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761623498/enotaris/users/2/identity/initial/initial_1761623497_sqZSieSh.png', 'seed/users/3/identity/initial.png', '2025-10-27 19:26:07', '2025-11-02 19:51:01', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138260/enotaris/users/3/identity/photo/photo_1762138257_2L0TPUEJ.png', 'enotaris/users/3/identity/photo/photo_1762138257_2L0TPUEJ'),
(4, 4, '7078070554837549', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138252/enotaris/users/3/identity/ktp/ktp_1762138246_bExv1Axz.jpg', 'seed/users/4/identity/ktp.jpg', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138255/enotaris/users/3/identity/kk/kk_1762138253_N5TPlJTR.jpg', 'seed/users/4/identity/kk.jpg', '675736073062316', 'https://via.placeholder.com/800x500.png?text=NPWP%20User%204', 'seed/users/4/identity/npwp.jpg', NULL, 'https://via.placeholder.com/800x500.png?text=KTP%20NOTARIS%20User%204', 'seed/users/4/identity/ktp_notaris.jpg', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761623495/enotaris/users/2/identity/sign/sign_1761623492_fy7OdBy5.png', 'enotaris/users/2/identity/sign/sign_1761623492_fy7OdBy5', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761623498/enotaris/users/2/identity/initial/initial_1761623497_sqZSieSh.png', 'seed/users/4/identity/initial.png', '2025-10-27 19:26:08', '2025-10-27 19:26:08', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138260/enotaris/users/3/identity/photo/photo_1762138257_2L0TPUEJ.png', 'seed/users/4/identity/photo.jpg'),
(5, 5, '2028361282369723', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138252/enotaris/users/3/identity/ktp/ktp_1762138246_bExv1Axz.jpg', 'seed/users/5/identity/ktp.jpg', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138255/enotaris/users/3/identity/kk/kk_1762138253_N5TPlJTR.jpg', 'seed/users/5/identity/kk.jpg', '676183350713494', 'https://via.placeholder.com/800x500.png?text=NPWP%20User%205', 'seed/users/5/identity/npwp.jpg', NULL, 'https://via.placeholder.com/800x500.png?text=KTP%20NOTARIS%20User%205', 'seed/users/5/identity/ktp_notaris.jpg', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761623495/enotaris/users/2/identity/sign/sign_1761623492_fy7OdBy5.png', 'enotaris/users/2/identity/sign/sign_1761623492_fy7OdBy5', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761623498/enotaris/users/2/identity/initial/initial_1761623497_sqZSieSh.png', 'seed/users/5/identity/initial.png', '2025-10-27 19:26:08', '2025-10-27 19:26:08', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138260/enotaris/users/3/identity/photo/photo_1762138257_2L0TPUEJ.png', 'seed/users/5/identity/photo.jpg'),
(6, 6, '4403265102701421', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138252/enotaris/users/3/identity/ktp/ktp_1762138246_bExv1Axz.jpg', 'seed/users/6/identity/ktp.jpg', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138255/enotaris/users/3/identity/kk/kk_1762138253_N5TPlJTR.jpg', 'seed/users/6/identity/kk.jpg', '774942343129924', 'https://via.placeholder.com/800x500.png?text=NPWP%20User%206', 'seed/users/6/identity/npwp.jpg', NULL, 'https://via.placeholder.com/800x500.png?text=KTP%20NOTARIS%20User%206', 'seed/users/6/identity/ktp_notaris.jpg', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761623495/enotaris/users/2/identity/sign/sign_1761623492_fy7OdBy5.png', 'enotaris/users/2/identity/sign/sign_1761623492_fy7OdBy5', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761623498/enotaris/users/2/identity/initial/initial_1761623497_sqZSieSh.png', 'seed/users/6/identity/initial.png', '2025-10-27 19:26:09', '2025-10-27 19:26:09', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138260/enotaris/users/3/identity/photo/photo_1762138257_2L0TPUEJ.png', 'seed/users/6/identity/photo.jpg'),
(7, 7, '1234567891111111', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761620013/enotaris/users/7/identity/ktp/ktp_1761619996_HHsSGbsG.png', 'enotaris/users/7/identity/ktp/ktp_1761619996_HHsSGbsG', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761620016/enotaris/users/7/identity/kk/kk_1761620013_59YT4UN5.jpg', 'enotaris/users/7/identity/kk/kk_1761620013_59YT4UN5', '1234567891111111', NULL, NULL, NULL, NULL, NULL, 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761620018/enotaris/users/7/identity/sign/sign_1761620015_fq87gznn.png', 'enotaris/users/7/identity/sign/sign_1761620015_fq87gznn', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1761620021/enotaris/users/7/identity/initial/initial_1761620018_v514NBvj.png', 'enotaris/users/7/identity/initial/initial_1761620018_v514NBvj', '2025-10-27 19:53:40', '2025-10-27 19:53:40', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1762138260/enotaris/users/3/identity/photo/photo_1762138257_2L0TPUEJ.png', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `main_value_deeds`
--

CREATE TABLE `main_value_deeds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deed_id` bigint(20) UNSIGNED NOT NULL,
  `main_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2024_06_15_111154_create_products_table', 1),
(6, '2024_06_16_084019_create_roles_table', 1),
(7, '2024_06_16_113049_create_role_id_to_users_table', 1),
(8, '2024_09_06_025833_add_image_to_products_table', 1),
(9, '2025_08_20_082508_add_verify_columns_to_users_table', 1),
(10, '2025_08_20_125528_add_columns_to_users_table', 1),
(11, '2025_08_20_125702_create_identities_table', 1),
(12, '2025_08_20_125733_create_deeds_table', 1),
(13, '2025_08_20_125838_create_activity_table', 1),
(14, '2025_08_20_125839_create_requirements_table', 1),
(15, '2025_08_20_125912_create_document_requirements_table', 1),
(16, '2025_08_20_125938_create_draft_deeds_table', 1),
(17, '2025_08_20_130003_create_schedules_table', 1),
(18, '2025_08_20_130028_create_main_value_deeds_table', 1),
(19, '2025_08_21_005819_add_avatar_to_users_table', 1),
(20, '2025_08_21_025645_add_verification_notes_to_users_table', 1),
(21, '2025_08_22_033201_add_requirement_id_to_document_requirements_table', 1),
(22, '2025_08_22_034928_add_requirement_name_to_document_requirements_table', 1),
(23, '2025_08_22_073926_add_name_to_main_value_deeds_table', 1),
(24, '2025_08_24_073800_add_file_photo_to_identities_table', 1),
(25, '2025_08_26_023413_add_name_to_table_activity_table', 1),
(26, '2025_08_26_042004_add_location_to_table_schedule_table', 1),
(27, '2025_08_29_062708_create_tracks_table', 1),
(28, '2025_08_29_062919_create_client_activity_table', 1),
(29, '2025_08_29_063358_add_track_id_to_activities_table', 1),
(30, '2025_08_29_064815_create_client_activities_table', 1),
(31, '2025_09_02_035059_add_order_to_client_activity_table', 1),
(32, '2025_09_03_032200_add_todo_status_to_tracks', 1),
(33, '2025_09_08_075854_add_city_province_postal_code_to_users_table', 1),
(34, '2025_09_09_041409_alter_custom_value_to_longtext_on_draft_deeds_table', 1),
(35, '2025_09_09_043529_alter_nullable_cols_on_draft_deeds_table', 1),
(36, '2025_09_10_044720_create_templates_table', 1),
(37, '2025_09_10_093452_add_name_to_deed_templates_table', 1),
(38, '2025_09_16_020248_create_client_drafts_table', 1),
(39, '2025_09_22_012804_add_ttd_columns_to_draft_deeds', 1),
(40, '2025_09_22_012810_create_signatures_table', 1),
(41, '2025_09_23_010905_add_reference_number_to_draft_deeds_table', 1),
(42, '2025_09_29_014309_create_category_blogs_table', 1),
(43, '2025_09_29_014317_create_blogs_table', 1),
(44, '2025_09_29_014323_create_blog_category_table', 1),
(45, '2025_09_30_014820_add_user_id_to_templates_table', 1),
(46, '2025_09_30_030749_add_file_columns_to_templates_table', 1),
(47, '2025_09_30_044125_create_partners_table', 1),
(48, '2025_10_01_014201_create_settings_table', 1),
(49, '2025_10_03_033359_add_google_id_to_users_table', 1),
(50, '2025_10_04_095010_add_expired_at_to_password_reset_tokens_table', 1),
(51, '2025_10_08_014336_add_description_to_templates_table', 1),
(52, '2025_10_08_015227_add_logo_to_templates_table', 1),
(55, '2025_11_10_013636_create_deed_requirement_templates_table', 2),
(56, '2025_11_10_013644_add_deed_requirement_template_id_to_document_requirements_table', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `partners`
--

CREATE TABLE `partners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `partners`
--

INSERT INTO `partners` (`id`, `name`, `link`, `image`, `image_path`, `created_at`, `updated_at`) VALUES
(2, 'Github', 'https://github.com', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1759885456/enotaris/partners/partner_20251008010413.png', 'enotaris/partners/partner_20251008010413', '2025-10-07 11:04:15', '2025-10-07 11:04:15'),
(3, 'Google', 'https://google.com', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1759885484/enotaris/partners/partner_20251008010441.png', 'enotaris/partners/partner_20251008010441', '2025-10-07 11:04:43', '2025-10-07 11:04:43'),
(4, 'Docker', 'https://docker.com', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1759885519/enotaris/partners/partner_20251008010515.png', 'enotaris/partners/partner_20251008010515', '2025-10-07 11:05:18', '2025-10-07 11:05:18'),
(5, 'Petrokimia Gresik', 'https://petrokimiagresik.com', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1759886411/enotaris/partners/partner_20251008012009.png', 'enotaris/partners/partner_20251008012009', '2025-10-07 11:20:11', '2025-10-07 11:20:11'),
(6, 'Microsoft', 'https://microsoft.com', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1759886438/enotaris/partners/partner_20251008012036.png', 'enotaris/partners/partner_20251008012036', '2025-10-07 11:20:39', '2025-10-07 11:20:39'),
(7, 'Vercel', 'https://vercel.com', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1759886538/enotaris/partners/partner_20251008012215.png', 'enotaris/partners/partner_20251008012215', '2025-10-07 11:22:19', '2025-10-07 11:22:19'),
(8, 'Cloudinary', 'https://cloudinary.com', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1759886571/enotaris/partners/partner_20251008012246.png', 'enotaris/partners/partner_20251008012246', '2025-10-07 11:22:51', '2025-10-07 11:22:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `expired_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(14, 'App\\Models\\User', 2, 'user-token', 'db4ec37363b954959fc03139faba3cc29ee7e8c12baa89ac52763091e6692bf4', '[\"notaris\"]', '2025-10-27 21:25:25', '2025-10-28 20:46:18', '2025-10-27 20:46:18', '2025-10-27 21:25:25'),
(15, 'App\\Models\\User', 2, 'user-token', 'b933bf636943ca6f923930b86372599143bb1a1c61f5fb944e3aad3d3a81fd57', '[\"notaris\"]', '2025-10-29 19:35:49', '2025-10-30 18:49:26', '2025-10-29 18:49:26', '2025-10-29 19:35:49'),
(20, 'App\\Models\\User', 3, 'auth-token', '526006a263bd5bd80a1642861664a4755dd542bee3dbe47c08c58cf93864786f', '[\"*\"]', '2025-10-29 20:54:27', NULL, '2025-10-29 20:54:24', '2025-10-29 20:54:27'),
(21, 'App\\Models\\User', 1, 'user-token', '4566b6af5aee8524473b7a279fdfbef692c87a87a184f1037fa09d3141d16fd5', '[\"admin\"]', '2025-10-29 21:53:47', '2025-10-30 21:52:13', '2025-10-29 21:52:13', '2025-10-29 21:53:47'),
(22, 'App\\Models\\User', 1, 'user-token', '532f5fb1b8329ab61946074ae1436d7063a5e2622de6a2abb3f31dbef8ce0260', '[\"admin\"]', '2025-10-29 21:56:12', '2025-10-30 21:56:09', '2025-10-29 21:56:09', '2025-10-29 21:56:12'),
(23, 'App\\Models\\User', 2, 'user-token', '4f8a1d5824333c261012b4b581b7ca5d574bb51ff83ce3b50009fd8985e6a79d', '[\"notaris\"]', '2025-11-01 19:03:47', '2025-11-02 19:01:52', '2025-11-01 19:01:53', '2025-11-01 19:03:47'),
(31, 'App\\Models\\User', 2, 'user-token', '7651ec41dabd4e9b40ba59603e33226edaf4c115fa43aab73f6801fd9cb0749f', '[\"notaris\"]', '2025-11-02 18:37:39', '2025-11-03 18:34:47', '2025-11-02 18:34:47', '2025-11-02 18:37:39'),
(33, 'App\\Models\\User', 1, 'user-token', 'dba7405f2aa23c1759b2da07fb360d7063751f1e8746004e170e926996623873', '[\"admin\"]', '2025-11-02 19:02:06', '2025-11-03 19:02:04', '2025-11-02 19:02:04', '2025-11-02 19:02:06'),
(35, 'App\\Models\\User', 2, 'user-token', '71fbf713fe8749b3c70c78098348abd9565dfca51c082e5cdbc596b94b42e93d', '[\"notaris\"]', '2025-11-02 19:03:15', '2025-11-03 19:03:07', '2025-11-02 19:03:07', '2025-11-02 19:03:15'),
(36, 'App\\Models\\User', 3, 'auth-token', '07fe56e52774742d8bf163711593773f2d16ecb0f46e4daa729414e9335ba861', '[\"*\"]', '2025-11-02 19:50:34', NULL, '2025-11-02 19:42:54', '2025-11-02 19:50:34'),
(37, 'App\\Models\\User', 1, 'user-token', '0ca4fac28ae9e99cb79aa2007446f3169d571167a05f856370dd39c745f72f35', '[\"admin\"]', '2025-11-02 20:22:53', '2025-11-03 20:22:50', '2025-11-02 20:22:50', '2025-11-02 20:22:53'),
(38, 'App\\Models\\User', 2, 'user-token', 'edc6d3fe3e6f5646b70f58651d84cb8ea24913b8f33206026a0d5b8da1fa9d9c', '[\"notaris\"]', '2025-11-02 21:40:27', '2025-11-03 21:14:45', '2025-11-02 21:14:45', '2025-11-02 21:40:27'),
(39, 'App\\Models\\User', 1, 'user-token', '9fed815b0aa8812e413d5cfbea24244de6686597e8a886e5c4bd00fca1cb2d61', '[\"admin\"]', '2025-11-02 21:31:19', '2025-11-03 21:29:33', '2025-11-02 21:29:33', '2025-11-02 21:31:19'),
(41, 'App\\Models\\User', 1, 'user-token', 'd78a91b2246667c00f8b0978e4ee9f7c368412914d2138c5a11c7a9fae32a2a2', '[\"admin\"]', '2025-11-03 07:17:41', '2025-11-04 02:56:41', '2025-11-03 02:56:41', '2025-11-03 07:17:41'),
(42, 'App\\Models\\User', 1, 'user-token', '4fd92e861d911a9f2145664929d29a98ce6450bd99a3cabe619e2f851e935206', '[\"admin\"]', '2025-11-03 12:42:16', '2025-11-04 12:05:19', '2025-11-03 12:05:19', '2025-11-03 12:42:16'),
(45, 'App\\Models\\User', 2, 'user-token', '7afa9ea2fe63422cfcd2225fdfaacebc254b788dd97b2c2fd91267b2dd18fa30', '[\"notaris\"]', '2025-11-03 22:18:10', '2025-11-04 19:47:57', '2025-11-03 19:47:57', '2025-11-03 22:18:10'),
(47, 'App\\Models\\User', 7, 'user-token', '9a4bce5e9a36c19a3b05d4b3e401724f5611f63dfe415cbdd3f6662b7339238e', '[\"penghadap\"]', '2025-11-05 21:38:13', '2025-11-06 20:48:11', '2025-11-05 20:48:11', '2025-11-05 21:38:13'),
(49, 'App\\Models\\User', 2, 'user-token', 'bcc83e077f40d72ba83f6d1fbd2c6a46d0add9914052008b7b7e02c3ab087a4b', '[\"notaris\"]', '2025-11-05 21:38:11', '2025-11-06 20:49:27', '2025-11-05 20:49:27', '2025-11-05 21:38:11'),
(54, 'App\\Models\\User', 1, 'user-token', '1a1e560e75f2b59240957269866a8e9e7773152fcf69812f45eb82278a71bfe6', '[\"admin\"]', '2025-11-06 00:43:39', '2025-11-07 00:24:06', '2025-11-06 00:24:06', '2025-11-06 00:43:39'),
(58, 'App\\Models\\User', 2, 'user-token', '02d52294011428c15d6c3502ebcea4a820b69276095853e7a37e45984ef964a8', '[\"notaris\"]', '2025-11-07 00:23:24', '2025-11-07 22:29:47', '2025-11-06 22:29:47', '2025-11-07 00:23:24'),
(59, 'App\\Models\\User', 2, 'user-token', 'fdd597aba651ab799adbec73977deccd986b493c49122cddd38c53bdc6348274', '[\"notaris\"]', '2025-11-09 22:37:48', '2025-11-10 19:16:06', '2025-11-09 19:16:06', '2025-11-09 22:37:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Jacques Hayes', '44481', 'Hic et ipsam eum.', NULL, '2025-10-27 19:25:53', '2025-10-27 19:25:53'),
(2, 'Kristin Marks', '37683', 'Odio dolorum asperiores quibusdam enim magni.', NULL, '2025-10-27 19:25:53', '2025-10-27 19:25:53'),
(3, 'Prof. Alexandrea Ferry', '56316', 'Dolorem animi id vitae suscipit illo qui.', NULL, '2025-10-27 19:25:53', '2025-10-27 19:25:53'),
(4, 'Prof. Urban Collier', '63514', 'Autem reprehenderit aperiam veritatis aut.', NULL, '2025-10-27 19:25:53', '2025-10-27 19:25:53'),
(5, 'Lavonne McKenzie', '12340', 'Exercitationem voluptates aperiam neque odit sit dolor.', NULL, '2025-10-27 19:25:53', '2025-10-27 19:25:53'),
(6, 'Prof. Rocky Effertz Jr.', '25336', 'Beatae ex iste quam sunt voluptas.', NULL, '2025-10-27 19:25:53', '2025-10-27 19:25:53'),
(7, 'Prof. Bernadette Lowe PhD', '89754', 'Temporibus eveniet dolorem perspiciatis sunt.', NULL, '2025-10-27 19:25:53', '2025-10-27 19:25:53'),
(8, 'Prof. Scot Schneider DDS', '52605', 'Modi eos natus recusandae qui.', NULL, '2025-10-27 19:25:53', '2025-10-27 19:25:53'),
(9, 'Coralie Kovacek', '89217', 'Accusantium sunt nesciunt sint libero.', NULL, '2025-10-27 19:25:53', '2025-10-27 19:25:53'),
(10, 'Neha Johnson', '27160', 'Ut et consequuntur sequi ipsum.', NULL, '2025-10-27 19:25:53', '2025-10-27 19:25:53'),
(11, 'Mrs. Isabel Ondricka III', '58728', 'Occaecati exercitationem nobis dolore.', NULL, '2025-10-27 19:25:53', '2025-10-27 19:25:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `requirements`
--

CREATE TABLE `requirements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_file` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `requirements`
--

INSERT INTO `requirements` (`id`, `activity_id`, `name`, `is_file`, `created_at`, `updated_at`) VALUES
(1, 2, 'NPWP', 0, '2025-11-02 18:28:03', '2025-11-02 18:28:03'),
(2, 2, 'Surat Kuasa', 1, '2025-11-02 18:28:24', '2025-11-02 18:28:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'admin', '2025-10-27 19:25:53', '2025-10-27 19:25:53'),
(2, 'penghadap', '2025-10-27 19:25:53', '2025-10-27 19:25:53'),
(3, 'notaris', '2025-10-27 19:25:53', '2025-10-27 19:25:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `schedules`
--

INSERT INTO `schedules` (`id`, `activity_id`, `date`, `time`, `notes`, `created_at`, `updated_at`, `location`) VALUES
(1, 1, '2025-10-29', '17:46:00', 'Dokumen yang dipersiapkan', '2025-10-27 20:46:56', '2025-10-27 20:46:56', 'Kantor Notaris'),
(2, 2, '2025-11-03', '15:35:00', 'Catatan khusus', '2025-11-02 18:35:25', '2025-11-02 18:35:25', 'Kantor Notaris'),
(3, 4, '2025-11-07', '19:30:00', 'Catatan Tambahan', '2025-11-06 22:30:29', '2025-11-06 22:30:29', 'Kantor Notaris');

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

-- --------------------------------------------------------

--
-- Struktur dari tabel `signatures`
--

CREATE TABLE `signatures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `draft_deed_id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `page` int(10) UNSIGNED NOT NULL,
  `kind` enum('image','draw') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'image',
  `x_ratio` decimal(8,5) NOT NULL,
  `y_ratio` decimal(8,5) NOT NULL,
  `w_ratio` decimal(8,5) NOT NULL,
  `h_ratio` decimal(8,5) NOT NULL,
  `image_data_url` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `signatures`
--

INSERT INTO `signatures` (`id`, `draft_deed_id`, `activity_id`, `user_id`, `page`, `kind`, `x_ratio`, `y_ratio`, `w_ratio`, `h_ratio`, `image_data_url`, `source_image_url`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, 1, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-10-27 21:11:42', '2025-10-27 21:11:42'),
(2, 1, 1, 2, 2, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-10-27 21:11:42', '2025-10-27 21:11:42'),
(3, 1, 1, 2, 3, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-10-27 21:11:42', '2025-10-27 21:11:42'),
(4, 1, 1, 2, 4, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-10-27 21:11:42', '2025-10-27 21:11:42'),
(5, 1, 1, 2, 5, 'image', '0.77381', '0.00926', '0.21429', '0.06270', NULL, NULL, '2025-10-27 21:11:43', '2025-10-27 21:11:43'),
(6, 1, 1, 2, 1, 'image', '0.38690', '0.00000', '0.21429', '0.06270', NULL, NULL, '2025-10-27 21:17:14', '2025-10-27 21:17:14'),
(7, 1, 1, 2, 2, 'image', '0.38690', '0.00000', '0.21429', '0.06270', NULL, NULL, '2025-10-27 21:17:14', '2025-10-27 21:17:14'),
(8, 1, 1, 2, 3, 'image', '0.38690', '0.00000', '0.21429', '0.06270', NULL, NULL, '2025-10-27 21:17:14', '2025-10-27 21:17:14'),
(9, 1, 1, 2, 4, 'image', '0.38690', '0.00000', '0.21429', '0.06270', NULL, NULL, '2025-10-27 21:17:14', '2025-10-27 21:17:14'),
(10, 1, 1, 2, 5, 'image', '0.38690', '0.00000', '0.21429', '0.06270', NULL, NULL, '2025-10-27 21:17:14', '2025-10-27 21:17:14'),
(11, 1, 1, 2, 5, 'image', '0.22381', '0.59524', '0.33333', '0.09753', NULL, NULL, '2025-10-27 21:17:40', '2025-10-27 21:17:40'),
(12, 1, 1, 2, 1, 'draw', '0.49160', '0.93257', '0.05488', '0.03881', NULL, NULL, '2025-10-27 21:18:39', '2025-10-27 21:18:39'),
(13, 1, 1, 2, 2, 'draw', '0.49160', '0.93257', '0.05488', '0.03881', NULL, NULL, '2025-10-27 21:18:39', '2025-10-27 21:18:39'),
(14, 1, 1, 2, 3, 'draw', '0.49160', '0.93257', '0.05488', '0.03881', NULL, NULL, '2025-10-27 21:18:39', '2025-10-27 21:18:39'),
(15, 1, 1, 2, 4, 'draw', '0.49160', '0.93257', '0.05488', '0.03881', NULL, NULL, '2025-10-27 21:18:39', '2025-10-27 21:18:39'),
(16, 1, 1, 2, 5, 'draw', '0.49160', '0.93257', '0.05488', '0.03881', NULL, NULL, '2025-10-27 21:18:39', '2025-10-27 21:18:39'),
(17, 1, 1, 2, 1, 'draw', '0.44994', '0.88888', '0.24643', '0.10017', NULL, NULL, '2025-10-27 21:19:08', '2025-10-27 21:19:08'),
(18, 1, 1, 2, 2, 'draw', '0.44994', '0.88888', '0.24643', '0.10017', NULL, NULL, '2025-10-27 21:19:08', '2025-10-27 21:19:08'),
(19, 1, 1, 2, 3, 'draw', '0.44994', '0.88888', '0.24643', '0.10017', NULL, NULL, '2025-10-27 21:19:08', '2025-10-27 21:19:08'),
(20, 1, 1, 2, 4, 'draw', '0.44994', '0.88888', '0.24643', '0.10017', NULL, NULL, '2025-10-27 21:19:08', '2025-10-27 21:19:08'),
(21, 1, 1, 2, 5, 'draw', '0.44994', '0.88888', '0.24643', '0.10017', NULL, NULL, '2025-10-27 21:19:08', '2025-10-27 21:19:08'),
(22, 1, 1, 2, 1, 'draw', '0.71780', '0.54713', '0.23452', '0.13721', NULL, NULL, '2025-10-27 21:19:40', '2025-10-27 21:19:40'),
(23, 1, 1, 2, 2, 'draw', '0.71780', '0.54713', '0.23452', '0.13721', NULL, NULL, '2025-10-27 21:19:40', '2025-10-27 21:19:40'),
(24, 1, 1, 2, 3, 'draw', '0.71780', '0.54713', '0.23452', '0.13721', NULL, NULL, '2025-10-27 21:19:40', '2025-10-27 21:19:40'),
(25, 1, 1, 2, 4, 'draw', '0.71780', '0.54713', '0.23452', '0.13721', NULL, NULL, '2025-10-27 21:19:40', '2025-10-27 21:19:40'),
(26, 1, 1, 2, 5, 'draw', '0.71780', '0.54713', '0.23452', '0.13721', NULL, NULL, '2025-10-27 21:19:40', '2025-10-27 21:19:40'),
(27, 1, 1, 2, 5, 'image', '0.40357', '0.91342', '0.27223', '0.07965', NULL, NULL, '2025-10-27 21:23:43', '2025-10-27 21:23:43'),
(28, 1, 1, 2, 1, 'image', '0.44643', '0.01094', '0.21429', '0.06270', NULL, NULL, '2025-10-27 21:24:14', '2025-10-27 21:24:14'),
(29, 1, 1, 2, 2, 'image', '0.44643', '0.01094', '0.21429', '0.06270', NULL, NULL, '2025-10-27 21:24:14', '2025-10-27 21:24:14'),
(30, 1, 1, 2, 3, 'image', '0.44643', '0.01094', '0.21429', '0.06270', NULL, NULL, '2025-10-27 21:24:14', '2025-10-27 21:24:14'),
(31, 1, 1, 2, 4, 'image', '0.44643', '0.01094', '0.21429', '0.06270', NULL, NULL, '2025-10-27 21:24:14', '2025-10-27 21:24:14'),
(32, 1, 1, 2, 5, 'image', '0.44643', '0.01094', '0.21429', '0.06270', NULL, NULL, '2025-10-27 21:24:14', '2025-10-27 21:24:14'),
(33, 1, 1, 2, 1, 'draw', '0.71065', '0.90791', '0.16071', '0.09007', 'data:image/png;base64,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', NULL, '2025-10-27 21:24:52', '2025-10-27 21:24:52'),
(34, 1, 1, 2, 2, 'draw', '0.71065', '0.90791', '0.16071', '0.09007', 'data:image/png;base64,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', NULL, '2025-10-27 21:24:52', '2025-10-27 21:24:52'),
(35, 1, 1, 2, 3, 'draw', '0.71065', '0.90791', '0.16071', '0.09007', 'data:image/png;base64,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', NULL, '2025-10-27 21:24:52', '2025-10-27 21:24:52'),
(36, 1, 1, 2, 4, 'draw', '0.71065', '0.90791', '0.16071', '0.09007', 'data:image/png;base64,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', NULL, '2025-10-27 21:24:52', '2025-10-27 21:24:52'),
(37, 1, 1, 2, 5, 'draw', '0.71065', '0.90791', '0.16071', '0.09007', 'data:image/png;base64,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', NULL, '2025-10-27 21:24:52', '2025-10-27 21:24:52'),
(38, 1, 1, 2, 5, 'image', '0.19167', '0.60702', '0.33333', '0.09753', NULL, NULL, '2025-10-27 21:25:15', '2025-10-27 21:25:15'),
(39, 1, 1, 2, 1, 'image', '0.40833', '0.00926', '0.21429', '0.06270', NULL, NULL, '2025-10-29 19:34:58', '2025-10-29 19:34:58'),
(40, 1, 1, 2, 2, 'image', '0.40833', '0.00926', '0.21429', '0.06270', NULL, NULL, '2025-10-29 19:34:58', '2025-10-29 19:34:58'),
(41, 1, 1, 2, 3, 'image', '0.40833', '0.00926', '0.21429', '0.06270', NULL, NULL, '2025-10-29 19:34:58', '2025-10-29 19:34:58'),
(42, 1, 1, 2, 4, 'image', '0.40833', '0.00926', '0.21429', '0.06270', NULL, NULL, '2025-10-29 19:34:58', '2025-10-29 19:34:58'),
(43, 1, 1, 2, 5, 'image', '0.40833', '0.00926', '0.21429', '0.06270', NULL, NULL, '2025-10-29 19:34:58', '2025-10-29 19:34:58'),
(44, 1, 1, 2, 5, 'image', '0.19048', '0.59271', '0.33333', '0.09753', NULL, NULL, '2025-10-29 19:35:40', '2025-10-29 19:35:40'),
(45, 2, 2, 2, 1, 'draw', '0.10708', '0.02054', '0.09789', '0.06084', 'data:image/png;base64,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', NULL, '2025-11-02 18:36:40', '2025-11-02 18:36:40'),
(46, 2, 2, 2, 2, 'draw', '0.10708', '0.02054', '0.09789', '0.06084', 'data:image/png;base64,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', NULL, '2025-11-02 18:36:40', '2025-11-02 18:36:40'),
(47, 2, 2, 2, 3, 'draw', '0.10708', '0.02054', '0.09789', '0.06084', 'data:image/png;base64,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', NULL, '2025-11-02 18:36:40', '2025-11-02 18:36:40'),
(48, 2, 2, 2, 4, 'draw', '0.10708', '0.02054', '0.09789', '0.06084', 'data:image/png;base64,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', NULL, '2025-11-02 18:36:40', '2025-11-02 18:36:40');
INSERT INTO `signatures` (`id`, `draft_deed_id`, `activity_id`, `user_id`, `page`, `kind`, `x_ratio`, `y_ratio`, `w_ratio`, `h_ratio`, `image_data_url`, `source_image_url`, `created_at`, `updated_at`) VALUES
(49, 2, 2, 2, 5, 'draw', '0.10708', '0.02054', '0.09789', '0.06084', 'data:image/png;base64,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', NULL, '2025-11-02 18:36:40', '2025-11-02 18:36:40'),
(50, 2, 2, 2, 1, 'image', '0.40952', '0.00084', '0.21429', '0.06270', NULL, NULL, '2025-11-02 18:37:07', '2025-11-02 18:37:07'),
(51, 2, 2, 2, 2, 'image', '0.40952', '0.00084', '0.21429', '0.06270', NULL, NULL, '2025-11-02 18:37:07', '2025-11-02 18:37:07'),
(52, 2, 2, 2, 3, 'image', '0.40952', '0.00084', '0.21429', '0.06270', NULL, NULL, '2025-11-02 18:37:07', '2025-11-02 18:37:07'),
(53, 2, 2, 2, 4, 'image', '0.40952', '0.00084', '0.21429', '0.06270', NULL, NULL, '2025-11-02 18:37:07', '2025-11-02 18:37:07'),
(54, 2, 2, 2, 5, 'image', '0.40952', '0.00084', '0.21429', '0.06270', NULL, NULL, '2025-11-02 18:37:07', '2025-11-02 18:37:07'),
(55, 2, 2, 2, 5, 'image', '0.64405', '0.62470', '0.33333', '0.09753', NULL, NULL, '2025-11-02 18:37:31', '2025-11-02 18:37:31'),
(56, 2, 2, 2, 1, 'image', '0.41905', '0.00758', '0.21429', '0.06270', NULL, NULL, '2025-11-02 21:16:05', '2025-11-02 21:16:05'),
(57, 2, 2, 2, 2, 'image', '0.41905', '0.00758', '0.21429', '0.06270', NULL, NULL, '2025-11-02 21:16:05', '2025-11-02 21:16:05'),
(58, 2, 2, 2, 3, 'image', '0.41905', '0.00758', '0.21429', '0.06270', NULL, NULL, '2025-11-02 21:16:05', '2025-11-02 21:16:05'),
(59, 2, 2, 2, 4, 'image', '0.41905', '0.00758', '0.21429', '0.06270', NULL, NULL, '2025-11-02 21:16:05', '2025-11-02 21:16:05'),
(60, 2, 2, 2, 5, 'image', '0.41905', '0.00758', '0.21429', '0.06270', NULL, NULL, '2025-11-02 21:16:05', '2025-11-02 21:16:05'),
(61, 2, 2, 2, 1, 'image', '0.04643', '0.01178', '0.21429', '0.06270', NULL, NULL, '2025-11-02 21:17:04', '2025-11-02 21:17:04'),
(62, 2, 2, 2, 2, 'image', '0.04643', '0.01178', '0.21429', '0.06270', NULL, NULL, '2025-11-02 21:17:04', '2025-11-02 21:17:04'),
(63, 2, 2, 2, 3, 'image', '0.04643', '0.01178', '0.21429', '0.06270', NULL, NULL, '2025-11-02 21:17:04', '2025-11-02 21:17:04'),
(64, 2, 2, 2, 4, 'image', '0.04643', '0.01178', '0.21429', '0.06270', NULL, NULL, '2025-11-02 21:17:04', '2025-11-02 21:17:04'),
(65, 2, 2, 2, 5, 'image', '0.04643', '0.01178', '0.21429', '0.06270', NULL, NULL, '2025-11-02 21:17:04', '2025-11-02 21:17:04'),
(66, 2, 2, 2, 5, 'image', '0.14405', '0.66181', '0.33333', '0.09753', NULL, NULL, '2025-11-03 20:44:43', '2025-11-03 20:44:43'),
(67, 1, 1, 2, 1, 'image', '0.41905', '0.00337', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:04:40', '2025-11-05 19:04:40'),
(68, 1, 1, 2, 2, 'image', '0.41905', '0.00337', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:04:40', '2025-11-05 19:04:40'),
(69, 1, 1, 2, 3, 'image', '0.41905', '0.00337', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:04:40', '2025-11-05 19:04:40'),
(70, 1, 1, 2, 4, 'image', '0.41905', '0.00337', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:04:40', '2025-11-05 19:04:40'),
(71, 1, 1, 2, 5, 'image', '0.41905', '0.00337', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:04:40', '2025-11-05 19:04:40'),
(72, 1, 1, 2, 1, 'image', '0.76071', '0.01010', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:04:40', '2025-11-05 19:04:40'),
(73, 1, 1, 2, 2, 'image', '0.76071', '0.01010', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:04:40', '2025-11-05 19:04:40'),
(74, 1, 1, 2, 3, 'image', '0.76071', '0.01010', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:04:40', '2025-11-05 19:04:40'),
(75, 1, 1, 2, 4, 'image', '0.76071', '0.01010', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:04:40', '2025-11-05 19:04:40'),
(76, 1, 1, 2, 5, 'image', '0.76071', '0.01010', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:04:40', '2025-11-05 19:04:40'),
(77, 1, 1, 2, 1, 'image', '0.02976', '0.00926', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:04:40', '2025-11-05 19:04:40'),
(78, 1, 1, 2, 2, 'image', '0.02976', '0.00926', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:04:40', '2025-11-05 19:04:40'),
(79, 1, 1, 2, 3, 'image', '0.02976', '0.00926', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:04:40', '2025-11-05 19:04:40'),
(80, 1, 1, 2, 4, 'image', '0.02976', '0.00926', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:04:40', '2025-11-05 19:04:40'),
(81, 1, 1, 2, 5, 'image', '0.02976', '0.00926', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:04:40', '2025-11-05 19:04:40'),
(82, 1, 1, 2, 1, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(83, 1, 1, 2, 2, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(84, 1, 1, 2, 3, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(85, 1, 1, 2, 4, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(86, 1, 1, 2, 5, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(87, 1, 1, 2, 1, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(88, 1, 1, 2, 2, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(89, 1, 1, 2, 3, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(90, 1, 1, 2, 4, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(91, 1, 1, 2, 5, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(92, 1, 1, 2, 1, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(93, 1, 1, 2, 2, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(94, 1, 1, 2, 3, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(95, 1, 1, 2, 4, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(96, 1, 1, 2, 5, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(97, 1, 1, 2, 1, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(98, 1, 1, 2, 2, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(99, 1, 1, 2, 3, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(100, 1, 1, 2, 4, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(101, 1, 1, 2, 5, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(102, 1, 1, 2, 1, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(103, 1, 1, 2, 2, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(104, 1, 1, 2, 3, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(105, 1, 1, 2, 4, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(106, 1, 1, 2, 5, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:06:45', '2025-11-05 19:06:45'),
(107, 1, 1, 2, 1, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:07:41', '2025-11-05 19:07:41'),
(108, 1, 1, 2, 2, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:07:41', '2025-11-05 19:07:41'),
(109, 1, 1, 2, 3, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:07:41', '2025-11-05 19:07:41'),
(110, 1, 1, 2, 4, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:07:41', '2025-11-05 19:07:41'),
(111, 1, 1, 2, 5, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:07:41', '2025-11-05 19:07:41'),
(112, 1, 1, 2, 1, 'image', '0.79405', '0.01010', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:07:41', '2025-11-05 19:07:41'),
(113, 1, 1, 2, 2, 'image', '0.79405', '0.01010', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:07:41', '2025-11-05 19:07:41'),
(114, 1, 1, 2, 3, 'image', '0.79405', '0.01010', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:07:41', '2025-11-05 19:07:41'),
(115, 1, 1, 2, 4, 'image', '0.79405', '0.01010', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:07:41', '2025-11-05 19:07:41'),
(116, 1, 1, 2, 5, 'image', '0.79405', '0.01010', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:07:41', '2025-11-05 19:07:41'),
(117, 1, 1, 2, 1, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:07:41', '2025-11-05 19:07:41'),
(118, 1, 1, 2, 2, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:07:41', '2025-11-05 19:07:41'),
(119, 1, 1, 2, 3, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:07:41', '2025-11-05 19:07:41'),
(120, 1, 1, 2, 4, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:07:41', '2025-11-05 19:07:41'),
(121, 1, 1, 2, 5, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:07:41', '2025-11-05 19:07:41'),
(122, 1, 1, 2, 1, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(123, 1, 1, 2, 2, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(124, 1, 1, 2, 3, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(125, 1, 1, 2, 4, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(126, 1, 1, 2, 5, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(127, 1, 1, 2, 1, 'image', '0.46429', '0.00673', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(128, 1, 1, 2, 2, 'image', '0.46429', '0.00673', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(129, 1, 1, 2, 3, 'image', '0.46429', '0.00673', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(130, 1, 1, 2, 4, 'image', '0.46429', '0.00673', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(131, 1, 1, 2, 5, 'image', '0.46429', '0.00673', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(132, 1, 1, 2, 1, 'image', '0.79762', '0.00842', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(133, 1, 1, 2, 2, 'image', '0.79762', '0.00842', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(134, 1, 1, 2, 3, 'image', '0.79762', '0.00842', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(135, 1, 1, 2, 4, 'image', '0.79762', '0.00842', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(136, 1, 1, 2, 5, 'image', '0.79762', '0.00842', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(137, 1, 1, 2, 1, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(138, 1, 1, 2, 2, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(139, 1, 1, 2, 3, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(140, 1, 1, 2, 4, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(141, 1, 1, 2, 5, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(142, 1, 1, 2, 1, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(143, 1, 1, 2, 2, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(144, 1, 1, 2, 3, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(145, 1, 1, 2, 4, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(146, 1, 1, 2, 5, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:08:54', '2025-11-05 19:08:54'),
(147, 1, 1, 2, 1, 'image', '0.03452', '0.01768', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:14:07', '2025-11-05 19:14:07'),
(148, 1, 1, 2, 2, 'image', '0.40357', '0.00926', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:14:07', '2025-11-05 19:14:07'),
(149, 1, 1, 2, 3, 'image', '0.80714', '0.01852', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:14:07', '2025-11-05 19:14:07'),
(150, 1, 1, 2, 1, 'draw', '0.49399', '0.01434', '0.07600', '0.03296', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHcAAABJCAYAAAAHSblXAAAL6ElEQVR4AezdBbA0OREH8Ie7u7sf7u5wuHtBIYW7++F2uEPhzuEOhbu7u8PhekhRFNz/t/ftu91sZuWtvDe786p7M+lkMjPpSHenk3fErfb/XSaf8Mbgj4L/b8APh3724EZBW5l7vnDp/sGvBj8WvGHwjMEmuHwSXh7cKGgLc08arrwh+NOg3vmVhAcGzxucFi46bcZ1ydcG5p4qlf3N4I2CpwvuFDSInd7byvvawNwnp2ZPEpwHPpWbHxDcKGgDcy88BUc+nzy3DR47eIQKXiq0DwY3CtrA3DNXOPKb0AhTz0t4nqD59GUJ/xHsYF8NtIG5+151KDhFYucP3jX4jWAHlRpoK3Mrn7JQ0loU1jF3LdhY/4iOufV6WQtqW5l7yrWo/SV/RBuY+4VKHXwutNMHOxhTA21g7vsq73/q0D4ZHGdPTvJmQxuY+9iwiE6bYAiYJS0adD14qFoOj7SBuf/J6143+MtgCXrwJ0I8Q7CDogbawFyv/LP8XDrYxODPJu2cwQ4GamAJzB0ofbGXlvsumyJ/FSzBkuCnQ7xYsIN9NdAm5nrlH+fHIkCtBx8vaebg6yfsIDXQNubmlbf0YEP097dG/44a0puDDwxuPLSRuZiGwRfPxReDNXhiiK8OHjm4sdBW5mLYn/LDOe5dCWtwixAN0ydKuJHQZuZi2L/yc53gK4I1uESI3Gs2zvMx373Vdub6hv/l59bBxwdrcJoQDd/XTLhRsA7M7TPsobngavPfhCUcK4R3BB8XXKdvzuc0w7p9KFcbuvDvKp/Mt+ohoX8gePzgimH1j1s35qpBxgwuOF8WqeAVQvtacBaf52RvH6wjc3Hh1/m5ZNA2kwQjcNpQLBveJOHawroyF8P+nZ8bBx8WrMHRQnx98JnBaYBgxjn+kGS294iUnsu9C+vM3H6tE6KsKlGb+rTB8B6JfDw4yfH9+clzriDhzN6jt+WamrVnmbwJzA0Ptt6eH4sKP09YA+ZMa8b8n2vpxwnxGsESbEjrM9koUabvanxTmKuSv54fQlTTzgN+WZYO+UIn6xD8PbG/BZsAkw9K4reDNw/uCdgk5qrwv+TnykE7BBNU4Tmh1ixeVwudlJ2gEc6RlNcEfxJ8cPBkwV2DTWNuv6KtGtnTS+jq0wbDWyWCkWdJ2Acqlh56vRAM4QkagesPi5ltLxqKeGPmZSW0g7nL+XpLgxdK0T8M1sAeJLpyqS6ZY+nRmPyl2o0FTUPRk58bOqeCBKuBTWauGv5Wfi4YJHAlGAG7BqlLhuoyEZM1jismgWkzwVi4S1IxmR08l8uHTWeuGiYoUZUeJNKAhKzPJK02vPZ1XkM4ndm8nqxVOGaoTKTj5vxkWQx0zD28Hp+Uy8sFfxusAVXqO0m4e7AGhvd7JeEEQbrvuN7sPI/3JB8VK8FyoGPucL1a3KcuCYdTDosdPcGzgowe49xpMRaDzc0fTf4akL6ZQJlCa+lz0zrmjlahnqsH3ydJTdI0owed9p7JMw5I1axZTbov1UlDYtocV86O0jrmNlfb05Ok8jm953IE9OJnhIo5kw5ieV3yYTK7dC6HwDz+kVDM2QkWBx1zx9clRzx+WvdOtqZeLJ3U3dQ7c2sPDM/y/r4XG/45U6J6OWeDXC4GOuZOV496qEUDm89qd1hMYJm6QS1xgGahgf1aoxkg9y5J0i/JFan8Ngknw4QcHXMnVNBAMod4cy1puakXvyr59cIEjUDX5bhH8q5lIpW/NAl3C84FHXNnrz4Gjaa5+BgpzrlZCcbCwUnF4PcnbIJnJ+GswR1Dx9ydVZ1h1fz51MrtBKcKeYTE2HHVUJ8QbAJHMTWlTaR3zJ1YRWMz0HfLDBhf0sbFOe3ZPWFhoszHtMmGXdKninfMnaqaGjNxpy0TqTUlbVLcOjKfL6bMMq8Fh3EGkzL/drxj7nZVzHRBr31r7rhIsIQXlIQZ4neo5HWgGl17ZgZ3zK3U5hjSuZPGIGHotdiQ6BC8JbHa7sOQpwIHgh9QyemICFPATGeAdMyt1GSFxBxpyOSqc9NKOtIP8rMI/fQxKcezEgyBIyJYw6buwRvL3KFqa44cJUmW8cyj46TgPyffVYKWDxPMDYQojaUsCIP1YCbLMm0k3jF3pEq2CeZVR/1yfd0mVi5eHJrh2lCdy4WARqKx1E4QwGBzsPcb+7COufXquUDIDjfjM5XLKtjcbQ68fVJr53SEPBdoLHTpJgbb82TtuPEhHXNHq4bZT89oclJnZ7YGe8vcypSYYGmg/CYGW0Vq2njee6GOub1q6P1YXCcwMfsx4veIAz928tNFrRD9YoC+7EsMtnOxNjowYdpRUX2HZTDXyeaPztOsoFAL/phr/1FkXvxnymGys2Rmo5dhi9Bhuc1ymWHU/zL4UPLxbDQXsvOyAHFOu1no+wetyrDZ6pnWZPVAXo4c3sydyTIC3wuFI13NipSkpYNFCwz2/eXDfJ86L+kL3VnvDAoCiAp/eJ6klRs6TpjrRQCjvOOITpzCKPYECh/lcDGuMTwRtWRbNB1XdLvku19Qy2bleW2u3xtkDcIse3jtH3plaNxhElSBdGqlRmOqZlgRkQ5c29Li8Vx/hEM4T891zgQ3TRYZQwYBY5p/NjH0Ai2IHCnv+MLgI4MW5Mc1hGRZKhg5aq49/LF0pqGHz8JcvYN5TA+wZGU9kpvmHVOifTYJ1hJUmk1ej8jXWZA3hPtHGYQuw74FelJzklcCeqkpr3zYo0rCOOaS0jhkGw7Ml+Y1LdjcdfKyoA2LE7icZGfYf1O+XR0RuLirWkywmiNPkpYCtsOUBXumhrhNrzHX8hPhhKnLVoqdtEr3m3c1EHOj8yjmRT6+5m8Ni7cgjwdTg20fhB3v7XmGTi6j5tTtD13BBZ3T8OgIYTsJ9W69nPTtnbzvol7D8ExwLMsb6r2DzOUHxL3DjYST8sZJcSI74UWFWy3xkYaupj2xk8or03kOMvNxPaXYkyAJRv71jEr8Q25QiaYNEvGkfTlcZXhVEMAIKixRT0sZfI5NObmcG8zP9GbDOad1kr6jHLjqNEnm0z7UNFHm1Xs18B69z1w9gRQ5q+EbQ3nqE6T0cEcUqPBe4Sv6uVKeQ/Uhpd8p15NAY7O9ksStktmNDad62H1zM32XBG6kOVviVn9sNfFfPI0IIe0YSPp2F5o36dRGF8O6hkWmmaVgal/NTUfjZmTZVoVshNpvQskqhXvmU5LPyghmQh/uEK+QVwYYY9hnTGCGo/pMerhRxDRjmnA2ZE1nLMugp6sbDVjDpxJhOpXL1hGM0cDL+6aN07UJZBYnyDQ2eRtu2QmunkIM9QkaQWMsEy0PWmvuMVdBho8yk7jW5aPsdlMpVkbsc7GLfJ6PUvYs6PxGdl6q1ztzIyOG92ZET7QRGD6cZWEINFz5N62NmWdI4H6KIf4zqAZOFrhW7tdoNKJc7gjUs2lCw313SiCkqWffbD41quCVxp3kLQew1Pys2MYPMCyz2sg4iFYlzF+GCsMR4WAwffCa5cT8ag1SY9DD/5oMJOxFofmUzy/Va5pj/lSIIZfwxTqlEvJKSwOyADuv4V4j6vdu//mT5cvwu9OHW97zzRbxlUW+0Lj7dev7amXvj7nmyzKRQMXjoKSLk04p0loTphuqif96tR5i/j6ujLuApNRr57l6E2HJMJforoDeTQ+2Nuv4BO6wrGYvytusQi7ZD3PzrBEw1/RbRhk6TsDKiNa0TF1u5KXGEDREjc55FxrdmKxLS5pU8HeTgTbCEESjIM2bb+1JWobMcnATc/Meexr0SEwk1Bi2TCFUoj390sXLUYv4XNlNaPTsD+XiJN551bGDMFcvLZ67a1FzNQM9yZGKQhplu+ZXZA6lRjBYGPYNv4Qa/7lk1154wQ82lOvJFmGoY76TJUxvN1qadqyCaRhNjzZCmA4OxNx5JEjSnJUWhTnYg4pgyCHia4mzotNUOYCRCEmNpNE75ysIE+ZQ+iyBIqSNACMUfdY8bR3ZtENlNaQ31a25nSB3CObS4TAHx5tqjARs6YulRS+iVxoOqSj0MYXZBKXlERZIt01ldfQV1cChAAAA///SVURkAAAABklEQVQDABS6HFAszNzJAAAAAElFTkSuQmCC', NULL, '2025-11-05 19:17:27', '2025-11-05 19:17:27'),
(151, 1, 1, 2, 3, 'draw', '0.10470', '0.02108', '0.07600', '0.03296', 'data:image/png;base64,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', NULL, '2025-11-05 19:17:27', '2025-11-05 19:17:27'),
(152, 1, 1, 2, 4, 'draw', '0.49399', '0.01434', '0.07600', '0.03296', 'data:image/png;base64,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', NULL, '2025-11-05 19:17:27', '2025-11-05 19:17:27'),
(153, 1, 1, 2, 5, 'image', '0.18571', '0.59945', '0.33333', '0.09753', NULL, NULL, '2025-11-05 19:17:53', '2025-11-05 19:17:53'),
(154, 1, 1, 2, 5, 'image', '0.20357', '0.58850', '0.33333', '0.09753', NULL, NULL, '2025-11-05 19:52:35', '2025-11-05 19:52:35'),
(155, 1, 1, 2, 1, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:53:30', '2025-11-05 19:53:30'),
(156, 1, 1, 2, 2, 'image', '0.38333', '0.01431', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:53:30', '2025-11-05 19:53:30'),
(157, 1, 1, 2, 4, 'image', '0.78333', '0.01178', '0.21429', '0.06270', NULL, NULL, '2025-11-05 19:53:30', '2025-11-05 19:53:30'),
(158, 1, 1, 2, 5, 'image', '0.16667', '0.59440', '0.33333', '0.09753', NULL, NULL, '2025-11-05 19:55:45', '2025-11-05 19:55:45'),
(159, 1, 1, 2, 5, 'image', '0.19286', '0.60450', '0.33333', '0.09753', NULL, NULL, '2025-11-05 20:06:03', '2025-11-05 20:06:03'),
(160, 1, 1, 2, 2, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 20:07:08', '2025-11-05 20:07:08'),
(161, 1, 1, 2, 3, 'image', '0.44643', '0.01263', '0.21429', '0.06270', NULL, NULL, '2025-11-05 20:07:08', '2025-11-05 20:07:08'),
(162, 1, 1, 2, 2, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-05 20:24:43', '2025-11-05 20:24:43'),
(163, 1, 1, 2, 3, 'image', '0.43929', '0.01010', '0.21429', '0.06270', NULL, NULL, '2025-11-05 20:24:43', '2025-11-05 20:24:43'),
(164, 1, 1, 2, 4, 'image', '0.74405', '0.16330', '0.21429', '0.06270', NULL, NULL, '2025-11-05 20:24:43', '2025-11-05 20:24:43'),
(165, 1, 1, 2, 5, 'image', '0.21429', '0.60450', '0.33333', '0.09753', NULL, NULL, '2025-11-05 20:25:07', '2025-11-05 20:25:07'),
(166, 4, 4, 2, 1, 'draw', '0.18648', '0.13636', '0.00153', '0.40984', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAPNCAYAAACj4LvYAAAAeUlEQVR4AezSwQmAQBAEwcH8gzIzLY3Ar9DQcPdcijm2nduu5+Pd+mAIIQQCagkhEFBLCIGAWkIIBNQSQiCglhACAbWEEAjoB0tw5VunYgghBAJqCSEQUEsIgYBaQggE1BJCIKCWEAIBtYQQCOhXS3DvUzdTCOEjwg0AAP//+50wcQAAAAZJREFUAwACkAlauwQYtgAAAABJRU5ErkJggg==', NULL, '2025-11-06 23:31:19', '2025-11-06 23:31:19'),
(167, 4, 4, 2, 2, 'draw', '0.23690', '0.07660', '0.00306', '0.82159', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAegCAYAAAABGZg2AAACX0lEQVR4AezcQWoDMBBD0SH3v3M7ksGSQ2/QDx71k+WjFLLpZ2Z+vk8f7mfv48PXYwAB5Arwy3ApTgByHO4CcilOAHIc7gJyKU4AchzuAnIpTgByHO4CcilOAHIc7gJyKU78a5BD8C4grwff9L88AAEkAvy5iIULEDNkAImFCxAzZACJhQsQM2QAiYULEDNkAImFCxAzZACJhesz/vEOSq8H33a/PAABJAL8uYiFCxAzZACJhQsQM2QAiYULEDNkAImFCxAzZACJhQsQM2QAiYXrM+Ofz6D0cAzfdr88AAEkAvy5iIULEDNkAImFCxAzZACJhQsQM2QAiYULEDNkAImFCxAzZACJhUsgjh4+bI1tQBahHyCtsQ3IIvQDpDW2AVmEfoC0xjYgi9APkNbYBmQR+gHSGtuALEI/QFpjG5BF6PfvQRpDDYgU6gApDCUgUqgDpDCUgEihDpDCUAIihTpACkMJiBTqACkMJSBSqAOkMJSASKEOkMJQAiKFOkAKQwmIFOoAKQwlIFKoA6QwlIBIoQ6QwlACIoU6QApDCYgU6gApDCUgUqgDpDCUgEihDpApjU1AFqEfIK2xDcgi9AOkNbYBWYR+gLTGNiCL0A+Q1tgGZBH6AdIa24AsQj9AWmMbkEXoB0hrbAMyswx5gMTCBYgZMoDEwgWIGTKAxMIFiBkygMTCBYgZMoDEwgWIGTKAxMIFiBkygMTCBYgZMoDMTDj4R/dt4eY3xAwZQGLhAsQMGUBi4QLEDBlAYuECxAwZQGLhAsQMGUBi4QLEDBlAYuECxAwZQGRR9yfILwAAAP//SPp14QAAAAZJREFUAwDuKRQ8tp+L2AAAAABJRU5ErkJggg==', NULL, '2025-11-06 23:31:19', '2025-11-06 23:31:19'),
(168, 4, 4, 2, 1, 'image', '0.02619', '0.01599', '0.21429', '0.06270', NULL, NULL, '2025-11-06 23:32:39', '2025-11-06 23:32:39'),
(169, 4, 4, 2, 2, 'image', '0.41429', '0.00758', '0.21429', '0.06270', NULL, NULL, '2025-11-06 23:32:39', '2025-11-06 23:32:39'),
(170, 4, 4, 2, 3, 'image', '0.78333', '0.00421', '0.21429', '0.06270', NULL, NULL, '2025-11-06 23:32:39', '2025-11-06 23:32:39'),
(171, 4, 4, 2, 4, 'image', '0.64524', '0.89743', '0.33333', '0.09753', NULL, NULL, '2025-11-06 23:33:07', '2025-11-06 23:33:07'),
(172, 4, 4, 2, 1, 'image', '0.81190', '0.00758', '0.21429', '0.06270', NULL, NULL, '2025-11-09 21:21:20', '2025-11-09 21:21:20'),
(173, 4, 4, 2, 2, 'image', '0.02857', '0.02020', '0.21429', '0.06270', NULL, NULL, '2025-11-09 21:21:20', '2025-11-09 21:21:20'),
(174, 4, 4, 2, 4, 'image', '0.43810', '0.01263', '0.21429', '0.06270', NULL, NULL, '2025-11-09 21:21:20', '2025-11-09 21:21:20'),
(175, 4, 4, 2, 4, 'image', '0.63929', '0.88144', '0.33333', '0.09753', NULL, NULL, '2025-11-09 21:22:20', '2025-11-09 21:22:20'),
(176, 4, 4, 2, 1, 'image', '0.02619', '0.01263', '0.21429', '0.06270', NULL, NULL, '2025-11-09 21:33:53', '2025-11-09 21:33:53'),
(177, 4, 4, 2, 3, 'image', '0.78810', '0.00673', '0.21429', '0.06270', NULL, NULL, '2025-11-09 21:33:53', '2025-11-09 21:33:53'),
(178, 4, 4, 2, 4, 'image', '0.46429', '0.00842', '0.21429', '0.06270', NULL, NULL, '2025-11-09 21:33:53', '2025-11-09 21:33:53'),
(179, 4, 4, 2, 1, 'draw', '0.18810', '0.14244', '0.00192', '0.77255', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAMAAAcrCAYAAAB4cAxqAAAA90lEQVR4AezU24nDUBAFwcH552yp9QhiUMFeWP+ZnvL5zcz/fX04/7//fJjRQIN+DRxwwEEFOKiCPeCAgwpwUAV7wAEHFeCgCnv2oG/7Pt+a3ixwwAEHFeCgCvaAAw4qwEEV7AEHHFSAgyrYg70Out/znNEZo8ABBxxUgIMq2AMOOKgAB1WwBxxwUAEOqmAPFjvogPdzRmdMAgcccFABDqpgDzjgoAIcVMEecMBBBTiogj3Y7KALXs8ZnTEIHHDAQQU4qII94ICDCnBQBXvAAQcV4KAK9mC1g07Yc0Zn5KACHFTBHnDAQQU4qII94ICDCnBQhY/uwQEAAP//sN0eQQAAAAZJREFUAwA/SxFUXTpXRQAAAABJRU5ErkJggg==', NULL, '2025-11-09 21:35:28', '2025-11-09 21:35:28'),
(180, 4, 4, 2, 2, 'draw', '0.23571', '0.13487', '0.00192', '0.77255', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAMAAAcrCAYAAAB4cAxqAAAA90lEQVR4AezU24nDUBAFwcH552yp9QhiUMFeWP+ZnvL5zcz/fX04/7//fJjRQIN+DRxwwEEFOKiCPeCAgwpwUAV7wAEHFeCgCnv2oG/7Pt+a3ixwwAEHFeCgCvaAAw4qwEEV7AEHHFSAgyrYg70Out/znNEZo8ABBxxUgIMq2AMOOKgAB1WwBxxwUAEOqmAPFjvogPdzRmdMAgcccFABDqpgDzjgoAIcVMEecMBBBTiogj3Y7KALXs8ZnTEIHHDAQQU4qII94ICDCnBQBXvAAQcV4KAK9mC1g07Yc0Zn5KACHFTBHnDAQQU4qII94ICDCnBQhY/uwQEAAP//sN0eQQAAAAZJREFUAwA/SxFUXTpXRQAAAABJRU5ErkJggg==', NULL, '2025-11-09 21:35:28', '2025-11-09 21:35:28'),
(181, 4, 4, 2, 4, 'draw', '0.18810', '0.14244', '0.00192', '0.77255', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAMAAAcrCAYAAAB4cAxqAAAA90lEQVR4AezU24nDUBAFwcH552yp9QhiUMFeWP+ZnvL5zcz/fX04/7//fJjRQIN+DRxwwEEFOKiCPeCAgwpwUAV7wAEHFeCgCnv2oG/7Pt+a3ixwwAEHFeCgCvaAAw4qwEEV7AEHHFSAgyrYg70Out/znNEZo8ABBxxUgIMq2AMOOKgAB1WwBxxwUAEOqmAPFjvogPdzRmdMAgcccFABDqpgDzjgoAIcVMEecMBBBTiogj3Y7KALXs8ZnTEIHHDAQQU4qII94ICDCnBQBXvAAQcV4KAK9mC1g07Yc0Zn5KACHFTBHnDAQQU4qII94ICDCnBQhY/uwQEAAP//sN0eQQAAAAZJREFUAwA/SxFUXTpXRQAAAABJRU5ErkJggg==', NULL, '2025-11-09 21:35:28', '2025-11-09 21:35:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `templates`
--

CREATE TABLE `templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `templates`
--

INSERT INTO `templates` (`id`, `user_id`, `name`, `description`, `custom_value`, `file`, `logo`, `logo_path`, `file_path`, `created_at`, `updated_at`) VALUES
(1, 1, 'Perjanjian Sewa Menyewa', 'Perjanjian sewa menyewa properti/ruangan berdasarkan kesepakatan para pihak.', '<h2 class=\"ql-align-center\">PERJANJIAN SEWA MENYEWA</h2><p class=\"ql-align-center\">Nomor : {{reference_number}}</p><p>– Pada hari ini, {{today}}</p><p>– tanggal .............................................................</p><p>– Pukul .................................................................</p><p>– Berhadapan dengan saya, {{notaris_name}}, Notaris di {{schedule_place}}, dengan dihadiri oleh para saksi yang saya, Notaris, kenal dan akan disebutkan nama-namanya pada bahagian akhir akta ini:</p><p><strong>I. Tuan {{penghadap1_name}}</strong></p><p> ..............................................................</p><p> ..............................................................</p><p> ..............................................................</p><p><strong>II. Tuan {{penghadap2_name}}</strong></p><p> ..............................................................</p><p> ..............................................................</p><p> ..............................................................</p><p>– menurut keterangannya dalam hal ini bertindak dalam jabatannya selaku Presiden Direktur dari Perseroan Terbatas PT. .........., berkedudukan di Jakarta yang anggaran dasarnya beserta perubahannya telah mendapat persetujuan dari Menteri Kehakiman dan Hak Asasi Manusia berturut-turut:</p><p>..............................................................</p><p> ..............................................................</p><p> ..............................................................</p><p> ..............................................................</p><p>selanjutnya disebut: <strong>Pihak Kedua</strong> atau <strong>Penyewa</strong>.</p><p>– Para penghadap telah saya, Notaris, kenal.</p><p>– Para penghadap menerangkan terlebih dahulu:</p><p>– bahwa Pihak Pertama adalah pemilik dari bangunan Rumah Toko (Ruko) yang hendak disewakan kepada Pihak Kedua yang akan disebutkan di bawah ini dan Pihak Kedua menerangkan menyewa dari Pihak Pertama berupa:</p><p>– 1 (satu) unit bangunan Rumah Toko (Ruko) berlantai 3 (tiga) berikut turutannya, lantai keramik, dinding tembok, atap dak, aliran listrik sebesar 2.200 Watt, dilengkapi air dari jet pump, berdiri di atas sebidang tanah Sertifikat HGB Nomor: ............ seluas ...... m² (....................................), penerbitan sertifikat tanggal ..........................., tercantum atas nama .................. yang telah diuraikan dalam Gambar Situasi tanggal ............ nomor ............; Sertifikat tanah diterbitkan oleh Kantor Pertanahan Kabupaten Bekasi, terletak di Provinsi Jawa Barat, Kabupaten Bekasi, Kecamatan Cibitung, Desa Ganda Mekar, setempat dikenal sebagai Mega Mall MM.2100 Blok B Nomor 8.</p><p>– Berdasarkan keterangan-keterangan tersebut di atas, kedua belah pihak sepakat membuat perjanjian sewa-menyewa dengan syarat-syarat dan ketentuan-ketentuan sebagai berikut:</p><p><strong>----------------------- Pasal 1.</strong></p><p>Perjanjian sewa-menyewa ini berlangsung untuk jangka waktu 2 (dua) tahun terhitung sejak tanggal ............ sampai dengan tanggal ............</p><p>– Penyerahan Ruko akan dilakukan dalam keadaan kosong/tidak dihuni pada tanggal .................. dengan penyerahan semua kunci-kuncinya.</p><p><strong>----------------------- Pasal 2.</strong></p><p>– Uang kontrak sewa disepakati sebesar Rp. ............ (....................................) untuk 2 (dua) tahun masa sewa.</p><p>– Jumlah uang sewa sebesar Rp. ............ (....................................) tersebut dibayar oleh Pihak Kedua kepada Pihak Pertama pada saat penandatanganan akta ini atau pada tanggal .................. dengan kwitansi tersendiri, dan akta ini berlaku sebagai tanda penerimaan yang sah.</p><p><strong>----------------------- Pasal 3.</strong></p><p>– Pihak Kedua hanya akan menggunakan yang disewakan dalam akta ini sebagai tempat kegiatan perkantoran/usaha.</p><p>– Jika diperlukan, Pihak Pertama memberikan surat rekomendasi/keterangan yang diperlukan Pihak Kedua sepanjang tidak melanggar hukum.</p><p>– Pihak Kedua wajib mentaati peraturan-peraturan pihak yang berwajib dan menjamin Pihak Pertama tidak mendapat teguran/tuntutan apapun karenanya.</p><p><strong>----------------------- Pasal 4.</strong></p><p>– Hanya dengan persetujuan tertulis Pihak Pertama, Pihak Kedua boleh mengadakan perubahan/penambahan pada bangunan; seluruh biaya dan tanggung jawab pada Pihak Kedua, dan pada akhir masa kontrak menjadi hak Pihak Pertama.</p><p>– Penyerahan nyata dari yang disewakan oleh Pihak Pertama kepada Pihak Kedua dilakukan pada tanggal .................. dengan penyerahan semua kunci-kunci.</p><p><strong>----------------------- Pasal 5.</strong></p><p>Pihak Pertama memberi izin kepada Pihak Kedua untuk pemasangan/penambahan antara lain:</p><ol><li>Sekat-sekat pada ruangan;</li><li>Antena radio/CD;</li><li>Line telepon;</li><li>Air Conditioner (AC);</li><li>Penambahan daya listrik;</li><li>Saluran fax;</li><li>Internet;</li><li>TV Kabel;</li><li>Shower;</li><li>Penggantian W/C;</li><li>Katrol pengangkut barang lantai 1–3;</li><li>Peralatan keamanan;</li><li>Peralatan pendukung usaha (rak/mesin) tanpa merusak struktur bangunan.</li></ol><p>– Setelah masa kontrak berakhir, Pihak Kedua mengembalikan seperti keadaan semula dengan biaya Pihak Kedua.</p><p>– Pihak Kedua boleh mengganti kunci ruangan di dalam bangunan (kecuali pintu utama); pada akhir masa kontrak, kunci-kunci diserahkan ke Pihak Pertama.</p><p>– Pihak Pertama menjamin yang disewakan adalah miliknya dan bebas dari tuntutan pihak lain.</p><p>– Selama masa sewa, Pihak Pertama boleh memeriksa bangunan sewaktu-waktu.</p><p><strong>----------------------- Pasal 6.</strong></p><p>– Selama masa kontrak, pembayaran langganan listrik/air/telepon dan kewajiban lain terkait pemakaian dibayar Pihak Kedua hingga bulan terakhir dengan bukti pembayaran setiap bulan.</p><p>– Pihak Pertama membayar Pajak Bumi dan Bangunan (PBB) untuk objek sewa.</p><p><strong>----------------------- Pasal 7.</strong></p><p>– Pihak Kedua wajib memelihara yang disewa dengan baik; kerusakan karena kelalaian diperbaiki atas biaya Pihak Kedua.</p><p>– Apabila terjadi force majeure (kebakaran—kecuali kelalaian Pihak Kedua—sabotase, badai, banjir, gempa) sehingga objek musnah, para pihak dibebaskan dari tuntutan.</p><p><strong>----------------------- Pasal 8.</strong></p><p>– Pihak Pertama menjamin tidak ada tuntutan atau gangguan dari pihak lain atas yang disewa selama kontrak.</p><p><strong>----------------------- Pasal 9.</strong></p><p>Pihak Kedua, dengan persetujuan tertulis Pihak Pertama, boleh mengalihkan/memindahkan hak kontrak pada pihak lain, sebagian maupun seluruhnya, selama masa kontrak berlaku.</p><p><strong>----------------------- Pasal 10.</strong></p><p>Pihak Kedua wajib memberi pemberitahuan mengenai berakhir/akan diperpanjangnya kontrak kepada Pihak Pertama selambat-lambatnya 2 (dua) bulan sebelum berakhir.</p><p><strong>----------------------- Pasal 11.</strong></p><p>Pada saat berakhirnya kontrak dan tidak ada perpanjangan, Pihak Kedua menyerahkan kembali objek sewa dalam keadaan kosong, terpelihara baik, dengan semua kunci pada tanggal ..................</p><p>Apabila terlambat, Pihak Kedua dikenakan denda sebesar Rp. 27.500,- per hari selama 7 (tujuh) hari pertama; jika masih tidak diserahkan, Pihak Kedua memberi kuasa kepada Pihak Pertama (dengan hak substitusi) untuk melakukan pengosongan dengan bantuan pihak berwajib, atas biaya dan risiko Pihak Kedua.</p><p><strong>----------------------- Pasal 12.</strong></p><p>Selama masa kontrak belum berakhir, perjanjian ini tidak berakhir karena:</p><ol><li>Meninggalnya salah satu pihak;</li><li>Pihak Pertama mengalihkan hak milik atas objek sewa kepada pihak lain;</li><li>Dalam hal salah satu pihak meninggal dunia, ahli waris/penggantinya wajib melanjutkan perjanjian sampai berakhir; pemilik baru tunduk pada seluruh ketentuan akta ini.</li></ol><p><strong>----------------------- Pasal 13.</strong></p><p>Untuk menjamin pembayaran listrik, air, telepon, keamanan, dan kewajiban lain bulan terakhir, Pihak Kedua menyerahkan uang jaminan sebesar Rp. 2.000.000,- (dua juta rupiah) pada saat penyerahan kunci, dengan kwitansi tersendiri. Kelebihan dikembalikan Pihak Pertama; kekurangan ditambah oleh Pihak Kedua.</p><p><strong>----------------------- Pasal 14.</strong></p><p>Hal-hal yang belum cukup diatur akan dibicarakan kemudian secara musyawarah untuk mufakat.</p><p><strong>----------------------- Pasal 15.</strong></p><p>Pajak-pajak yang mungkin ada terkait akta ini dibayar oleh Pihak Kedua untuk dan atas nama Pihak Pertama.</p><p><strong>----------------------- Pasal 16.</strong></p><p>Biaya-biaya yang berkaitan dengan akta ini dibayar dan menjadi tanggungan Pihak Pertama.</p><p><strong>----------------------- Pasal 17.</strong></p><p>Kedua belah pihak memilih domisili hukum yang sah di Kepaniteraan Pengadilan Negeri Bekasi.</p><p><strong>DEMIKIAN AKTA INI</strong></p><p>– Dibuat dan diresmikan di Bekasi pada hari dan tanggal sebagaimana awal akta ini, dengan dihadiri oleh:</p><ol><li>Nyonya ........................................</li><li>Nyonya ........................................</li></ol><p>Keduanya Karyawan Kantor Notaris, sebagai saksi-saksi.</p><p>– Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p>', NULL, NULL, NULL, NULL, '2025-09-10 03:22:38', '2025-09-27 21:39:11'),
(2, 1, 'Hak Waris', 'Penetapan ahli waris dan pembagian harta peninggalan pewaris.', '<div class=\"ql-align-center\">\n  <h2 style=\"margin:0; font-weight:700;\">KETERANGAN HAK WARIS</h2>\n  <div style=\"margin-top:4px;\">Nomor: {{reference_number}}</div>\n</div>\n\n<p class=\"ql-align-justify\">\n  Pada hari ini, hari {{day_name}}, tanggal {{date_long}}, pukul {{time_wib}} WIB ({{time_wib_words}} Waktu Indonesia bagian Barat),\n  menghadap di hadapan saya, {{notary_name}}, Sarjana Hukum, Notaris di {{city}}, dengan dihadiri oleh saksi-saksi yang saya,\n  Notaris, kenal dan akan disebut pada bagian akhir akta ini.\n</p>\n\n<h3>I. PENGHADAP PERTAMA</h3>\n<p class=\"ql-align-justify no-page-break\">\n  Nyonya {{party1_fullname}} {{party1_alias_opt}}, {{party1_job}}, bertempat tinggal di {{party1_address_full}};\n  Kartu Tanda Penduduk Nomor: {{party1_ktp}}.\n</p>\n\n<h3>II. PENGHADAP KEDUA</h3>\n<p class=\"ql-align-justify\">\n  Nyonya {{party2_fullname}} {{party2_alias_opt}}, {{party2_job}}, bertempat tinggal di {{party2_address_full}};\n  Kartu Tanda Penduduk Nomor: {{party2_ktp}}.\n</p>\n\n<p class=\"ql-align-justify\">\n  Para penghadap tersebut telah dikenal oleh saya, Notaris.\n</p>\n\n<div class=\"page-break-after\"></div>\n\n<h3>KETERANGAN PARA PENGHADAP</h3>\n\n<p class=\"ql-align-justify\">\n  Bahwa almarhum Tuan {{pewaris_name}} {{pewaris_alias_opt}}, Warganegara Indonesia, telah meninggal dunia di {{pewaris_death_city}},\n  pada tanggal {{pewaris_death_date_long}} ({{pewaris_death_date_num}}), demikian seperti ternyata dari Akta Kematian tertanggal\n  {{akta_kematian_date_long}} ({{akta_kematian_date_num}}) Nomor {{akta_kematian_number}} yang dikeluarkan oleh\n  {{akta_kematian_issuer}}; akta mana aslinya diperlihatkan kepada saya, Notaris.\n</p>\n\n<p class=\"ql-align-justify\">\n  Bahwa almarhum Tuan {{pewaris_name_short}} {{pewaris_alias_opt}} selanjutnya akan disebut juga “pewaris”, menurut keterangan para\n  penghadap telah kawin sah dengan Nyonya {{spouse_fullname}} {{spouse_alias_opt}}, demikian berdasarkan Akta Perkawinan/Golongan\n  Tionghoa tanggal {{akta_kawin_date_long}} ({{akta_kawin_date_num}}) Nomor {{akta_kawin_number}} yang dikeluarkan oleh\n  {{akta_kawin_issuer}}; akta mana aslinya diperlihatkan kepada saya, Notaris.\n</p>\n\n<p class=\"ql-align-justify\">\n  Bahwa dari perkawinan tersebut telah dilahirkan {{children_count_words}} ({{children_count_num}}) orang anak, yaitu:\n</p>\n\n<ol class=\"ql-align-justify\" style=\"padding-left:24px;\">\n  <li class=\"keep-together\">\n    Penghadap Nyonya {{child1_now_name}}, dahulu bernama {{child1_old_name_opt}}, disebut juga {{child1_alias_opt}},\n    yang dilahirkan pada tanggal {{child1_birth_date_long}} ({{child1_birth_date_num}}) di {{child1_birth_city}},\n    berdasarkan Akta Kelahiran tanggal {{child1_akta_date_long}} ({{child1_akta_date_num}}) Nomor {{child1_akta_number}}\n    yang dikeluarkan oleh {{child1_akta_issuer}}; aslinya diperlihatkan kepada saya, Notaris.\n  </li>\n  <li class=\"keep-together\">\n    Nyonya {{child2_now_name}}, yang dilahirkan di {{child2_birth_city}}, pada tanggal {{child2_birth_date_long}} ({{child2_birth_date_num}}),\n    berdasarkan Akta Kelahiran tanggal {{child2_akta_date_long}} ({{child2_akta_date_num}}) Nomor {{child2_akta_number}}\n    yang dikeluarkan oleh {{child2_akta_issuer}}; aslinya diperlihatkan kepada saya, Notaris.\n  </li>\n  <li class=\"keep-together\">\n    Nona {{child3_now_name}}, disebut juga {{child3_alias_opt}}, sekarang bernama {{child3_current_name_opt}},\n    dilahirkan di {{child3_birth_city}} pada tanggal {{child3_birth_date_long}} ({{child3_birth_date_num}}),\n    berdasarkan Akta Kelahiran tanggal {{child3_akta_date_long}} ({{child3_akta_date_num}}) Nomor {{child3_akta_number}}\n    yang dikeluarkan oleh {{child3_akta_issuer}}; aslinya diperlihatkan kepada saya, Notaris.\n  </li>\n  <li class=\"keep-together\">\n    Tuan {{child4_now_name}}, dilahirkan pada tanggal {{child4_birth_date_long}} ({{child4_birth_date_num}}),\n    berdasarkan Akta Kelahiran tanggal {{child4_akta_date_long}} ({{child4_akta_date_num}}) Nomor {{child4_akta_number}}\n    yang dikeluarkan oleh {{child4_akta_issuer}}; aslinya diperlihatkan kepada saya, Notaris.\n  </li>\n  <li class=\"keep-together\">\n    Tuan/Nona {{child5_now_name}}, dilahirkan di {{child5_birth_city}} pada tanggal {{child5_birth_date_long}} ({{child5_birth_date_num}}),\n    berdasarkan Akta Kelahiran tanggal {{child5_akta_date_long}} ({{child5_akta_date_num}}) Nomor {{child5_akta_number}}\n    yang dikeluarkan oleh {{child5_akta_issuer}}; aslinya diperlihatkan kepada saya, Notaris.\n  </li>\n</ol>\n\n<p class=\"ql-align-justify\">\n  Bahwa “pewaris” tidak meninggalkan turunan atau saudara lain selain dari para penghadap dan {{child2_now_name}},\n  {{child3_now_name}} {{child3_current_name_opt}}, {{child4_now_name}}, dan {{child5_now_name}} tersebut.\n</p>\n\n<p class=\"ql-align-justify\">\n  Bahwa menurut surat dari {{no_will_issuer}} tanggal {{no_will_date_long}} ({{no_will_date_num}}) Nomor {{no_will_number}},\n  “pewaris” tidak meninggalkan surat wasiat.\n</p>\n\n<div class=\"page-break-after\"></div>\n\n<h3>PERNYATAAN</h3>\n<p class=\"ql-align-justify\">\n  Para penghadap tersebut di atas selanjutnya dengan ini menerangkan:\n</p>\n<ul class=\"ql-align-justify\" style=\"padding-left:24px;\">\n  <li>Bahwa para penghadap mengetahui dan dapat membenarkan segala sesuatu yang diuraikan di atas;</li>\n  <li>Bahwa para penghadap bersedia jika perlu memperkuat segala sesuatu yang diuraikan di atas dengan sumpah.</li>\n</ul>\n\n<p class=\"ql-align-justify\">\n  Maka sekarang berdasarkan keterangan-keterangan tersebut di atas dan surat-surat yang diperlihatkan kepada saya, Notaris, serta\n  berdasarkan hukum yang berlaku bagi para penghadap dan {{child2_now_name}}, {{child3_now_name}} {{child3_current_name_opt}},\n  {{child4_now_name}}, dan {{child5_now_name}}, maka saya, Notaris, menerangkan dalam akta ini:\n</p>\n\n<h3>PEMBAGIAN HAK ATAS HARTA PENINGGALAN</h3>\n<ol class=\"ql-align-justify\" style=\"padding-left:24px;\">\n  <li>Nyonya {{spouse_fullname}} mendapat {{portion_spouse}} bagian.</li>\n  <li>Nyonya {{party2_fullname_short}} {{party2_alias_opt}} mendapat {{portion_child2}} bagian.</li>\n  <li>Nyonya {{child2_now_name}} mendapat {{portion_childB}} bagian.</li>\n  <li>Nona {{child3_now_name}} {{child3_alias_opt}} {{child3_current_name_opt}} mendapat {{portion_childC}} bagian.</li>\n  <li>Tuan {{child4_now_name}} mendapat {{portion_childD}} bagian.</li>\n  <li>Nona/Tuan {{child5_now_name}} mendapat {{portion_childE}} bagian.</li>\n</ol>\n\n<p class=\"ql-align-justify\">\n  Bahwa para penghadap dan {{child2_now_name}}, {{child3_now_name}} {{child3_current_name_opt}}, {{child4_now_name}}, dan {{child5_now_name}},\n  merupakan para ahli waris tersendiri dari “pewaris” dengan mengecualikan siapapun juga, serta berhak untuk menuntut dan menerima\n  seluruh barang-barang dan harta kekayaan yang termasuk harta peninggalan “pewaris”. Selanjutnya, mereka berhak memberi\n  tanda terima untuk segala penerimaan harta kekayaan dan barang.\n</p>\n\n<p class=\"ql-align-justify\">\n  Dari segala sesuatu yang tersebut di atas ini dengan segala akibat-akibatnya, para penghadap telah memilih tempat kediaman\n  hukum yang sah dan tidak berubah di Kantor Panitera Pengadilan Negeri {{pengadilan_negeri_kota}}.\n</p>\n\n<div class=\"page-break-after\"></div>\n\n<h3>PENUTUP</h3>\n<p class=\"ql-align-justify\">\n  Demikianlah akta ini, dibuat dengan dihadiri oleh Tuan {{witness1_name}} dan Tuan {{witness2_name}}, kedua-duanya Pegawai\n  Kantor Notaris, bertempat tinggal di {{city}}, sebagai saksi-saksi.\n</p>\n<p class=\"ql-align-justify\">\n  Segera setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka ditandatangani oleh para\n  penghadap, para saksi, dan saya, Notaris.\n</p>\n<p class=\"ql-align-justify\">\n  Dilangsungkan dengan tanpa perubahan. Dilangsungkan dan diresmikan sebagai minuta di {{city}}, pada hari, tanggal, dan tahun seperti\n  disebut pada awal. Minuta akta ini telah ditandatangani dengan sempurna. Diberikan sebagai salinan yang sama bunyinya.\n</p>\n\n<p class=\"ql-align-right\" style=\"margin-top:32px;\">\n  {{city}}, {{date_long}}<br/>\n  {{notary_name}}<br/>\n  Notaris di {{city}}\n</p>', NULL, NULL, NULL, NULL, '2025-09-09 10:00:00', '2025-10-27 19:26:09'),
(3, 1, 'Perseroan Komanditer', 'Pendirian usaha bersama antara sekutu aktif dan sekutu pasif dalam bentuk CV.', '<div style=\"text-align:center;margin-bottom:8px\">\n  <h2 style=\"margin:0\">AKTA PENDIRIAN PERSEROAN KOMANDITER</h2>\n  <div style=\"font-size:12px\">Nomor : {{reference_number}}</div>\n</div>\n\n<p>– Pada hari ini, {{today}}</p>\n<p>– tanggal .............................................................</p>\n<p>– Pukul .................................................................</p>\n\n<p>\n– Berhadapan dengan saya, {{notaris_name}}, Notaris di {{schedule_place}},\ndengan dihadiri oleh saksi-saksi yang saya, Notaris, kenal dan akan disebutkan pada bagian akhir akta ini:\n</p>\n\n<p><b>I. Tuan {{penghadap1_name}}</b><br/>\n{{penghadap1_identitas_line1}}<br/>\n{{penghadap1_identitas_line2}}<br/>\n{{penghadap1_identitas_line3}}\n</p>\n\n<p><b>II. Nyonya {{penghadap2_name}}</b><br/>\n{{penghadap2_identitas_line1}}<br/>\n{{penghadap2_identitas_line2}}<br/>\n{{penghadap2_identitas_line3}}\n</p>\n\n<p><b>III. Nyonya/Tuan {{penghadap3_name}}</b><br/>\n{{penghadap3_identitas_line1}}<br/>\n{{penghadap3_identitas_line2}}<br/>\n{{penghadap3_identitas_line3}}\n</p>\n\n<p>– Para penghadap telah saya, Notaris, kenal.</p>\n\n<p>\n– Para penghadap menerangkan dengan akta ini telah saling setuju dan semufakat untuk mendirikan suatu\nPerseroan Komanditer (Commanditaire Vennootschap) dengan Anggaran Dasar sebagai berikut:\n</p>\n\n<div class=\"page-break-after\"></div>\n\n<p style=\"text-align:center\"><b>NAMA DAN TEMPAT KEDUDUKAN<br/>Pasal 1</b></p>\n<ol style=\"margin-left:18px\">\n  <li>Perseroan ini bernama Perseroan Komanditer: <b>{{cv_name_upper}}</b> (selanjutnya disebut “Perseroan”).</li>\n  <li>Perseroan berkedudukan di {{domisili_kota}}, dengan cabang/perwakilan di tempat lain yang dianggap perlu oleh (para) Pesero Pengurus.</li>\n</ol>\n\n<p style=\"text-align:center\"><b>WAKTU<br/>Pasal 2</b></p>\n<p>– Perseroan didirikan untuk waktu yang tidak ditentukan dan mulai berlaku sejak akta ini ditandatangani.</p>\n\n<p style=\"text-align:center\"><b>MAKSUD DAN TUJUAN<br/>Pasal 3</b></p>\n<p>Maksud dan tujuan Perseroan sebagai berikut:</p>\n<ol style=\"margin-left:18px\">\n  <li>Distribusi/supplier/leveransir/grosir/komisioner/keagenan berbagai barang (kecuali keagenan perjalanan);</li>\n  <li>Perdagangan umum (impor, ekspor, lokal, antarpulau) sendiri maupun komisi;</li>\n  <li>Industri (konveksi/garment, butik, alat rumah tangga, kerajinan, souvenir, kayu, besi);</li>\n  <li>Jasa: perawatan/perbaikan elektrikal-mekanikal-teknikal & komputer; warnet/wartel/pos; cleaning service; boga; pengiriman barang;</li>\n  <li>Kontraktor/biro bangunan (gedung, perumahan, jalan, jembatan, irigasi), pemasangan aluminium/gypsum/kaca/furnitur & instalasi listrik/air/gas/telekomunikasi;</li>\n  <li>Pengadaan alat & kebutuhan kantor; pertamanan/landscaping; interior & eksterior; periklanan & reklame; percetakan/penjilidan/pengepakan;</li>\n  <li>Pengangkutan darat; perbengkelan; perkebunan, kehutanan, pertanian, peternakan, perikanan;</li>\n  <li>Segala kegiatan lain yang menunjang tujuan Perseroan sepanjang peraturan perundang-undangan.</li>\n</ol>\n<p>– Perseroan dapat mendirikan/ikut mendirikan badan lain yang sejenis di dalam/luar negeri sesuai peraturan.</p>\n\n<div class=\"page-break-after\"></div>\n\n<p style=\"text-align:center\"><b>MODAL<br/>Pasal 4</b></p>\n<ol style=\"margin-left:18px\">\n  <li>Modal Perseroan tidak ditentukan besarnya; akan ternyata pada buku Perseroan, termasuk porsi tiap pesero.</li>\n  <li>Setoran uang dan/atau inbreng dicatat pada perhitungan modal masing-masing dan diberi tanda bukti yang ditandatangani para pesero.</li>\n  <li>(Para) Pesero Pengurus juga mencurahkan tenaga, pikiran, dan keahliannya untuk kepentingan Perseroan.</li>\n</ol>\n\n<p style=\"text-align:center\"><b>PENGURUSAN & TANGGUNG JAWAB — (PARA) PESERO PENGURUS<br/>Pasal 5</b></p>\n<ol style=\"margin-left:18px\">\n  <li>Tuan {{pesero_pengurus_name}} adalah Pesero Pengurus bertanggung jawab penuh; Nyonya/Tuan {{pesero_komanditer1_name}} dan {{pesero_komanditer2_name}} adalah Pesero Komanditer yang bertanggung jawab sampai modal yang dimasukkan.</li>\n  <li>\n    Tuan {{direktur_name}} selaku Direktur (atau wakil/yang ditunjuk bila berhalangan) mewakili dan mengikat Perseroan, namun untuk:\n    <ol style=\"margin-left:18px\">\n      <li>Perolehan/pelepasan/pemindahan hak atas benda tetap;</li>\n      <li>Meminjam/meminjamkan uang (kecuali penarikan dana Perseroan di bank/tempat lain);</li>\n      <li>Menggadaikan/membebani harta Perseroan;</li>\n      <li>Mengikat Perseroan sebagai penjamin;</li>\n      <li>Mengangkat/mencabut kuasa;</li>\n    </ol>\n    – harus dengan persetujuan lebih dahulu/ turut ditandatangani Pesero Komanditer.\n  </li>\n  <li>(Para) Pesero Pengurus memegang buku-buku, uang, dan hal-hal lain usaha Perseroan; berwenang mengangkat/memberhentikan karyawan & menetapkan gaji.</li>\n</ol>\n\n<p style=\"text-align:center\"><b>WEWENANG (PARA) PESERO KOMANDITER<br/>Pasal 6</b></p>\n<ol style=\"margin-left:18px\">\n  <li>Berwenang memasuki aset Perseroan (kantor/gedung/bangunan) dan memeriksa buku-buku, uang, dan keadaan usaha.</li>\n  <li>(Para) Pesero Pengurus wajib memberi keterangan yang diminta.</li>\n</ol>\n\n<p style=\"text-align:center\"><b>PENGUNDURAN DIRI / MENINGGAL DUNIA / PAILIT<br/>Pasal 7–10</b></p>\n<p>\n– Ketentuan pengunduran diri (pemberitahuan ≥ 3 bulan), kelanjutan usaha bila pesero meninggal (dengan kuasa ahli waris ≤ 3 bulan), status keluar bila pailit/surseance/pengampuan, serta pembayaran bagian pesero yang keluar menurut neraca terakhir (≤ 3 bulan, tanpa bunga) dan hak pesero tersisa untuk melanjutkan usaha dengan sisa aktiva-pasiva dan tetap memakai nama Perseroan.\n</p>\n\n<div class=\"page-break-after\"></div>\n\n<p style=\"text-align:center\"><b>PENUTUPAN BUKU & NERACA<br/>Pasal 11</b></p>\n<ol style=\"margin-left:18px\">\n  <li>Setiap akhir Desember buku ditutup; paling lambat akhir Maret dibuat neraca & laba-rugi. Pertama kali ditutup: {{first_closing_date_long}} ({{first_closing_date_num}}).</li>\n  <li>Dokumen disimpan di kantor Perseroan; dapat dilihat (Para) Pesero Komanditer 14 hari sejak dibuat.</li>\n  <li>Jika tidak ada keberatan dalam 14 hari, dianggap sah dan semua pesero menandatangani (acquit et decharge kepada (Para) Pesero Pengurus).</li>\n  <li>Bila tidak mufakat, dapat minta hakim menunjuk 3 arbiter; para pesero tunduk pada putusan para arbiter.</li>\n</ol>\n\n<p style=\"text-align:center\"><b>KEUNTUNGAN (Pasal 12) — KERUGIAN (Pasal 13) — DANA CADANGAN (Pasal 14)</b></p>\n<p>\n– Keuntungan dibagi sesuai perbandingan modal; dibayarkan ≤ 1 bulan setelah pengesahan neraca/laba-rugi.\nKerugian ditanggung sesuai perbandingan; Pesero Komanditer hanya sampai modal setorannya. Dana cadangan dapat disisihkan/ digunakan sebagai modal kerja sesuai kesepakatan; hasil/rugi diperhitungkan pada laba-rugi.\n</p>\n\n<p style=\"text-align:center\"><b>PENGALIHAN BAGIAN (Pasal 15) — HAL-HAL LAIN (Pasal 16) — DOMISILI (Pasal 17)</b></p>\n<p>\n– Pengalihan/pembebanan bagian pesero harus dengan persetujuan pesero lain. Hal yang belum cukup diatur diputuskan musyawarah.\nPara pesero memilih domisili di Kepaniteraan Pengadilan Negeri {{domisili_kota}}.\n</p>\n\n<div class=\"page-break-after\"></div>\n\n<p><b>AKTA INI</b></p>\n<p>– Dibuat sebagai minuta dan diresmikan di {{schedule_place}} pada hari dan tanggal seperti pada awal akta ini, dengan saksi-saksi:</p>\n<ol style=\"margin-left:18px\">\n  <li>{{saksi1_name}}, {{saksi1_identitas_desc}}</li>\n  <li>{{saksi2_name}}, {{saksi2_identitas_desc}}</li>\n</ol>\n<p>Keduanya Karyawan Kantor Notaris, sebagai saksi-saksi.</p>\n<p>– Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p>', NULL, NULL, NULL, NULL, '2025-09-09 10:00:00', '2025-10-27 19:26:09'),
(4, 1, 'Pendirian PT', 'Pendirian badan usaha berbentuk Perseroan Terbatas oleh para pendiri sesuai ketentuan hukum.', '<h2 class=\"ql-align-center\">AKTA PENDIRIAN PERSEROAN TERBATAS</h2><p class=\"ql-align-center\">Nomor : {{reference_number}}</p><p>– Pada hari ini, {{today}}</p><p>– tanggal .............................................................</p><p>– Pukul .................................................................</p><p>– Berhadapan dengan saya, {{notaris_name}}, Notaris di {{schedule_place}}, dengan dihadiri oleh para saksi yang saya, Notaris, kenal dan akan disebutkan nama-namanya pada bagian akhir akta ini:</p><p><strong>I. {{penghadap1_name}}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p><strong>II. {{penghadap2_name}}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>Para penghadap tersebut di atas, bertindak untuk dan atas nama diri mereka sendiri, dengan ini sepakat untuk mendirikan suatu badan hukum berbentuk <strong>Perseroan Terbatas</strong> (selanjutnya disebut “Perseroan”) dengan ketentuan sebagai berikut:</p><p><strong>----------------------- Pasal 1.</strong></p><p><strong>Nama dan Tempat Kedudukan</strong></p><p>Perseroan ini bernama <strong>PT. {{company_name}}</strong> dan berkedudukan di <strong>{{company_city}}</strong>.</p><p><strong>----------------------- Pasal 2.</strong></p><p><strong>Maksud dan Tujuan</strong></p><p>Perseroan didirikan dengan maksud dan tujuan untuk menjalankan usaha di bidang <strong>{{business_field}}</strong> serta kegiatan lain yang berhubungan dan mendukungnya, sesuai dengan ketentuan peraturan perundang-undangan yang berlaku.</p><p><strong>----------------------- Pasal 3.</strong></p><p><strong>Modal Perseroan</strong></p><p>Modal dasar Perseroan ditetapkan sebesar Rp. {{modal_dasar}},– ({{modal_dasar_terbilang}}), yang terbagi atas {{jumlah_saham}} ({{jumlah_saham_terbilang}}) saham, masing-masing bernilai nominal Rp. {{nilai_saham}},– ({{nilai_saham_terbilang}}).</p><p>Modal ditempatkan dan disetor penuh oleh para pendiri sebagai berikut:</p><ol><li>{{penghadap1_name}} sebesar Rp. {{modal_penghadap1}} ({{modal_penghadap1_terbilang}});</li><li>{{penghadap2_name}} sebesar Rp. {{modal_penghadap2}} ({{modal_penghadap2_terbilang}}).</li></ol><p><strong>----------------------- Pasal 4.</strong></p><p><strong>Susunan Pengurus</strong></p><p>Untuk pertama kalinya Perseroan menunjuk dan mengangkat:</p><ol><li>{{direktur_name}} sebagai Direktur;</li><li>{{komisaris_name}} sebagai Komisaris.</li></ol><p>Yang bersangkutan bersedia dan menerima jabatan tersebut.</p><p><strong>----------------------- Pasal 5.</strong></p><p><strong>Jangka Waktu</strong></p><p>Perseroan didirikan untuk jangka waktu yang tidak terbatas, terhitung sejak tanggal akta ini ditandatangani.</p><p><strong>----------------------- Pasal 6.</strong></p><p><strong>Anggaran Dasar dan Ketentuan Lain</strong></p><p>Hal-hal yang belum diatur dalam akta ini akan diatur lebih lanjut dalam anggaran dasar Perseroan dan/atau berdasarkan keputusan Rapat Umum Pemegang Saham sesuai dengan peraturan perundang-undangan yang berlaku.</p><p><strong>----------------------- Pasal 7.</strong></p><p><strong>Pengesahan dan Pendaftaran</strong></p><p>Notaris akan mengajukan permohonan pengesahan badan hukum Perseroan ini kepada Menteri Hukum dan Hak Asasi Manusia Republik Indonesia sesuai dengan ketentuan yang berlaku.</p><p><strong>----------------------- Penutup</strong></p><p>– Para penghadap telah saya, Notaris, kenal.</p><p>– Akta ini dibuat di {{schedule_place}} pada hari, tanggal, bulan, dan tahun sebagaimana tersebut di awal akta ini.</p><p>Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p><p><strong>Saksi-saksi:</strong></p><ol><li>Nyonya ........................................</li><li>Nyonya ........................................</li></ol><p>Demikian akta ini dibuat sebagai alat bukti sah pendirian Perseroan Terbatas.</p>', NULL, NULL, NULL, NULL, '2025-09-22 15:47:56', '2025-09-22 15:48:24'),
(5, 1, 'Perjanjian Kerja', 'Kesepakatan antara pemberi kerja dan pekerja yang mengatur hak, kewajiban, serta syarat kerja.', '<h2 class=\"ql-align-center\">PERJANJIAN KERJA</h2><p class=\"ql-align-center\">Nomor : {{reference_number}}</p><p>– Pada hari ini, {{today}}</p><p>– tanggal .............................................................</p><p>– Pukul .................................................................</p><p>– Berhadapan dengan saya, {{notaris_name}}, Notaris di {{schedule_place}}, dengan dihadiri oleh para saksi yang saya, Notaris, kenal dan akan disebutkan nama-namanya pada bagian akhir akta ini:</p><p><strong>I. {{penghadap1_name}}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>selanjutnya disebut <strong>Pihak Pertama</strong> atau <strong>Pemberi Kerja</strong>.</p><p><strong>II. {{penghadap2_name}}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>selanjutnya disebut <strong>Pihak Kedua</strong> atau <strong>Pekerja</strong>.</p><p>Para penghadap menerangkan bahwa mereka dengan ini sepakat untuk membuat dan menandatangani Perjanjian Kerja dengan ketentuan-ketentuan sebagai berikut:</p><p><strong>----------------------- Pasal 1.</strong></p><p><strong>Jabatan dan Tugas</strong></p><p>Pihak Kedua bekerja pada Pihak Pertama dengan jabatan <strong>{{job_title}}</strong> dan bertanggung jawab melaksanakan tugas sesuai dengan arahan dan ketentuan perusahaan.</p><p><strong>----------------------- Pasal 2.</strong></p><p><strong>Waktu dan Tempat Kerja</strong></p><p>Pihak Kedua mulai bekerja sejak tanggal {{start_date}} dan ditempatkan di {{work_location}} dengan jam kerja sesuai kebijakan perusahaan.</p><p><strong>----------------------- Pasal 3.</strong></p><p><strong>Masa Kontrak</strong></p><p>Perjanjian kerja ini berlaku selama {{contract_duration}} terhitung sejak tanggal {{start_date}} sampai dengan tanggal {{end_date}}, dan dapat diperpanjang berdasarkan kesepakatan kedua belah pihak.</p><p><strong>----------------------- Pasal 4.</strong></p><p><strong>Gaji dan Fasilitas</strong></p><p>Pihak Pertama memberikan gaji sebesar Rp. {{salary}} ({{salary_in_words}}) per bulan kepada Pihak Kedua, dibayarkan setiap akhir bulan. Fasilitas lain dapat diberikan sesuai kebijakan perusahaan.</p><p><strong>----------------------- Pasal 5.</strong></p><p><strong>Kewajiban dan Hak</strong></p><p>Pihak Kedua berkewajiban menaati peraturan kerja, menjaga kerahasiaan data, dan melaksanakan tugas dengan penuh tanggung jawab. Pihak Pertama wajib memberikan hak-hak pekerja sesuai peraturan perundang-undangan ketenagakerjaan.</p><p><strong>----------------------- Pasal 6.</strong></p><p><strong>Pemutusan Hubungan Kerja</strong></p><p>Perjanjian kerja dapat berakhir apabila:</p><ol><li>Berakhirnya jangka waktu perjanjian kerja;</li><li>Salah satu pihak mengundurkan diri atau diberhentikan sesuai peraturan perusahaan;</li><li>Terjadi pelanggaran berat terhadap ketentuan perjanjian kerja atau peraturan yang berlaku.</li></ol><p><strong>----------------------- Pasal 7.</strong></p><p><strong>Penyelesaian Perselisihan</strong></p><p>Segala perselisihan yang timbul akibat pelaksanaan perjanjian kerja ini akan diselesaikan secara musyawarah untuk mufakat. Jika tidak tercapai, maka akan diselesaikan melalui mekanisme hukum sesuai ketentuan perundang-undangan.</p><p><strong>----------------------- Penutup</strong></p><p>Demikian perjanjian ini dibuat dalam rangkap dua, masing-masing memiliki kekuatan hukum yang sama, ditandatangani oleh kedua belah pihak di hadapan Notaris dan para saksi.</p><p><strong>Saksi-saksi:</strong></p><ol><li>Nyonya ........................................</li><li>Nyonya ........................................</li></ol><p>Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p>', NULL, NULL, NULL, NULL, '2025-09-09 10:00:00', '2025-10-07 12:47:29'),
(6, 1, 'Perubahan Anggaran Dasar', 'Perubahan data perseroan (nama, tujuan, modal, pengurus) berdasarkan keputusan RUPS.', '<h2 class=\"ql-align-center\">AKTA PERUBAHAN ANGGARAN DASAR PERSEROAN TERBATAS</h2><p class=\"ql-align-center\">Nomor : {{reference_number}}</p><p>– Pada hari ini, {{today}}</p><p>– tanggal .............................................................</p><p>– Pukul .................................................................</p><p>– Berhadapan dengan saya, {{notaris_name}}, Notaris di {{schedule_place}}, dengan dihadiri oleh para saksi yang saya, Notaris, kenal dan akan disebutkan nama-namanya pada bagian akhir akta ini:</p><p><strong>I. {{penghadap1_name}}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p><strong>II. {{penghadap2_name}}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>Kedua penghadap tersebut bertindak sebagai para pemegang saham dan pendiri <strong>Perseroan Terbatas PT. {{company_name}}</strong>, yang didirikan berdasarkan Akta Nomor {{akta_pendirian_nomor}} tanggal {{akta_pendirian_tanggal}} dibuat di hadapan {{notaris_pendirian}}, Notaris di {{notaris_pendirian_kota}}, dan telah memperoleh pengesahan dari Menteri Hukum dan Hak Asasi Manusia Republik Indonesia berdasarkan Surat Keputusan Nomor {{sk_menkumham_nomor}} tanggal {{sk_menkumham_tanggal}}.</p><p>Para penghadap dengan ini menerangkan bahwa dalam Rapat Umum Pemegang Saham Luar Biasa yang diselenggarakan pada tanggal {{rapat_tanggal}}, para pemegang saham Perseroan telah mengambil keputusan untuk melakukan perubahan terhadap Anggaran Dasar Perseroan, dengan ketentuan sebagai berikut:</p><p><strong>----------------------- Pasal 1.</strong></p><p><strong>Perubahan Nama dan Kedudukan</strong></p><p>Nama Perseroan yang semula <strong>PT. {{old_company_name}}</strong> diubah menjadi <strong>PT. {{new_company_name}}</strong>, dan berkedudukan di <strong>{{company_city}}</strong>.</p><p><strong>----------------------- Pasal 2.</strong></p><p><strong>Perubahan Maksud dan Tujuan</strong></p><p>Maksud dan tujuan Perseroan diubah menjadi untuk menjalankan kegiatan usaha di bidang <strong>{{new_business_field}}</strong>, serta kegiatan lain yang mendukungnya sesuai ketentuan peraturan perundang-undangan.</p><p><strong>----------------------- Pasal 3.</strong></p><p><strong>Perubahan Modal</strong></p><p>Modal dasar Perseroan diubah dari sebesar Rp. {{old_modal_dasar}} ({{old_modal_dasar_terbilang}}) menjadi sebesar Rp. {{new_modal_dasar}} ({{new_modal_dasar_terbilang}}), terbagi atas {{jumlah_saham}} ({{jumlah_saham_terbilang}}) saham dengan nilai nominal Rp. {{nilai_saham}},– ({{nilai_saham_terbilang}}) per saham.</p><p><strong>----------------------- Pasal 4.</strong></p><p><strong>Perubahan Susunan Pengurus</strong></p><p>Susunan pengurus Perseroan diubah dan ditetapkan menjadi sebagai berikut:</p><ol><li>{{direktur_name}} sebagai Direktur;</li><li>{{komisaris_name}} sebagai Komisaris.</li></ol><p><strong>----------------------- Pasal 5.</strong></p><p><strong>Ketentuan Lain</strong></p><p>Hal-hal lain dalam Anggaran Dasar Perseroan yang tidak diubah dengan akta ini tetap berlaku sebagaimana semula.</p><p><strong>----------------------- Pasal 6.</strong></p><p><strong>Pengesahan</strong></p><p>Perubahan Anggaran Dasar ini akan diajukan untuk memperoleh persetujuan dari Menteri Hukum dan Hak Asasi Manusia Republik Indonesia sesuai dengan ketentuan peraturan perundang-undangan yang berlaku.</p><p><strong>----------------------- Penutup</strong></p><p>– Para penghadap telah saya, Notaris, kenal.</p><p>– Akta ini dibuat di {{schedule_place}} pada hari, tanggal, bulan, dan tahun sebagaimana tersebut di awal akta ini.</p><p>Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p><p><strong>Saksi-saksi:</strong></p><ol><li>Nyonya ........................................</li><li>Nyonya ........................................</li></ol><p>Demikian akta ini dibuat untuk digunakan sebagaimana mestinya.</p>', NULL, NULL, NULL, NULL, '2025-09-09 10:00:00', '2025-10-07 12:27:17'),
(7, 2, 'Pendirian Rumah', 'Pendirian Rumah Orang', '<p>Tes asek</p>', NULL, NULL, NULL, NULL, '2025-10-27 20:13:18', '2025-10-27 20:13:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tracks`
--

CREATE TABLE `tracks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status_invite` enum('todo','pending','done','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `status_respond` enum('todo','pending','done','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `status_docs` enum('todo','pending','done','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `status_draft` enum('todo','pending','done','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `status_schedule` enum('todo','pending','done','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `status_sign` enum('todo','pending','done','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `status_print` enum('todo','pending','done','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tracks`
--

INSERT INTO `tracks` (`id`, `status_invite`, `status_respond`, `status_docs`, `status_draft`, `status_schedule`, `status_sign`, `status_print`, `created_at`, `updated_at`) VALUES
(1, 'done', 'done', 'done', 'done', 'done', 'todo', 'pending', '2025-10-27 20:41:29', '2025-10-27 20:46:56'),
(2, 'done', 'done', 'done', 'done', 'done', 'done', 'done', '2025-11-02 18:25:05', '2025-11-03 20:45:03'),
(3, 'done', 'todo', 'pending', 'pending', 'pending', 'pending', 'pending', '2025-11-03 19:02:54', '2025-11-03 19:02:54'),
(4, 'done', 'done', 'done', 'done', 'done', 'todo', 'pending', '2025-11-05 20:47:00', '2025-11-06 22:30:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_verification` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes_verification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verify_key` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expired_key` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `google_id`, `file_avatar_path`, `file_avatar`, `telepon`, `gender`, `email_verified_at`, `password`, `address`, `city`, `province`, `postal_code`, `status_verification`, `notes_verification`, `verify_key`, `expired_key`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', 'admin@gmail.com', NULL, NULL, NULL, '081200000000', 'male', '2025-09-08 08:28:18', '$2y$12$Argw2543hZySEd/17h7RA.pTwcvIYKaB028shg0xrneYNGvb65pse', 'Jl. Merpati No. 10', 'Jakarta', 'DKI Jakarta', '10220', 'approved', NULL, '5HSAKDU', NULL, NULL, '2025-10-27 19:26:07', '2025-10-27 19:26:07'),
(2, 3, 'Adam Aditya', 'adam@gmail.com', NULL, NULL, NULL, '081200000001', 'male', '2025-09-08 07:36:15', '$2y$12$Z/mUJgzsMYDlcUr4kki4A.SKcfyoRxexH1l1CxB6fQTKW01BDMNlu', 'Jl. Melati No. 1', 'Jakarta', 'DKI Jakarta', '10110', 'approved', NULL, 'QK4R08F', NULL, NULL, '2025-10-27 19:26:07', '2025-10-27 19:26:07'),
(3, 2, 'DEVANO ALIF RAMADHAN', 'devanorama123@gmail.com', '103993609786336409326', NULL, NULL, '081200000002', 'male', '2025-09-08 07:51:53', '$2y$12$EiwqBTOGQ8rpWZ/iXI2P8e3TAv2OfUpocGH6A9qMAi8JULl0B6Dym', 'Jl. Kenanga No. 2', 'Bandung', 'Jawa Barat', '40111', 'approved', NULL, '7RZWDO0', NULL, NULL, '2025-10-27 19:26:07', '2025-10-29 20:45:14'),
(4, 2, 'Iwang', 'iwang@gmail.com', NULL, NULL, NULL, '081200000003', 'male', '2025-09-07 17:00:00', '$2y$12$7ORwnbZwLHOpoSSmeGMVKuChQI8Llb2f5m9tUfOpMd59pCqH2xqj.', 'Jl. Cendana No. 3', 'Surabaya', 'Jawa Timur', '60293', 'approved', NULL, 'TOCTPNP', NULL, NULL, '2025-10-27 19:26:08', '2025-10-27 19:26:08'),
(5, 2, 'Yasmin Zakiyah Firmasyah', 'yasmin@gmail.com', NULL, NULL, NULL, '081200000004', 'female', '2025-09-07 17:00:00', '$2y$12$EkPB9UHgz3uFQKxY24hOTOh4twx4r7ogG8fAxdMzpOdn.DDGGR8ua', 'Jl. Flamboyan No. 4', 'Yogyakarta', 'DI Yogyakarta', '55281', 'approved', NULL, 'A9QZJ9O', NULL, NULL, '2025-10-27 19:26:08', '2025-10-27 19:26:08'),
(6, 2, 'Dhika', 'dhika@gmail.com', NULL, NULL, NULL, '081200000005', 'male', '2025-09-07 17:00:00', '$2y$12$205lyLlbfN/hC/i9pt1NB.5e7qr57IHwn2e0LBwjMSYqrRFgQk1v.', 'Jl. Anggrek No. 5', 'Semarang', 'Jawa Tengah', '50135', 'approved', NULL, 'IDEZVZ5', NULL, NULL, '2025-10-27 19:26:09', '2025-10-27 19:26:09'),
(7, 2, 'Firashinta', 'firashinta@gmail.com', NULL, NULL, NULL, '08391118249922', 'female', '2025-10-27 19:51:32', '$2y$12$htmtjyscrGs6RF3Cbtw4l.N05rnnRCBT07ekeeTr6SSbfKcKQdMGu', 'Bratang 002/001 Sawocangkring, Wonoayu, Surabaya', 'KABUPATEN SURABAYA', 'JAWA TIMUR', NULL, 'approved', NULL, NULL, NULL, NULL, '2025-10-27 19:40:52', '2025-10-27 20:39:52');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_track_id_foreign` (`track_id`),
  ADD KEY `idx_activity_notaris` (`user_notaris_id`),
  ADD KEY `idx_activity_deed` (`deed_id`),
  ADD KEY `idx_activity_tracking` (`tracking_code`);

--
-- Indeks untuk tabel `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blogs_user_id_created_at_index` (`user_id`,`created_at`);

--
-- Indeks untuk tabel `blog_category`
--
ALTER TABLE `blog_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blog_category_blog_id_category_blog_id_unique` (`blog_id`,`category_blog_id`),
  ADD KEY `blog_category_category_blog_id_index` (`category_blog_id`);

--
-- Indeks untuk tabel `category_blogs`
--
ALTER TABLE `category_blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_blogs_name_unique` (`name`);

--
-- Indeks untuk tabel `client_activities`
--
ALTER TABLE `client_activities`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `client_activity`
--
ALTER TABLE `client_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_activity_user_id_foreign` (`user_id`),
  ADD KEY `client_activity_activity_id_foreign` (`activity_id`);

--
-- Indeks untuk tabel `client_drafts`
--
ALTER TABLE `client_drafts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_drafts_user_id_foreign` (`user_id`),
  ADD KEY `client_drafts_draft_deed_id_foreign` (`draft_deed_id`);

--
-- Indeks untuk tabel `deeds`
--
ALTER TABLE `deeds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deeds_user_notaris_id_foreign` (`user_notaris_id`);

--
-- Indeks untuk tabel `deed_requirement_templates`
--
ALTER TABLE `deed_requirement_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deed_requirement_templates_deed_id_foreign` (`deed_id`);

--
-- Indeks untuk tabel `document_requirements`
--
ALTER TABLE `document_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_requirements_activity_notaris_id_foreign` (`activity_notaris_id`),
  ADD KEY `document_requirements_user_id_foreign` (`user_id`),
  ADD KEY `document_requirements_requirement_id_foreign` (`requirement_id`),
  ADD KEY `document_requirements_deed_requirement_template_id_index` (`deed_requirement_template_id`);

--
-- Indeks untuk tabel `draft_deeds`
--
ALTER TABLE `draft_deeds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `draft_deeds_activity_id_foreign` (`activity_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `identities`
--
ALTER TABLE `identities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `identities_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `main_value_deeds`
--
ALTER TABLE `main_value_deeds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `main_value_deeds_deed_id_foreign` (`deed_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `requirements`
--
ALTER TABLE `requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requirements_activity_id_foreign` (`activity_id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedules_activity_id_foreign` (`activity_id`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `signatures`
--
ALTER TABLE `signatures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `signatures_draft_deed_id_foreign` (`draft_deed_id`),
  ADD KEY `signatures_activity_id_foreign` (`activity_id`);

--
-- Indeks untuk tabel `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `templates_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `tracks`
--
ALTER TABLE `tracks`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_google_id_unique` (`google_id`),
  ADD KEY `users_role_id_foreign` (`role_id`),
  ADD KEY `users_verify_key_index` (`verify_key`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activity`
--
ALTER TABLE `activity`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `blog_category`
--
ALTER TABLE `blog_category`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `category_blogs`
--
ALTER TABLE `category_blogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `client_activities`
--
ALTER TABLE `client_activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `client_activity`
--
ALTER TABLE `client_activity`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `client_drafts`
--
ALTER TABLE `client_drafts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `deeds`
--
ALTER TABLE `deeds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `deed_requirement_templates`
--
ALTER TABLE `deed_requirement_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `document_requirements`
--
ALTER TABLE `document_requirements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `draft_deeds`
--
ALTER TABLE `draft_deeds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `identities`
--
ALTER TABLE `identities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `main_value_deeds`
--
ALTER TABLE `main_value_deeds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT untuk tabel `partners`
--
ALTER TABLE `partners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `requirements`
--
ALTER TABLE `requirements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `signatures`
--
ALTER TABLE `signatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT untuk tabel `templates`
--
ALTER TABLE `templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `tracks`
--
ALTER TABLE `tracks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `activity`
--
ALTER TABLE `activity`
  ADD CONSTRAINT `activity_deed_id_foreign` FOREIGN KEY (`deed_id`) REFERENCES `deeds` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `activity_track_id_foreign` FOREIGN KEY (`track_id`) REFERENCES `tracks` (`id`),
  ADD CONSTRAINT `activity_user_notaris_id_foreign` FOREIGN KEY (`user_notaris_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `blog_category`
--
ALTER TABLE `blog_category`
  ADD CONSTRAINT `blog_category_blog_id_foreign` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_category_category_blog_id_foreign` FOREIGN KEY (`category_blog_id`) REFERENCES `category_blogs` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `client_activity`
--
ALTER TABLE `client_activity`
  ADD CONSTRAINT `client_activity_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_activity_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `client_drafts`
--
ALTER TABLE `client_drafts`
  ADD CONSTRAINT `client_drafts_draft_deed_id_foreign` FOREIGN KEY (`draft_deed_id`) REFERENCES `draft_deeds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_drafts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `deeds`
--
ALTER TABLE `deeds`
  ADD CONSTRAINT `deeds_user_notaris_id_foreign` FOREIGN KEY (`user_notaris_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `deed_requirement_templates`
--
ALTER TABLE `deed_requirement_templates`
  ADD CONSTRAINT `deed_requirement_templates_deed_id_foreign` FOREIGN KEY (`deed_id`) REFERENCES `deeds` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `document_requirements`
--
ALTER TABLE `document_requirements`
  ADD CONSTRAINT `document_requirements_activity_notaris_id_foreign` FOREIGN KEY (`activity_notaris_id`) REFERENCES `activity` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_requirements_deed_requirement_template_id_foreign` FOREIGN KEY (`deed_requirement_template_id`) REFERENCES `deed_requirement_templates` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `document_requirements_requirement_id_foreign` FOREIGN KEY (`requirement_id`) REFERENCES `requirements` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `document_requirements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `draft_deeds`
--
ALTER TABLE `draft_deeds`
  ADD CONSTRAINT `draft_deeds_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `identities`
--
ALTER TABLE `identities`
  ADD CONSTRAINT `identities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `main_value_deeds`
--
ALTER TABLE `main_value_deeds`
  ADD CONSTRAINT `main_value_deeds_deed_id_foreign` FOREIGN KEY (`deed_id`) REFERENCES `deeds` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `requirements`
--
ALTER TABLE `requirements`
  ADD CONSTRAINT `requirements_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `signatures`
--
ALTER TABLE `signatures`
  ADD CONSTRAINT `signatures_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `signatures_draft_deed_id_foreign` FOREIGN KEY (`draft_deed_id`) REFERENCES `draft_deeds` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `templates`
--
ALTER TABLE `templates`
  ADD CONSTRAINT `templates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
