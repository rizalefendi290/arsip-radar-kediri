<?php
session_start();


// Hubungkan ke database atau include config
require 'config.php';

// Ambil data koran dari database
$stmt = $pdo->query('SELECT * FROM newspapers');
$newspapers = $stmt->fetchAll(PDO::FETCH_ASSOC);

require 'components/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital Radar Kediri</title>
</head>

<body class="bg-gray-100">
    <form class="max-w-md mx-auto mt-10" action="search.php" method="GET">
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
    <!-- Main Content -->
    <main class="container mx-auto py-4 px-4">
        <!-- Daftar Koran -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <?php foreach ($newspapers as $newspaper): ?>
            <div class="bg-white shadow-md rounded-lg p-4">
                <h3 class="text-lg font-bold mb-2"><?php echo htmlspecialchars($newspaper['title']); ?></h3>
                <p class="text-gray-600 mb-2">Tanggal Terbit:
                    <?php echo htmlspecialchars($newspaper['publication_date']); ?></p>
                <p class="text-gray-600 mb-2">Kategori: <?php echo htmlspecialchars($newspaper['category']); ?></p>
                <a href="user/view_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-block">Lihat
                    Koran</a>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

</body>
<?php
require 'components/footer.php'
?>
</html>