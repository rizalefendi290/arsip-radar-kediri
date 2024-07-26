<?php
session_start();

// Hubungkan ke database atau include config
require '../config.php';

// Pagination
$itemsPerPage = 10; // jumlah item per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Filter tipe koran
$newspaperType = isset($_GET['newspaper_type']) ? $_GET['newspaper_type'] : '';

// Hitung total item
if ($newspaperType) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM newspapers WHERE category_id = ?');
    $stmt->execute([$newspaperType]);
} else {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM newspapers');
    $stmt->execute();
}
$totalItems = $stmt->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Ambil data koran
if ($newspaperType) {
    $stmt = $pdo->prepare('SELECT * FROM newspapers WHERE category_id = ? LIMIT ? OFFSET ?');
    $stmt->execute([$newspaperType, $itemsPerPage, $offset]);
} else {
    $stmt = $pdo->prepare('SELECT * FROM newspapers LIMIT ? OFFSET ?');
    $stmt->execute([$itemsPerPage, $offset]);
}
$newspapers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
} else {
    $role = 'user'; // Default jika tidak ada role yang ditentukan
}
$query = "SELECT id, name FROM categories";
$stmt = $pdo->query($query);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Koran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="bg-gray-100">
    <script>
    <?php if ($deleteSuccess): ?>
    Swal.fire({
        title: 'Success',
        text: 'Koran berhasil dihapus.',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'all_newspaper.php';
        }
    });
    <?php else: ?>
    Swal.fire({
        title: 'Error',
        text: 'Gagal menghapus koran.',
        icon: 'error',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'all_newspaper.php';
        }
    });
    <?php endif; ?>
    </script>
    <nav class="bg-white border-gray-200 dark:bg-gray-900">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="index.php" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo" />
                <span
                    class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white sm:text-m md:text-md">Perpustakaan
                    Digital</span>
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
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li>
                        <a href="../admin/admin_dashboard.php"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Dashboard
                            Admin</a>
                    </li>
                    <?php endif; ?>
                    <li class="relative">
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
                            <span class="ml-1 text-sm font-medium text-gray-800 md:ml-2 rtl:ml-0 rtl:mr-2">Daftar
                                Koran</span>
                        </div>
                    </li>
                </ol>

                <form class="flex-grow max-w-md mx-auto mt-4 md:mt-0" action="../search.php" method="GET">
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
                <div class="mt-4 md:mt-0">
                    <form action="" method="get">
                        <label for="newspaper_type" class="sr-only">Filter Tipe Koran:</label>
                        <select name="newspaper_type" id="newspaper_type"
                            class="border border-gray-300 rounded-lg px-2 py-1 text-sm focus:ring focus:ring-blue-300 focus:outline-none">
                            <option value="">Semua Tipe</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['id']) ?>"
                                <?php if ($newspaperType == $category['id']) echo 'selected'; ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit"
                            class="ml-2 px-4 py-1 text-sm text-white bg-blue-500 rounded-lg hover:bg-blue-600">Filter
                        </button>
                    </form>

                </div>
            </nav>
        </div>
    </header>
    <div class="container mx-auto items-center flex justify-between">
        <a href="../index.php"
            class="flex bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded inline-block">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 border-b-2 border-gray-300 bg-gray-200 text-left text-xs leading-4 text-gray-600 uppercase tracking-wider">
                                Judul</th>
                            <th
                                class="px-6 py-3 border-b-2 border-gray-300 bg-gray-200 text-left text-xs leading-4 text-gray-600 uppercase tracking-wider">
                                Tanggal Terbit</th>
                            <th
                                class="px-6 py-3 border-b-2 border-gray-300 bg-gray-200 text-left text-xs leading-4 text-gray-600 uppercase tracking-wider">
                                Kategori</th>
                            <th
                                class="px-6 py-3 border-b-2 border-gray-300 bg-gray-200 text-left text-xs leading-4 text-gray-600 uppercase tracking-wider">
                                Tema / Isi</th>
                            <th
                                class="px-6 py-3 border-b-2 border-gray-300 bg-gray-200 text-left text-xs leading-4 text-gray-600 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <?php foreach ($newspapers as $newspaper): ?>
                        <?php
                         // Fetch categories associated with this newspaper
                        $stmt = $pdo->prepare('SELECT name FROM categories WHERE id = ?');
                        $stmt->execute([$newspaper['category_id']]);
                        $category = $stmt->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <tr>
                            <td class="px-6 py-4 border-b border-gray-300 whitespace-nowrap overflow-hidden text-ellipsis max-w-xs">
                                <?php echo htmlspecialchars($newspaper['title']); ?></td>
                            <td class="px-6 py-4 border-b border-gray-300 whitespace-nowrap overflow-hidden text-ellipsis max-w-xs">
                                <?php echo htmlspecialchars($newspaper['publication_date']); ?></td>

                            <td class="px-6 py-4 border-b border-gray-300 whitespace-nowrap overflow-hidden text-ellipsis max-w-xs">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </td>
                            <td class="px-6 py-4 border-b border-gray-300 whitespace-nowrap overflow-hidden text-ellipsis max-w-xs">
                                <?php echo htmlspecialchars($newspaper['category']); ?></td>
                            <td class="px-6 py-4 border-b border-gray-300 whitespace-nowrap overflow-hidden text-ellipsis max-w-xs">
                                <a href="view_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                    class="text-blue-500 hover:text-blue-700">Lihat</a>
                                <?php if ($role === 'admin'): ?>
                                <a href="../admin/edit_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                    class="text-green-500 hover:text-green-700 ml-2">Edit</a>
                                <a href="delete_newspaper.php?id=<?php echo $newspaper['id']; ?>"
                                    class="text-red-500 hover:text-red-700 ml-2"
                                    onclick="return confirmDelete(<?php echo $newspaper['id']; ?>);">Hapus</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t bg-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <a href="?page=<?php echo max($page - 1, 1); ?>&newspaper_type=<?php echo $newspaperType; ?>"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</a>
                        <a href="?page=<?php echo min($page + 1, $totalPages); ?>&newspaper_type=<?php echo $newspaperType; ?>"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</a>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium"><?php echo ($offset + 1); ?></span>
                                to
                                <span
                                    class="font-medium"><?php echo min($offset + $itemsPerPage, $totalItems); ?></span>
                                of
                                <span class="font-medium"><?php echo $totalItems; ?></span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex shadow-sm -space-x-px" aria-label="Pagination">
                                <a href="?page=1&newspaper_type=<?php echo $newspaperType; ?>"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">First</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                    </svg>
                                </a>
                                <a href="?page=<?php echo max($page - 1, 1); ?>&newspaper_type=<?php echo $newspaperType; ?>"
                                    class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </a>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>&newspaper_type=<?php echo $newspaperType; ?>"
                                    class="relative inline-flex items-center px-4 py-2 border <?php echo $page == $i ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> text-sm font-medium">
                                    <?php echo $i; ?>
                                </a>
                                <?php endfor; ?>
                                <a href="?page=<?php echo min($page + 1, $totalPages); ?>&newspaper_type=<?php echo $newspaperType; ?>"
                                    class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                                <a href="?page=<?php echo $totalPages; ?>&newspaper_type=<?php echo $newspaperType; ?>"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Last</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 17l5-5m0 0l-5-5m5 5H6" />
                                    </svg>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    const role = "<?php echo $role; ?>";
    </script>
    <script>
    function confirmDelete(newspaperId) {
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
                // Redirect to delete script with newspaperId
                window.location.href = 'delete_newspaper.php?id=' + newspaperId;
            }
        });
        // Prevent the default action of the link
        return false;
    }
    </script>
</body>

</html>