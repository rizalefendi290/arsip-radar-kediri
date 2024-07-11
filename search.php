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

// Ambil data koran yang sesuai dengan query pencarian
$stmt = $pdo->prepare('SELECT * FROM newspapers WHERE title LIKE ? OR category LIKE ?');
$searchTerm = "%$query%";
$stmt->execute([$searchTerm, $searchTerm]);
$newspapers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian - <?php echo htmlspecialchars($query); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
</head>

<body class="bg-gray-100">
    <nav class="bg-white border-gray-200 dark:bg-gray-900">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl p-4">
            <a href="https://flowbite.com" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo" />
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Perpustakaan Digital
                    Radar Kediri</span>
            </a>
            <div class="flex items-center space-x-6 rtl:space-x-reverse">
                <?php
                if (isset($_SESSION['username'])) {
                    // Jika user sudah login
                    $username = htmlspecialchars($_SESSION['username']);
                    $role = $_SESSION['role'];

                    echo '<div class="relative inline-block text-left">';
                    echo '<button id="dropdown-toggle" type="button" class="text-white hover:underline focus:outline-none">';
                    echo $username;
                    echo '</button>';
                    echo '<div id="dropdown-menu" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 hidden">';
                    echo '<div class="py-1">';
                    echo '<a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>';
                    if ($role === 'admin') {
                        echo '<a href="index.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Beranda</a>';
                        echo '<a href="admin/panduan_admin.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Panduan Admin</a>';
                        echo '<a href="admin/daftar_user.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Daftar User</a>';
                        echo '<a href="admin/admin_dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard Admin</a>'; // Tambahkan tautan ke dashboard admin
                    }
                    echo '<a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    // Jika belum login, tampilkan link Login
                    echo '<a href="login.php" class="text-sm text-blue-600 dark:text-blue-500 hover:underline">Login</a>';
                }
                ?>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="bg-white shadow">
        <div class="container mx-auto py-4 px-4">
            <h1 class="text-2xl font-bold">Hasil Pencarian untuk "<?php echo htmlspecialchars($query); ?>"</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto py-4 px-4">
        <?php if (count($newspapers) > 0) : ?>
        <div class="overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No.
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Judul Koran
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Terbit
                        </th>
                        <th class="py-2 px-4">
                            <span class="text-sm font-medium text-gray-700">Kategori</span>
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($newspapers as $index => $newspaper) : ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo $index + 1; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo htmlspecialchars($newspaper['title']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo date('d-m-Y', strtotime($newspaper['publication_date'])); ?>
                        </td>
                        <td class="py-2 px-4 whitespace-nowrap">
                            <span
                                class="text-sm text-gray-900"><?php echo htmlspecialchars($newspaper['category']); ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="user/view_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                class="text-green-600 hover:text-green-900 mr-2">Lihat</a>
                            <a href="admin/edit_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                            <a href="admin/delete_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                class="text-red-600 hover:text-red-900"
                                onclick="return confirm('Are you sure you want to delete this newspaper?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else : ?>
        <p class="text-gray-600">Tidak ada hasil yang ditemukan untuk "<?php echo htmlspecialchars($query); ?>".</p>
        <?php endif; ?>
        <a href="index.php"
            class="mt-4 ml-4 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded inline-block">Kembali
            ke Dashboard</a>
    </main>

    <!-- Footer (opsional) -->
    <?php require 'components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
    <script src="js/app.js"></script>
</body>

</html>