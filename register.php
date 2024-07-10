<?php
require 'config.php';

$errors = [];
$username = $email = $role = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $passwordConfirm = $_POST['passwordConfirm'];
    $role = $_POST['role'];  // Get role from form input

    // Validasi username
    if (empty($username)) {
        $errors[] = 'Username is required';
    } elseif (strlen($username) < 6 || strlen($username) > 20) {
        $errors[] = 'Username must be between 6 and 20 characters';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores';
    }

    // Validasi email
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    // Validasi password
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }

    // Validasi konfirmasi password
    if ($password !== $passwordConfirm) {
        $errors[] = 'Passwords do not match';
    }

    // Validasi role
    if ($role !== 'user' && $role !== 'admin') {
        $errors[] = 'Invalid role selected';
    }

    if (empty($errors)) {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)');
        if ($stmt->execute([$username, $email, $hashedPassword, $role])) {
            echo 'User registered successfully';
        } else {
            echo 'Error registering user';
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
    <section>
        <div class="bg-transparent h-screen flex flex-col items-center justify-center">
            <img src="assets/image/logo3.png" alt="" class="mt-10">
            <div class="max-w-md mx-auto mb-20 bg-white p-8 rounded shadow-md bg-opacity-50">
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
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
    
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"
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
    
                    <div class="mb-4">
                        <label for="role" class="block text-sm font-medium text-gray-700">Role:</label>
                        <select id="role" name="role"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="user" <?php if ($role === 'user') echo 'selected'; ?>>User</option>
                            <option value="admin" <?php if ($role === 'admin') echo 'selected'; ?>>Admin</option>
                        </select>
                    </div>
    
                    <button type="submit"
                        class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Register
                    </button>
                </form>
                <p class="mt-2 text-center">Sudah mempunyai akun? <a href="login.php">Login Sekarang</a></p>
            </div>
        </div>
    </section>
    
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
</body>

</html>