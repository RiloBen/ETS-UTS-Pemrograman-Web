<?php
session_start();

// Jika pengguna sudah login, arahkan langsung ke halaman utama
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Menyertakan file koneksi database
require_once '../config/koneksi.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validasi input dasar
    if (empty($username) || empty($password)) {
        $error = "Username dan Password tidak boleh kosong!";
    } else {
        // Cek apakah username sudah ada di database menggunakan prepared statement
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error = "Username sudah digunakan, silakan pilih yang lain.";
        } else {
            // Hash password agar aman di database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Simpan akun baru ke database
            $stmt_insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt_insert->bind_param("ss", $username, $hashed_password);

            if ($stmt_insert->execute()) {
                $success = "Registrasi berhasil! Silakan lanjut ke halaman login.";
            } else {
                $error = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Xpense Tracker</title>
    <!-- Menghubungkan ke file CSS utama yang akan kita buat nanti -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-bg">
    <div class="auth-container">
        <h2 class="auth-title">Daftar Akun</h2>
        <p class="auth-subtitle">Buat akun untuk mencatat pengeluaran Anda</p>
        
        <!-- Menampilkan pesan error atau sukses -->
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Masukkan username" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Masukkan password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>
        
        <p class="auth-link">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </p>
    </div>
</body>
</html>
