<?php
session_start();
// Wajib memanggil koneksi terlebih dahulu
require_once '../config/koneksi.php';

$error = '';

// Proses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal     = $_POST['tanggal'];
    $category_id = (int)$_POST['category_id'];
    $deskripsi   = trim($_POST['deskripsi']);
    $jumlah      = (float)$_POST['jumlah'];
    $user_id     = $_SESSION['user_id'];

    // Validasi input (tidak boleh kosong dari sisi server)
    if (empty($tanggal) || empty($category_id) || empty($jumlah)) {
        $error = "Tanggal, kategori, dan jumlah harus diisi!";
    } elseif ($jumlah <= 0) {
        // Validasi agar tidak bisa input angka minus atau nol
        $error = "Jumlah pengeluaran harus angka positif (lebih dari 0)!";
    } else {
        // Simpan ke database menggunakan prepared statement supaya aman dari SQL injection
        $stmt = $conn->prepare("INSERT INTO expenses (user_id, category_id, tanggal, deskripsi, jumlah) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iissd", $user_id, $category_id, $tanggal, $deskripsi, $jumlah);
        
        if ($stmt->execute()) {
            // Setelah tersimpan, lemparkan kembali ke dashboard
            header("Location: index.php");
            exit();
        } else {
            $error = "Gagal menyimpan data: " . $conn->error;
        }
        $stmt->close();
    }
}

// Menampilkan tampilan layout mulai dari sini (termasuk cek session)
include '../partials/header.php';

// Menarik list kategori dari tabel categories untuk dropdown (select)
$kategori_query = "SELECT id, name FROM categories ORDER BY name ASC";
$kategori_result = $conn->query($kategori_query);
?>

<div class="page-header">
    <h2>Tambah Pengeluaran</h2>
    <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
</div>

<div class="card">
    <!-- Menampilkan peringatan / error jika validasi meleset -->
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="tambah.php">
        <div class="form-group">
            <label for="tanggal">Tanggal Pengeluaran <span class="text-danger">*</span></label>
            <!-- Secara default kita set nilainya hari ini agar lebih mudah -->
            <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="form-group">
            <label for="category_id">Kategori <span class="text-danger">*</span></label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                <?php while ($kat = $kategori_result->fetch_assoc()): ?>
                    <option value="<?= $kat['id'] ?>"><?= htmlspecialchars($kat['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="jumlah">Jumlah Pengeluaran (Rp) <span class="text-danger">*</span></label>
            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" step="0.01" placeholder="Contoh: 50000" required>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" placeholder="Catatan opsional (misal: Makan siang di KFC)"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Pengeluaran</button>
    </form>
</div>

<?php 
// Menutup layout HTML
include '../partials/footer.php'; 
?>
