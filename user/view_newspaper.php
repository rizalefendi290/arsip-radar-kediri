<?php
session_start();

// Hubungkan ke database atau include config
require '../config.php';

// Ambil ID koran dari query parameter
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: user_dashboard.php'); // Redirect ke halaman user dashboard jika tidak ada ID
    exit;
}

// Ambil data koran dari database
$stmt = $pdo->prepare('SELECT * FROM newspapers WHERE id = ?');
$stmt->execute([$id]);
$newspaper = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$newspaper) {
    header('Location: user_dashboard.php'); // Redirect ke halaman user dashboard jika koran tidak ditemukan
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Koran - <?php echo htmlspecialchars($newspaper['title']); ?></title>
    <!-- Tambahkan link CSS untuk styling (misalnya menggunakan Tailwind CSS) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
</head>

<body class="bg-gray-100">
    <nav class="bg-white border-gray-200 dark:bg-gray-900">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl p-4">
            <a href="../index.php" class="flex items-center space-x-3 rtl:space-x-reverse">
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
                    echo '<a href="../profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>';
                    if ($role === 'admin') {
                        echo '<a href="../index.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Beranda</a>';
                        echo '<a href="../admin/admin_dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard Admin</a>'; // Tambahkan tautan ke dashboard admin
                    }
                    echo '<a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    // Jika belum login, tampilkan link Login
                    echo '<a href="../login.php" class="text-sm text-blue-600 dark:text-blue-500 hover:underline">Login</a>';
                }
            ?>
            </div>
        </div>
    </nav>
    <form class="max-w-md mx-auto my-5" action="../search.php" method="GET">
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
    <!-- Header -->
    <header class="">
        <div class="container mx-auto py-4 px-4">
            <nav class="flex justify-between" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="../index.php"
                            class="inline-flex items-center text-sm font-medium text-black hover:text-blue-600 dark:hover:text-gray-600">
                            <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
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
                                class="ms-1 text-sm font-medium text-black hover:text-blue-600 dark:hover:text-gray-600"><?php echo htmlspecialchars($newspaper['title']); ?></span>
                        </div>
                    </li>
                </ol>
                <a href="../index.php"
                    class="flex mt-4 ml-4 bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded inline-block">
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h14M5 12l4-4m-4 4 4 4" />
                    </svg>
                    Kembali</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto py-4 px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4">
                <h2 class="text-xl font-bold mb-4"><?php echo htmlspecialchars($newspaper['title']); ?></h2>
                <p class="text-black text-2xl mb-2">Tanggal Terbit:
                    <?php echo htmlspecialchars($newspaper['publication_date']); ?></p>
                <p class="text-black text-xl mb-2">Kategori: <?php echo htmlspecialchars($newspaper['category']); ?></p>
            </div>
            <div class="p-4">
                <object data="../uploads/<?php echo htmlspecialchars($newspaper['pdf_file']); ?>" type="application/pdf"
                    width="100%" height="800px">
                    <p class="text-black">Maaf, browser Anda tidak mendukung penampilan PDF. Silakan <a
                            href="../uploads/<?php echo htmlspecialchars($newspaper['pdf_file']); ?>">unduh dokumen</a>
                        untuk melihatnya.</p>
                </object>
            </div>
        </div>
    </main>
    <!-- Footer (opsional) -->
    <?php
require '../components/footer.php'
?>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
    <script src="/js/app.js"></script>
</body>

</html>