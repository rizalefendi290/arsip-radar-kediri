<?php
session_start();

// Hubungkan ke database atau include config
require 'config.php';

// Ambil data koran dari database
$stmt = $pdo->query('SELECT * FROM newspapers');
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
    <title>Perpustakaan Digital Radar Kediri</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
</head>

<body class="bg-gray-100">
<?php
require 'components/header.php'
?>
    <!-- Main Content -->
    <main class="container mx-auto py-4 px-4 flex justify-center items-center min-h-screen">
        <!-- Daftar Koran -->
        <div class="w-full max-w-md">
            <h1 class="text-center font-bold text-2xl font-sans">Pencarian Koran</h1>
            <form class="max-w-md mx-auto my-5" action="search.php" method="GET">
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
    </main>

    <footer
        class="fixed bottom-0 left-0 z-20 w-full p-4 bg-white border-t border-gray-200 shadow md:flex md:items-center md:justify-between md:p-6 dark:bg-gray-800 dark:border-gray-600">
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