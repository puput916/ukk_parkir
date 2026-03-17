<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') header("Location: ../index.php");

if(isset($_POST['tambah'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama_area']);
    $kapasitas = $_POST['kapasitas'];
    exec_query("INSERT INTO tb_area_parkir (nama_area, kapasitas) VALUES ('$nama', '$kapasitas')");
}
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    exec_query("DELETE FROM tb_area_parkir WHERE id_area = $id");
    header("Location: area_manage.php");
}

$areas = query("SELECT * FROM tb_area_parkir");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Area Parkir</title>
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
            <a href="tarif_manage.php">Kelola Tarif</a>
            <a href="area_manage.php" class="active">Kelola Area</a>
        </div>
        <div class="nav-user">
            <span style="font-size: 14px; font-weight: 600;"><?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="../logout.php" class="nav-logout">Logout</a>
        </div>
    </nav>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2>Pemetaan Area Parkir</h2>
        </div>
        <div style="display: flex; gap: 30px; align-items: flex-start;">
            <div class="form-card" style="flex: 1;">
                <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px;">Tambah Lokasi Baru</h3>
                <form method="POST">
            <input type="text" name="nama_area" placeholder="Nama Area" required>
            <input type="number" name="kapasitas" placeholder="Kapasitas" required>
                <button type="submit" name="tambah" class="btn-custom">Simpan Area</button>
                </form>
            </div>

            <div class="table-container" style="flex: 2; margin-top: 0;">
                <table>
                    <tr><th>Nama Area</th><th>Kapasitas Maks</th><th>Terisi Saat Ini</th><th style="text-align: right;">Aksi</th></tr>
                    <?php foreach($areas as $a): ?>
                    <tr>
                        <td style="font-weight: 600;"><?= $a['nama_area'] ?></td>
                        <td><?= $a['kapasitas'] ?> Kendaraan</td>
                        <td>
                            <span style="background: #eef2ff; color: #4f46e5; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                <?= $a['terisi'] ?> Unit
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="?hapus=<?= $a['id_area'] ?>" class="btn-custom btn-danger" style="padding: 6px 12px; font-size: 13px; width: auto;" onclick="return confirm('Hapus area permanen?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
