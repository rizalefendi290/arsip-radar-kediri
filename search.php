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

// Hitung total item
$stmt = $pdo->prepare('SELECT COUNT(*) FROM newspapers WHERE title LIKE ? OR category LIKE ?');
$searchTerm = "%$query%";
$stmt->execute([$searchTerm, $searchTerm]);
$totalItems = $stmt->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Ambil data koran yang sesuai dengan query pencarian
$stmt = $pdo->prepare('SELECT * FROM newspapers WHERE title LIKE ? OR category LIKE ? LIMIT ? OFFSET ?');
$stmt->execute([$searchTerm, $searchTerm, $itemsPerPage, $offset]);
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
            <a href="index.php" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo" />
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Perpustakaan Digital
                    Radar Kediri</span>
            </a>
            <div class="flex items-center space-x-6 rtl:space-x-reverse">
                <?php
                if (isset($_SESSION['name'])) {
                    // Jika user sudah login
                    $username = htmlspecialchars($_SESSION['name']);
                    $role = $_SESSION['role'];

                    echo '<div class="relative inline-block text-left">';
                    echo '<button id="dropdown-toggle" type="button" class="text-white text-xl underline hover:underline focus:outline-none">';
                    echo $username;
                    echo '</button>';
                    echo '<div id="dropdown-menu" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 hidden">';
                    echo '<div class="py-1">';
                    echo '<a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>';
                    if ($role === 'admin') {
                        echo '<a href="index.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Beranda</a>';
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
    <header class="shadow">
    <div class="container mx-auto py-4 px-4">
        <nav class="flex justify-between items-center" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="index.php" class="inline-flex items-center text-sm font-medium text-black hover:text-blue-600 dark:hover:text-gray-600">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="m19 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        Beranda
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                        </svg>
                        <span class="ms-1 text-sm font-medium text-black hover:text-blue-600 dark:hover:text-gray-600">Hasil Pencarian</span>
                    </div>
                </li>
            </ol>
            <form class="flex-grow max-w-md mx-4" action="search.php" method="GET">
                <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="search" id="default-search" name="query" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Telusuri, Koran..." required />
                    <button type="submit" class="text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
                </div>
            </form>
            <a href="../index.php" class="flex bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded inline-block">
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4" />
                </svg>
                Kembali
            </a>
        </nav>
    </div>
    <div class="container mx-auto py-4 px-4">
        <h1 class="text-2xl font-bold text-center">Hasil Pencarian untuk "<?php echo htmlspecialchars($query); ?>"</h1>
    </div>
</header>


    <div class="container mx-auto px-4 py-4">

    </div>

    <!-- Main Content -->
    <main class="container mx-auto py-4 px-4">
        <?php if (count($newspapers) > 0) : ?>
        <div class="row">
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 gap-4">
                <?php foreach ($newspapers as $index => $newspaper) : ?>
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <a href="user/view_newspaper.php?id=<?php echo $newspaper['id']; ?>">
                            <h3 class="text-lg font-bold leading-6 text-black hover:text-gray-700">
                                <?php echo htmlspecialchars($newspaper['title']); ?></h3>
                        </a>
                        <p class="mt-1 max-w-2xl text-sm text-gray-950">
                            <?php echo htmlspecialchars($newspaper['category']); ?></p>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                        <dl class="sm:divide-y sm:divide-gray-200">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-black ms-5">Tanggal Terbit :
                                    <?php echo date('d-m-Y', strtotime($newspaper['publication_date'])); ?></dt>
                            </div>
                        </dl>
                    </div>
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex justify-center">
                            <a href="user/view_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                class="text-green-600 hover:text-green-900 mr-2">Lihat</a>
                            <?php if ($role === 'admin') : ?>
                            <a href="admin/edit_newspaper.php?id=<?php echo $newpaper['id']; ?>"
                                class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                            <a href="admin/delete_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                class="text-red-600 hover:text-red-900"
                                onclick="return confirm('Are you sure you want to delete this newspaper?')">Delete</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            <nav class="flex justify-center">
                <ul class="pagination flex items-center space-x-2">
                    <?php if ($page > 1) : ?>
                    <li>
                        <a href="?query=<?php echo urlencode($query); ?>&page=<?php echo $page - 1; ?>"
                            class="px-3 py-2 bg-gray-300 text-gray-800 rounded">Previous</a>
                    </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <li>
                        <a href="?query=<?php echo urlencode($query); ?>&page=<?php echo $i; ?>"
                            class="px-3 py-2 <?php echo $i === $page ? 'bg-gray-800 text-white' : 'bg-gray-300 text-gray-800'; ?> rounded"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages) : ?>
                    <li>
                        <a href="?query=<?php echo urlencode($query); ?>&page=<?php echo $page + 1; ?>"
                            class="px-3 py-2 bg-gray-300 text-gray-800 rounded">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php else : ?>
        <p class="text-gray-600">Tidak ada hasil yang ditemukan untuk "<?php echo htmlspecialchars($query); ?>".</p>
        <?php endif; ?>
    </main>

    <!-- Footer (opsional) -->
    <footer
        class="container-fluid bottom-0 left-0 z-20 w-full p-4 bg-white border-t border-gray-200 shadow md:flex md:items-center md:justify-between md:p-6 dark:bg-gray-800 dark:border-gray-600">
        <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2024 <a href="https://flowbite.com/"
                class="hover:underline">Perpustakaan Digital Radar Kediri</a>. All Rights Reserved.
        </span>
        <ul class="flex flex-wrap items-center mt-3 text-sm font-medium text-gray-500 dark:text-gray-400 sm:mt-0">
            <li>
                <a href="https://radarkediri.jawapos.com/" class="hover:underline me-4 md:me-6">Kunjungi juga website
                    resmi kami</a>
            </li>
            <li>
                <a href="https://radarkediri.jawapos.com/kontak" class="hover:underline">Contact</a>
            </li>
        </ul>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
    <script src="js/app.js"></script>
</body>

</html>