<?php
require 'config.php';

$errors = [];
$username = $email = $name = $role = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $name = trim($_POST['name']); // Menambahkan pengambilan input name
    $password = $_POST['password'];
    $passwordConfirm = $_POST['passwordConfirm'];
    $role = 'user';  // Default role

    //validasi username
    if (empty($username)) {
        $errors[] = 'Username is required';
    } elseif (strlen($username) < 6 || strlen($username) > 20) {
        $errors[] = 'Username harus berisi 6-20 kata';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username hanya boleh berisi huruf, angka, dan garis bawah';
    } else {
        // Cek apakah username sudah ada
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Username sudah terdaftar';
        }
    }
    // Validasi email
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    } else {
        // Cek apakah email sudah ada
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Email sudah terdaftar';
        }
    }

    // Validasi nama
    if (empty($name)) {
        $errors[] = 'Name is required';
    }

    // Validasi password
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Kata sandi harus terdiri dari minimal 8 karakter';
    }

    // Validasi konfirmasi password
    if ($password !== $passwordConfirm) {
        $errors[] = 'Passwords tidak sesuai';
    }

    // Validasi role
    if ($role !== 'user' && $role !== 'admin') {
        $errors[] = 'Invalid role selected';
    }

    if (empty($errors)) {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $stmt = $pdo->prepare('INSERT INTO users (username, email, name, password, role) VALUES (?, ?, ?, ?, ?)');
        if ($stmt->execute([$username, $email, $name, $hashedPassword, $role])) {
            $success = true;
        } else {
            $errors[] = 'Error registering user';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    body {
        background-image: url('assets/image/background.jpg');
        object-fit: fill;
        background-position: center;
        height: auto;
    }
    </style>
</head>

<body>
    <?php if (isset($success) && $success): ?>
    <script>
    Swal.fire({
        title: 'Registrasi Berhasil',
        text: 'User telah berhasil terdaftar.',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'login.php';
        }
    });
    </script>
    <?php elseif (!empty($errors)): ?>
    <script>
    Swal.fire({
        title: 'Error',
        html: '<?php echo implode("<br>", array_map("htmlspecialchars", $errors)); ?>',
        icon: 'error',
        confirmButtonText: 'OK'
    });
    </script>
    <?php endif; ?>


    <section>
        <div class="bg-transparent h-screen flex flex-col items-center justify-center">
            <div class="max-w-md mx-auto my-3 bg-white p-8 rounded shadow-md bg-opacity-70">
                <img src="assets/image/logo3.png" alt="" class="mt-0">
                <h2 class="text-2xl mb-4 text-center">Register</h2>
                <?php if (!empty($errors)): ?>
                <ul class="text-red-500">
                    <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                <form action="register.php" method="POST">
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700">Username:</label>
                        <input type="text" id="username" name="username"
                            value="<?php echo htmlspecialchars($username); ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                        <input type="password" id="password" name="password"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div class="mb-4">
                        <label for="passwordConfirm" class="block text-sm font-medium text-gray-700">Confirm
                            Password:</label>
                        <input type="password" id="passwordConfirm" name="passwordConfirm"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <button type="submit"
                        class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Register
                    </button>
                </form>
                <p class="mt-2 text-center">Sudah mempunyai akun? <a href="login.php"
                        class="text-blue-900 hover:text-blue-500">Login Sekarang</a></p>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
</body>

</html>