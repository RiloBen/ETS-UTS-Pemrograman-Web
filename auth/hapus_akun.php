<?php
session_start();
require_once '../config/koneksi.php';

// Pastikan hanya user yang sudah login yang bisa mengakses file ini
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password_input = $_POST['password'];

    if (empty($password_input)) {
        $error = "Password tidak boleh kosong!";
    } else {
        // Ambil hash password dari database
        $stmt_check = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt_check->bind_param("i", $user_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verifikasi password yang dimasukkan
            if (password_verify($password_input, $user['password'])) {
                // Password benar, lanjutkan penghapusan akun
                $stmt_delete = $conn->prepare("DELETE FROM users WHERE id = ?");
                $stmt_delete->bind_param("i", $user_id);
                
                if ($stmt_delete->execute()) {
                    session_unset();
                    session_destroy();
                    // Arahkan kembali ke halaman login sesudah akun dihapus
                    header("Location: login.php");
                    exit();
                } else {
                    $error = "Gagal menghapus akun: " . $conn->error;
                }
                $stmt_delete->close();
            } else {
                $error = "Password salah! Penghapusan akun dibatalkan.";
            }
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
    <title>Hapus Akun - Xpense Tracker</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .btn-danger-block {
            background-color: var(--danger);
            color: white;
            width: 100%;
            display: block;
            margin-top: 1rem;
        }
        .btn-danger-block:hover {
            background-color: #dc2626; /* Darker red */
            color: white;
        }
    </style>
</head>
<body class="auth-bg">
    <div class="auth-container">
        <h2 class="auth-title text-danger">Hapus Akun</h2>
        <p class="auth-subtitle">Aksi ini <strong>permanen</strong>. Seluruh riwayat pengeluaran Anda akan hilang dan tidak dapat dikembalikan.<br><br>Silakan masukkan password Anda untuk konfirmasi.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="hapus_akun.php">
            <div class="form-group">
                <label for="password">Password Anda</label>
                <input type="password" name="password" id="password" required>
            </div>
            
            <button type="submit" class="btn btn-danger-block">Ya, Hapus Akun Saya</button>
        </form>
        
        <p class="auth-link">
            <a href="../expenses/index.php">Batal dan kembali ke Dashboard</a>
        </p>
    </div>
</body>
</html>
