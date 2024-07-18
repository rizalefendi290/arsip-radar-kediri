<?php
// Pastikan hanya admin yang dapat mengakses halaman ini
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect ke halaman login jika bukan admin
    exit;
}

// Hubungkan ke database atau include config
require '../config.php';

// Pengaturan default untuk pengurutan
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'publication_date'; // Default pengurutan berdasarkan tanggal terbit
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC'; // Default urutan descending

// Inisialisasi variabel untuk filter tanggal
$filterDay = '';
$filterMonth = '';
$filterYear = '';

// Memproses filter tanggal jika ada request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filterDay = isset($_POST['filter_day']) ? $_POST['filter_day'] : '';
    $filterMonth = isset($_POST['filter_month']) ? $_POST['filter_month'] : '';
    $filterYear = isset($_POST['filter_year']) ? $_POST['filter_year'] : '';

    // Validasi input bulan dan tahun
    if ($filterMonth >= 1 && $filterMonth <= 12 && $filterYear >= 1900 && $filterYear <= date('Y')) {
        // Konstruksi format tanggal berdasarkan input
        $filterDate = sprintf('%04d-%02d-%02d', $filterYear, $filterMonth, $filterDay);

        // Filter berdasarkan tanggal
        $filterCondition = ' WHERE YEAR(publication_date) = :filterYear AND MONTH(publication_date) = :filterMonth';
        if ($filterDay) {
            $filterCondition .= ' AND DAY(publication_date) = :filterDay';
        }
    }
}

// Pagination
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Jumlah data per halaman
$offset = ($page - 1) * $limit; // Offset data

// Query dasar untuk mengambil data koran dari database dengan pengurutan dan filter (jika ada)
$sql = 'SELECT newspapers.*, categories.name AS category_name FROM newspapers LEFT JOIN categories ON newspapers.category_id = categories.id';
$countSql = 'SELECT COUNT(*) AS total FROM newspapers';

// Menambahkan filter tanggal ke query jika dipilih
if (!empty($filterDay) || !empty($filterMonth) || !empty($filterYear)) {
    $sql .= $filterCondition;
    $countSql .= $filterCondition;
}

$sql .= ' ORDER BY ' . $sortBy . ' ' . $sortOrder; // Default pengurutan

$stmt = $pdo->prepare($sql . ' LIMIT :limit OFFSET :offset');
$stmtCount = $pdo->prepare($countSql);

// Bind parameter jika ada filter tanggal
if (!empty($filterDay) || !empty($filterMonth) || !empty($filterYear)) {
    if (!empty($filterYear)) {
        $stmt->bindParam(':filterYear', $filterYear, PDO::PARAM_INT);
        $stmtCount->bindParam(':filterYear', $filterYear, PDO::PARAM_INT);
    }
    if (!empty($filterMonth)) {
        $stmt->bindParam(':filterMonth', $filterMonth, PDO::PARAM_INT);
        $stmtCount->bindParam(':filterMonth', $filterMonth, PDO::PARAM_INT);
    }
    if (!empty($filterDay)) {
        $stmt->bindParam(':filterDay', $filterDay, PDO::PARAM_INT);
        $stmtCount->bindParam(':filterDay', $filterDay, PDO::PARAM_INT);
    }
}

$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$newspapers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtCount->execute();
$totalResults = $stmtCount->fetchColumn();

// Total halaman
$totalPages = ceil($totalResults / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="sweetalert2.min.css">
</head>

<body class="bg-gray-200">
    <div class="flex flex-col md:flex-row">
        <?php
            require 'navbar_admin.php'
        ?>
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
                <main class="flex-1 p-4 md:px-8 md:py-4 lg:px-12">
                    <form class="max-w-md mx-auto mb-5" action="../search.php" method="GET">
                        <label for="default-search"
                            class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input type="search" id="default-search" name="query"
                                class="block w-full py-2 pl-9 pr-4 text-sm text-gray-900 placeholder-gray-500 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:border-gray-600 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search newspapers..." />
                            <button type="submit"
                                class="absolute inset-y-0 right-0 flex items-center justify-center px-4 text-gray-700 bg-white border border-gray-300 rounded-r-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <span class="sr-only">Search</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-5.2-5.2m-0.4-7.6a8 8 0 1 0-3.2 6.4 8 8 0 0 0 3.2-6.4Z" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                        <div class="flex justify-center items-center mb-4">
                            <h2 class="text-2xl font-semibold text-black dark:text-black">Admin Dashboard - Data Koran
                            </h2>
                        </div>

                        <!-- Form filter tanggal -->
                        <form action="admin_dashboard.php" method="POST" class="mb-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Input untuk hari -->
                                <div>
                                    <label for="filter_day"
                                        class="block text-sm font-medium text-black mb-1">Hari:</label>
                                    <input type="number" id="filter_day" name="filter_day" min="1" max="31"
                                        value="<?php echo htmlspecialchars($filterDay); ?>"
                                        class="block w-full mt-1 py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <!-- Input untuk bulan -->
                                <div>
                                    <label for="filter_month"
                                        class="block text-sm font-medium text-black mb-1">Bulan:</label>
                                    <input type="number" id="filter_month" name="filter_month" min="1" max="12"
                                        value="<?php echo htmlspecialchars($filterMonth); ?>"
                                        class="block w-full mt-1 py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <!-- Input untuk tahun -->
                                <div>
                                    <label for="filter_year"
                                        class="block text-sm font-medium text-black mb-1">Tahun:</label>
                                    <input type="number" id="filter_year" name="filter_year" min="1900"
                                        max="<?php echo date('Y'); ?>"
                                        value="<?php echo htmlspecialchars($filterYear); ?>"
                                        class="block w-full mt-1 py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            <!-- Tombol filter -->
                            <div class="flex justify-end mt-4">
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    Filter
                                </button>
                            </div>
                        </form>

                        <!-- Tabel data koran -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">Judul</th>
                                        <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">Kategori</th>
                                        <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">Tema</th>
                                        <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">Tanggal Terbit</th>
                                        <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($newspapers as $newspaper): ?>
                                    <tr>
                                        <td class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">
                                            <?php echo htmlspecialchars($newspaper['title']); ?>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">
                                            <?php echo htmlspecialchars($newspaper['category_name']); ?>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">
                                            <?php echo htmlspecialchars($newspaper['category']); ?>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">
                                            <?php echo htmlspecialchars($newspaper['publication_date']); ?>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-300 dark:border-gray-700">
                                            <a href="view_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                                class="text-emerald-600 hover:text-emerald-700 mr-2">View</a>
                                            <a href="edit_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                                class="text-blue-500 hover:text-blue-600 mr-2">Edit</a>
                                            <a href="delete_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                                class="text-red-500 hover:text-red-600"
                                                onclick="return confirmDelete('<?php echo $newspaper['id']; ?>')">Delete</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="flex justify-center mt-4">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>"
                                class="px-3 py-2 mx-1 text-white bg-blue-500 rounded-lg hover:bg-blue-700 <?php echo ($i === $page) ? 'bg-blue-700' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                            <?php endfor; ?>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>

</html>
