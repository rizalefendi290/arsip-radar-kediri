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
    header('Location: admin_dashboard.php'); // Redirect ke halaman admin jika tidak ada ID
    exit;
}

// Hapus koran dari database berdasarkan ID
$stmt = $pdo->prepare('DELETE FROM newspapers WHERE id = ?');
if ($stmt->execute([$id])) {
    // Redirect kembali ke halaman admin dashboard setelah menghapus
    header('Location: admin_dashboard.php');
    exit;
} else {
    echo "Gagal menghapus koran.";
}
?>
