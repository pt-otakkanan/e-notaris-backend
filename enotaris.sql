-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Sep 2025 pada 05.41
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
(1, 1, 1, 1, 1, 'ACT-IFJJG3I4', '2025-09-09 02:55:42', '2025-09-09 02:55:42', 'Pendirian CV Kode Muda'),
(2, 2, 1, 1, 1, 'ACT-I1OCCHEE', '2025-09-09 03:37:15', '2025-09-09 03:37:15', 'Pendirian CV Otak Kanan');

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
(3, 2, 1, 'approved', 1, '2025-09-09 02:55:57', '2025-09-09 02:55:57'),
(4, 4, 1, 'approved', 2, '2025-09-09 02:55:57', '2025-09-09 02:55:57'),
(6, 5, 2, 'approved', 1, '2025-09-09 03:37:15', '2025-09-09 03:37:15'),
(7, 3, 2, 'approved', 2, '2025-09-09 03:37:15', '2025-09-09 03:37:15');

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
(1, 1, 'Pendirian CV', 'Akta untuk mendirikan CV', '2025-09-09 02:44:29', '2025-09-09 02:44:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `document_requirements`
--

CREATE TABLE `document_requirements` (
  `id` bigint(20) UNSIGNED NOT NULL,
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

INSERT INTO `document_requirements` (`id`, `activity_notaris_id`, `user_id`, `requirement_id`, `requirement_name`, `is_file_snapshot`, `value`, `file`, `file_path`, `status_approval`, `created_at`, `updated_at`) VALUES
(3, 1, 2, 2, 'NIK', 0, '213123213222', NULL, NULL, 'pending', '2025-09-09 03:33:53', '2025-09-09 03:34:30'),
(4, 1, 4, 2, 'NIK', 0, '9999999999', NULL, NULL, 'pending', '2025-09-09 03:33:53', '2025-09-09 03:35:05'),
(5, 1, 2, 3, 'NPWP', 0, '34234324323', NULL, NULL, 'pending', '2025-09-09 03:34:03', '2025-09-09 03:34:37'),
(6, 1, 4, 3, 'NPWP', 0, '876543344566', NULL, NULL, 'pending', '2025-09-09 03:34:03', '2025-09-09 03:35:13'),
(7, 1, 2, 4, 'Surat Kuasa', 1, NULL, 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1757414092/enotaris/activities/1/requirements/2/req_1757414086_U8Ol45Ay.jpg', 'enotaris/activities/1/requirements/2/req_1757414086_U8Ol45Ay', 'pending', '2025-09-09 03:34:16', '2025-09-09 03:34:52'),
(8, 1, 4, 4, 'Surat Kuasa', 1, NULL, NULL, NULL, 'pending', '2025-09-09 03:34:16', '2025-09-09 03:34:16'),
(9, 2, 5, 5, 'NIK', 0, NULL, NULL, NULL, 'pending', '2025-09-09 03:38:23', '2025-09-09 03:38:23'),
(10, 2, 3, 5, 'NIK', 0, NULL, NULL, NULL, 'pending', '2025-09-09 03:38:23', '2025-09-09 03:38:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `draft_deeds`
--

CREATE TABLE `draft_deeds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `custom_value_template` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reading_schedule` timestamp NULL DEFAULT NULL,
  `status_approval` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `draft_deeds`
--

INSERT INTO `draft_deeds` (`id`, `activity_id`, `custom_value_template`, `reading_schedule`, `status_approval`, `file`, `file_path`, `created_at`, `updated_at`) VALUES
(1, 1, '<h2 class=\"ql-align-center\">PERJANJIAN SEWA MENYEWA</h2><p class=\"ql-align-center\">Nomor : {activity_code}</p><p>– Pada hari ini, {today}</p><p>– tanggal .............................................................</p><p>– Pukul .................................................................</p><p>– Berhadapan dengan saya ini, {notaris_name}, Notaris di {schedule_place}, dengan dihadiri oleh para saksi yang saya, Notaris, kenal dan akan disebutkan nama-namanya pada bahagian akhir akta ini:</p><p><strong>I. Tuan {penghadap1_name}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p><strong>II. Tuan {penghadap2_name}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>– menurut keterangannya dalam hal ini bertindak dalam jabatannya selaku Presiden Direktur dari Perseroan Terbatas PT. .........., berkedudukan di Jakarta yang anggaran dasarnya beserta perubahannya telah mendapat persetujuan dari Menteri Kehakiman dan Hak Asasi Manusia berturut-turut:</p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>selanjutnya disebut: <strong>Pihak Kedua</strong> atau <strong>Penyewa</strong>.</p><p>– Para penghadap telah saya, Notaris, kenal.</p><p>– Para penghadap menerangkan terlebih dahulu:</p><p>– bahwa Pihak Pertama adalah pemilik dari bangunan Rumah Toko (Ruko) yang hendak disewakan kepada Pihak Kedua yang akan disebutkan di bawah ini dan Pihak Kedua menerangkan menyewa dari Pihak Pertama berupa:</p><p>– 1 (satu) unit bangunan Rumah Toko (Ruko) berlantai 3 (tiga) berikut turutannya, lantai keramik, dinding tembok, atap dak, aliran listrik sebesar 2.200 Watt, dilengkapi air dari jet pump, berdiri di atas sebidang tanah Sertifikat HGB Nomor: ............ seluas ...... m² (....................................), penerbitan sertifikat tanggal ..........................., tercantum atas nama .................. yang telah diuraikan dalam Gambar Situasi tanggal ............ nomor ............; Sertifikat tanah diterbitkan oleh Kantor Pertanahan Kabupaten Bekasi, terletak di Provinsi Jawa Barat, Kabupaten Bekasi, Kecamatan Cibitung, Desa Ganda Mekar, setempat dikenal sebagai Mega Mall MM.2100 Blok B Nomor 8.</p><p>– Berdasarkan keterangan-keterangan tersebut di atas, kedua belah pihak sepakat membuat perjanjian sewa-menyewa dengan syarat-syarat dan ketentuan-ketentuan sebagai berikut:</p><p><strong>----------------------- Pasal 1.</strong></p><p>Perjanjian sewa-menyewa ini berlangsung untuk jangka waktu 2 (dua) tahun terhitung sejak tanggal ............ sampai dengan tanggal ............</p><p>– Penyerahan Ruko akan dilakukan dalam keadaan kosong/tidak dihuni pada tanggal .................. dengan penyerahan semua kunci-kuncinya.</p><p><strong>----------------------- Pasal 2.</strong></p><p>– Uang kontrak sewa disepakati sebesar Rp. ............ (....................................) untuk 2 (dua) tahun masa sewa.</p><p>– Jumlah uang sewa sebesar Rp. ............ (....................................) tersebut dibayar oleh Pihak Kedua kepada Pihak Pertama pada saat penandatanganan akta ini atau pada tanggal .................. dengan kwitansi tersendiri, dan akta ini berlaku sebagai tanda penerimaan yang sah.</p><p><strong>----------------------- Pasal 3.</strong></p><p>– Pihak Kedua hanya akan menggunakan yang disewakan dalam akta ini sebagai tempat kegiatan perkantoran/usaha.</p><p>– Jika diperlukan, Pihak Pertama memberikan surat rekomendasi/keterangan yang diperlukan Pihak Kedua sepanjang tidak melanggar hukum.</p><p>– Pihak Kedua wajib mentaati peraturan-peraturan pihak yang berwajib dan menjamin Pihak Pertama tidak mendapat teguran/tuntutan apapun karenanya.</p><p><strong>----------------------- Pasal 4.</strong></p><p>– Hanya dengan persetujuan tertulis Pihak Pertama, Pihak Kedua boleh mengadakan perubahan/penambahan pada bangunan; seluruh biaya dan tanggung jawab pada Pihak Kedua, dan pada akhir masa kontrak menjadi hak Pihak Pertama.</p><p>– Penyerahan nyata dari yang disewakan oleh Pihak Pertama kepada Pihak Kedua dilakukan pada tanggal .................. dengan penyerahan semua kunci-kunci.</p><p><strong>----------------------- Pasal 5.</strong></p><p>Pihak Pertama memberi izin kepada Pihak Kedua untuk pemasangan/penambahan antara lain:</p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Sekat-sekat pada ruangan;</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Antena radio/CD;</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Line telepon;</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Air Conditioner (AC);</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Penambahan daya listrik;</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Saluran fax;</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Internet;</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>TV Kabel;</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Shower;</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Penggantian W/C;</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Katrol pengangkut barang lantai 1–3;</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Peralatan keamanan;</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Peralatan pendukung usaha (rak/mesin) tanpa merusak struktur bangunan.</li></ol><p>– Setelah masa kontrak berakhir, Pihak Kedua mengembalikan seperti keadaan semula dengan biaya Pihak Kedua.</p><p>– Pihak Kedua boleh mengganti kunci ruangan di dalam bangunan (kecuali pintu utama); pada akhir masa kontrak, kunci-kunci diserahkan ke Pihak Pertama.</p><p>– Pihak Pertama menjamin yang disewakan adalah miliknya dan bebas dari tuntutan pihak lain.</p><p>– Selama masa sewa, Pihak Pertama boleh memeriksa bangunan sewaktu-waktu.</p><p><strong>----------------------- Pasal 6.</strong></p><p>– Selama masa kontrak, pembayaran langganan listrik/air/telepon dan kewajiban lain terkait pemakaian dibayar Pihak Kedua hingga bulan terakhir dengan bukti pembayaran setiap bulan.</p><p>– Pihak Pertama membayar Pajak Bumi dan Bangunan (PBB) untuk objek sewa.</p><p><strong>----------------------- Pasal 7.</strong></p><p>– Pihak Kedua wajib memelihara yang disewa dengan baik; kerusakan karena kelalaian diperbaiki atas biaya Pihak Kedua.</p><p>– Apabila terjadi force majeure (kebakaran—kecuali kelalaian Pihak Kedua—sabotase, badai, banjir, gempa) sehingga objek musnah, para pihak dibebaskan dari tuntutan.</p><p><strong>----------------------- Pasal 8.</strong></p><p>– Pihak Pertama menjamin tidak ada tuntutan atau gangguan dari pihak lain atas yang disewa selama kontrak.</p><p><strong>----------------------- Pasal 9.</strong></p><p>Pihak Kedua, dengan persetujuan tertulis Pihak Pertama, boleh mengalihkan/memindahkan hak kontrak pada pihak lain, sebagian maupun seluruhnya, selama masa kontrak berlaku.</p><p><strong>----------------------- Pasal 10.</strong></p><p>Pihak Kedua wajib memberi pemberitahuan mengenai berakhir/akan diperpanjangnya kontrak kepada Pihak Pertama selambat-lambatnya 2 (dua) bulan sebelum berakhir.</p><p><strong>----------------------- Pasal 11.</strong></p><p>Pada saat berakhirnya kontrak dan tidak ada perpanjangan, Pihak Kedua menyerahkan kembali objek sewa dalam keadaan kosong, terpelihara baik, dengan semua kunci pada tanggal ..................</p><p>Apabila terlambat, Pihak Kedua dikenakan denda sebesar Rp. 27.500,- per hari selama 7 (tujuh) hari pertama; jika masih tidak diserahkan, Pihak Kedua memberi kuasa kepada Pihak Pertama (dengan hak substitusi) untuk melakukan pengosongan dengan bantuan pihak berwajib, atas biaya dan risiko Pihak Kedua.</p><p><strong>----------------------- Pasal 12.</strong></p><p>Selama masa kontrak belum berakhir, perjanjian ini tidak berakhir karena:</p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Meninggalnya salah satu pihak;</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Pihak Pertama mengalihkan hak milik atas objek sewa kepada pihak lain;</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Dalam hal salah satu pihak meninggal dunia, ahli waris/penggantinya wajib melanjutkan perjanjian sampai berakhir; pemilik baru tunduk pada seluruh ketentuan akta ini.</li></ol><p><strong>----------------------- Pasal 13.</strong></p><p>Untuk menjamin pembayaran listrik, air, telepon, keamanan, dan kewajiban lain bulan terakhir, Pihak Kedua menyerahkan uang jaminan sebesar Rp. 2.000.000,- (dua juta rupiah) pada saat penyerahan kunci, dengan kwitansi tersendiri. Kelebihan dikembalikan Pihak Pertama; kekurangan ditambah oleh Pihak Kedua.</p><p><strong>----------------------- Pasal 14.</strong></p><p>Hal-hal yang belum cukup diatur akan dibicarakan kemudian secara musyawarah untuk mufakat.</p><p><strong>----------------------- Pasal 15.</strong></p><p>Pajak-pajak yang mungkin ada terkait akta ini dibayar oleh Pihak Kedua untuk dan atas nama Pihak Pertama.</p><p><strong>----------------------- Pasal 16.</strong></p><p>Biaya-biaya yang berkaitan dengan akta ini dibayar dan menjadi tanggungan Pihak Pertama.</p><p><strong>----------------------- Pasal 17.</strong></p><p>Kedua belah pihak memilih domisili hukum yang sah di Kepaniteraan Pengadilan Negeri Bekasi.</p><p><strong>DEMIKIAN AKTA INI</strong></p><p>– Dibuat dan diresmikan di Bekasi pada hari dan tanggal sebagaimana awal akta ini, dengan dihadiri oleh:</p><ol><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Nyonya ........................................</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Nyonya ........................................</li></ol><p>Keduanya Karyawan Kantor Notaris, sebagai saksi-saksi.</p><p>– Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p><p><br></p><p>{signatures_block}</p>', NULL, 'pending', 'https://res.cloudinary.com/dr1qyzdld/image/upload/v1757560626/enotaris/activities/1/drafts/draft_pdf_20250911031705.pdf', 'enotaris/activities/1/drafts/draft_pdf_20250911031705', '2025-09-09 02:55:42', '2025-09-10 20:17:06'),
(2, 2, NULL, NULL, 'pending', NULL, NULL, '2025-09-09 03:37:15', '2025-09-09 03:37:15');

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `file_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `identities`
--

INSERT INTO `identities` (`id`, `user_id`, `ktp`, `file_ktp`, `file_ktp_path`, `file_kk`, `file_kk_path`, `npwp`, `file_npwp`, `file_npwp_path`, `ktp_notaris`, `file_ktp_notaris`, `file_ktp_notaris_path`, `file_sign`, `file_sign_path`, `created_at`, `updated_at`, `file_photo`, `file_photo_path`) VALUES
(1, 1, '3310713559979411', 'https://via.placeholder.com/800x500.png?text=KTP%20User%201', 'seed/users/1/identity/ktp.jpg', 'https://via.placeholder.com/800x500.png?text=KK%20User%201', 'seed/users/1/identity/kk.jpg', '411989026882951', 'https://via.placeholder.com/800x500.png?text=NPWP%20User%201', 'seed/users/1/identity/npwp.jpg', '0909233487889531', 'https://via.placeholder.com/800x500.png?text=KTP%20NOTARIS%20User%201', 'seed/users/1/identity/ktp_notaris.jpg', 'https://via.placeholder.com/800x500.png?text=SIGN%20User%201', 'seed/users/1/identity/sign.png', '2025-09-09 02:43:11', '2025-09-09 02:43:11', 'https://via.placeholder.com/800x500.png?text=PHOTO%20User%201', 'seed/users/1/identity/photo.jpg'),
(2, 2, '3359099026159586', 'https://via.placeholder.com/800x500.png?text=KTP%20User%202', 'seed/users/2/identity/ktp.jpg', 'https://via.placeholder.com/800x500.png?text=KK%20User%202', 'seed/users/2/identity/kk.jpg', '893637576268384', 'https://via.placeholder.com/800x500.png?text=NPWP%20User%202', 'seed/users/2/identity/npwp.jpg', NULL, 'https://via.placeholder.com/800x500.png?text=KTP%20NOTARIS%20User%202', 'seed/users/2/identity/ktp_notaris.jpg', 'https://via.placeholder.com/800x500.png?text=SIGN%20User%202', 'seed/users/2/identity/sign.png', '2025-09-09 02:43:11', '2025-09-09 02:43:11', 'https://via.placeholder.com/800x500.png?text=PHOTO%20User%202', 'seed/users/2/identity/photo.jpg'),
(3, 3, '3853782001755457', 'https://via.placeholder.com/800x500.png?text=KTP%20User%203', 'seed/users/3/identity/ktp.jpg', 'https://via.placeholder.com/800x500.png?text=KK%20User%203', 'seed/users/3/identity/kk.jpg', '757163820097367', 'https://via.placeholder.com/800x500.png?text=NPWP%20User%203', 'seed/users/3/identity/npwp.jpg', NULL, 'https://via.placeholder.com/800x500.png?text=KTP%20NOTARIS%20User%203', 'seed/users/3/identity/ktp_notaris.jpg', 'https://via.placeholder.com/800x500.png?text=SIGN%20User%203', 'seed/users/3/identity/sign.png', '2025-09-09 02:43:12', '2025-09-09 02:43:12', 'https://via.placeholder.com/800x500.png?text=PHOTO%20User%203', 'seed/users/3/identity/photo.jpg'),
(4, 4, '7688044810248873', 'https://via.placeholder.com/800x500.png?text=KTP%20User%204', 'seed/users/4/identity/ktp.jpg', 'https://via.placeholder.com/800x500.png?text=KK%20User%204', 'seed/users/4/identity/kk.jpg', '462208604039640', 'https://via.placeholder.com/800x500.png?text=NPWP%20User%204', 'seed/users/4/identity/npwp.jpg', NULL, 'https://via.placeholder.com/800x500.png?text=KTP%20NOTARIS%20User%204', 'seed/users/4/identity/ktp_notaris.jpg', 'https://via.placeholder.com/800x500.png?text=SIGN%20User%204', 'seed/users/4/identity/sign.png', '2025-09-09 02:43:12', '2025-09-09 02:43:12', 'https://via.placeholder.com/800x500.png?text=PHOTO%20User%204', 'seed/users/4/identity/photo.jpg'),
(5, 5, '6682229784362483', 'https://via.placeholder.com/800x500.png?text=KTP%20User%205', 'seed/users/5/identity/ktp.jpg', 'https://via.placeholder.com/800x500.png?text=KK%20User%205', 'seed/users/5/identity/kk.jpg', '428905851937484', 'https://via.placeholder.com/800x500.png?text=NPWP%20User%205', 'seed/users/5/identity/npwp.jpg', NULL, 'https://via.placeholder.com/800x500.png?text=KTP%20NOTARIS%20User%205', 'seed/users/5/identity/ktp_notaris.jpg', 'https://via.placeholder.com/800x500.png?text=SIGN%20User%205', 'seed/users/5/identity/sign.png', '2025-09-09 02:43:12', '2025-09-09 02:43:12', 'https://via.placeholder.com/800x500.png?text=PHOTO%20User%205', 'seed/users/5/identity/photo.jpg'),
(6, 6, '8517526596212864', 'https://via.placeholder.com/800x500.png?text=KTP%20User%206', 'seed/users/6/identity/ktp.jpg', 'https://via.placeholder.com/800x500.png?text=KK%20User%206', 'seed/users/6/identity/kk.jpg', '649319340539792', 'https://via.placeholder.com/800x500.png?text=NPWP%20User%206', 'seed/users/6/identity/npwp.jpg', NULL, 'https://via.placeholder.com/800x500.png?text=KTP%20NOTARIS%20User%206', 'seed/users/6/identity/ktp_notaris.jpg', 'https://via.placeholder.com/800x500.png?text=SIGN%20User%206', 'seed/users/6/identity/sign.png', '2025-09-09 02:43:13', '2025-09-09 02:43:13', 'https://via.placeholder.com/800x500.png?text=PHOTO%20User%206', 'seed/users/6/identity/photo.jpg');

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
(36, '2025_09_10_044720_create_templates_table', 2),
(37, '2025_09_10_093452_add_name_to_deed_templates_table', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
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
(1, 'App\\Models\\User', 1, 'user-token', '5a4970b06aeb5d3092a2048cd51ddd28f988127b8ee42091ae9a8d2690643ec3', '[\"notaris\"]', '2025-09-09 03:13:15', '2025-09-09 03:14:03', '2025-09-09 02:44:04', '2025-09-09 03:13:15'),
(3, 'App\\Models\\User', 1, 'user-token', 'd8ad8091aa83071608fa5b7dd0698b6d646b617bb525adf8c7936ac202bb7956', '[\"notaris\"]', '2025-09-09 04:06:36', '2025-09-09 04:06:39', '2025-09-09 03:36:39', '2025-09-09 04:06:36'),
(4, 'App\\Models\\User', 1, 'user-token', '6b0f43859af6cd7330dd49fbe5409a352f04ac1f5d20264321dcf87adcbc9b84', '[\"notaris\"]', '2025-09-09 04:24:17', '2025-09-09 04:40:43', '2025-09-09 04:10:43', '2025-09-09 04:24:17'),
(5, 'App\\Models\\User', 1, 'user-token', '64e7ef9e27cd1f039ed6cd4d64aa4a2b0bea22e086346f442d44856a1af83ad8', '[\"notaris\"]', '2025-09-09 05:03:56', '2025-09-09 05:33:05', '2025-09-09 05:03:05', '2025-09-09 05:03:56'),
(6, 'App\\Models\\User', 1, 'user-token', '7847e39d85db57c8067ca6e513a17bb68da1663c26278bbf17942625e8d43779', '[\"notaris\"]', '2025-09-09 18:12:46', '2025-09-09 18:13:49', '2025-09-09 17:43:49', '2025-09-09 18:12:46'),
(7, 'App\\Models\\User', 1, 'user-token', 'cd2bec26afd59686e517b13afe5ede46bbcb53ae4a43aab8e10f998b2191422c', '[\"notaris\"]', '2025-09-09 18:42:25', '2025-09-09 18:45:03', '2025-09-09 18:15:03', '2025-09-09 18:42:25'),
(8, 'App\\Models\\User', 1, 'user-token', '61ddbc63078b0d012f265ed6b101b0f53c5809db54611b4c4908396f1dada7ee', '[\"notaris\"]', '2025-09-09 19:23:17', '2025-09-09 19:23:20', '2025-09-09 18:53:20', '2025-09-09 19:23:17'),
(9, 'App\\Models\\User', 1, 'user-token', '06baf2d8bb044dee5a5f04d8fa0b80b23c410adab0ab1cd6332ae562886d3f70', '[\"notaris\"]', '2025-09-09 19:38:49', '2025-09-09 19:54:51', '2025-09-09 19:24:51', '2025-09-09 19:38:49'),
(10, 'App\\Models\\User', 1, 'user-token', '559a1ea4adbd424bf149c9378e8baa1c26900395ad8942fbd40863226aebe4d1', '[\"notaris\"]', '2025-09-09 20:28:29', '2025-09-09 20:32:29', '2025-09-09 20:02:29', '2025-09-09 20:28:29'),
(11, 'App\\Models\\User', 1, 'user-token', 'd53c2b1c9a8c6abc744eadf60db2049d8f65583172d1328d7c2276e6d06bd6cd', '[\"notaris\"]', '2025-09-09 20:43:44', '2025-09-09 21:04:21', '2025-09-09 20:34:21', '2025-09-09 20:43:44'),
(12, 'App\\Models\\User', 1, 'user-token', 'e06da4c65140ebbc8b555ad1f9f26d816641a3daa398b3dd7249ac5e20758161', '[\"notaris\"]', '2025-09-09 21:36:51', '2025-09-09 21:54:09', '2025-09-09 21:24:09', '2025-09-09 21:36:51'),
(13, 'App\\Models\\User', 1, 'user-token', '3936c9bf2fd260b1e6394bbd9aa07e95b6235245f1b5ed750ccf8ca08645ae0d', '[\"notaris\"]', '2025-09-09 22:32:27', '2025-09-09 22:36:29', '2025-09-09 22:06:29', '2025-09-09 22:32:27'),
(14, 'App\\Models\\User', 1, 'user-token', '7591bec8b254d58b7fb18b8531d608f027da44c41958d4c5d1c84a6e99dc74c3', '[\"notaris\"]', '2025-09-10 03:01:28', '2025-09-10 03:13:21', '2025-09-10 02:43:21', '2025-09-10 03:01:28'),
(15, 'App\\Models\\User', 1, 'user-token', '47f04840a545c9aae60ba26b2276e4819854c82b8b8a4ae9166dfacc643f96ee', '[\"notaris\"]', '2025-09-10 03:05:51', '2025-09-10 03:31:36', '2025-09-10 03:01:36', '2025-09-10 03:05:51'),
(16, 'App\\Models\\User', 1, 'user-token', 'f1bb9fc684b7d1aa4d0d43c6e467f244b35ddec943edad29f50b34bbbc80bf81', '[\"notaris\"]', '2025-09-10 03:25:57', '2025-09-10 03:36:30', '2025-09-10 03:06:30', '2025-09-10 03:25:57'),
(17, 'App\\Models\\User', 1, 'user-token', '4a06364d6d0dba4942c509ea52084d9645ebb2f710112f6ac68de45b2a403b07', '[\"notaris\"]', '2025-09-10 03:53:36', '2025-09-10 04:10:53', '2025-09-10 03:40:53', '2025-09-10 03:53:36'),
(18, 'App\\Models\\User', 1, 'user-token', '5352cdd338b4a9b3cbfc691e6161879376fe87a67bf9d0667b40051a707ddc52', '[\"notaris\"]', '2025-09-10 18:15:14', '2025-09-10 18:15:50', '2025-09-10 17:45:51', '2025-09-10 18:15:14'),
(19, 'App\\Models\\User', 1, 'user-token', 'd4f4c286b99b7f07f41cd3cc5f06b25b9d76a3d035643f919c2a62e83dfdc9a9', '[\"notaris\"]', '2025-09-10 18:20:30', '2025-09-10 18:46:21', '2025-09-10 18:16:21', '2025-09-10 18:20:30'),
(20, 'App\\Models\\User', 1, 'user-token', '660b8305e914fb9bf1760eb4024a74557e47d2c05049c2f2f7303a8dd470cb4b', '[\"notaris\"]', '2025-09-10 19:16:51', '2025-09-10 19:19:28', '2025-09-10 18:49:28', '2025-09-10 19:16:51'),
(23, 'App\\Models\\User', 1, 'user-token', 'c20d6c240db65ce4d6958c9a6737d1d4d58ebc50a997fd13bdf57126d0d147a9', '[\"notaris\"]', '2025-09-10 20:01:03', '2025-09-10 20:07:14', '2025-09-10 19:37:14', '2025-09-10 20:01:03'),
(24, 'App\\Models\\User', 1, 'user-token', '9ea88d63ed9d02ee8028615471e314611c6aa0ea39e838b247166267f2d1e711', '[\"notaris\"]', '2025-09-10 20:17:02', '2025-09-10 20:44:12', '2025-09-10 20:14:12', '2025-09-10 20:17:02');

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
(1, 'Mr. Cody Hartmann II', '83403', 'Perspiciatis eum tempore tempora.', NULL, '2025-09-09 02:37:38', '2025-09-09 02:37:38'),
(2, 'Prof. Erika Zulauf', '66642', 'Dolor molestias at ut.', NULL, '2025-09-09 02:37:38', '2025-09-09 02:37:38'),
(3, 'Earnestine Jast DDS', '64217', 'Omnis animi ut non sunt ipsam tempora.', NULL, '2025-09-09 02:37:38', '2025-09-09 02:37:38'),
(4, 'Mrs. Aida Durgan Jr.', '95368', 'Odit in voluptatibus accusamus.', NULL, '2025-09-09 02:37:38', '2025-09-09 02:37:38'),
(5, 'Ms. Liana Mayert', '51291', 'Aliquid eligendi eligendi quam rerum laboriosam.', NULL, '2025-09-09 02:37:38', '2025-09-09 02:37:38'),
(6, 'Jane Waelchi', '81183', 'Ullam temporibus expedita in error voluptatum et.', NULL, '2025-09-09 02:37:38', '2025-09-09 02:37:38'),
(7, 'Jessie Lynch', '55965', 'Repellendus distinctio praesentium quaerat fuga iusto ut.', NULL, '2025-09-09 02:37:38', '2025-09-09 02:37:38'),
(8, 'Prof. Evans White', '40938', 'Ullam eaque assumenda blanditiis.', NULL, '2025-09-09 02:37:38', '2025-09-09 02:37:38'),
(9, 'Angeline Mohr', '59187', 'Ut sit libero suscipit molestias.', NULL, '2025-09-09 02:37:38', '2025-09-09 02:37:38'),
(10, 'Dr. Jimmie Monahan', '12375', 'Ut saepe exercitationem exercitationem.', NULL, '2025-09-09 02:37:38', '2025-09-09 02:37:38'),
(11, 'Ilene Renner DDS', '28420', 'Culpa tenetur qui libero aut.', NULL, '2025-09-09 02:37:38', '2025-09-09 02:37:38');

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
(2, 1, 'NIK', 0, '2025-09-09 03:33:53', '2025-09-09 03:33:53'),
(3, 1, 'NPWP', 0, '2025-09-09 03:34:03', '2025-09-09 03:34:03'),
(4, 1, 'Surat Kuasa', 1, '2025-09-09 03:34:16', '2025-09-09 03:34:16'),
(5, 2, 'NIK', 0, '2025-09-09 03:38:23', '2025-09-09 03:38:23');

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
(1, 'admin', '2025-09-09 02:37:39', '2025-09-09 02:37:39'),
(2, 'penghadap', '2025-09-09 02:37:39', '2025-09-09 02:37:39'),
(3, 'notaris', '2025-09-09 02:37:39', '2025-09-09 02:37:39');

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

-- --------------------------------------------------------

--
-- Struktur dari tabel `templates`
--

CREATE TABLE `templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `templates`
--

INSERT INTO `templates` (`id`, `name`, `custom_value`, `created_at`, `updated_at`) VALUES
(1, 'Perjanjian Sewa Menyewa', '<div style=\"text-align:center;margin-bottom:8px\">\n  <h2 style=\"margin:0\">PERJANJIAN SEWA MENYEWA</h2>\n  <div style=\"font-size:12px\">Nomor : {activity_code}</div>\n</div>\n\n<p>&ndash; Pada hari ini, {today}</p>\n<p>&ndash; tanggal .............................................................</p>\n<p>&ndash; Pukul .................................................................</p>\n\n<p>&ndash; Berhadapan dengan saya, {notaris_name}, Notaris di {schedule_place}, \ndengan dihadiri oleh para saksi yang saya, Notaris, kenal dan akan disebutkan nama-namanya pada bahagian akhir akta ini:</p>\n\n<p><b>I. Tuan {penghadap1_name}</b><br/>\n..............................................................<br/>\n..............................................................<br/>\n..............................................................</p>\n\n<p><b>II. Tuan {penghadap2_name}</b><br/>\n..............................................................<br/>\n..............................................................<br/>\n..............................................................</p>\n\n<p>&ndash; menurut keterangannya dalam hal ini bertindak dalam jabatannya selaku Presiden Direktur dari Perseroan Terbatas PT. .........., berkedudukan di Jakarta yang anggaran dasarnya beserta perubahannya telah mendapat persetujuan dari Menteri Kehakiman dan Hak Asasi Manusia berturut-turut:</p>\n\n<p>\n..............................................................<br/>\n..............................................................<br/>\n..............................................................<br/>\n..............................................................\n</p>\n\n<p>selanjutnya disebut: <b>Pihak Kedua</b> atau <b>Penyewa</b>.</p>\n\n<p>&ndash; Para penghadap telah saya, Notaris, kenal.</p>\n\n<p>&ndash; Para penghadap menerangkan terlebih dahulu:</p>\n\n<p>&ndash; bahwa Pihak Pertama adalah pemilik dari bangunan Rumah Toko (Ruko) yang hendak disewakan kepada Pihak Kedua yang akan disebutkan di bawah ini dan Pihak Kedua menerangkan menyewa dari Pihak Pertama berupa:</p>\n\n<p>&ndash; 1 (satu) unit bangunan Rumah Toko (Ruko) berlantai 3 (tiga) berikut turutannya, lantai keramik, dinding tembok, atap dak, aliran listrik sebesar 2.200 Watt, dilengkapi air dari jet pump, berdiri di atas sebidang tanah Sertifikat HGB Nomor: ............ seluas ...... m² (....................................), penerbitan sertifikat tanggal ..........................., tercantum atas nama .................. yang telah diuraikan dalam Gambar Situasi tanggal ............ nomor ............; Sertifikat tanah diterbitkan oleh Kantor Pertanahan Kabupaten Bekasi, terletak di Provinsi Jawa Barat, Kabupaten Bekasi, Kecamatan Cibitung, Desa Ganda Mekar, setempat dikenal sebagai Mega Mall MM.2100 Blok B Nomor 8.</p>\n\n<p>&ndash; Berdasarkan keterangan-keterangan tersebut di atas, kedua belah pihak sepakat membuat perjanjian sewa-menyewa dengan syarat-syarat dan ketentuan-ketentuan sebagai berikut:</p>\n\n<p><b>----------------------- Pasal 1.</b></p>\n<p>Perjanjian sewa-menyewa ini berlangsung untuk jangka waktu 2 (dua) tahun terhitung sejak tanggal ............ sampai dengan tanggal ............</p>\n<p>&ndash; Penyerahan Ruko akan dilakukan dalam keadaan kosong/tidak dihuni pada tanggal .................. dengan penyerahan semua kunci-kuncinya.</p>\n\n<p><b>----------------------- Pasal 2.</b></p>\n<p>&ndash; Uang kontrak sewa disepakati sebesar Rp. ............ (....................................) untuk 2 (dua) tahun masa sewa.</p>\n<p>&ndash; Jumlah uang sewa sebesar Rp. ............ (....................................) tersebut dibayar oleh Pihak Kedua kepada Pihak Pertama pada saat penandatanganan akta ini atau pada tanggal .................. dengan kwitansi tersendiri, dan akta ini berlaku sebagai tanda penerimaan yang sah.</p>\n\n<p><b>----------------------- Pasal 3.</b></p>\n<p>&ndash; Pihak Kedua hanya akan menggunakan yang disewakan dalam akta ini sebagai tempat kegiatan perkantoran/usaha.</p>\n<p>&ndash; Jika diperlukan, Pihak Pertama memberikan surat rekomendasi/keterangan yang diperlukan Pihak Kedua sepanjang tidak melanggar hukum.</p>\n<p>&ndash; Pihak Kedua wajib mentaati peraturan-peraturan pihak yang berwajib dan menjamin Pihak Pertama tidak mendapat teguran/tuntutan apapun karenanya.</p>\n\n<p><b>----------------------- Pasal 4.</b></p>\n<p>&ndash; Hanya dengan persetujuan tertulis Pihak Pertama, Pihak Kedua boleh mengadakan perubahan/penambahan pada bangunan; seluruh biaya dan tanggung jawab pada Pihak Kedua, dan pada akhir masa kontrak menjadi hak Pihak Pertama.</p>\n<p>&ndash; Penyerahan nyata dari yang disewakan oleh Pihak Pertama kepada Pihak Kedua dilakukan pada tanggal .................. dengan penyerahan semua kunci-kunci.</p>\n\n<p><b>----------------------- Pasal 5.</b></p>\n<p>Pihak Pertama memberi izin kepada Pihak Kedua untuk pemasangan/penambahan antara lain:</p>\n<ul style=\"margin-left:18px\">\n  <li>Sekat-sekat pada ruangan;</li>\n  <li>Antena radio/CD;</li>\n  <li>Line telepon;</li>\n  <li>Air Conditioner (AC);</li>\n  <li>Penambahan daya listrik;</li>\n  <li>Saluran fax;</li>\n  <li>Internet;</li>\n  <li>TV Kabel;</li>\n  <li>Shower;</li>\n  <li>Penggantian W/C;</li>\n  <li>Katrol pengangkut barang lantai 1–3;</li>\n  <li>Peralatan keamanan;</li>\n  <li>Peralatan pendukung usaha (rak/mesin) tanpa merusak struktur bangunan.</li>\n</ul>\n<p>&ndash; Setelah masa kontrak berakhir, Pihak Kedua mengembalikan seperti keadaan semula dengan biaya Pihak Kedua.</p>\n<p>&ndash; Pihak Kedua boleh mengganti kunci ruangan di dalam bangunan (kecuali pintu utama); pada akhir masa kontrak, kunci-kunci diserahkan ke Pihak Pertama.</p>\n<p>&ndash; Pihak Pertama menjamin yang disewakan adalah miliknya dan bebas dari tuntutan pihak lain.</p>\n<p>&ndash; Selama masa sewa, Pihak Pertama boleh memeriksa bangunan sewaktu-waktu.</p>\n\n<p><b>----------------------- Pasal 6.</b></p>\n<p>&ndash; Selama masa kontrak, pembayaran langganan listrik/air/telepon dan kewajiban lain terkait pemakaian dibayar Pihak Kedua hingga bulan terakhir dengan bukti pembayaran setiap bulan.</p>\n<p>&ndash; Pihak Pertama membayar Pajak Bumi dan Bangunan (PBB) untuk objek sewa.</p>\n\n<p><b>----------------------- Pasal 7.</b></p>\n<p>&ndash; Pihak Kedua wajib memelihara yang disewa dengan baik; kerusakan karena kelalaian diperbaiki atas biaya Pihak Kedua.</p>\n<p>&ndash; Apabila terjadi force majeure (kebakaran—kecuali kelalaian Pihak Kedua—sabotase, badai, banjir, gempa) sehingga objek musnah, para pihak dibebaskan dari tuntutan.</p>\n\n<p><b>----------------------- Pasal 8.</b></p>\n<p>&ndash; Pihak Pertama menjamin tidak ada tuntutan atau gangguan dari pihak lain atas yang disewa selama kontrak.</p>\n\n<p><b>----------------------- Pasal 9.</b></p>\n<p>Pihak Kedua, dengan persetujuan tertulis Pihak Pertama, boleh mengalihkan/memindahkan hak kontrak pada pihak lain, sebagian maupun seluruhnya, selama masa kontrak berlaku.</p>\n\n<p><b>----------------------- Pasal 10.</b></p>\n<p>Pihak Kedua wajib memberi pemberitahuan mengenai berakhir/akan diperpanjangnya kontrak kepada Pihak Pertama selambat-lambatnya 2 (dua) bulan sebelum berakhir.</p>\n\n<p><b>----------------------- Pasal 11.</b></p>\n<p>Pada saat berakhirnya kontrak dan tidak ada perpanjangan, Pihak Kedua menyerahkan kembali objek sewa dalam keadaan kosong, terpelihara baik, dengan semua kunci pada tanggal ..................</p>\n<p>Apabila terlambat, Pihak Kedua dikenakan denda sebesar Rp. 27.500,- per hari selama 7 (tujuh) hari pertama; jika masih tidak diserahkan, Pihak Kedua memberi kuasa kepada Pihak Pertama (dengan hak substitusi) untuk melakukan pengosongan dengan bantuan pihak berwajib, atas biaya dan risiko Pihak Kedua.</p>\n\n<p><b>----------------------- Pasal 12.</b></p>\n<p>Selama masa kontrak belum berakhir, perjanjian ini tidak berakhir karena:</p>\n<ul style=\"margin-left:18px\">\n  <li>Meninggalnya salah satu pihak;</li>\n  <li>Pihak Pertama mengalihkan hak milik atas objek sewa kepada pihak lain;</li>\n  <li>Dalam hal salah satu pihak meninggal dunia, ahli waris/penggantinya wajib melanjutkan perjanjian sampai berakhir; pemilik baru tunduk pada seluruh ketentuan akta ini.</li>\n</ul>\n\n<p><b>----------------------- Pasal 13.</b></p>\n<p>Untuk menjamin pembayaran listrik, air, telepon, keamanan, dan kewajiban lain bulan terakhir, Pihak Kedua menyerahkan uang jaminan sebesar Rp. 2.000.000,- (dua juta rupiah) pada saat penyerahan kunci, dengan kwitansi tersendiri. Kelebihan dikembalikan Pihak Pertama; kekurangan ditambah oleh Pihak Kedua.</p>\n\n<p><b>----------------------- Pasal 14.</b></p>\n<p>Hal-hal yang belum cukup diatur akan dibicarakan kemudian secara musyawarah untuk mufakat.</p>\n\n<p><b>----------------------- Pasal 15.</b></p>\n<p>Pajak-pajak yang mungkin ada terkait akta ini dibayar oleh Pihak Kedua untuk dan atas nama Pihak Pertama.</p>\n\n<p><b>----------------------- Pasal 16.</b></p>\n<p>Biaya-biaya yang berkaitan dengan akta ini dibayar dan menjadi tanggungan Pihak Pertama.</p>\n\n<p><b>----------------------- Pasal 17.</b></p>\n<p>Kedua belah pihak memilih domisili hukum yang sah di Kepaniteraan Pengadilan Negeri Bekasi.</p>\n\n<p><b>DEMIKIAN AKTA INI</b></p>\n<p>&ndash; Dibuat dan diresmikan di Bekasi pada hari dan tanggal sebagaimana awal akta ini, dengan dihadiri oleh:</p>\n<ol style=\"margin-left:18px\">\n  <li>Nyonya ........................................</li>\n  <li>Nyonya ........................................</li>\n</ol>\n<p>Keduanya Karyawan Kantor Notaris, sebagai saksi-saksi.</p>\n<p>&ndash; Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p>\n<hr style=\"margin:24px 0;border:0;border-top:1px solid #000\" />', '2025-09-10 10:22:38', NULL),
(2, 'Hak Waris', '<div class=\"ql-align-center\">\n  <h2 style=\"margin:0; font-weight:700;\">KETERANGAN HAK WARIS</h2>\n  <div style=\"margin-top:4px;\">Nomor: {doc_number}</div>\n</div>\n\n<p class=\"ql-align-justify\">\n  Pada hari ini, hari {day_name}, tanggal {date_long}, pukul {time_wib} WIB ({time_wib_words} Waktu Indonesia bagian Barat),\n  menghadap di hadapan saya, {notary_name}, Sarjana Hukum, Notaris di {city}, dengan dihadiri oleh saksi-saksi yang saya,\n  Notaris, kenal dan akan disebut pada bagian akhir akta ini.\n</p>\n\n<h3>I. PENGHADAP PERTAMA</h3>\n<p class=\"ql-align-justify no-page-break\">\n  Nyonya {party1_fullname} {party1_alias_opt}, {party1_job}, bertempat tinggal di {party1_address_full};\n  Kartu Tanda Penduduk Nomor: {party1_ktp}.\n</p>\n\n<h3>II. PENGHADAP KEDUA</h3>\n<p class=\"ql-align-justify\">\n  Nyonya {party2_fullname} {party2_alias_opt}, {party2_job}, bertempat tinggal di {party2_address_full};\n  Kartu Tanda Penduduk Nomor: {party2_ktp}.\n</p>\n\n<p class=\"ql-align-justify\">\n  Para penghadap tersebut telah dikenal oleh saya, Notaris.\n</p>\n\n<div class=\"page-break-after\"></div>\n\n<h3>KETERANGAN PARA PENGHADAP</h3>\n\n<p class=\"ql-align-justify\">\n  Bahwa almarhum Tuan {pewaris_name} {pewaris_alias_opt}, Warganegara Indonesia, telah meninggal dunia di {pewaris_death_city},\n  pada tanggal {pewaris_death_date_long} ({pewaris_death_date_num}), demikian seperti ternyata dari Akta Kematian tertanggal\n  {akta_kematian_date_long} ({akta_kematian_date_num}) Nomor {akta_kematian_number} yang dikeluarkan oleh\n  {akta_kematian_issuer}; akta mana aslinya diperlihatkan kepada saya, Notaris.\n</p>\n\n<p class=\"ql-align-justify\">\n  Bahwa almarhum Tuan {pewaris_name_short} {pewaris_alias_opt} selanjutnya akan disebut juga “pewaris”, menurut keterangan para\n  penghadap telah kawin sah dengan Nyonya {spouse_fullname} {spouse_alias_opt}, demikian berdasarkan Akta Perkawinan/Golongan\n  Tionghoa tanggal {akta_kawin_date_long} ({akta_kawin_date_num}) Nomor {akta_kawin_number} yang dikeluarkan oleh\n  {akta_kawin_issuer}; akta mana aslinya diperlihatkan kepada saya, Notaris.\n</p>\n\n<p class=\"ql-align-justify\">\n  Bahwa dari perkawinan tersebut telah dilahirkan {children_count_words} ({children_count_num}) orang anak, yaitu:\n</p>\n\n<ol class=\"ql-align-justify\" style=\"padding-left:24px;\">\n  <li class=\"keep-together\">\n    Penghadap Nyonya {child1_now_name}, dahulu bernama {child1_old_name_opt}, disebut juga {child1_alias_opt},\n    yang dilahirkan pada tanggal {child1_birth_date_long} ({child1_birth_date_num}) di {child1_birth_city},\n    berdasarkan Akta Kelahiran tanggal {child1_akta_date_long} ({child1_akta_date_num}) Nomor {child1_akta_number}\n    yang dikeluarkan oleh {child1_akta_issuer}; aslinya diperlihatkan kepada saya, Notaris.\n  </li>\n  <li class=\"keep-together\">\n    Nyonya {child2_now_name}, yang dilahirkan di {child2_birth_city}, pada tanggal {child2_birth_date_long} ({child2_birth_date_num}),\n    berdasarkan Akta Kelahiran tanggal {child2_akta_date_long} ({child2_akta_date_num}) Nomor {child2_akta_number}\n    yang dikeluarkan oleh {child2_akta_issuer}; aslinya diperlihatkan kepada saya, Notaris.\n  </li>\n  <li class=\"keep-together\">\n    Nona {child3_now_name}, disebut juga {child3_alias_opt}, sekarang bernama {child3_current_name_opt},\n    dilahirkan di {child3_birth_city} pada tanggal {child3_birth_date_long} ({child3_birth_date_num}),\n    berdasarkan Akta Kelahiran tanggal {child3_akta_date_long} ({child3_akta_date_num}) Nomor {child3_akta_number}\n    yang dikeluarkan oleh {child3_akta_issuer}; aslinya diperlihatkan kepada saya, Notaris.\n  </li>\n  <li class=\"keep-together\">\n    Tuan {child4_now_name}, dilahirkan pada tanggal {child4_birth_date_long} ({child4_birth_date_num}),\n    berdasarkan Akta Kelahiran tanggal {child4_akta_date_long} ({child4_akta_date_num}) Nomor {child4_akta_number}\n    yang dikeluarkan oleh {child4_akta_issuer}; aslinya diperlihatkan kepada saya, Notaris.\n  </li>\n  <li class=\"keep-together\">\n    Tuan/Nona {child5_now_name}, dilahirkan di {child5_birth_city} pada tanggal {child5_birth_date_long} ({child5_birth_date_num}),\n    berdasarkan Akta Kelahiran tanggal {child5_akta_date_long} ({child5_akta_date_num}) Nomor {child5_akta_number}\n    yang dikeluarkan oleh {child5_akta_issuer}; aslinya diperlihatkan kepada saya, Notaris.\n  </li>\n</ol>\n\n<p class=\"ql-align-justify\">\n  Bahwa “pewaris” tidak meninggalkan turunan atau saudara lain selain dari para penghadap dan {child2_now_name},\n  {child3_now_name} {child3_current_name_opt}, {child4_now_name}, dan {child5_now_name} tersebut.\n</p>\n\n<p class=\"ql-align-justify\">\n  Bahwa menurut surat dari {no_will_issuer} tanggal {no_will_date_long} ({no_will_date_num}) Nomor {no_will_number},\n  “pewaris” tidak meninggalkan surat wasiat.\n</p>\n\n<div class=\"page-break-after\"></div>\n\n<h3>PERNYATAAN</h3>\n<p class=\"ql-align-justify\">\n  Para penghadap tersebut di atas selanjutnya dengan ini menerangkan:\n</p>\n<ul class=\"ql-align-justify\" style=\"padding-left:24px;\">\n  <li>Bahwa para penghadap mengetahui dan dapat membenarkan segala sesuatu yang diuraikan di atas;</li>\n  <li>Bahwa para penghadap bersedia jika perlu memperkuat segala sesuatu yang diuraikan di atas dengan sumpah.</li>\n</ul>\n\n<p class=\"ql-align-justify\">\n  Maka sekarang berdasarkan keterangan-keterangan tersebut di atas dan surat-surat yang diperlihatkan kepada saya, Notaris, serta\n  berdasarkan hukum yang berlaku bagi para penghadap dan {child2_now_name}, {child3_now_name} {child3_current_name_opt},\n  {child4_now_name}, dan {child5_now_name}, maka saya, Notaris, menerangkan dalam akta ini:\n</p>\n\n<h3>PEMBAGIAN HAK ATAS HARTA PENINGGALAN</h3>\n<ol class=\"ql-align-justify\" style=\"padding-left:24px;\">\n  <li>Nyonya {spouse_fullname} mendapat {portion_spouse} bagian.</li>\n  <li>Nyonya {party2_fullname_short} {party2_alias_opt} mendapat {portion_child2} bagian.</li>\n  <li>Nyonya {child2_now_name} mendapat {portion_childB} bagian.</li>\n  <li>Nona {child3_now_name} {child3_alias_opt} {child3_current_name_opt} mendapat {portion_childC} bagian.</li>\n  <li>Tuan {child4_now_name} mendapat {portion_childD} bagian.</li>\n  <li>Nona/Tuan {child5_now_name} mendapat {portion_childE} bagian.</li>\n</ol>\n\n<p class=\"ql-align-justify\">\n  Bahwa para penghadap dan {child2_now_name}, {child3_now_name} {child3_current_name_opt}, {child4_now_name}, dan {child5_now_name},\n  merupakan para ahli waris tersendiri dari “pewaris” dengan mengecualikan siapapun juga, serta berhak untuk menuntut dan menerima\n  seluruh barang-barang dan harta kekayaan yang termasuk harta peninggalan “pewaris”. Selanjutnya, mereka berhak memberi\n  tanda terima untuk segala penerimaan harta kekayaan dan barang.\n</p>\n\n<p class=\"ql-align-justify\">\n  Dari segala sesuatu yang tersebut di atas ini dengan segala akibat-akibatnya, para penghadap telah memilih tempat kediaman\n  hukum yang sah dan tidak berubah di Kantor Panitera Pengadilan Negeri {pengadilan_negeri_kota}.\n</p>\n\n<div class=\"page-break-after\"></div>\n\n<h3>PENUTUP</h3>\n<p class=\"ql-align-justify\">\n  Demikianlah akta ini, dibuat dengan dihadiri oleh Tuan {witness1_name} dan Tuan {witness2_name}, kedua-duanya Pegawai\n  Kantor Notaris, bertempat tinggal di {city}, sebagai saksi-saksi.\n</p>\n<p class=\"ql-align-justify\">\n  Segera setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka ditandatangani oleh para\n  penghadap, para saksi, dan saya, Notaris.\n</p>\n<p class=\"ql-align-justify\">\n  Dilangsungkan dengan tanpa perubahan. Dilangsungkan dan diresmikan sebagai minuta di {city}, pada hari, tanggal, dan tahun seperti\n  disebut pada awal. Minuta akta ini telah ditandatangani dengan sempurna. Diberikan sebagai salinan yang sama bunyinya.\n</p>\n\n<p class=\"ql-align-right\" style=\"margin-top:32px;\">\n  {city}, {date_long}<br/>\n  {notary_name}<br/>\n  Notaris di {city}\n</p>\n', '2025-09-09 17:00:00', NULL),
(3, ' Perseroan Komanditer', '<div style=\"text-align:center;margin-bottom:8px\">\n  <h2 style=\"margin:0\">AKTA PENDIRIAN PERSEROAN KOMANDITER</h2>\n  <h3 style=\"margin:2px 0 0 0\">{cv_name_upper}</h3>\n  <div style=\"font-size:12px\">Nomor : {activity_code}</div>\n</div>\n\n<p>&ndash; Pada hari ini, {today}</p>\n<p>&ndash; tanggal .............................................................</p>\n<p>&ndash; Pukul .................................................................</p>\n\n<p>\n&ndash; Berhadapan dengan saya, {notaris_name}, Notaris di {schedule_place},\ndengan dihadiri oleh saksi-saksi yang saya, Notaris, kenal dan akan disebutkan pada bagian akhir akta ini:\n</p>\n\n<p><b>I. Tuan {penghadap1_name}</b><br/>\n{penghadap1_identitas_line1}<br/>\n{penghadap1_identitas_line2}<br/>\n{penghadap1_identitas_line3}\n</p>\n\n<p><b>II. Nyonya {penghadap2_name}</b><br/>\n{penghadap2_identitas_line1}<br/>\n{penghadap2_identitas_line2}<br/>\n{penghadap2_identitas_line3}\n</p>\n\n<p><b>III. Nyonya/Tuan {penghadap3_name}</b><br/>\n{penghadap3_identitas_line1}<br/>\n{penghadap3_identitas_line2}<br/>\n{penghadap3_identitas_line3}\n</p>\n\n<p>&ndash; Para penghadap telah saya, Notaris, kenal.</p>\n\n<p>\n&ndash; Para penghadap menerangkan dengan akta ini telah saling setuju dan semufakat untuk mendirikan suatu\nPerseroan Komanditer (Commanditaire Vennootschap) dengan Anggaran Dasar sebagai berikut:\n</p>\n\n<div class=\"page-break-after\"></div>\n\n<p style=\"text-align:center\"><b>NAMA DAN TEMPAT KEDUDUKAN<br/>Pasal 1</b></p>\n<ol style=\"margin-left:18px\">\n  <li>Perseroan ini bernama Perseroan Komanditer: <b>{cv_name_upper}</b> (selanjutnya disebut “Perseroan”).</li>\n  <li>Perseroan berkedudukan di {domisili_kota}, dengan cabang/perwakilan di tempat lain yang dianggap perlu oleh (para) Pesero Pengurus.</li>\n</ol>\n\n<p style=\"text-align:center\"><b>WAKTU<br/>Pasal 2</b></p>\n<p>&ndash; Perseroan didirikan untuk waktu yang tidak ditentukan dan mulai berlaku sejak akta ini ditandatangani.</p>\n\n<p style=\"text-align:center\"><b>MAKSUD DAN TUJUAN<br/>Pasal 3</b></p>\n<p>Maksud dan tujuan Perseroan sebagai berikut:</p>\n<ol style=\"margin-left:18px\">\n  <li>Distribusi/supplier/leveransir/grosir/komisioner/keagenan berbagai barang (kecuali keagenan perjalanan);</li>\n  <li>Perdagangan umum (impor, ekspor, lokal, antarpulau) sendiri maupun komisi;</li>\n  <li>Industri (konveksi/garment, butik, alat rumah tangga, kerajinan, souvenir, kayu, besi);</li>\n  <li>Jasa: perawatan/perbaikan elektrikal-mekanikal-teknikal & komputer; warnet/wartel/pos; cleaning service; boga; pengiriman barang;</li>\n  <li>Kontraktor/biro bangunan (gedung, perumahan, jalan, jembatan, irigasi), pemasangan aluminium/gypsum/kaca/furnitur & instalasi listrik/air/gas/telekomunikasi;</li>\n  <li>Pengadaan alat & kebutuhan kantor; pertamanan/landscaping; interior & eksterior; periklanan & reklame; percetakan/penjilidan/pengepakan;</li>\n  <li>Pengangkutan darat; perbengkelan; perkebunan, kehutanan, pertanian, peternakan, perikanan;</li>\n  <li>Segala kegiatan lain yang menunjang tujuan Perseroan sepanjang peraturan perundang-undangan.</li>\n</ol>\n<p>&ndash; Perseroan dapat mendirikan/ikut mendirikan badan lain yang sejenis di dalam/luar negeri sesuai peraturan.</p>\n\n<div class=\"page-break-after\"></div>\n\n<p style=\"text-align:center\"><b>MODAL<br/>Pasal 4</b></p>\n<ol style=\"margin-left:18px\">\n  <li>Modal Perseroan tidak ditentukan besarnya; akan ternyata pada buku Perseroan, termasuk porsi tiap pesero.</li>\n  <li>Setoran uang dan/atau inbreng dicatat pada perhitungan modal masing-masing dan diberi tanda bukti yang ditandatangani para pesero.</li>\n  <li>(Para) Pesero Pengurus juga mencurahkan tenaga, pikiran, dan keahliannya untuk kepentingan Perseroan.</li>\n</ol>\n\n<p style=\"text-align:center\"><b>PENGURUSAN & TANGGUNG JAWAB — (PARA) PESERO PENGURUS<br/>Pasal 5</b></p>\n<ol style=\"margin-left:18px\">\n  <li>Tuan {pesero_pengurus_name} adalah Pesero Pengurus bertanggung jawab penuh; Nyonya/Tuan {pesero_komanditer1_name} dan {pesero_komanditer2_name} adalah Pesero Komanditer yang bertanggung jawab sampai modal yang dimasukkan.</li>\n  <li>\n    Tuan {direktur_name} selaku Direktur (atau wakil/yang ditunjuk bila berhalangan) mewakili dan mengikat Perseroan, namun untuk:\n    <ol style=\"margin-left:18px\">\n      <li>Perolehan/pelepasan/pemindahan hak atas benda tetap;</li>\n      <li>Meminjam/meminjamkan uang (kecuali penarikan dana Perseroan di bank/tempat lain);</li>\n      <li>Menggadaikan/membebani harta Perseroan;</li>\n      <li>Mengikat Perseroan sebagai penjamin;</li>\n      <li>Mengangkat/mencabut kuasa;</li>\n    </ol>\n    &ndash; harus dengan persetujuan lebih dahulu/ turut ditandatangani Pesero Komanditer.\n  </li>\n  <li>(Para) Pesero Pengurus memegang buku-buku, uang, dan hal-hal lain usaha Perseroan; berwenang mengangkat/memberhentikan karyawan & menetapkan gaji.</li>\n</ol>\n\n<p style=\"text-align:center\"><b>WEWENANG (PARA) PESERO KOMANDITER<br/>Pasal 6</b></p>\n<ol style=\"margin-left:18px\">\n  <li>Berwenang memasuki aset Perseroan (kantor/gedung/bangunan) dan memeriksa buku-buku, uang, dan keadaan usaha.</li>\n  <li>(Para) Pesero Pengurus wajib memberi keterangan yang diminta.</li>\n</ol>\n\n<p style=\"text-align:center\"><b>PENGUNDURAN DIRI / MENINGGAL DUNIA / PAILIT<br/>Pasal 7–10</b></p>\n<p>\n&ndash; Ketentuan pengunduran diri (pemberitahuan ≥ 3 bulan), kelanjutan usaha bila pesero meninggal (dengan kuasa ahli waris ≤ 3 bulan), status keluar bila pailit/surseance/pengampuan, serta pembayaran bagian pesero yang keluar menurut neraca terakhir (≤ 3 bulan, tanpa bunga) dan hak pesero tersisa untuk melanjutkan usaha dengan sisa aktiva-pasiva dan tetap memakai nama Perseroan.\n</p>\n\n<div class=\"page-break-after\"></div>\n\n<p style=\"text-align:center\"><b>PENUTUPAN BUKU & NERACA<br/>Pasal 11</b></p>\n<ol style=\"margin-left:18px\">\n  <li>Setiap akhir Desember buku ditutup; paling lambat akhir Maret dibuat neraca & laba-rugi. Pertama kali ditutup: {first_closing_date_long} ({first_closing_date_num}).</li>\n  <li>Dokumen disimpan di kantor Perseroan; dapat dilihat (Para) Pesero Komanditer 14 hari sejak dibuat.</li>\n  <li>Jika tidak ada keberatan dalam 14 hari, dianggap sah dan semua pesero menandatangani (acquit et decharge kepada (Para) Pesero Pengurus).</li>\n  <li>Bila tidak mufakat, dapat minta hakim menunjuk 3 arbiter; para pesero tunduk pada putusan para arbiter.</li>\n</ol>\n\n<p style=\"text-align:center\"><b>KEUNTUNGAN (Pasal 12) — KERUGIAN (Pasal 13) — DANA CADANGAN (Pasal 14)</b></p>\n<p>\n&ndash; Keuntungan dibagi sesuai perbandingan modal; dibayarkan ≤ 1 bulan setelah pengesahan neraca/laba-rugi.\nKerugian ditanggung sesuai perbandingan; Pesero Komanditer hanya sampai modal setorannya. Dana cadangan dapat disisihkan/ digunakan sebagai modal kerja sesuai kesepakatan; hasil/rugi diperhitungkan pada laba-rugi.\n</p>\n\n<p style=\"text-align:center\"><b>PENGALIHAN BAGIAN (Pasal 15) — HAL-HAL LAIN (Pasal 16) — DOMISILI (Pasal 17)</b></p>\n<p>\n&ndash; Pengalihan/pembebanan bagian pesero harus dengan persetujuan pesero lain. Hal yang belum cukup diatur diputuskan musyawarah.\nPara pesero memilih domisili di Kepaniteraan Pengadilan Negeri {domisili_kota}.\n</p>\n\n<div class=\"page-break-after\"></div>\n\n<p><b>AKTA INI</b></p>\n<p>&ndash; Dibuat sebagai minuta dan diresmikan di {schedule_place} pada hari dan tanggal seperti pada awal akta ini, dengan saksi-saksi:</p>\n<ol style=\"margin-left:18px\">\n  <li>{saksi1_name}, {saksi1_identitas_desc}</li>\n  <li>{saksi2_name}, {saksi2_identitas_desc}</li>\n</ol>\n<p>Keduanya Karyawan Kantor Notaris, sebagai saksi-saksi.</p>\n<p>&ndash; Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p>\n\n<hr style=\"margin:24px 0;border:0;border-top:1px solid #000\"/>\n', '2025-09-09 17:00:00', NULL),
(5, 'tess', '<p>Pasti Dirimuuuuuuuuuuu&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;tolong lihat aku</p>', '2025-09-10 03:44:49', '2025-09-10 19:59:44');

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
(1, 'done', 'done', 'todo', 'todo', 'pending', 'pending', 'pending', '2025-09-09 02:55:42', '2025-09-09 02:55:42'),
(2, 'done', 'done', 'todo', 'todo', 'pending', 'pending', 'pending', '2025-09-09 03:37:15', '2025-09-09 03:37:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `file_avatar_path`, `file_avatar`, `telepon`, `gender`, `email_verified_at`, `password`, `address`, `city`, `province`, `postal_code`, `status_verification`, `notes_verification`, `verify_key`, `expired_key`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 3, 'Adam Aditya', 'adam@gmail.com', NULL, NULL, NULL, NULL, '2025-09-08 07:36:15', '$2y$12$5OEL8E.8LpIKeUSbCsirl.4VTtcBt3rYOTDenXHOm45r0l84TCwoG', NULL, NULL, NULL, NULL, 'approved', NULL, 'QK4R08F', NULL, NULL, '2025-09-09 02:41:00', '2025-09-09 02:43:11'),
(2, 2, 'DEVANO ALIF RAMADHAN', 'devanorama123@gmail.com', NULL, NULL, NULL, NULL, '2025-09-08 07:51:53', '$2y$12$cuLpLapKh5poA41ccqfqEeujnm448YY/SJJObTAwHmwqN1LqDBfhO', NULL, NULL, NULL, NULL, 'approved', NULL, '7RZWDO0', NULL, NULL, '2025-09-09 02:43:11', '2025-09-09 02:43:11'),
(3, 2, 'Iwang', 'iwang@gmail.com', NULL, NULL, NULL, NULL, '2025-09-07 17:00:00', '$2y$12$zMrHq5m40Ntk.1Q2hplKX.o.zynQRZSC/nSQ8SvN8jCeo.rImSLzO', NULL, NULL, NULL, NULL, 'approved', NULL, 'TOCTPNP', NULL, NULL, '2025-09-09 02:43:12', '2025-09-09 02:43:12'),
(4, 2, 'Yasmin Zakiyah Firmasyah', 'yasmin@gmail.com', NULL, NULL, NULL, NULL, '2025-09-07 17:00:00', '$2y$12$e.rBn68jrGI1wWt/skITAuGyi1/e621w9IaxdekZOGRGDwQr7GBta', NULL, NULL, NULL, NULL, 'approved', NULL, 'A9QZJ9O', NULL, NULL, '2025-09-09 02:43:12', '2025-09-09 02:43:12'),
(5, 2, 'Dhika', 'dhika@gmail.com', NULL, NULL, NULL, NULL, '2025-09-07 17:00:00', '$2y$12$T0JRX8Q15VZ.5VHTo2AHeOvWiw0vQE0eujgjQC90IDA.DyX1T1d/S', NULL, NULL, NULL, NULL, 'approved', NULL, 'IDEZVZ5', NULL, NULL, '2025-09-09 02:43:12', '2025-09-09 02:43:12'),
(6, 1, 'admin', 'admin@gmail.com', NULL, NULL, NULL, NULL, '2025-09-08 08:28:18', '$2y$12$9IM8zHPvu5meGClS7Wg5EO2rgw5zE7591gPLloISGK0qGOlLczNAe', NULL, NULL, NULL, NULL, 'approved', NULL, '5HSAKDU', NULL, NULL, '2025-09-09 02:43:13', '2025-09-09 02:43:13');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_deed_id_foreign` (`deed_id`),
  ADD KEY `activity_user_notaris_id_foreign` (`user_notaris_id`),
  ADD KEY `activity_track_id_foreign` (`track_id`);

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
-- Indeks untuk tabel `deeds`
--
ALTER TABLE `deeds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deeds_user_notaris_id_foreign` (`user_notaris_id`);

--
-- Indeks untuk tabel `document_requirements`
--
ALTER TABLE `document_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_requirements_activity_notaris_id_foreign` (`activity_notaris_id`),
  ADD KEY `document_requirements_user_id_foreign` (`user_id`),
  ADD KEY `document_requirements_requirement_id_foreign` (`requirement_id`);

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
-- Indeks untuk tabel `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `users_role_id_foreign` (`role_id`),
  ADD KEY `users_verify_key_index` (`verify_key`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activity`
--
ALTER TABLE `activity`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `client_activities`
--
ALTER TABLE `client_activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `client_activity`
--
ALTER TABLE `client_activity`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `deeds`
--
ALTER TABLE `deeds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `document_requirements`
--
ALTER TABLE `document_requirements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `draft_deeds`
--
ALTER TABLE `draft_deeds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `identities`
--
ALTER TABLE `identities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `main_value_deeds`
--
ALTER TABLE `main_value_deeds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `requirements`
--
ALTER TABLE `requirements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `templates`
--
ALTER TABLE `templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tracks`
--
ALTER TABLE `tracks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- Ketidakleluasaan untuk tabel `client_activity`
--
ALTER TABLE `client_activity`
  ADD CONSTRAINT `client_activity_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_activity_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `deeds`
--
ALTER TABLE `deeds`
  ADD CONSTRAINT `deeds_user_notaris_id_foreign` FOREIGN KEY (`user_notaris_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `document_requirements`
--
ALTER TABLE `document_requirements`
  ADD CONSTRAINT `document_requirements_activity_notaris_id_foreign` FOREIGN KEY (`activity_notaris_id`) REFERENCES `activity` (`id`) ON DELETE CASCADE,
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
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
