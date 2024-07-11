<?php
// Start session and ensure only admin can access the page
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect to login page if not admin
    exit;
}

// Database connection using PDO
require '../config.php';

// Pagination settings
$limit = 10; // Number of entries per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total number of users
$totalUsersSql = "SELECT COUNT(*) FROM users";
$totalUsersStmt = $pdo->prepare($totalUsersSql);
$totalUsersStmt->execute();
$totalUsers = $totalUsersStmt->fetchColumn();

// Calculate total pages
$totalPages = ceil($totalUsers / $limit);

// Fetch users from the database with limit and offset
$sql = "SELECT name, username, email, role, created_at FROM users LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Daftar Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
</head>

<body>
    <div class="flex flex-col md:flex-row">
        <?php
require 'navbar_admin.php'
?>
        <div class="p-4 sm:ml-64">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
        <main class="mt-0 p-4">
            <div class="min-h-screen flex items-center justify-center bg-gray-100">
                <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-4xl">
                    <h1 class="text-2xl font-bold mb-6 text-center">Daftar Pengguna</h1>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50">Nama</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50">Nama Pengguna</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50">Email</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50">Jenis Pengguna</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50">Tanggal Pendaftaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                            if ($users) {
                                foreach ($users as $user) {
                                    echo "<tr>";
                                    echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($user['name']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($user['username']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($user['email']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($user['role']) . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-200'>" . htmlspecialchars($user['created_at']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='py-2 px-4 border-b border-gray-200 text-center'>Tidak ada pengguna yang ditemukan.</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($totalPages > 1) : ?>
                    <div class="mt-4 flex justify-center">
                        <nav class="flex space-x-2" aria-label="Pagination">
                            <?php if ($page > 1) : ?>
                            <a href="?page=<?php echo $page - 1; ?>"
                                class="py-2 px-3 bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-lg"><?php echo "Previous"; ?></a>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                            <a href="?page=<?php echo $i; ?>"
                                class="<?php echo $i === $page ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> py-2 px-3 rounded-lg"><?php echo $i; ?></a>
                            <?php endfor; ?>
                            <?php if ($page < $totalPages) : ?>
                            <a href="?page=<?php echo $page + 1; ?>"
                                class="py-2 px-3 bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-lg"><?php echo "Next"; ?></a>
                            <?php endif; ?>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
</body>

</html>