<?php
session_start();

// Include config file
require_once '../config.php';

// Define variables and initialize with empty values
$name = "";
$name_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    $input_name = trim($_POST["name"]);
    if (empty($input_name)) {
        $name_err = "Please enter a category name.";
    } else {
        $name = $input_name;
    }

    // Check input errors before inserting into database
    if (empty($name_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO categories (name) VALUES (:name)";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":name", $param_name);
            $param_name = $name;
            if ($stmt->execute()) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Category Created',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location = 'manage_categories.php';
                    });
                </script>";
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kategori</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .container {
            max-width: 800px;
            margin: auto;
        }
    </style>
</head>
<body class="bg-gray-100 py-10">
    <div class="flex flex-col md:flex-row">
        <?php require 'navbar_admin.php'; ?>
        <div class="p-4 sm:ml-64">
            <div class="p-4 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
                <div class="container bg-white shadow-lg rounded-lg px-4 py-6">
                    <h2 class="text-2xl font-bold mb-4">Tambah Data Kategori</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                            <input type="text" name="name" id="name"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?php echo (!empty($name_err)) ? 'border-red-500' : ''; ?>"
                                   value="<?php echo $name; ?>" required>
                            <span class="text-xs text-red-500"><?php echo $name_err; ?></span>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-300">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
