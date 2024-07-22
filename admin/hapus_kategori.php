<?php
// hapus_kategori.php

include '../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Query untuk menghapus data kategori berdasarkan ID
    $query = "DELETE FROM categories WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        header('Location: manage_categories.php');
        exit;
    } else {
        die("Gagal menghapus kategori.");
    }
} else {
    die("ID Kategori tidak diberikan.");
}
?>
