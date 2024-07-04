<?php
// Pastikan hanya admin yang dapat mengakses halaman ini
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect ke halaman login jika bukan admin
    exit;
}

// Hubungkan ke database atau include config
require '../config.php';

// Ambil data koran dari database (contoh saja)
$stmt = $pdo->query('SELECT * FROM newspapers');
$newspapers = $stmt->fetchAll(PDO::FETCH_ASSOC);

require '../components/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Tambahkan link CSS untuk styling (misalnya menggunakan Tailwind CSS) -->
</head>

<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="container mx-auto py-4 px-4">
            <h1 class="text-2xl font-bold">Admin Dashboard</h1>
            <!-- Menu navigasi (opsional) -->
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto py-4 px-4">
        <!-- Tombol untuk tambah koran baru -->
        <a href="add_newspaper.php"
            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mb-4 inline-block">Tambah Koran
            Baru</a>

        <!-- Tabel untuk daftar koran -->
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
                            <a href="view_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-2 rounded inline-block mr-2">Lihat</a>
                            <a href="edit_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-2 rounded inline-block">Edit</a>
                            <a href="delete_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded inline-block ml-2">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Footer (opsional) -->
    <footer class="bg-gray-200 text-center py-2 mt-4">
        <p>&copy; 2024 Your Newspaper Archive</p>
    </footer>
</body>

</html>