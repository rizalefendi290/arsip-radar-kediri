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
        header('Location: profile.php');
        exit;
    }
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
                if (isset($_SESSION['name'])) {
                    // Jika user sudah login
                    $username = htmlspecialchars($_SESSION['name']);
                    $role = $_SESSION['role'];

                    echo '<div class="relative inline-block text-left">';
                    echo '<button id="dropdown-toggle" type="button" class="text-white hover:underline focus:outline-none">';
                    echo $username;
                    echo '</button>';
                    echo '<div id="dropdown-menu" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 hidden">';
                    echo '<div class="py-1">';
                    echo '<a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>';
                    if ($role === 'admin') {
                        echo '<a href="index.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Beranda</a>';
                        echo '<a href="admin/panduan_admin.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Panduan Admin</a>';
                        echo '<a href="admin/daftar_user.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Daftar User</a>';
                        echo '<a href="admin/admin_dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard Admin</a>'; // Tambahkan tautan ke dashboard admin
                    }
                    echo '<a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    // Jika belum login, tampilkan link Login
                    echo '<a href="login.php" class="text-sm text-blue-600 dark:text-blue-500 hover:underline">Login</a>';
                }
                ?>
            </div>
        </div>
    </nav>
    <div class="container mx-auto py-4 px-4">
        <div class="bg-white p-6 rounded shadow-md max-w-lg mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-center">Edit Profil</h1>
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 text-red-700 p-4 mb-4 rounded">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
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
<?php
require 'components/footer.php'
?>
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
<script src="js/app.js"></script>
</body>

</html>
