<?php
// Pastikan variabel session tersedia karena ini akan dipanggil di dalam file yang sudah memanggil session_start()
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Xpense Tracker</title>
    <!-- CSS dihubungkan dengan asumsi file memanggil header letaknya di dalam folder expenses/ -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="layout-wrapper">
        <!-- Sidebar Kiri -->
        <aside class="sidebar">
            <div class="sidebar-header" style="display: flex; align-items: center; gap: 0.8rem;">
                <img src="../assets/img/logo.png" alt="Logo" style="width: 100px; height: 100px; border-radius: 50%;">
                <h2>Xpense Tracker</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="tambah.php">Tambah Pengeluaran</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../auth/ganti_password.php" class="btn-logout" style="display: block; margin-bottom: 0.5rem; color: #cbd5e1 !important;">Ganti Password</a>
                <a href="../auth/logout.php" class="btn-logout" style="display: block; margin-bottom: 0.5rem;" onclick="return confirm('Apakah Anda yakin ingin logout dari aplikasi?')">Logout</a>
                <!-- Link Hapus Akun menuju halaman verifikasi password -->
                <a href="../auth/hapus_akun.php" class="btn-logout" style="display: block; font-size: 0.85rem; color: #ef4444 !important;">Hapus Akun</a>
            </div>
        </aside>

        <!-- Konten Utama di Kanan -->
        <main class="main-content">
            <!-- Header Atas -->
            <header class="top-header">
                <div class="user-greeting">
                    Selamat datang, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong>!
                </div>
            </header>
            
            <!-- Area konten halaman akan disisipkan di bawah ini -->
            <div class="content-container">
