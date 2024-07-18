<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect ke halaman login jika bukan admin
    exit;
}
// Koneksi ke database menggunakan PDO
include '../config.php';

// Query untuk mengambil data kategori
$query = "SELECT * FROM categories";
$stmt = $pdo->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kategori</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <?php require 'navbar_admin.php'; ?>

    <div class="p-4 sm:ml-64">
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
        <div class="max-w-4xl mx-auto bg-white shadow-md rounded my-8">
        <div class="flex justify-between items-center border-b p-6">
            <h2 class="text-2xl font-bold">Data Kategori</h2>
            <a href="tambah_kategori.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-300">Tambah Kategori</a>
        </div>
        
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="border-b-2 border-gray-300 py-2 px-4 text-left">ID</th>
                    <th class="border-b-2 border-gray-300 py-2 px-4 text-left">Nama Kategori</th>
                    <th class="border-b-2 border-gray-300 py-2 px-4 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                <tr class="hover:bg-gray-100">
                    <td class="border-b border-gray-300 py-2 px-4"><?php echo $category['id']; ?></td>
                    <td class="border-b border-gray-300 py-2 px-4"><?php echo $category['name']; ?></td>
                    <td class="border-b border-gray-300 py-2 px-4">
                        <a href="edit_kategori.php?id=<?php echo $category['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                        <span class="mx-1">|</span>
                        <a href="hapus_kategori.php?id=<?php echo $category['id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Anda yakin ingin menghapus kategori ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
        </div>
    </div>

</body>
</html>
