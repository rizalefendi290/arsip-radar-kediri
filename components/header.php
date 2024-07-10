<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital Radar Kediri</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <nav class="bg-white border-gray-200 dark:bg-gray-900">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl p-4">
            <a href="https://flowbite.com" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo" />
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Perpustakaan Digital Radar Kediri</span>
            </a>
            <div class="flex items-center space-x-6 rtl:space-x-reverse">
                <?php
            if (isset($_SESSION['username'])) {
                // Jika user sudah login
                $username = htmlspecialchars($_SESSION['username']);
                $role = $_SESSION['role'];

                echo '<div class="relative inline-block text-left">';
                echo '<button id="dropdown-toggle" type="button" class="text-white hover:underline focus:outline-none">';
                echo $username;
                echo '</button>';
                echo '<div id="dropdown-menu" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 hidden">';
                echo '<div class="py-1">';
                echo '<a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>';
                if ($role === 'admin') {
                    echo '<a href="../admin/panduan_admin.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Panduan Admin</a>';
                }
                echo '<a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            } else {
                // Jika belum login, tampilkan link Login
                echo '<a href="../login.php" class="text-sm text-blue-600 dark:text-blue-500 hover:underline">Login</a>';
            }
        ?>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdownToggle = document.getElementById('dropdown-toggle');
            const dropdownMenu = document.getElementById('dropdown-menu');

            dropdownToggle.addEventListener('click', function () {
                dropdownMenu.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside of it
            document.addEventListener('click', function (event) {
                if (!dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html>
