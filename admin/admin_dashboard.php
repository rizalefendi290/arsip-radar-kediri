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

// Inisialisasi $filterCondition dengan nilai kosong
$filterConditions = [];
$params = [];

// Memproses filter tanggal jika ada request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filterMonth = isset($_POST['filter_month']) ? $_POST['filter_month'] : '';
    $filterYear = isset($_POST['filter_year']) ? $_POST['filter_year'] : '';
    $filterCategory = isset($_POST['filter_category']) ? $_POST['filter_category'] : '';

    $params = [];

    // Validasi input bulan dan tahun
    if (!empty($filterMonth) && $filterMonth >= 1 && $filterMonth <= 12) {
        $filterConditions[] = ' MONTH(publication_date) = :filterMonth';
        $params[':filterMonth'] = $filterMonth;
    }

    if (!empty($filterYear) && $filterYear >= 1900 && $filterYear <= date('Y')) {
        $filterConditions[] = ' YEAR(publication_date) = :filterYear';
        $params[':filterYear'] = $filterYear;
    }
    if (!empty($filterCategory)) {
        $filterCondition .= (!empty($filterCondition)) ? ' AND ' : ' WHERE ';
        $filterCondition .= 'newspapers.category_id = :category_id';
        $params[':category_id'] = $filterCategory;
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
if (!empty($filterConditions)) {
    $sql .= ' WHERE ' . implode(' AND ', $filterConditions);
}
$sql .= ' ORDER BY ' . $sortBy . ' ' . $sortOrder; // Default pengurutan

$stmt = $pdo->prepare($sql . ' LIMIT :limit OFFSET :offset');
$stmtCount = $pdo->prepare('SELECT COUNT(*) AS total FROM newspapers' . (empty($filterConditions) ? '' : ' WHERE ' . implode(' AND ', $filterConditions)));

// Bind parameter jika ada filter tanggal
foreach ($params as $param => &$value) {
    $stmt->bindParam($param, $value, PDO::PARAM_INT);
    $stmtCount->bindParam($param, $value, PDO::PARAM_INT);
}

$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$newspapers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtCount->execute();
$totalResults = $stmtCount->fetchColumn();

// Total halaman
$totalPages = ceil($totalResults / $limit);
// Query untuk mengambil kategori dari database
$stmtCategories = $pdo->prepare("SELECT * FROM categories");
$stmtCategories->execute();
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);
//search
$searchTitle = isset($_GET['title']) ? $_GET['search_title'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-200">
    <div class="flex flex-col md:flex-row">
        <?php require 'navbar_admin.php'; ?>
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
                <h1 class="text-center font-bold text-2xl">Dashboard Admin</h1>
                <main class="flex-1 p-4 md:px-8 md:py-4 lg:px-12">
                    <form class="max-w-md mx-auto mb-5" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- search -->
                            <div class="col-span-2">
                                <label for="search_title" class="block text-sm font-medium text-gray-700">Cari Judul Koran:</label>
                                <input type="text" id="search_title" name="search_title" value="<?php echo htmlspecialchars($searchTitle); ?>" class="block w-full mt-1 py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <!-- Input untuk bulan -->
                            <div>
                                <label for="filter_month" class="block text-sm font-medium text-gray-700">Bulan:</label>
                                <input type="number" id="filter_month" name="filter_month" min="1" max="12" value="<?php echo htmlspecialchars($filterMonth); ?>" class="block w-full mt-1 py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <!-- Input untuk tahun -->
                            <div>
                                <label for="filter_year" class="block text-sm font-medium text-gray-700">Tahun:</label>
                                <input type="number" id="filter_year" name="filter_year" min="1900" max="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars($filterYear); ?>" class="block w-full mt-1 py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <!-- Tombol filter -->
                        <div class="flex justify-end mt-4">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                Filter
                            </button>
                            <?php if (!empty($filterMonth) || !empty($filterYear)): ?>
                            <a href="admin_dashboard.php" class="ml-4 px-4 py-2 bg-gray-300 text-gray-700 font-semibold rounded-lg shadow-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400">
                                Hapus Filter
                            </a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <!-- Tabel data koran -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b border-gray-300">Judul</th>
                                    <th class="py-2 px-4 border-b border-gray-300">Kategori</th>
                                    <th class="py-2 px-4 border-b border-gray-300">Tema / Isi</th>
                                    <th class="py-2 px-4 border-b border-gray-300">Tanggal Terbit</th>
                                    <th class="py-2 px-4 border-b border-gray-300">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($newspapers as $newspaper): ?>
                                <tr>
                                    <td class="py-2 px-4 border-b border-gray-300">
                                        <?php echo htmlspecialchars($newspaper['title']); ?>
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-300">
                                        <?php echo htmlspecialchars($newspaper['category_name']); ?>
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-300">
                                        <?php echo htmlspecialchars($newspaper['category']); ?>
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-300">
                                        <?php echo htmlspecialchars($newspaper['publication_date']); ?>
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-300">
                                        <a href="view_newspaper.php?id=<?php echo $newspaper['id']; ?>" class="text-blue-500 hover:text-blue-600">View</a>
                                        <a href="edit_newspaper.php?id=<?php echo $newspaper['id']; ?>" class="text-blue-500 hover:text-blue-600 ml-2">Edit</a>
                                        <a href="#" class="text-red-500 hover:text-red-600 ml-2" onclick="confirmDelete(<?php echo $newspaper['id']; ?>)">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-center mt-4">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $sortOrder; ?>" class="px-3 py-2 mx-1 text-white bg-blue-500 rounded-lg hover:bg-blue-700 <?php echo ($i === $page) ? 'bg-blue-700' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                        <?php endfor; ?>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Anda yakin ingin menghapus data ini?',
                text: "Tindakan ini tidak dapat diurungkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_newspaper.php?id=' + id;
                }
            })
        }
    </script>
</body>

</html>
