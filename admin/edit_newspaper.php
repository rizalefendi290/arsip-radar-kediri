<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

require '../config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: admin_dashboard.php');
    exit;
}

$stmt = $pdo->prepare('SELECT newspapers.*, categories.name AS category_name FROM newspapers LEFT JOIN categories ON newspapers.category_id = categories.id WHERE newspapers.id = ?');
$stmt->execute([$id]);
$newspaper = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$newspaper) {
    header('Location: admin_dashboard.php');
    exit;
}
$categoriesStmt = $pdo->query('SELECT id, name FROM categories');
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);


$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $publication_date = $_POST['publication_date'];
    $category = $_POST['category'];
    $category_id = $_POST['category_id'];

    $fileUploaded = false;
    $fileUploadDir = '../uploads/';
    $fileNewPath = $newspaper['pdf_file'];

    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['file']['name'];
        $fileTmpName = $_FILES['file']['tmp_name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExtension === 'pdf') {
            $fileNewName = uniqid('newspaper_') . '.' . $fileExtension;
            $fileNewPath = $fileUploadDir . $fileNewName;
            if (move_uploaded_file($fileTmpName, $fileNewPath)) {
                $fileUploaded = true;
            } else {
                $errors[] = 'Gagal mengunggah file PDF';
            }
        } else {
            $errors[] = 'File harus berformat PDF';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE newspapers SET title = ?, publication_date = ?, category = ?, category_id = ?, newspaper_type = ?, pdf_file = ? WHERE id = ?');
        $params = [$title, $publication_date, $category, $category_id, $newspaper_type, $fileNewPath, $id];
        if (!$fileUploaded) {
            $params = [$title, $publication_date, $category, $category_id, $newspaper_type, $newspaper['pdf_file'], $id];
        }
        if ($stmt->execute($params)) {
            $_SESSION['edit_success'] = true;
            header('Location: edit_newspaper.php?id=' . $id);
            exit;
        } else {
            $errors[] = 'Gagal menyimpan perubahan';
        }
    }
}


if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
} else {
    $role = 'user';
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
    <?php require 'navbar_admin.php'; ?>
    <div class="p-4 sm:ml-64">
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-10">
            <div class="flex flex-col md:flex-row">
                <div class="flex-1 p-4">
                    <header class="bg-white shadow mb-4">
                        <div class="container mx-auto py-4 px-4 flex justify-between">
                            <h1 class="text-2xl font-bold">Edit Koran - <?php echo htmlspecialchars($newspaper['title']); ?></h1>
                        </div>
                    </header>
                    <div class="bg-white shadow-md rounded-lg p-4">
                        <?php if (!empty($errors)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                            <strong class="font-bold">Terjadi kesalahan!</strong>
                            <span class="block sm:inline"><?php echo implode('<br>', $errors); ?></span>
                        </div>
                        <?php endif; ?>
                        <form action="edit_newspaper.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Judul:</label>
                                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($newspaper['title']); ?>"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div class="mb-4">
                                <label for="publication_date" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Terbit:</label>
                                <input type="date" id="publication_date" name="publication_date" value="<?php echo htmlspecialchars($newspaper['publication_date']); ?>"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div class="mb-4">
                                <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Tema:</label>
                                <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($newspaper['category']); ?>"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>                            <div class="mb-4">
                                <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Kategori:</label>
                                <select id="category_id" name="category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $newspaper['category_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
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
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
    <script src="../js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['edit_success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Data koran berhasil diedit!',
            confirmButtonText: 'OK'
        }).then(() => {
            <?php unset($_SESSION['edit_success']); ?>
            window.location.href = 'edit_newspaper.php?id=<?php echo $id; ?>';
        });
        <?php endif; ?>
    });
    </script>
</body>

</html>
