<?php
session_start();

// Jika pengguna sudah login, arahkan ke halaman utama (dashboard)
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../config/koneksi.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Username dan Password tidak boleh kosong!";
    } else {
        // Ambil data user dari database berdasarkan username
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Jika username ditemukan
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verifikasi kecocokan password yang diinput dengan hash di database
            if (password_verify($password, $user['password'])) {
                // Password benar, set variabel session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Arahkan ke halaman utama
                header("Location: ../index.php");
                exit();
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Xpense Tracker</title>
    <!-- Link ke file CSS utama -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-bg">
    <div class="auth-container">
        <div style="text-align: center; margin-bottom: 1rem;">
            <img src="../assets/img/logo.png" alt="Logo" style="width: 200px; height: 200px; border-radius: 50%;">
        </div>
        <h2 class="auth-title">Xpense Tracker</h2>
        <p class="auth-subtitle">Silakan login untuk mengelola pengeluaran Anda</p>
        
        <!-- Menampilkan pesan error jika ada -->
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Masukkan username" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Masukkan password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        
        <p class="auth-link">
            Belum punya akun? <a href="register.php">Daftar sekarang</a>
        </p>
    </div>
</body>
</html>
