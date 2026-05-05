<?php
require_once 'config/database.php';

// TODO: Validasi ID dari GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?message=ID tidak ditemukan");
    exit;
}

$id = $_GET['id'];

// Validasi ID harus angka
if (!is_numeric($id)) {
    header("Location: index.php?message=ID tidak valid");
    exit;
}

// TODO: Cek keberadaan data
$query = "SELECT id_kategori FROM kategori WHERE id_kategori = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php?message=Data tidak ditemukan");
    exit;
}

// TODO: Delete data
$deleteQuery = "DELETE FROM kategori WHERE id_kategori = ?";
$deleteStmt = $conn->prepare($deleteQuery);
$deleteStmt->bind_param("i", $id);
$deleteStmt->execute();

// Cek affected_rows
if ($deleteStmt->affected_rows > 0) {
    header("Location: index.php?message=Data berhasil dihapus");
} else {
    header("Location: index.php?message=Gagal menghapus data");
}

exit;
?>