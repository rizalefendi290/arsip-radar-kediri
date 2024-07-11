-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Jul 2024 pada 16.48
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `newspapers`
--

INSERT INTO `newspapers` (`id`, `title`, `publication_date`, `category`, `thumbnail`, `pdf_file`, `created_at`) VALUES
(4, 'korann2', '2024-07-10', 'koran2', NULL, '../uploads/KELOMPOK 4_PENJAMINAN MUTU_REVIEW WEB_INFORMATIKA 6A.pdf', '2024-07-04 14:34:56'),
(5, 'awdwa', '2024-07-18', 'sepak bola', NULL, '../uploads/21161562014_RIZAL EFENDI_REVIEW JURNAL 3_INFORMATIKA 6A.pdf', '2024-07-04 14:47:29');

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
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `role`) VALUES
(2, 'user', 'user@gmail.com', '$2y$10$qQTFl1XhqrjKfEaxyCS.2uaFcdscg5go4wT9Yxr/vx3Ivhwr8MJO.', '2024-07-04 13:05:24', 'user'),
(6, 'admin', 'admin@gmail.com', '$2y$10$EX4ucXT80r/vazYU9fE0nOb0fG0Glp8WxWeb1Dr6kye5mrpKnEXSW', '2024-07-04 13:13:30', 'admin'),
(7, 'user123098', 'user1@gmail.com', '$2y$10$KJ/ZmcLOhRM6QvSFVcSBEe4APZCtDH6dtCibeMVTxtEAm5heKJZ36', '2024-07-07 17:39:23', 'user');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
