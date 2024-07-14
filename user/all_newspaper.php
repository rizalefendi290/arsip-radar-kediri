<?php
session_start();

// Hubungkan ke database atau include config
require '../config.php';

// Pagination
$itemsPerPage = 10; // jumlah item per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Hitung total item
$stmt = $pdo->prepare('SELECT COUNT(*) FROM newspapers');
$stmt->execute();
$totalItems = $stmt->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Ambil data koran
$stmt = $pdo->prepare('SELECT * FROM newspapers LIMIT ? OFFSET ?');
$stmt->execute([$itemsPerPage, $offset]);
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
    <title>Daftar Koran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
</head>

<body class="bg-gray-100">
    <nav class="bg-white border-gray-200 dark:bg-gray-900">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="index.php" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo" />
                <span
                    class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white sm:text-m md:text-md">Arsip Radar Kediri</span>
            </a>
            <button data-collapse-toggle="navbar-default" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                aria-controls="navbar-default" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>
            <div class="hidden w-full md:block md:w-auto" id="navbar-default">
                <ul
                    class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                    <li>
                        <a href="../index.php"
                            class="block py-2 px-3 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0 dark:text-white md:dark:text-blue-500"
                            aria-current="page">Beranda</a>
                    </li>
                    <li>
                        <a href="all_newspaper.php"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Semua
                            Koran</a>
                    </li>
                    </li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li>
                        <a href="../admin/admin_dashboard.php"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Dashboard
                            Admin</a>
                    </li>
                    <?php endif; ?> <li class="relative">
                        <?php if (isset($_SESSION['name'])): ?>
                        <button id="userMenuButton"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">
                            <?php echo htmlspecialchars($_SESSION['name']); ?>
                        </button>
                        <div id="userMenuDropdown"
                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg dark:bg-gray-800">
                            <a href="../profile.php"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">Profile</a>
                            <a href="../logout.php"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">Logout</a>
                        </div>
                        <script>
                        document.getElementById('userMenuButton').addEventListener('click', function() {
                            var dropdown = document.getElementById('userMenuDropdown');
                            if (dropdown.classList.contains('hidden')) {
                                dropdown.classList.remove('hidden');
                            } else {
                                dropdown.classList.add('hidden');
                            }
                        });
                        </script>
                        <?php else: ?>
                        <a href="../login.php"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Login</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="shadow">
        <div class="container mx-auto py-4 px-4">
            <nav class="flex flex-col items-center md:flex-row md:justify-between" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="../index.php"
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
                                class="ms-1 text-sm font-medium text-black hover:text-blue-600 dark:hover:text-gray-600">Semua
                                Koran</span>
                        </div>
                    </li>
                </ol>
                <form class="flex-grow max-w-md mx-4 mt-4 md:mt-0" action="../search.php" method="GET">
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
                            class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Telusuri, Koran..." required />
                        <button type="submit"
                            class="text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
                    </div>
                </form>
                <a href="../index.php"
                    class="flex bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded inline-block mt-4 md:mt-0">
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h14M5 12l4-4m-4 4 4 4" />
                    </svg>
                    Kembali
                </a>
            </nav>
        </div>
    </header>

    <div class="container mx-auto px-4 py-4">
        <?php if (count($newspapers) > 0) : ?>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            <?php foreach ($newspapers as $index => $newspaper) : ?>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <a href="user/view_newspaper.php?id=<?php echo $newspaper['id']; ?>">
                        <h3 class="text-lg font-bold leading-6 text-black hover:text-gray-700">
                            <?php echo htmlspecialchars($newspaper['title']); ?>
                        </h3>
                    </a>
                    <p class="mt-1 max-w-2xl text-sm text-gray-950">
                        <?php echo htmlspecialchars($newspaper['category']); ?>
                    </p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-black ms-5">
                                Tanggal Terbit: <?php echo date('d-m-Y', strtotime($newspaper['publication_date'])); ?>
                            </dt>
                        </div>
                    </dl>
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <div class="flex justify-center">
                        <a href="view_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                            class="text-green-600 hover:text-green-900 mr-2">Lihat</a>
                        <?php if (isset($role) && $role === 'admin') : ?>
                        <a href="../admin/edit_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                            class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                        <a href="../admin/delete_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                            class="text-red-600 hover:text-red-900"
                            onclick="return confirm('Are you sure you want to delete this newspaper?')">Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-4">
            <nav class="flex justify-center">
                <ul class="pagination flex items-center space-x-2">
                    <?php if ($page > 1) : ?>
                    <li>
                        <a href="?page=<?php echo $page - 1; ?>"
                            class="px-3 py-2 bg-gray-300 text-gray-800 rounded">Previous</a>
                    </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <li>
                        <a href="?page=<?php echo $i; ?>"
                            class="px-3 py-2 <?php echo $i === $page ? 'bg-gray-800 text-white' : 'bg-gray-300 text-gray-800'; ?> rounded"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages) : ?>
                    <li>
                        <a href="?page=<?php echo $page + 1; ?>"
                            class="px-3 py-2 bg-gray-300 text-gray-800 rounded">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php else : ?>
        <p class="text-gray-600">Tidak ada data koran yang ditemukan.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
    <script src="js/app.js"></script>
    <script>
    // Script untuk menangani menu drawer
    const showButton = document.querySelector('[data-drawer-show]');
    const hideButton = document.querySelector('[data-drawer-hide]');
    const drawer = document.getElementById('drawer-disable-body-scrolling');

    showButton.addEventListener('click', function() {
        drawer.classList.remove('-translate-x-full');
    });

    hideButton.addEventListener('click', function() {
        drawer.classList.add('-translate-x-full');
    });
    </script>
</body>

</html>