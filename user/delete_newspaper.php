<?php
// Pastikan hanya admin yang dapat mengakses halaman ini
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect ke halaman login jika bukan admin
    exit;
}

// Hubungkan ke database atau include config
require '../config.php';

// Ambil ID koran dari query parameter
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: all_newspaper.php'); // Redirect ke halaman admin jika tidak ada ID
    exit;
}

// Ambil path file PDF sebelum menghapus dari database
$stmt = $pdo->prepare('SELECT pdf_file FROM newspapers WHERE id = ?');
$stmt->execute([$id]);
$koran = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$koran) {
    header('Location: all_newspaper.php'); // Redirect ke halaman admin jika koran tidak ditemukan
    exit;
}

$pdf_file_path = $koran['pdf_file'];

// Hapus koran dari database berdasarkan ID
$stmt = $pdo->prepare('DELETE FROM newspapers WHERE id = ?');
if ($stmt->execute([$id])) {
    // Hapus file PDF dari folder uploads jika ada
    if (file_exists($pdf_file_path)) {
        unlink($pdf_file_path); // Hapus file dari folder uploads
    }
    // Redirect kembali ke halaman admin dashboard setelah menghapus
    header('Location: all_newspaper.php');
    exit;
} else {
    echo "Gagal menghapus koran.";
}
?>
