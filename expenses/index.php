<?php
session_start();
// Wajib mengecek session login, tapi secara logika kita cek setelah include koneksi
require_once '../config/koneksi.php';

// Memasukkan header (yang juga berisi pengecekan session. Jika belum login, dilempar ke login.php)
include '../partials/header.php';

// Ambil data user yang sedang login
$user_id = $_SESSION['user_id'];

// Mengambil nilai filter bulan dan tahun. Jika tidak ada di URL, pakai bulan dan tahun berjalan
$bulan_filter = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun_filter = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Query untuk mengambil data sesuai user, bulan, dan tahun menggunakan prepared statements
$query = "SELECT e.id, e.tanggal, c.name AS kategori, e.deskripsi, e.jumlah 
          FROM expenses e 
          JOIN categories c ON e.category_id = c.id 
          WHERE e.user_id = ? AND MONTH(e.tanggal) = ? AND YEAR(e.tanggal) = ?
          ORDER BY e.tanggal DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $user_id, $bulan_filter, $tahun_filter);
$stmt->execute();
$result = $stmt->get_result();

$total_pengeluaran = 0;
$jumlah_transaksi = $result->num_rows;
$data_pengeluaran = [];

// Memasukkan data ke dalam array dan menghitung total
while ($row = $result->fetch_assoc()) {
    $data_pengeluaran[] = $row;
    $total_pengeluaran += $row['jumlah'];
}
$stmt->close();
?>

<div class="page-header">
    <h2>Dashboard Pengeluaran</h2>
    <a href="tambah.php" class="btn btn-primary">+ Tambah Data</a>
</div>

<!-- Kartu Ringkasan (Summary) -->
<div class="summary-cards">
    <div class="card card-summary">
        <h3>Total Pengeluaran Bulan Ini</h3>
        <p class="amount text-teal">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></p>
    </div>
    <div class="card card-summary">
        <h3>Jumlah Transaksi</h3>
        <p class="amount"><?= $jumlah_transaksi ?> <small>Transaksi</small></p>
    </div>
</div>

<!-- Area Filter -->
<div class="card filter-card">
    <form method="GET" action="index.php" class="form-inline">
        <label for="bulan">Filter:</label>
        <select name="bulan" id="bulan" class="form-control">
            <?php
            $nama_bulan = [
                "01" => "Januari", "02" => "Februari", "03" => "Maret", 
                "04" => "April", "05" => "Mei", "06" => "Juni", 
                "07" => "Juli", "08" => "Agustus", "09" => "September", 
                "10" => "Oktober", "11" => "November", "12" => "Desember"
            ];
            foreach ($nama_bulan as $num => $name) {
                $selected = ($num == $bulan_filter) ? 'selected' : '';
                echo "<option value='$num' $selected>$name</option>";
            }
            ?>
        </select>
        
        <select name="tahun" id="tahun" class="form-control">
            <?php
            $tahun_sekarang = date('Y');
            // Menampilkan range 5 tahun ke belakang hingga tahun ini
            for ($i = $tahun_sekarang - 5; $i <= $tahun_sekarang; $i++) {
                $selected = ($i == $tahun_filter) ? 'selected' : '';
                echo "<option value='$i' $selected>$i</option>";
            }
            ?>
        </select>
        
        <button type="submit" class="btn btn-secondary">Terapkan Filter</button>
    </form>
</div>

<!-- Tabel Data Pengeluaran -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah (Rp)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($jumlah_transaksi > 0): ?>
                    <?php $no = 1; foreach ($data_pengeluaran as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <!-- Format tanggal DD-MM-YYYY -->
                            <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                            <td><span class="badge"><?= htmlspecialchars($row['kategori']) ?></span></td>
                            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                            <!-- Format Rupiah -->
                            <td class="text-right">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                            <td class="table-actions">
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                <!-- Meminta konfirmasi javascript sebelum hapus data -->
                                <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data pengeluaran ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- State jika data kosong -->
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding: 2rem;">Belum ada data pengeluaran yang tercatat pada periode ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
// Memasukkan footer
include '../partials/footer.php'; 
?>
