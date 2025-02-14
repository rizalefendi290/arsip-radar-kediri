<?php
// Pastikan hanya admin yang dapat mengakses halaman ini
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect ke halaman login jika bukan admin
    exit;
}

// Hubungkan ke database atau include config
require '../config.php';

// Ambil ID koran dari query parameter dan validasi
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    echo '<script>
        alert("ID tidak valid.");
        window.location.href = "admin_dashboard.php";
    </script>';
    exit;
}

// Ambil path file PDF sebelum menghapus dari database
$stmt = $pdo->prepare('SELECT pdf_file FROM newspapers WHERE id = :id');
$stmt->execute([':id' => $id]);
$koran = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$koran) {
    header('Location: admin_dashboard.php');
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
    header('Location: admin_dashboard.php');
    exit;
} else {
    echo "Gagal menghapus koran.";
}

?>
