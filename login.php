<?php
include 'includes/functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Pembersihan input
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');

    if (empty($username) || empty($password)) {
        $error_message = "Both fields are required.";
    } else {
        $user = loginUser($username, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: index.php');
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    }
}
require "components/header.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>

<body class="">
    <section class="flex items-center justify-center min-h-screen bg-slate-800">
        <div class="grid justify-items-center">
            <img src="assets/image/logo3.png" alt="" class="mb-2" width="500">
            <div class="w-full max-w-sm p-8 bg-white rounded shadow-md">
                <h2 class="text-2xl font-bold text-center">Login</h2>
                <form action="login.php" method="POST" class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username or Email</label>
                        <input type="text" name="username" id="username" placeholder="Username or Email" required
                            class="w-full px-4 py-2 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password" placeholder="Password" required
                            class="w-full px-4 py-2 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                    </div>
                    <div>
                        <button type="submit"
                            class="w-full px-4 py-2 font-bold text-white bg-indigo-600 rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-opacity-50">Login</button>
                    </div>
                </form>
                <p class="mt-4 text-sm text-center text-gray-600">Don't have an account? <a href="register.php"
                        class="text-indigo-600 hover:underline">Register</a></p>
            </div>
        </div>
    </section>

</body>

</html>