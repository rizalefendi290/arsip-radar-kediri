<?php
// edit_kategori.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect ke halaman login jika bukan admin
    exit;
}
include '../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Query untuk mengambil data kategori berdasarkan ID
    $query = "SELECT * FROM categories WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$category) {
        die("Kategori tidak ditemukan.");
    }
} else {
    die("ID Kategori tidak diberikan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proses form untuk update data kategori
    $nama_kategori = $_POST['name'];
    
    // Query untuk update data kategori
    $query = "UPDATE categories SET name = :name WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':name', $nama_kategori);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        header('Location: manage_categories.php');
        exit;
    } else {
        echo "Gagal mengupdate kategori.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <?php
    require 'navbar_admin.php'
    ?>
    <div class="p-4 sm:ml-64">
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
        <div class="max-w-md mx-auto bg-white shadow-md rounded my-8 p-4">
        <h2 class="text-2xl font-bold mb-4">Edit Kategori</h2>
        <form action="" method="POST">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-300">Simpan</button>
            </div>
        </form>
    </div>
        </div>
    </div>
</body>
</html>
