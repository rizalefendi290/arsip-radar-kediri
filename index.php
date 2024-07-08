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
    <title>User Dashboard</title>
    <!-- Tambahkan link CSS untuk styling (misalnya menggunakan Tailwind CSS) -->
    
<form class="max-w-md mx-auto" action="search.php" methode="GET">   
    <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
    <div class="relative">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
            </svg>
        </div>
        <input type="search" id="default-search" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Telusuri, Koran..." required />
        <button type="submit" class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
    </div>
</form>

</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="container mx-auto py-4 px-4">
            <h1 class="text-2xl font-bold">User Dashboard</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto py-4 px-4">
        <h2 class="text-xl font-bold mb-4">Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        
        <!-- Daftar Koran -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <?php foreach ($newspapers as $newspaper): ?>
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h3 class="text-lg font-bold mb-2"><?php echo htmlspecialchars($newspaper['title']); ?></h3>
                    <p class="text-gray-600 mb-2">Tanggal Terbit: <?php echo htmlspecialchars($newspaper['publication_date']); ?></p>
                    <p class="text-gray-600 mb-2">Kategori: <?php echo htmlspecialchars($newspaper['category']); ?></p>
                    <a href="user/view_newspaper.php?id=<?php echo $newspaper['id']; ?>" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-block">Lihat Koran</a>
                </div>
            <?php endforeach; ?>
        </div>
        
        <a href="logout.php" class="mt-4 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded inline-block">Logout</a>
    </main>

    <!-- Footer (opsional) -->
    <footer class="bg-gray-200 text-center py-2 mt-4">
        <p>&copy; <?php echo date('Y'); ?> Your Newspaper Archive</p>
    </footer>
</body>
</html>
