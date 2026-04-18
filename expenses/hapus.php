<?php
session_start();
require_once '../config/koneksi.php';

// Pastikan hanya user yang sudah login yang bisa mengakses file ini
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Periksa apakah ada parameter id di URL yang akan dihapus
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $expense_id = (int)$_GET['id'];

    // Hapus data dari tabel. 
    // Syarat utamanya adalah: ID pengeluaran harus cocok DAN user_id juga harus cocok dengan data session, demi keamanan.
    $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $expense_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Setelah selesai (baik berhasil dihapus atau gagal/tidak ketemu id-nya), kembalikan ke dashboard
header("Location: index.php");
exit();
?>
