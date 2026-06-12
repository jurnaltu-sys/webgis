-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 05 Mar 2026 pada 13.56
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_webgis`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `foto_wisata`
--

CREATE TABLE `foto_wisata` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `wisata_id` bigint(20) UNSIGNED NOT NULL,
  `url` varchar(255) NOT NULL,
  `is_cover` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `foto_wisata`
--

INSERT INTO `foto_wisata` (`id`, `wisata_id`, `url`, `is_cover`) VALUES
(11, 63, 'wisata/kxKC7ijrMtQQ7d5mMOihDb8tCnVU78aQcBLFp8mx.jpg', 0),
(12, 63, 'wisata/vexuF7q4osETYsriC7VGXjNhiBlkjCzeMJY7zFXO.jpg', 0),
(13, 63, 'wisata/LkW6ZdcF5TBfMG3u1cluv3B9O3FZbSh7ntVJS7pP.jpg', 1),
(14, 62, 'wisata/8yYgz8hZJvLbOLRpOAVGQwCaeLZrkFbwM1EW6cSk.jpg', 1),
(15, 62, 'wisata/3cOlTU9MaXbH0B96HjenzZKjFGj6mFcwTdGhZc59.jpg', 0),
(16, 62, 'wisata/bvG9R0G6eTVU7S2z99C4IvIFDdAmKCricNUBCZgv.jpg', 0),
(17, 61, 'wisata/Emr51Q5wfcjcMc1pseloCL8qYmbVd79DcC6ZKUpf.jpg', 0),
(18, 61, 'wisata/NFHVfamX6u4P5gUbxUPHmnwt2qyBIGK6nuPnizEq.jpg', 1),
(19, 61, 'wisata/zATn6vnzcxWIFvl3BAmhxUIxgImazZF4gs6hdiOU.webp', 0),
(20, 60, 'wisata/SaGrvdJ3pwevPWP3c2mmPOC2dju5m8nwmZOn7MkM.jpg', 1),
(21, 60, 'wisata/xYFbsIo8Gh7xGDNPrghDb0OKcf0khV3LWKsqDjhn.jpg', 0),
(22, 60, 'wisata/Mu8AiQmGl4aejFSx2C2CYEnZ8RxWRbgbWuvrW8Yz.jpg', 0),
(23, 59, 'wisata/qtf2elCxMONZMi6ZAufATvfKWrHKdmWT6zwJmLfe.jpg', 0),
(24, 59, 'wisata/CMNpm7Bhokco4e52gkVZluyYA0XE0ZbruvHqh2BJ.jpg', 1),
(25, 59, 'wisata/wfELjmJMdSG4y1TcFIRk7t00ZQ1mW61Tm1wJuKfy.webp', 0),
(26, 58, 'wisata/i52XuzweFq92k6bzdgIAuVuwtcnXc4qOr2SpAgdY.jpg', 1),
(27, 58, 'wisata/oAy6wnow7OWfvsTulwYv8qWpBU0w6xpVaohO5g63.jpg', 0),
(28, 58, 'wisata/Heaxx7378MHMwuNYZPZPWHU2CKUvxO8LwvyXAWdX.jpg', 0),
(29, 57, 'wisata/QQuIaJFAe3LaPcWDiI4RTOlTxCyO4NyUOuBK3phB.jpg', 1),
(30, 57, 'wisata/Veh3lOPvtpEvBOLMD80liPO0CYdgZnV6f3mb2NyO.jpg', 0),
(31, 57, 'wisata/KEOTd5jXBKeMqMEE0ohuWFyBMOJk66yl9o9VXp0z.jpg', 0),
(32, 56, 'wisata/s3MwJJltjRXGJT4X5ZohUbAQqRIqZBTLF1v0q32L.webp', 1),
(33, 56, 'wisata/BhxCkIGBP2yBGOGv8F7bXp33c9cWyJ40GCZ7xF6K.png', 0),
(34, 56, 'wisata/pJh4tGGUfzWHzIjZSPLvVbJUzSrMMIIc2d7QuH8A.jpg', 0),
(35, 55, 'wisata/Z2RSDjFhxBhJ3dP0sgcbn6PzEVr5pNPGt0jcnfpu.webp', 1),
(36, 55, 'wisata/CjYsDScXhhe2hRvXCCWk5iCHpxTjMQBvqTkiuk3y.webp', 0),
(37, 55, 'wisata/98ZLSsr2IEVSRR7e4RDoUpadR3AGH7jzceTVfU8K.jpg', 0),
(38, 54, 'wisata/0qmnxv0jeKTJDbnhVbUYiXKhrhNhJLnV7AqgI5A2.jpg', 0),
(39, 54, 'wisata/cnpuPXJtxtKf6gMeatu9pocTHQ7Jwn9ofsJG7bMK.jpg', 0),
(40, 54, 'wisata/TCj6JwzBI1I3nyUpm9HvjbjM1TJR5OxokLxZWf4I.jpg', 1),
(41, 53, 'wisata/pCi2ePURMl0B6isiOypk2eG8Zmahmd5J44cGNDEx.webp', 1),
(42, 53, 'wisata/ctP6ruB2q5wVSUxVpzSZkmIXIoxFVV7JxIcWnerb.jpg', 0),
(43, 53, 'wisata/eaPdnonUPbdw3i9gcioG7dDgzSPrCGuS5kx75InA.webp', 0),
(44, 52, 'wisata/GyAZJvtMICs272e2r8kQcWpK0m5V5SqpuVFjMp0L.jpg', 1),
(45, 52, 'wisata/9qhngaP13Cz99cnl9lZAe6fuwGZgMburpQTArxpU.jpg', 0),
(46, 52, 'wisata/w285CLIJMjzROTPX4LAIQHiX4U4Z7crbXaV6pWen.jpg', 0),
(47, 51, 'wisata/R436MOsqVtbAzUwk3V2nqxWhoYXYq9OzNnYxZvTt.jpg', 1),
(48, 51, 'wisata/UBfRJqyPpms2nsaKRWrJjTky6VcvN2DhjsxY5Vjm.jpg', 0),
(49, 51, 'wisata/OgjIBYSnQcuLaTLvRUAgEu1xRkdjNWjnrZp41RBG.png', 0),
(50, 50, 'wisata/3vUtZyvPyVw1elEoveKplCJlHhPaR2UPvbDfUnCU.jpg', 1),
(51, 50, 'wisata/O6uR4YzVNZcMUC3DvfDgEig4euGd0sIW9ErP02Ds.webp', 0),
(52, 50, 'wisata/nv8cAo8z63ZnTjqczqgCUIje9N013zriVKAF8KkU.webp', 0),
(53, 49, 'wisata/epjK2Em4mv8EMtQaifm7gZlUyBXcidRxQSa2HyjL.jpg', 1),
(54, 49, 'wisata/79PpU4qSARDPudNbinmTQCSqNCqnEUMix0dfISm1.png', 0),
(55, 49, 'wisata/DiOGKrwshJKXxdkG3gHIF2EG9kL4DAvEuNjAqSEL.webp', 0),
(56, 48, 'wisata/hA8fTsmRMyGXkrkgisA0Kv4swafUscRTcdjdprrB.jpg', 1),
(57, 48, 'wisata/H2w95BZd5J0IqdzBhuUXvjSCdgienkRImYZnJSxL.jpg', 0),
(58, 48, 'wisata/zA4eaoLMwW3IEVctXIFwNffk6znz3qVpqfgaKVsE.jpg', 0),
(59, 47, 'wisata/hTqHe9yR5lVI5IJVMKsw6V4WA948tCekIRPoDsgw.webp', 1),
(60, 47, 'wisata/O4EHkzHKxDNQxQG8cfPapcnX9ucrUyzGsmiGlXNq.png', 0),
(61, 47, 'wisata/Bz4a0QCAoDApseaRByPxfNzx9bWiqhakttIgUWbZ.jpg', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama`) VALUES
(1, 'Alam'),
(2, 'Budaya');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '0001_01_01_000003_create_sessions_table', 2),
(5, '2026_02_07_000000_create_wisata_table', 2),
(6, '2026_02_07_120000_recreate_wisata_table', 3),
(7, '2026_02_07_130000_create_foto_wisata_table', 4),
(8, '2026_02_07_140000_create_kategori_table', 5),
(9, '2026_02_07_150000_add_role_to_users_table', 6),
(10, '2026_02_07_160000_create_rattings_table', 7),
(11, '2026_02_10_000001_create_rekomendasi_table', 8);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rattings`
--

CREATE TABLE `rattings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `wisata_id` bigint(20) UNSIGNED NOT NULL,
  `ratting` tinyint(4) NOT NULL,
  `ulasan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `rattings`
--

INSERT INTO `rattings` (`id`, `user_id`, `wisata_id`, `ratting`, `ulasan`) VALUES
(1, 25, 63, 4, NULL),
(2, 25, 62, 3, NULL),
(3, 25, 61, 5, NULL),
(4, 25, 60, 5, NULL),
(5, 25, 59, 4, NULL),
(6, 25, 58, 0, NULL),
(7, 25, 57, 5, NULL),
(8, 25, 56, 0, NULL),
(9, 25, 55, 0, NULL),
(10, 25, 54, 0, NULL),
(11, 25, 53, 0, NULL),
(12, 25, 52, 0, NULL),
(13, 25, 51, 0, NULL),
(14, 25, 50, 0, NULL),
(15, 25, 49, 0, NULL),
(16, 25, 48, 0, NULL),
(17, 25, 47, 0, NULL),
(18, 26, 63, 0, NULL),
(19, 26, 62, 4, NULL),
(20, 26, 61, 0, NULL),
(21, 26, 60, 3, NULL),
(22, 26, 59, 0, NULL),
(23, 26, 58, 5, NULL),
(24, 26, 57, 4, NULL),
(25, 26, 56, 0, NULL),
(26, 26, 55, 0, NULL),
(27, 26, 54, 0, NULL),
(28, 26, 53, 3, NULL),
(29, 26, 52, 0, NULL),
(30, 26, 51, 0, NULL),
(31, 26, 50, 0, NULL),
(32, 26, 49, 0, NULL),
(33, 26, 48, 0, NULL),
(34, 26, 47, 0, NULL),
(35, 27, 63, 5, NULL),
(36, 27, 62, 0, NULL),
(37, 27, 61, 4, NULL),
(38, 27, 60, 0, NULL),
(39, 27, 59, 0, NULL),
(40, 27, 58, 0, NULL),
(41, 27, 57, 5, NULL),
(42, 27, 56, 4, NULL),
(43, 27, 55, 0, NULL),
(44, 27, 54, 0, NULL),
(45, 27, 53, 0, NULL),
(46, 27, 52, 3, NULL),
(47, 27, 51, 0, NULL),
(48, 27, 50, 0, NULL),
(49, 27, 49, 0, NULL),
(50, 27, 48, 0, NULL),
(51, 27, 47, 0, NULL),
(52, 28, 63, 0, NULL),
(53, 28, 62, 0, NULL),
(54, 28, 61, 0, NULL),
(55, 28, 60, 4, NULL),
(56, 28, 59, 5, NULL),
(57, 28, 58, 0, NULL),
(58, 28, 57, 0, NULL),
(59, 28, 56, 4, NULL),
(60, 28, 55, 3, NULL),
(61, 28, 54, 0, NULL),
(62, 28, 53, 0, NULL),
(63, 28, 52, 0, NULL),
(64, 28, 51, 0, NULL),
(65, 28, 50, 0, NULL),
(66, 28, 49, 0, NULL),
(67, 28, 48, 0, NULL),
(68, 28, 47, 0, NULL),
(69, 29, 63, 3, NULL),
(70, 29, 62, 0, NULL),
(71, 29, 61, 0, NULL),
(72, 29, 60, 0, NULL),
(73, 29, 59, 4, NULL),
(74, 29, 58, 5, NULL),
(75, 29, 57, 0, NULL),
(76, 29, 56, 0, NULL),
(77, 29, 55, 0, NULL),
(78, 29, 54, 3, NULL),
(79, 29, 53, 4, NULL),
(80, 29, 52, 0, NULL),
(81, 29, 51, 0, NULL),
(82, 29, 50, 0, NULL),
(83, 29, 49, 0, NULL),
(84, 29, 48, 0, NULL),
(85, 29, 47, 0, NULL),
(86, 30, 63, 0, NULL),
(87, 30, 62, 5, NULL),
(88, 30, 61, 0, NULL),
(89, 30, 60, 4, NULL),
(90, 30, 59, 0, NULL),
(91, 30, 58, 0, NULL),
(92, 30, 57, 0, NULL),
(93, 30, 56, 3, NULL),
(94, 30, 55, 0, NULL),
(95, 30, 54, 0, NULL),
(96, 30, 53, 0, NULL),
(97, 30, 52, 4, NULL),
(98, 30, 51, 0, NULL),
(99, 30, 50, 0, NULL),
(100, 30, 49, 0, NULL),
(101, 30, 48, 0, NULL),
(102, 30, 47, 0, NULL),
(103, 31, 63, 4, NULL),
(104, 31, 62, 0, NULL),
(105, 31, 61, 5, NULL),
(106, 31, 60, 0, NULL),
(107, 31, 59, 0, NULL),
(108, 31, 58, 0, NULL),
(109, 31, 57, 0, NULL),
(110, 31, 56, 0, NULL),
(111, 31, 55, 4, NULL),
(112, 31, 54, 0, NULL),
(113, 31, 53, 0, NULL),
(114, 31, 52, 0, NULL),
(115, 31, 51, 3, NULL),
(116, 31, 50, 0, NULL),
(117, 31, 49, 0, NULL),
(118, 31, 48, 0, NULL),
(119, 31, 47, 0, NULL),
(120, 32, 63, 0, NULL),
(121, 32, 62, 0, NULL),
(122, 32, 61, 0, NULL),
(123, 32, 60, 0, NULL),
(124, 32, 59, 0, NULL),
(125, 32, 58, 4, NULL),
(126, 32, 57, 5, NULL),
(127, 32, 56, 0, NULL),
(128, 32, 55, 0, NULL),
(129, 32, 54, 4, NULL),
(130, 32, 53, 0, NULL),
(131, 32, 52, 0, NULL),
(132, 32, 51, 0, NULL),
(133, 32, 50, 3, NULL),
(134, 32, 49, 0, NULL),
(135, 32, 48, 0, NULL),
(136, 32, 47, 0, NULL),
(137, 33, 63, 0, NULL),
(138, 33, 62, 3, NULL),
(139, 33, 61, 0, NULL),
(140, 33, 60, 0, NULL),
(141, 33, 59, 4, NULL),
(142, 33, 58, 0, NULL),
(143, 33, 57, 0, NULL),
(144, 33, 56, 0, NULL),
(145, 33, 55, 0, NULL),
(146, 33, 54, 0, NULL),
(147, 33, 53, 0, NULL),
(148, 33, 52, 5, NULL),
(149, 33, 51, 0, NULL),
(150, 33, 50, 0, NULL),
(151, 33, 49, 4, NULL),
(152, 33, 48, 0, NULL),
(153, 33, 47, 0, NULL),
(154, 34, 63, 0, NULL),
(155, 34, 62, 0, NULL),
(156, 34, 61, 4, NULL),
(157, 34, 60, 0, NULL),
(158, 34, 59, 0, NULL),
(159, 34, 58, 0, NULL),
(160, 34, 57, 3, NULL),
(161, 34, 56, 0, NULL),
(162, 34, 55, 0, NULL),
(163, 34, 54, 0, NULL),
(164, 34, 53, 4, NULL),
(165, 34, 52, 0, NULL),
(166, 34, 51, 0, NULL),
(167, 34, 50, 0, NULL),
(168, 34, 49, 0, NULL),
(169, 34, 48, 5, NULL),
(170, 34, 47, 0, NULL),
(171, 35, 47, 5, NULL),
(172, 35, 59, 3, NULL),
(173, 35, 63, 5, NULL),
(174, 35, 54, 3, NULL),
(175, 35, 62, 5, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `rekomendasi`
--

CREATE TABLE `rekomendasi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `id_wisata` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `rekomendasi`
--

INSERT INTO `rekomendasi` (`id`, `id_user`, `id_wisata`) VALUES
(1431, 35, 51),
(1428, 35, 52),
(1429, 35, 55),
(1430, 35, 56),
(1427, 35, 60),
(1426, 35, 61);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1iAwqI02DEjmzmlFnfkCweflJmgSscKxt3bwl53C', 35, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiQjhjUVBvUHNBTVBPSkJ5Qm5TZTdkazNJenc4dDdRUnE4VURpZGNOUCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC93aXNhdGF3YW4vcmVrb21lbmRhc2kvcHJvc2VzIjtzOjU6InJvdXRlIjtzOjI4OiJ3aXNhdGF3YW4ucmVrb21lbmRhc2kucHJvc2VzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MzU7czo3OiJ1c2VyX2lkIjtpOjM1O3M6MTA6InVzZXJfZW1haWwiO3M6MTQ6Im1pcmFAZ21haWwuY29tIjtzOjk6InVzZXJfcm9sZSI7czo5OiJ3aXNhdGF3YW4iO30=', 1772714770),
('ejmdhElBiZDlGVGChlpY97kBhT4KjQ17ZZ8TtkzZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:148.0) Gecko/20100101 Firefox/148.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSmdmUk1STk54djFVNEJZZ1ZXMU5TRDd3bDlISVF5OEZ2TjF4TTc2UiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=', 1771067679);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('wisatawan','admin') NOT NULL DEFAULT 'wisatawan',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', NULL, '$2y$12$Cj3Q3iFAkLR9CxQlxyFO0e/oWbE4lmjqW28CgiXTvx9O9CqiFFm8W', 'admin', NULL, '2026-02-06 14:17:06', '2026-02-06 14:17:06'),
(25, 'Fitri Ramadhani', 'fitri.ramadhani@gmail.com', NULL, '$2y$12$0yDoc4ZrfAFNLhG8W5MnZOYCa.2Gr/Xx6dtlWYr2TBvhoShnSfsTO', 'wisatawan', NULL, '2026-02-08 03:04:18', '2026-02-10 19:52:20'),
(26, 'Rizky Maulana', 'rizky.maulana@gmail.com', NULL, '$2y$12$0LeV9wnh15AiqEJCdeil5eFJdxa29MqVKtE4wgt9s1JFO0uG36ssK', 'wisatawan', NULL, '2026-02-08 03:04:18', '2026-02-10 19:52:04'),
(27, 'Dewi Lestari', 'dewi.lestari@gmail.com', NULL, '$2y$12$8XMvFPQSD55xxa3.HiwczezQFkodAMt8qyXEkieIIPR81ge0yKTg.', 'wisatawan', NULL, '2026-02-08 03:04:18', '2026-02-10 19:51:51'),
(28, 'Ahmad Fauzi', 'ahmad.fauzi@gmail.com', NULL, '$2y$12$1DwjfTMR6dxMpW7qKhyax.Hx0VPOcGHggqdR96C4DJhG.l.3l9GGe', 'wisatawan', NULL, '2026-02-08 03:04:18', '2026-02-10 19:51:37'),
(29, 'Nurul Hidayah', 'nurul.hidayah@gmail.com', NULL, '$2y$12$MSZiPnqoemIOqNYZDuH/uu8byGvRHoEXeJr39hgIcSZuhyFPKlLL6', 'wisatawan', NULL, '2026-02-08 03:04:18', '2026-02-10 19:51:24'),
(30, 'Dedi Kurniawan', 'dedi.kurniawan@gmail.com', NULL, '$2y$12$w4tOQ5/Lgg29G4qRECdhx.s3wLXkYLV/kL85gvKrgJO1fX0raRvmC', 'wisatawan', NULL, '2026-02-08 03:04:18', '2026-02-10 19:51:11'),
(31, 'Rina Oktaviani', 'rina.oktaviani@gmail.com', NULL, '$2y$12$U4rOTvcpW2x.dMBMXg0g1.0R4FEj8LTzG1rT3oVLD46TD2V8Unqc.', 'wisatawan', NULL, '2026-02-08 03:04:18', '2026-02-10 19:50:56'),
(32, 'Siti Aisyah', 'siti.aisyah@gmail.com', NULL, '$2y$12$yCJ6SX81KPB/ck9Ccc2DI.AUb5D5n7M.yJU7.WOKrYrczqB8xve5e', 'wisatawan', NULL, '2026-02-08 03:04:18', '2026-02-10 19:50:44'),
(33, 'Budi Santoso', 'budi.santoso@gmail.com', NULL, '$2y$12$xnyOhhD7CnA4N/zA/ZhJC..cJQoHEDEVUCxxvY1xiP/DSXEGn1zJW', 'wisatawan', NULL, '2026-02-08 03:04:18', '2026-02-10 19:50:30'),
(34, 'Andi Pratama', 'andi.pratama@gmail.com', NULL, '$2y$12$aYLHmOzATbCvRQl6vOxW4e2liuKl1Y1Ij.z2WDojtclC2XlYhXkJS', 'wisatawan', NULL, '2026-02-08 03:04:18', '2026-02-10 19:49:49'),
(35, 'Mira Aulia', 'mira@gmail.com', NULL, '$2y$12$4aR9Ow/p39q3iqBxzy9DAuPU4b9/HsCD1t11EMQgz1h410kaEaFl2', 'wisatawan', NULL, '2026-02-08 04:22:05', '2026-02-10 19:52:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `wisata`
--

CREATE TABLE `wisata` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(150) NOT NULL,
  `slug` varchar(160) NOT NULL,
  `kategori_id` int(10) UNSIGNED NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `deskripsi` text NOT NULL,
  `fasilitas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`fasilitas`)),
  `jam_buka` varchar(50) DEFAULT NULL,
  `rating_avg` decimal(3,2) NOT NULL DEFAULT 0.00,
  `jml_rating` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `wisata`
--

INSERT INTO `wisata` (`id`, `nama`, `slug`, `kategori_id`, `latitude`, `longitude`, `deskripsi`, `fasilitas`, `jam_buka`, `rating_avg`, `jml_rating`) VALUES
(47, 'Air Terjun Bihewa', 'air-terjun-bihewa', 1, -3.15000000, 135.65000000, 'Air terjun tertinggi di Papua (40m, 7 tingkat), cocok trekking dan berenang.', '[\"Parkir\"]', '', 4.50, 1),
(48, 'Kwatisore', 'kwatisore', 1, -3.25000000, 135.08000000, 'Desa di Taman Nasional, spot berenang dengan hiu paus dan anggrek liar.', '[]', '', 4.80, 1),
(49, 'Taman Nasional Teluk Cendrawasih', 'taman-nasional-teluk-cendrawasih', 1, -2.50000000, 134.63300000, 'Taman laut terbesar Indonesia (1.4 juta ha), diving dengan biota laut kaya.', '[]', '', 4.70, 1),
(50, 'Pantai Gedo', 'pantai-gedo', 1, -3.30940000, 135.54660000, 'Pantai strategis dekat kota, pasir lembut, ombak tenang, fasilitas pemerintah.', '[\"Parkir\",\"Toilet\"]', '', 4.20, 1),
(51, 'Pantai Nusi', 'pantai-nusi', 1, -3.16610000, 135.65420000, 'Pantai pasir hitam unik, ideal diving/snorkeling dengan panorama bawah laut.', '[]', '', 4.30, 1),
(52, 'Pantai Ahe (Pulau Ahe)', 'pantai-ahe-pulau-ahe', 1, -3.40000000, 135.20000000, 'Pantai sepi untuk surfing/diving, air tosca, biota laut indah, dekat Mora.', '[]', '', 4.40, 1),
(53, 'Pantai Monalisa', 'pantai-monalisa', 1, -3.38000000, 135.35000000, 'Pantai baru dengan jembatan spot foto, sunset indah, snorkeling tersedia.', '[]', '', 4.10, 1),
(54, 'Danau Mamae', 'danau-mamae', 1, -3.33070000, 135.63120000, 'Danau hijau dengan bukit, cocok memancing dan hiking, dekat air terjun.', '[\"Parkir\"]', '', 4.00, 1),
(55, 'Pantai Burate', 'pantai-burate', 1, -3.35000000, 135.45000000, 'Pantai tenang untuk relaksasi, ombak lembut, suasana alam alami.', '[]', '', 3.90, 1),
(56, 'Pantai Yamari', 'pantai-yamari', 1, -3.36000000, 135.52000000, 'Pantai keluarga dekat kota, piknik dengan pepohonan rindang.', '[]', '', 4.00, 1),
(57, 'Pantai MAF (Pantai Nabire)', 'pantai-maf-pantai-nabire', 1, -3.36130000, 135.49470000, 'Pantai favorit sunset dekat bandara, ombak tenang, spot pesawat.', '[\"Parkir\"]', '', 4.20, 1),
(58, 'Pulau Pepaya', 'pulau-pepaya', 1, -3.19270000, 135.08130000, 'Pulau tak berpenghuni untuk snorkeling/birdwatching, biota karang kaya.', '[]', '', 4.60, 1),
(59, 'Air Terjun Kura-kura', 'air-terjun-kura-kura', 1, -3.42000000, 135.30000000, 'Air terjun kecil dekat Danau Mamae, spot berenang alami di hutan.', '[]', '', 4.00, 1),
(60, 'Pulau Mowirin', 'pulau-mowirin', 1, -3.30000000, 135.40000000, 'Pulau/Pantai', '[]', '', 4.30, 1),
(61, 'Pantai Irio', 'pantai-irio', 1, -3.35000000, 135.48000000, 'Pantai', '[\"Parkir\"]', '', 3.80, 1),
(62, 'Kolam Pemancingan Kalisemen Wanggar', 'kolam-pemancingan-kalisemen-wanggar', 1, -3.40000000, 135.60000000, 'Kolam Pemancingan', '[]', '', 3.50, 1),
(63, 'Bendungan Kalibumi Bumiraya Wanggar', 'bendungan-kalibumi-bumiraya-wanggar', 1, -3.38000000, 135.55000000, 'Bendungan', '[\"Parkir\"]', '', 3.70, 1);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `foto_wisata`
--
ALTER TABLE `foto_wisata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `foto_wisata_wisata_id_foreign` (`wisata_id`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

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
-- Indeks untuk tabel `rattings`
--
ALTER TABLE `rattings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rattings_user_id_foreign` (`user_id`),
  ADD KEY `rattings_wisata_id_foreign` (`wisata_id`);

--
-- Indeks untuk tabel `rekomendasi`
--
ALTER TABLE `rekomendasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rekomendasi_id_user_id_wisata_unique` (`id_user`,`id_wisata`),
  ADD KEY `rekomendasi_id_wisata_foreign` (`id_wisata`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indeks untuk tabel `wisata`
--
ALTER TABLE `wisata`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `foto_wisata`
--
ALTER TABLE `foto_wisata`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `rattings`
--
ALTER TABLE `rattings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT untuk tabel `rekomendasi`
--
ALTER TABLE `rekomendasi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1432;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `wisata`
--
ALTER TABLE `wisata`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `foto_wisata`
--
ALTER TABLE `foto_wisata`
  ADD CONSTRAINT `foto_wisata_wisata_id_foreign` FOREIGN KEY (`wisata_id`) REFERENCES `wisata` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `rattings`
--
ALTER TABLE `rattings`
  ADD CONSTRAINT `rattings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `rattings_wisata_id_foreign` FOREIGN KEY (`wisata_id`) REFERENCES `wisata` (`id`);

--
-- Ketidakleluasaan untuk tabel `rekomendasi`
--
ALTER TABLE `rekomendasi`
  ADD CONSTRAINT `rekomendasi_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rekomendasi_id_wisata_foreign` FOREIGN KEY (`id_wisata`) REFERENCES `wisata` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
