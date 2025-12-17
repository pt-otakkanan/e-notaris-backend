-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Des 2025 pada 02.39
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
-- Database: `enotaris2`
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
  `is_without_client` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(53, '2025_11_10_013636_create_deed_requirement_templates_table', 1),
(54, '2025_11_10_013644_add_deed_requirement_template_id_to_document_requirements_table', 1),
(55, '2025_11_24_041754_add_is_without_client_column_to_activities_table', 1);

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
(1, 'Mrs. Aniya Brekke', '86585', 'Delectus et et labore.', NULL, '2025-12-03 18:35:08', '2025-12-03 18:35:08'),
(2, 'Dr. Jovany Gaylord III', '74735', 'Quisquam illo est rerum pariatur vel laborum.', NULL, '2025-12-03 18:35:08', '2025-12-03 18:35:08'),
(3, 'Ms. Helene Beer', '60493', 'Deleniti at omnis necessitatibus.', NULL, '2025-12-03 18:35:08', '2025-12-03 18:35:08'),
(4, 'Kiarra Cremin DDS', '68281', 'Et incidunt fugit et beatae ut.', NULL, '2025-12-03 18:35:08', '2025-12-03 18:35:08'),
(5, 'Ozella Streich', '33699', 'Molestias velit quod explicabo qui impedit.', NULL, '2025-12-03 18:35:08', '2025-12-03 18:35:08'),
(6, 'Ms. Marcia Little Sr.', '56974', 'Et suscipit quae error.', NULL, '2025-12-03 18:35:08', '2025-12-03 18:35:08'),
(7, 'Prof. Ricardo Langosh', '27477', 'Atque perspiciatis provident praesentium ea.', NULL, '2025-12-03 18:35:08', '2025-12-03 18:35:08'),
(8, 'Ivory Williamson', '22145', 'Cupiditate est quod eveniet aut id.', NULL, '2025-12-03 18:35:08', '2025-12-03 18:35:08'),
(9, 'Ms. Katrine Leannon', '34585', 'Voluptates odit hic eos non sint molestias.', NULL, '2025-12-03 18:35:08', '2025-12-03 18:35:08'),
(10, 'Mckenzie Hegmann', '48876', 'Suscipit tempora eum quod.', NULL, '2025-12-03 18:35:08', '2025-12-03 18:35:08'),
(11, 'Orpha Mosciski', '23523', 'Alias distinctio soluta aliquid pariatur nostrum.', NULL, '2025-12-03 18:35:08', '2025-12-03 18:35:08');

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
(1, 'admin', '2025-12-03 18:35:08', '2025-12-03 18:35:08'),
(2, 'penghadap', '2025-12-03 18:35:08', '2025-12-03 18:35:08'),
(3, 'notaris', '2025-12-03 18:35:08', '2025-12-03 18:35:08');

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `blog_category`
--
ALTER TABLE `blog_category`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `category_blogs`
--
ALTER TABLE `category_blogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `client_activities`
--
ALTER TABLE `client_activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `client_activity`
--
ALTER TABLE `client_activity`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `client_drafts`
--
ALTER TABLE `client_drafts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `deeds`
--
ALTER TABLE `deeds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `deed_requirement_templates`
--
ALTER TABLE `deed_requirement_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `document_requirements`
--
ALTER TABLE `document_requirements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `draft_deeds`
--
ALTER TABLE `draft_deeds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `identities`
--
ALTER TABLE `identities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `main_value_deeds`
--
ALTER TABLE `main_value_deeds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT untuk tabel `partners`
--
ALTER TABLE `partners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `requirements`
--
ALTER TABLE `requirements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `signatures`
--
ALTER TABLE `signatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `templates`
--
ALTER TABLE `templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tracks`
--
ALTER TABLE `tracks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
