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
</head>

<body class="bg-gray-100">
<nav class="bg-white border-gray-200 dark:bg-gray-900">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl p-4">
            <a href="index.php" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo" />
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Perpustakaan Digital
                    Radar Kediri</span>
            </a>
            <div class="text-center">
                <button
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none"
                    type="button" data-drawer-target="drawer-disable-body-scrolling"
                    data-drawer-show="drawer-disable-body-scrolling" data-drawer-body-scrolling="false"
                    aria-controls="drawer-disable-body-scrolling">
                    <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Show Menu'; ?>
                </button>
            </div>

            <div id="drawer-disable-body-scrolling"
                class="fixed top-0 right-0 z-40 h-screen p-4 overflow-y-auto transition-transform -translate-x-full bg-white w-64 dark:bg-gray-800"
                tabindex="-1" aria-labelledby="drawer-disable-body-scrolling-label">
                <h5 id="drawer-disable-body-scrolling-label"
                    class="text-base font-semibold text-gray-500 uppercase dark:text-gray-400">Menu</h5>
                <button type="button" data-drawer-hide="drawer-disable-body-scrolling"
                    aria-controls="drawer-disable-body-scrolling"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 absolute top-2.5 end-2.5 inline-flex items-center justify-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close menu</span>
                </button>
                <div class="py-4 overflow-y-auto">
                    <ul class="space-y-2 font-medium">
                        <?php if (isset($_SESSION['name'])) : ?>
                        <li>
                            <a href="profile.php"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path fill-rule="evenodd"
                                        d="M12 20a7.966 7.966 0 0 1-5.002-1.756l.002.001v-.683c0-1.794 1.492-3.25 3.333-3.25h3.334c1.84 0 3.333 1.456 3.333 3.25v.683A7.966 7.966 0 0 1 12 20ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10c0 5.5-4.44 9.963-9.932 10h-.138C6.438 21.962 2 17.5 2 12Zm10-5c-1.84 0-3.333 1.455-3.333 3.25S10.159 13.5 12 13.5c1.84 0 3.333-1.455 3.333-3.25S13.841 7 12 7Z"
                                        clip-rule="evenodd" />
                                </svg>

                                <span class="ms-3">Profile</span>
                            </a>
                        </li>
                        <?php if ($role === 'admin') : ?>
                        <li>
                            <a href="index.php"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path fill-rule="evenodd"
                                        d="M11.293 3.293a1 1 0 0 1 1.414 0l6 6 2 2a1 1 0 0 1-1.414 1.414L19 12.414V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2v-6.586l-.293.293a1 1 0 0 1-1.414-1.414l2-2 6-6Z"
                                        clip-rule="evenodd" />
                                </svg>

                                <span class="ms-3">Beranda</span>
                            </a>
                        </li>
                        <li>
                            <a href="admin/admin_dashboard.php"
                                class="flex items-center p-2 text-white rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 22 21">
                                    <path
                                        d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                                    <path
                                        d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                                </svg>
                                <span class="ms-3">Dashboard Admin</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <li>
                            <a href="logout.php"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                <svg class="w-6 h-6 text-white dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 12H4m12 0-4 4m4-4-4-4m3-4h2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3h-2" />
                                </svg>

                                <span class="ms-3">Logout</span>
                            </a>
                        </li>
                        <?php else : ?>
                        <li>
                            <a href="login.php"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 22 21">
                                    <path
                                        d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                                    <path
                                        d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                                </svg>
                                <span class="ms-3">Login</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <main>
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

    </main>
<?php
require 'components/footer.php'
?>
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
<script src="js/app.js"></script>
</body>

</html>
