<?php
session_start();

// Hubungkan ke database atau include config
require '../config.php';

// Ambil ID koran dari query parameter
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: user_dashboard.php'); // Redirect ke halaman user dashboard jika tidak ada ID
    exit;
}

// Ambil data koran dari database
$stmt = $pdo->prepare('SELECT * FROM newspapers WHERE id = ?');
$stmt->execute([$id]);
$newspaper = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$newspaper) {
    header('Location: user_dashboard.php'); // Redirect ke halaman user dashboard jika koran tidak ditemukan
    exit;
}

require '../components/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Koran - <?php echo htmlspecialchars($newspaper['title']); ?></title>
    <!-- Tambahkan link CSS untuk styling (misalnya menggunakan Tailwind CSS) -->
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="container mx-auto py-4 px-4">
            <h1 class="text-2xl font-bold">Lihat Koran - <?php echo htmlspecialchars($newspaper['title']); ?></h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto py-4 px-4">
        <div class="bg-white shadow-md rounded-lg p-4">
            <h2 class="text-xl font-bold mb-4"><?php echo htmlspecialchars($newspaper['title']); ?></h2>
            <p class="text-gray-600 mb-2">Tanggal Terbit: <?php echo htmlspecialchars($newspaper['publication_date']); ?></p>
            <p class="text-gray-600 mb-2">Kategori: <?php echo htmlspecialchars($newspaper['category']); ?></p>
            <!-- Tampilkan dokumen PDF menggunakan tag object -->
            <object data="../uploads/<?php echo htmlspecialchars($newspaper['pdf_file']); ?>" type="application/pdf" width="100%" height="800px">
                <p class="text-gray-600">Maaf, browser Anda tidak mendukung penampilan PDF. Silakan <a href="../uploads/<?php echo htmlspecialchars($newspaper['pdf_file']); ?>">unduh dokumen</a> untuk melihatnya.</p>
            </object>
        </div>
        <a href="../index.php" class="mt-4 ml-4 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded inline-block">Kembali ke Dashboard</a>
    </main>

    <!-- Footer (opsional) -->
    <footer class="bg-gray-200 text-center py-2 mt-4">
        <p>&copy; <?php echo date('Y'); ?> Your Newspaper Archive</p>
    </footer>
</body>
</html>
