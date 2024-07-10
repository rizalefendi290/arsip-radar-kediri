<?php
session_start();

// Pastikan hanya user yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

// Hubungkan ke database atau include config
require 'config.php';

// Ambil query pencarian
$query = $_GET['query'] ?? '';
$query = trim($query);

// Jika query kosong, redirect ke dashboard
if (empty($query)) {
    header('Location: user_dashboard.php');
    exit;
}

// Ambil data koran yang sesuai dengan query pencarian
$stmt = $pdo->prepare('SELECT * FROM newspapers WHERE title LIKE ? OR category LIKE ?');
$searchTerm = "%$query%";
$stmt->execute([$searchTerm, $searchTerm]);
$newspapers = $stmt->fetchAll(PDO::FETCH_ASSOC);

require 'components/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian - <?php echo htmlspecialchars($query); ?></title>
    <!-- Tambahkan link CSS untuk styling (misalnya menggunakan Tailwind CSS) -->
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="container mx-auto py-4 px-4">
            <h1 class="text-2xl font-bold">Hasil Pencarian untuk "<?php echo htmlspecialchars($query); ?>"</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto py-4 px-4">
        <?php if (count($newspapers) > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-2 px-4">Judul</th>
                            <th class="py-2 px-4">Tanggal Terbit</th>
                            <th class="py-2 px-4">Kategori</th>
                            <th class="py-2 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php foreach ($newspapers as $newspaper): ?>
                        <tr>
                            <td class="py-2 px-4"><?php echo htmlspecialchars($newspaper['title']); ?></td>
                            <td class="py-2 px-4"><?php echo htmlspecialchars($newspaper['publication_date']); ?></td>
                            <td class="py-2 px-4"><?php echo htmlspecialchars($newspaper['category']); ?></td>
                            <td class="py-2 px-4">
                                <a href="user/view_newspaper.php?id=<?php echo $newspaper['id']; ?>" class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-2 rounded inline-block mr-2">Lihat</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-600">Tidak ada hasil yang ditemukan untuk "<?php echo htmlspecialchars($query); ?>".</p>
        <?php endif; ?>
        <a href="user_dashboard.php" class="mt-4 ml-4 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded inline-block">Kembali ke Dashboard</a>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-200 text-center py-2 mt-4">
        <p>&copy; <?php echo date('Y'); ?> Your Newspaper Archive</p>
    </footer>
</body>
</html>
