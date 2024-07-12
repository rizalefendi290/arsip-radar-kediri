<?php
require 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']);
    $password = trim($_POST['password']);

    if (empty($identifier)) {
        $errors[] = 'Username or Email is required';
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
    }

    if (empty($errors)) {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        } else {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        }
        $stmt->execute([$identifier]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on user role
            if ($user['role'] == 'admin') {
                header('Location: admin/admin_dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $errors[] = 'Invalid username/email or password';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Login</title>
    <style>
    body {
        background-image: url('assets/image/background.jpg');
        object-fit: cover;
        background-position: center;
        height: auto;
    }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
</head>

<body>
    <section>
        <div class="bg-transparent h-screen flex flex-col items-center justify-center">
            <div class="p-8 rounded shadow-md max-w-md w-full bg-white bg-opacity-70">
                <img src="assets/image/logo3.png" alt="" width="400" class="">
                <h2 class="text-2xl mb-6 text-center fw-bold">Login</h2>
                <?php if ($errors): ?>
                <ul class="text-red-500 mb-4">
                    <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                <form action="login.php" method="POST">
                    <div class="mb-4">
                        <label for="identifier" class="block text-black">Username or Email:</label>
                        <input type="text" id="identifier" name="identifier" class="form-input mt-1 block w-full"
                            autofocus>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-black">Password:</label>
                        <input type="password" id="password" name="password"
                            class="form-input mt-1 block w-full text-black">
                    </div>

                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white fw-bold px-4 py-2 rounded">Login</button>
                </form>
                <p>Belum mempunyai akun?
                    <a href="register.php" class="text-blue-900 hover:text-blue-500">Buat sekarang</a>
                </p>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>

</body>

</html>