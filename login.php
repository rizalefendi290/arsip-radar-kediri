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
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on user role
            if ($user['role'] == 'admin') {
                header('Location: admin/admin_dashboard.php');
            } else {
                header('Location: user/user_dashboard.php');
            }
            exit;
        } else {
            $errors[] = 'Invalid username/email or password';
        }
    }
}

require 'components/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <section>
        <div class="bg-gray-100 h-screen flex items-center justify-center">
            <div class="bg-white p-8 rounded shadow-md max-w-md w-full">
                <h2 class="text-2xl mb-6">Login</h2>
                <?php if ($errors): ?>
                <ul class="text-red-500 mb-4">
                    <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                <form action="login.php" method="POST">
                    <div class="mb-4">
                        <label for="identifier" class="block text-gray-700">Username or Email:</label>
                        <input type="text" id="identifier" name="identifier" class="form-input mt-1 block w-full"
                            autofocus>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-gray-700">Password:</label>
                        <input type="password" id="password" name="password" class="form-input mt-1 block w-full">
                    </div>

                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Login</button>
                </form>
                <p>Belum mempunyai akun?
                    <a href="register.php">buat sekarang</a>
                </p>
            </div>
        </div>
    </section>
</body>

</html>