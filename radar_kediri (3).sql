-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 15 Jul 2024 pada 15.52
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `radar_kediri`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `newspapers`
--

CREATE TABLE `newspapers` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `publication_date` date NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `pdf_file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `newspaper_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `newspapers`
--

INSERT INTO `newspapers` (`id`, `title`, `publication_date`, `category`, `thumbnail`, `pdf_file`, `created_at`, `newspaper_type`) VALUES
(16, 'Senin 17 Juli 2006', '2005-07-17', 'Pejabat Pemkab Tersangka, Persiba, Macan Putih di Hotel', NULL, '../uploads/Senin, 17 Juli 2006.pdf', '2024-07-11 08:37:26', NULL),
(18, 'Jumat 14 Juli 2006', '2006-07-14', 'Siswa Siluman, Pastek Nagih Janji, Banyak SD kurang murid, Persik', NULL, '../uploads/Jumat, 14 Juli 2006.pdf', '2024-07-11 08:40:28', NULL),
(20, 'Jumat 21 Juli 2006', '2006-07-21', 'Persik membara, Gonzales, Persik lawan arema, PSM vs Persekabpas', NULL, '../uploads/Jumat, 21 Juli 2006.pdf', '2024-07-11 08:41:52', NULL),
(22, 'Minggu, 04 Februari 2001', '2001-02-04', 'terowongan Gunung kelud', NULL, '../uploads/Minggu, 04 Februari 2001.pdf', '2024-07-11 11:38:23', NULL),
(23, 'Senin 12 Februari 2001', '2001-02-12', 'Pasang Telpon, Ditangkap bawa sebrankas uang brazil, Gembong curanmor,, Persik demam lapangan', NULL, '../uploads/Senin, 12 Februari 2001.pdf', '2024-07-12 08:53:19', NULL),
(24, 'Senin 05 Februari 2001', '2001-02-05', 'Perangkat Boikot, Nagih Utang, Cabang Atletik', NULL, '../uploads/Senin, 05 Februari 2001.pdf', '2024-07-12 08:55:25', NULL),
(25, 'Selasa 27 Februari 2001', '2001-02-27', 'Pemain Jepang gabung persik, Pengusaha mokong dijemput paksa', NULL, '../uploads/Selasa, 27 Februari 2001.pdf', '2024-07-12 09:01:43', NULL),
(26, 'Selasa 27 Februari 2001', '2001-02-27', 'Pemain Jepang gabung persik, Pengusaha mokong dijemput paksa', NULL, '../uploads/Selasa, 27 Februari 2001.pdf', '2024-07-12 09:02:47', NULL),
(27, 'Selasa, 20 Februari 2001', '2001-02-20', 'Pasien Meninggal,', NULL, '../uploads/Selasa, 20 Februari 2001.pdf', '2024-07-12 09:03:28', NULL),
(28, 'Selasa, 20 Februari 2001', '2001-02-20', 'Pasien Meninggal,', NULL, '../uploads/Selasa, 20 Februari 2001.pdf', '2024-07-12 09:08:49', NULL),
(29, 'Selasa, 13 Februari 2001', '2001-02-13', 'Buruh dan Warga tuntut tanah perkebunan, DPD Golkar dijaga polisi super ketat', NULL, '../uploads/Selasa, 13 Februari 2001.pdf', '2024-07-12 09:09:59', NULL),
(30, 'dawwa', '2024-07-04', 'sepak bola, madrid', NULL, '../uploads/admin,+Journal+manager,+Jurnal+7.+Muhammad+Arif+Sutrasno,+Noor+Yulia,+Nanda+Aula+Rumana,+Puteri+Fannya.pdf', '2024-07-15 12:52:06', 'jurnal');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `role`, `name`) VALUES
(2, 'user', 'user@gmail.com', '$2y$10$qQTFl1XhqrjKfEaxyCS.2uaFcdscg5go4wT9Yxr/vx3Ivhwr8MJO.', '2024-07-04 13:05:24', 'user', 'Paman'),
(6, 'admin', 'admin@gmail.com', '$2y$10$EX4ucXT80r/vazYU9fE0nOb0fG0Glp8WxWeb1Dr6kye5mrpKnEXSW', '2024-07-04 13:13:30', 'admin', 'admin'),
(7, 'user123098', 'user1@gmail.com', '$2y$10$KJ/ZmcLOhRM6QvSFVcSBEe4APZCtDH6dtCibeMVTxtEAm5heKJZ36', '2024-07-07 17:39:23', 'user', ''),
(8, 'user123', 'user123@gmail.com', '$2y$10$qdx/Lz8sCoZTU7Wha71ClOOihG26ZXbt0xqWADkhMGYuiCW5p52IC', '2024-07-11 11:41:25', 'user', ''),
(9, 'FatchurRidho', 'ridho@gmail.com', '$2y$10$W1QZ1xyuRx3CYtKSDlHDLuLJZxVGsndy6GdP29Crg74SOU4KNnFcO', '2024-07-15 09:33:52', 'user', ''),
(10, 'ridho009', 'ridho009@gmail.com', '$2y$10$fdLDX0komXIi5qhuYv63oO3CFYrmX2dvfvZcsUvAXcpns1ruXPETS', '2024-07-15 09:35:05', 'user', 'Ridho'),
(11, 'ridho001', 'ridho001@gmail.com', '$2y$10$86D/JRtxunv6GLTYLaRJj.3sI5zSd8n3rccBnOzPAfarm0lAtEv8C', '2024-07-15 12:25:51', 'user', 'Fatchur Ridho'),
(12, 'admin123', 'admin123@gmail.com', '$2y$10$cV.lNiYIRz5Vl3Z2HDjgUuClrg.VEr5FBSk7o9qKEcFapfWyFFBZu', '2024-07-15 12:27:53', 'user', 'admin123');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `newspapers`
--
ALTER TABLE `newspapers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `newspapers`
--
ALTER TABLE `newspapers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
