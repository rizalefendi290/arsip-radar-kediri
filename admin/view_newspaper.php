<?php
session_start();

// Hubungkan ke database atau include config
require '../config.php';

// Ambil ID koran dari query parameter
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php'); // Redirect ke halaman user dashboard jika tidak ada ID
    exit;
}

// Ambil data koran dari database
$stmt = $pdo->prepare('SELECT * FROM newspapers WHERE id = ?');
$stmt->execute([$id]);
$newspaper = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$newspaper) {
    header('Location: index.php'); // Redirect ke halaman user dashboard jika koran tidak ditemukan
    exit;
}
$category_id = $newspaper['category_id'];
$category_stmt = $pdo->prepare('SELECT name FROM categories WHERE id = ?');
$category_stmt->execute([$category_id]);
$category = $category_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Koran - <?php echo htmlspecialchars($newspaper['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <style>
    .pdf-viewer {
        overflow: auto;
        max-height: 80vh;
        /* Ensure PDF viewer doesn't overflow */
    }

    @media (max-width: 768px) {
        .pdf-viewer canvas {
            width: 100%;
        }
    }
    </style>
</head>

<body class="bg-gray-100">
    <nav class="bg-white border-gray-200 dark:bg-gray-900">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="../index.php" class="flex items-center space-x-3 rtl:space-x-reverse text-center sm:text-left">
                <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo" />
                <span class="self-center text-lg sm:text-sm md:text-lg font-semibold whitespace-nowrap dark:text-white">
                    Perpustakaan Digital
                </span>
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

            <div class="hidden w-full md:flex md:w-auto" id="navbar-default">
                <ul
                    class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                    <li>
                        <a href="index.php"
                            class="block py-2 px-3 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0 dark:text-white md:dark:text-blue-500"
                            aria-current="page">Beranda</a>
                    </li>
                    <li>
                        <a href="../user/all_newspaper.php"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Semua
                            Koran</a>
                    </li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li>
                        <a href="admin_dashboard.php"
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
                            class="hidden absolute right-0 top-full mt-2 w-full md:w-48 bg-white rounded-md shadow-lg dark:bg-gray-800">
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
                        <a href="login.php"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Login</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Header -->
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
                                class="ms-1 text-sm font-medium text-black hover:text-blue-600 dark:hover:text-gray-600">Lihat
                                Koran</span>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                            <span
                                class="ms-1 text-sm font-medium text-black hover:text-blue-600 dark:hover:text-gray-600">Koran
                                <?php echo htmlspecialchars($newspaper['title']); ?></span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </header>
    <!-- Main Content -->
    <main class="container mx-auto mt-5 px-4">
        <div class="bg-white shadow-md rounded-lg p-6 grid grid-cols-1 md:grid-cols-1 lg:grid-cols-1 gap-4 mx-auto">
            <div>
                <h2 class="text-2xl font-semibold mb-4"><?php echo htmlspecialchars($newspaper['title']); ?></h2>
                <div class="mb-4">
                    <span class="text-gray-600">Kategori:</span>
                    <span class="text-blue-600 font-semibold"><?php echo htmlspecialchars($category['name']); ?></span>
                </div>
                <div class="mb-4">
                    <span class="text-gray-600">Tanggal Terbit:</span>
                    <span class="text-blue-600 font-semibold"><?php echo htmlspecialchars($newspaper['publication_date']); ?></span>
                </div>
                <div class="mb-4 break-words">
                    <span class="text-gray-600">Tema / Isi :</span>
                    <span class="text-blue-600 font-semibold break-words"><?php echo htmlspecialchars($newspaper['category']); ?></span>
                </div>
            </div>
        </div>
        <div class="container mx-auto flex justify-center items-center p-4 bg-white mb-10">
            <iframe src="../uploads/<?php echo $newspaper['pdf_file']; ?>" width="100%" height="600px" style="border: none;"></iframe>
        </div>
    </main>
    <!-- Footer (opsional) -->
    <?php
require '../components/footer.php'
?>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
    <script src="../js/app.js"></script>
    <script>
    var url = '../uploads/<?php echo $newspaper['pdf_file']; ?>';
    var pdfViewer = document.getElementById('pdf-viewer');

    pdfjsLib.getDocument(url).promise.then(function(pdfDoc) {
        for (var pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
            pdfDoc.getPage(pageNum).then(function(page) {
                var viewport = page.getViewport({
                    scale: 0.9
                });
                var canvas = document.createElement('canvas');
                var context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                pdfViewer.appendChild(canvas);

                var renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                page.render(renderContext);
            });
        }
    });
    </script>
</body>

</html>