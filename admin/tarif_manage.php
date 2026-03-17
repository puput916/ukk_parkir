<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') header("Location: ../index.php");

if(isset($_POST['tambah'])){
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis_kendaraan']);
    $tarif = $_POST['tarif_per_jam'];
    exec_query("INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam) VALUES ('$jenis', '$tarif')");
}
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    exec_query("DELETE FROM tb_tarif WHERE id_tarif = $id");
    header("Location: tarif_manage.php");
}

$tarifs = query("SELECT * FROM tb_tarif");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Tarif</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="top-nav">
        <div class="nav-brand">
            🅿️ PARKIR-PRO <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">| Admin</span>
        </div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="user_manage.php">Kelola User</a>
            <a href="tarif_manage.php" class="active">Kelola Tarif</a>
            <a href="area_manage.php">Kelola Area</a>
        </div>
        <div class="nav-user">
            <span style="font-size: 14px; font-weight: 600;"><?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="../logout.php" class="nav-logout">Logout</a>
        </div>
    </nav>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2>Daftar Tarif Kendaraan</h2>
        </div>
        <div style="display: flex; gap: 30px; align-items: flex-start;">
            <div class="form-card" style="flex: 1;">
                <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px;">Tambah Tarif Baru</h3>
                <form method="POST">
            <input type="text" name="jenis_kendaraan" placeholder="Jenis Kendaraan" required>
            <input type="number" name="tarif_per_jam" placeholder="Tarif Per Jam" required>
                <button type="submit" name="tambah" class="btn-custom">Simpan Tarif</button>
                </form>
            </div>

            <div class="table-container" style="flex: 2; margin-top: 0;">
                <table>
                    <tr><th>ID Tarif</th><th>Kategori Kendaraan</th><th>Harga Per Jam</th><th style="text-align: right;">Aksi</th></tr>
                    <?php foreach($tarifs as $t): ?>
                    <tr>
                        <td style="color: var(--text-muted); font-family: monospace;">#<?= $t['id_tarif'] ?></td>
                        <td style="font-weight: 600;"><?= $t['jenis_kendaraan'] ?></td>
                        <td style="color: #10b981; font-weight: 600;">Rp <?= number_format($t['tarif_per_jam']) ?></td>
                        <td style="text-align: right;">
                            <a href="?hapus=<?= $t['id_tarif'] ?>" class="btn-custom btn-danger" style="padding: 6px 12px; font-size: 13px; width: auto;" onclick="return confirm('Yakin ingin menghapus tarif <?= $t['jenis_kendaraan'] ?>?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
