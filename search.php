<?php
// Pastikan session sudah dimulai
session_start();

// Sertakan file konfigurasi database
require 'config.php';

// Tangkap nilai pencarian dari input form
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Query untuk mencari koran berdasarkan judul atau kategori
$stmt = $pdo->prepare('SELECT * FROM newspapers WHERE title LIKE :id OR category LIKE :id');
$stmt->execute(['id' => "%$id%"]);
$newspapers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Sertakan header untuk tampilan pencarian
require 'components/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - User Dashboard</title>
    <!-- Tambahkan link CSS untuk styling (misalnya menggunakan Tailwind CSS) -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="container mx-auto py-4 px-4">
            <h1 class="text-2xl font-bold">Search Results</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto py-4 px-4">
        <h2 class="text-xl font-bold mb-4">Search results for "<?php echo htmlspecialchars($query); ?>"</h2>

        <!-- Daftar Hasil Pencarian -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <?php foreach ($newspapers as $newspaper): ?>
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h3 class="text-lg font-bold mb-2"><?php echo htmlspecialchars($newspaper['title']); ?></h3>
                    <p class="text-gray-600 mb-2">Tanggal Terbit: <?php echo htmlspecialchars($newspaper['publication_date']); ?></p>
                    <p class="text-gray-600 mb-2">Kategori: <?php echo htmlspecialchars($newspaper['category']); ?></p>
                    <a href="user/view_newspaper.php?id=<?php echo $newspaper['id']; ?>" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-block">Lihat Koran</a>
                </div>
            <?php endforeach; ?>
        </div>
        
        <a href="index.php" class="mt-4 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded inline-block">Kembali ke Dashboard</a>
    </main>

    <!-- Footer (opsional) -->
    <footer class="bg-gray-200 text-center py-2 mt-4">
        <p>&copy; <?php echo date('Y'); ?> Your Newspaper Archive</p>
    </footer>
</body>
</html>