<?php
session_start();
require_once '../config/koneksi.php';

$error = '';
// Ambil user id yang sedang aktif
$user_id = $_SESSION['user_id'];

// Pastikan ada parameter id di URL, jika tidak ada, kembalikan ke index
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$expense_id = (int)$_GET['id'];

// Proses penyimpanan data (Ketika Tombol Disubmit)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal     = $_POST['tanggal'];
    $category_id = (int)$_POST['category_id'];
    $deskripsi   = trim($_POST['deskripsi']);
    $jumlah      = (float)$_POST['jumlah'];

    if (empty($tanggal) || empty($category_id) || empty($jumlah)) {
        $error = "Tanggal, kategori, dan jumlah harus diisi!";
    } elseif ($jumlah <= 0) {
        $error = "Jumlah pengeluaran harus angka positif!";
    } else {
        // Melakukan UPDATE ke database. 
        // Menggunakan kondisi AND user_id = ? agar data milik user lain tidak bisa diedit sembarangan
        $stmt_update = $conn->prepare("UPDATE expenses SET category_id = ?, tanggal = ?, deskripsi = ?, jumlah = ? WHERE id = ? AND user_id = ?");
        $stmt_update->bind_param("issdii", $category_id, $tanggal, $deskripsi, $jumlah, $expense_id, $user_id);
        
        if ($stmt_update->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Gagal mengupdate data: " . $conn->error;
        }
        $stmt_update->close();
    }
}

// Mengambil data lama (Untuk Mengisi Form)
$stmt = $conn->prepare("SELECT * FROM expenses WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $expense_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Jika data dengan ID tersebut tidak ditemukan atau bukan milik akun ini
if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}
// Ekstrak datanya menjadi bentuk array
$expense_data = $result->fetch_assoc();
$stmt->close();

// Include layout visual setelah logika PHP beres
include '../partials/header.php';

// Ambil kategori untuk di-loop dalam <select>
$kategori_query = "SELECT id, name FROM categories ORDER BY name ASC";
$kategori_result = $conn->query($kategori_query);
?>

<div class="page-header">
    <h2>Edit Pengeluaran</h2>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</div>

<div class="card">
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Action mengarah ke edit.php?id=... agar tak kehilangan nilai ID-nya -->
    <form method="POST" action="edit.php?id=<?= $expense_id ?>">
        <div class="form-group">
            <label for="tanggal">Tanggal Pengeluaran <span class="text-danger">*</span></label>
            <!-- value diisi dengan data pengeluaran dari database -->
            <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= htmlspecialchars($expense_data['tanggal']) ?>" required>
        </div>

        <div class="form-group">
            <label for="category_id">Kategori <span class="text-danger">*</span></label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                <?php while ($kat = $kategori_result->fetch_assoc()): ?>
                    <!-- Tandai 'selected' jika ID Kategori sama dengan kategori pengeluaran ini sebelumnya -->
                    <?php $selected = ($kat['id'] == $expense_data['category_id']) ? 'selected' : ''; ?>
                    <option value="<?= $kat['id'] ?>" <?= $selected ?>><?= htmlspecialchars($kat['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="jumlah">Jumlah Pengeluaran (Rp) <span class="text-danger">*</span></label>
            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" step="0.01" value="<?= htmlspecialchars($expense_data['jumlah']) ?>" required>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($expense_data['deskripsi']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>

<?php 
include '../partials/footer.php'; 
?>
