<?php
session_start();

// Hapus semua variabel session
session_unset();

// Hancurkan session yang sedang berjalan
session_destroy();

// Arahkan kembali pengguna ke halaman login
header("Location: login.php");
exit();
?>
