<?php
session_start();

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/koneksi.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    $user_id = $_SESSION['user_id'];

    if (empty($password_lama) || empty($password_baru) || empty($konfirmasi_password)) {
        $error = "Semua kolom harus diisi!";
    } elseif ($password_baru !== $konfirmasi_password) {
        $error = "Password baru dan konfirmasi password tidak cocok!";
    } else {
        // Cek password lama
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password_lama, $user['password'])) {
                // Hash password baru
                $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
                
                // Update password
                $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt_update->bind_param("si", $hashed_password, $user_id);
                
                if ($stmt_update->execute()) {
                    $success = "Password berhasil diubah!";
                } else {
                    $error = "Terjadi kesalahan saat mengubah password.";
                }
                $stmt_update->close();
            } else {
                $error = "Password lama salah!";
            }
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
    <title>Ganti Password - Xpense Tracker</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-bg">
    <div class="auth-container">
        <h2 class="auth-title">Ganti Password</h2>
        <p class="auth-subtitle">Silakan masukkan password baru Anda</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="ganti_password.php">
            <div class="form-group">
                <label for="password_lama">Password Lama</label>
                <input type="password" name="password_lama" id="password_lama" required>
            </div>
            
            <div class="form-group">
                <label for="password_baru">Password Baru</label>
                <input type="password" name="password_baru" id="password_baru" required>
            </div>
            
            <div class="form-group">
                <label for="konfirmasi_password">Konfirmasi Password Baru</label>
                <input type="password" name="konfirmasi_password" id="konfirmasi_password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Simpan Password</button>
        </form>
        
        <p class="auth-link">
            <a href="../expenses/index.php">Kembali ke Dashboard</a>
        </p>
    </div>
</body>
</html>
