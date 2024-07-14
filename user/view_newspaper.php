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
    <title>Lihat Koran - <?php echo htmlspecialchars($newspaper['title']); ?></title>
    <!-- Tambahkan link CSS untuk styling (misalnya menggunakan Tailwind CSS) -->
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
    <script src="../js/app.js"></script>
</body>

</html>