<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
</head>
<body>
    <h1>Admin Page</h1>
    <p>Only accessible by admin.</p>
    <p><a href="index.php">Back to Home</a></p>
</body>
</html>
