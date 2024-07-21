<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Hubungkan ke database atau include config
require 'config.php';

// Ambil data pengguna dari database
$username = $_SESSION['username'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika form disubmit, proses pembaruan data pengguna
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = $_POST['username'] ?? '';
    $newName = $_POST['name'] ?? '';
    $newEmail = $_POST['email'] ?? '';
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $errors = [];

    // Validasi data input
    if (empty($newUsername) || empty($newName) || empty($newEmail)) {
        $errors[] = 'Username, nama, dan email tidak boleh kosong.';
    }
    if (!empty($newPassword) && $newPassword !== $confirmPassword) {
        $errors[] = 'Password dan konfirmasi password tidak cocok.';
    }

    // Jika tidak ada error, update data pengguna di database
    if (empty($errors)) {
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET username = :newUsername, name = :newName, email = :newEmail, password = :newPassword WHERE username = :username');
            $stmt->execute(['newUsername' => $newUsername, 'newName' => $newName, 'newEmail' => $newEmail, 'newPassword' => $hashedPassword, 'username' => $username]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET username = :newUsername, name = :newName, email = :newEmail WHERE username = :username');
            $stmt->execute(['newUsername' => $newUsername, 'newName' => $newName, 'newEmail' => $newEmail, 'username' => $username]);
        }
        $_SESSION['username'] = $newUsername;
        $_SESSION['name'] = $newName;
        $success = true;
    }
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
    <title>Edit Profil</title>
    <link rel="stylesheet" href="path/to/your/tailwind.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100">
<?php
require 'components/header.php';
?>
    <main>
    <div class="container mx-auto py-4 px-4">
            <nav class="flex flex-col items-center md:flex-row md:justify-between" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="index.php"
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
                                class="ms-1 text-sm font-medium text-black hover:text-blue-600 dark:hover:text-gray-600">Profile</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="container mx-auto py-4 px-4">
            <div class="bg-white p-6 rounded shadow-md max-w-lg mx-auto">
                <h1 class="text-2xl font-bold mb-6 text-center">Edit Profile</h1>
                <?php if (!empty($errors)): ?>
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: '<ul><?php foreach ($errors as $error) { echo "<li>" . htmlspecialchars($error) . "</li>"; } ?></ul>'
                        });
                    </script>
                <?php endif; ?>
                <?php if ($success): ?>
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Perubahan berhasil disimpan!'
                        }).then(function() {
                            window.location = 'profile.php';
                        });
                    </script>
                <?php endif; ?>
                <form action="profile.php" method="POST">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-700 font-bold mb-2">Username:</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="w-full px-3 py-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-bold mb-2">Nama:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="w-full px-3 py-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 font-bold mb-2">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full px-3 py-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 font-bold mb-2">Password Baru (kosongkan jika tidak ingin mengubah):</label>
                        <input type="password" id="password" name="password" class="w-full px-3 py-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label for="confirm_password" class="block text-gray-700 font-bold mb-2">Konfirmasi Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="w-full px-3 py-2 border rounded">
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded w-full">Simpan Perubahan</button>
                </form>
            </div>
        </div>

    </main>
<?php
require 'components/footer.php';
?>
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
<script src="js/app.js"></script>
</body>

</html>
