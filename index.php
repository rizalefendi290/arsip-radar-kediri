<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require "components/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?></h1>
    <p>You are logged in as <?php echo $_SESSION['role']; ?></p>
    <?php if ($_SESSION['role'] === 'admin'): ?>
        <p><a href="admin.php">Go to Admin Page</a></p>
    <?php endif; ?>
    <p><a href="logout.php">Logout</a></p>
</body>

<?php
require "components/footer.php";
?>
</html>
