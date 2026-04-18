<?php
// Konfigurasi koneksi ke database
$host = "localhost";
$user = "";       // Silakan sesuaikan jika MySQL Anda menggunakan username lain
$pass = ""; // Ganti dengan password MySQL Anda
$db   = "xpense_tracker";

// Membuat koneksi menggunakan mysqli
$conn = new mysqli($host, $user, $pass, $db);

// Memeriksa apakah koneksi berhasil
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
