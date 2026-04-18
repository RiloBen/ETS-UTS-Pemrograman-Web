<?php
session_start();

// Cek apakah ada informasi sesi pengguna yang aktif
if (isset($_SESSION['user_id'])) {
    // Jika pengguna sudah dalam keadaan login, arahkan menembus langsung ke folder Dashboard Pengeluaran
    header("Location: expenses/index.php");
    exit();
} else {
    // Jika belum pernah login/sesi sudah habis (logout), arahkan ke gerbang depan (Halaman Login).
    header("Location: auth/login.php");
    exit();
}
?>
