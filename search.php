<?php
session_start();

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

// Pagination
$itemsPerPage = 10; // jumlah item per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Kolom yang akan dicari
$searchColumns = ['title', 'publication_date', 'category', 'c.name'];

// Bangun query pencarian dinamis
$whereClauses = [];
$params = [];
$searchTerm = "%$query%";
foreach ($searchColumns as $column) {
    $whereClauses[] = "$column LIKE ?";
    $params[] = $searchTerm;
}
$whereSql = implode(' OR ', $whereClauses);

// Query untuk ambil kategori
$stmtCategories = $pdo->query("SELECT * FROM categories");
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

// Hitung total item
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM newspapers n 
    LEFT JOIN categories c ON n.category_id = c.id 
    WHERE $whereSql
");
$stmt->execute($params);
$totalItems = $stmt->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Ambil data koran yang sesuai dengan query pencarian
$stmt = $pdo->prepare("
    SELECT n.*, c.name as category_name 
    FROM newspapers n 
    LEFT JOIN categories c ON n.category_id = c.id 
    WHERE $whereSql 
    LIMIT ? OFFSET ?
");
$params[] = $itemsPerPage;
$params[] = $offset;
$stmt->execute($params);
$newspapers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
} else {
    $role = 'user'; // Default jika tidak ada role yang ditentukan
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian - <?php echo htmlspecialchars($query); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <!-- Tambahkan Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<body class="bg-gray-100">
    <?php require 'components/header.php' ?>

    <!-- Header -->
    <header class="shadow">
        <div class="container mx-auto py-4 px-4">
            <nav class="flex justify-between items-center" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="index.php"
                            class="inline-flex items-center text-sm font-medium text-black hover:text-blue-600 dark:hover:text-gray-600">
                            <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="m19 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                            </svg>
                            Beranda
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                            <span
                                class="ms-1 text-sm font-medium text-black hover:text-blue-600 dark:hover:text-gray-600">Hasil
                                Pencarian</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="container mx-auto items-center flex justify-between">
            <a href="index.php"
                class="flex bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded inline-block">
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 12h14M5 12l4-4m-4 4 4 4" />
                </svg>
                Kembali
            </a>

            <form class="flex-grow max-w-md mx-auto mt-4 md:mt-0" action="search.php" method="GET">
                <label for="default-search"
                    class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="search" id="default-search" name="query"
                        class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Telusuri, Koran..." required />
                    <button type="submit"
                        class="text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
                </div>
            </form>
        </div>
        <div class="container mx-auto py-4 px-4">
            <h1 class="text-2xl font-bold text-center">Hasil Pencarian untuk "<?php echo htmlspecialchars($query); ?>"
            </h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto py-4 px-4">
        <?php if (count($newspapers) > 0) : ?>
        <div class="row">
            <div class="container mx-auto px-4 py-6">
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 border-b-2 border-gray-300 bg-gray-200 text-left text-xs leading-4 text-gray-600 uppercase tracking-wider">
                                        Judul</th>
                                    <th
                                        class="px-6 py-3 border-b-2 border-gray-300 bg-gray-200 text-left text-xs leading-4 text-gray-600 uppercase tracking-wider">
                                        Tanggal Terbit</th>
                                    <th
                                        class="px-6 py-3 border-b-2 border-gray-300 bg-gray-200 text-left text-xs leading-4 text-gray-600 uppercase tracking-wider">
                                        Kategori</th>
                                    <th
                                        class="px-6 py-3 border-b-2 border-gray-300 bg-gray-200 text-left text-xs leading-4 text-gray-600 uppercase tracking-wider">
                                        Tema</th>
                                    <th
                                        class="px-6 py-3 border-b-2 border-gray-300 bg-gray-200 text-left text-xs leading-4 text-gray-600 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <?php foreach ($newspapers as $newspaper) : ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">
                                            <?php echo htmlspecialchars($newspaper['title']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">
                                            <?php echo htmlspecialchars($newspaper['publication_date']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">
                                            <?php echo htmlspecialchars($newspaper['category_name']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">
                                            <?php echo htmlspecialchars($newspaper['category']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-300">
                                        <a href="user/view_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                            class="text-blue-500 hover:text-blue-700">Lihat</a>
                                        <?php if ($role === 'admin'): ?>
                                        <a href="admin/edit_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                            class="text-green-500 hover:text-green-700 ml-2">Edit</a><a
                                            href="delete_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                            class="text-red-500 hover:text-red-700 ml-2"
                                            onclick="event.preventDefault(); 
             Swal.fire({
                 title: 'Apakah Anda yakin?',
                 text: 'Anda tidak dapat mengembalikan koran ini setelah dihapus!',
                 icon: 'warning',
                 showCancelButton: true,
                 confirmButtonColor: '#3085d6',
                 cancelButtonColor: '#d33',
                 confirmButtonText: 'Ya, hapus saja!',
                 cancelButtonText: 'Batal'
             }).then((result) => {
                 if (result.isConfirmed) {
                     window.location.href = 'user/delete_newspaper.php?id=<?php echo $newspaper['id']; ?>';
                 }
             });">Hapus</a>

                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pagination -->
        <div class="flex justify-center mt-4">
            <?php if ($page > 1) : ?>
            <a href="search.php?query=<?php echo urlencode($query); ?>&page=<?php echo $page - 1; ?>"
                class="text-blue-600 hover:text-blue-900">&laquo; Sebelumnya</a>
            <?php endif; ?>
            <?php if ($page < $totalPages) : ?>
            <a href="search.php?query=<?php echo urlencode($query); ?>&page=<?php echo $page + 1; ?>"
                class="ml-4 text-blue-600 hover:text-blue-900">Selanjutnya &raquo;</a>
            <?php endif; ?>
        </div>
        <?php else : ?>
        <script>
        // Tampilkan Sweet Alert jika tidak ada hasil
        Swal.fire({
            icon: 'info',
            title: 'Oops...',
            text: 'Tidak ada hasil pencarian untuk "<?php echo htmlspecialchars($query); ?>"',
        }).then(function() {
            window.location.href = 'index.php'; // Redirect ke halaman lain jika perlu
        });
        </script>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
</body>

</html>
