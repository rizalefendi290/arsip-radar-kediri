<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Redirect ke halaman login jika bukan admin
    exit;
}

// Hubungkan ke database atau include config
require '../config.php';

$errors = [];

// Inisialisasi variabel untuk form
$title = $publication_date = $category = $newspaper_type = '';

// Ambil data kategori dari database
$categories = [];
try {
    $stmt = $pdo->query('SELECT id, name FROM categories');
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Gagal mengambil data kategori: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil nilai dari form
    $title = trim($_POST['title']);
    $publication_date = $_POST['publication_date'];
    $category = $_POST['category']; // Mengambil ID kategori
    $newspaper_type = trim($_POST['newspaper_type']);
    $pdf_file = $_FILES['pdf_file'];

    // Validasi judul
    if (empty($title)) {
        $errors[] = 'Judul koran harus diisi';
    }

    // Validasi tanggal terbit
    if (empty($publication_date)) {
        $errors[] = 'Tanggal terbit harus diisi';
    }

    // Validasi kategori
    if (empty($category)) {
        $errors[] = 'Kategori harus diisi';
    }

    // Validasi tipe koran
    if (empty($newspaper_type)) {
        $errors[] = 'Tipe koran harus diisi';
    }

    // Validasi file PDF
    $allowed_extensions = ['pdf'];
    $file_extension = pathinfo($pdf_file['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($file_extension), $allowed_extensions)) {
        $errors[] = 'Format file harus PDF';
    }

    // Simpan file PDF ke folder uploads
    $upload_dir = '../uploads/';
    $pdf_file_path = $upload_dir . basename($pdf_file['name']);

    // Jika tidak ada error, lanjutkan proses penyimpanan
    if (empty($errors)) {
        if (move_uploaded_file($pdf_file['tmp_name'], $pdf_file_path)) {
            // Insert data ke database
            $sql = 'INSERT INTO newspapers (title, publication_date, category_id, category, pdf_file) VALUES (?, ?, ?, ?, ?)';
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$title, $publication_date, $category, $newspaper_type, $pdf_file_path])) {
                $_SESSION['success'] = 'Data koran berhasil ditambahkan.';
                header('Location: add_newspaper.php');
                exit();
            } else {
                $errors[] = 'Gagal menambahkan data koran';
            }
        } else {
            $errors[] = 'Error saat mengunggah file';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Koran Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100">
    <div class="flex flex-col md:flex-row">
        <?php require 'navbar_admin.php'; ?>
        <div class="sm:ml-64 mt-20 p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
            <main class="flex-1 p-4 md:px-8 md:py-4 lg:px-12">
                <header>
                    <div class="container mx-auto py-0 px-4 flex justify-center mb-5">
                        <h1 class="text-2xl font-bold">Tambah Koran Baru</h1>
                    </div>
                </header>
                <form action="add_newspaper.php" method="POST" enctype="multipart/form-data" class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
                    <?php if (!empty($errors)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline"> Terdapat kesalahan pada form:</span>
                        <ul class="mt-3 list-disc list-inside">
                            <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label for="title" class="block text-gray-700 font-bold mb-2">Judul</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" class="w-full px-3 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="mb-4">
                        <label for="publication_date" class="block text-gray-700 font-bold mb-2">Tanggal Terbit</label>
                        <input type="date" id="publication_date" name="publication_date" value="<?php echo htmlspecialchars($publication_date); ?>" class="w-full px-3 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="mb-4">
                        <label for="category" class="block text-gray-700 font-bold mb-2">Kategori</label>
                        <select id="category" name="category" class="w-full px-3 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-blue-500">
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($category == $cat['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="newspaper_type" class="block text-gray-700 font-bold mb-2">Tema / Isi</label>
                        <input type="text" id="newspaper_type" name="newspaper_type" value="<?php echo htmlspecialchars($newspaper_type); ?>" class="w-full px-3 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="mb-4">
                        <label for="pdf_file" class="block text-gray-700 font-bold mb-2">File PDF</label>
                        <input type="file" id="pdf_file" name="pdf_file" class="w-full px-3 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-blue-500">
                    </div>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Simpan</button>
                </form>

                <?php if (isset($_SESSION['success'])): ?>
                <script>
                Swal.fire({
                    title: 'Berhasil!',
                    text: '<?php echo $_SESSION['success']; ?>',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'admin_dashboard.php';
                });
                <?php unset($_SESSION['success']); ?>
                </script>
                <?php endif; ?>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
    <script src="../js/app.js"></script>
    <script src="sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
