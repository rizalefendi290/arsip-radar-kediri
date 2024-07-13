<?php
// Pastikan hanya admin yang dapat mengakses halaman ini
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect ke halaman login jika bukan admin
    exit;
}

// Hubungkan ke database atau include config
require '../config.php';

// Ambil ID koran dari query parameter
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: admin_dashboard.php'); // Redirect ke halaman admin jika tidak ada ID
    exit;
}

// Ambil data koran dari database
$stmt = $pdo->prepare('SELECT * FROM newspapers WHERE id = ?');
$stmt->execute([$id]);
$newspaper = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$newspaper) {
    header('Location: admin_dashboard.php'); // Redirect ke halaman admin jika koran tidak ditemukan
    exit;
}

// Proses form jika ada POST data
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $publication_date = $_POST['publication_date'];
    $category = $_POST['category'];
    
    // Handling file upload (optional)
    $fileUploaded = false;
    $fileUploadDir = '../uploads/';
    $fileNewPath = $newspaper['pdf_file']; // default to existing file path

    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['file']['name'];
        $fileTmpName = $_FILES['file']['tmp_name'];
        $fileType = $_FILES['file']['type'];
        $fileSize = $_FILES['file']['size'];
        
        // Check if file is a PDF
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExtension === 'pdf') {
            // Generate unique filename
            $fileNewName = uniqid('newspaper_') . '.' . $fileExtension;
            $fileNewPath = $fileUploadDir . $fileNewName;

            // Move uploaded file to destination directory
            if (move_uploaded_file($fileTmpName, $fileNewPath)) {
                $fileUploaded = true;
            } else {
                $errors[] = 'Gagal mengunggah file PDF';
            }
        } else {
            $errors[] = 'File harus berformat PDF';
        }
    }

    // Validasi form (misalnya, tambahkan validasi sesuai kebutuhan)
    if (empty($errors)) {
        // Update data koran ke database
        $stmt = $pdo->prepare('UPDATE newspapers SET title = ?, publication_date = ?, category = ?, pdf_file = ? WHERE id = ?');
        $params = [$title, $publication_date, $category, $fileNewPath, $id];
        
        if (!$fileUploaded) {
            // If no new file uploaded, keep existing file path
            $params = [$title, $publication_date, $category, $newspaper['pdf_file'], $id];
        }

        if ($stmt->execute($params)) {
            $_SESSION['edit_success'] = true; // Set session variable to indicate success
            header('Location: edit_newspaper.php?id=' . $id); // Redirect ke halaman edit setelah berhasil menyimpan
            exit;
        } else {
            $errors[] = 'Gagal menyimpan perubahan';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Koran - <?php echo htmlspecialchars($newspaper['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    echo '<a href="../profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>';
                    if ($role === 'admin') {
                        echo '<a href="../index.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Beranda</a>';
                        echo '<a href="../admin/panduan_admin.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Panduan Admin</a>';
                        echo '<a href="../admin/daftar_user.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Daftar User</a>';
                        echo '<a href="../admin/admin_dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard Admin</a>'; // Tambahkan tautan ke dashboard admin
                    }
                    echo '<a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    // Jika belum login, tampilkan link Login
                    echo '<a href="/path/to/login.php" class="text-sm text-blue-600 dark:text-blue-500 hover:underline">Login</a>';
                }
                ?>
            </div>
        </div>
    </nav>
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="container mx-auto py-4 px-4 flex justify-between">
            <h1 class="text-2xl font-bold">Edit Koran - <?php echo htmlspecialchars($newspaper['title']); ?></h1>
            
            <a href="admin_dashboard.php"
                    class="flex bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded inline-block">
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h14M5 12l4-4m-4 4 4 4" />
                    </svg>
                    Kembali
                </a>
        </div>
        
    </header>

    <!-- Main Content -->
    <main class="container mx-auto py-4 px-4">
        <div class="bg-white shadow-md rounded-lg p-4">
            <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                <strong class="font-bold">Terjadi kesalahan!</strong>
                <span class="block sm:inline"> <?php echo implode('<br>', $errors); ?></span>
            </div>
            <?php endif; ?>
            <form action="edit_newspaper.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Judul:</label>
                    <input type="text" id="title" name="title"
                        value="<?php echo htmlspecialchars($newspaper['title']); ?>"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label for="publication_date" class="block text-gray-700 text-sm font-bold mb-2">Tanggal
                        Terbit:</label>
                    <input type="date" id="publication_date" name="publication_date"
                        value="<?php echo htmlspecialchars($newspaper['publication_date']); ?>"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Kategori:</label>
                    <input type="text" id="category" name="category"
                        value="<?php echo htmlspecialchars($newspaper['category']); ?>"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label for="file" class="block text-gray-700 text-sm font-bold mb-2">File PDF:</label>
                    <input type="file" id="file" name="file" accept=".pdf"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Simpan
                    Perubahan</button>
                <a href="admin_dashboard.php"
                    class="ml-4 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded inline-block">Batal</a>
            </form>
        </div>
    </main>
<?php
require '../components/footer.php'
?>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
    <script src="../js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if (isset($_SESSION['edit_success'])): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data koran berhasil diedit!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    <?php unset($_SESSION['edit_success']); ?>
                    window.location.href = 'edit_newspaper.php?id=<?php echo $id; ?>'; // Redirect back to same page
                });
            <?php endif; ?>
        });
    </script>
</body>

</html>
