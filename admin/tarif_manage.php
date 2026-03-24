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
    <link rel="stylesheet" href="../assets/css/style.css?v=6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-logo"><img src="../assets/logo_web.png" alt="Logo"></div>
            <div class="sidebar-label">Menu</div>
                        <ul class="sidebar-menu">
                <a href="dashboard.php" ><span class="icon-box"><i class="fa-solid fa-gauge-high"></i></span> <span>Dashboard</span></a>
                <a href="user_manage.php" ><span class="icon-box"><i class="fa-solid fa-users"></i></span> <span>Kelola User</span></a>
                <a href="tarif_manage.php" class="active"><span class="icon-box"><i class="fa-solid fa-tags"></i></span> <span>Kelola Tarif</span></a>
                <a href="area_manage.php" ><span class="icon-box"><i class="fa-solid fa-map-location-dot"></i></span> <span>Kelola Area</span></a>
                <a href="log_aktivitas.php" ><span class="icon-box"><i class="fa-solid fa-clock-rotate-left"></i></span> <span>Log Aktivitas</span></a>
            </ul>
            <div class="sidebar-user">
                <div class="sidebar-user-info"><div class="avatar"><i class="fa-solid fa-user"></i></div><div class="user-detail"><span><?= $_SESSION['user']['nama_lengkap'] ?></span><small><?= $_SESSION['user']['role'] ?></small></div></div>
                <a href="../logout.php" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header"><h1><i class="fa-solid fa-tags"></i>Daftar Tarif Kendaraan</h1></div>

            <div style="display: flex; gap: 28px; align-items: flex-start;">
                <div class="form-card" style="flex: 1; max-width: 380px;">
                    <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px; font-weight: 700;"><i class="fa-solid fa-plus-circle" style="margin-right: 8px; color: var(--pink-400);"></i>Tambah Tarif Baru</h3>
                    <form method="POST">
                        <label><i class="fa-solid fa-car" style="margin-right: 5px; color: var(--text-muted);"></i> Jenis Kendaraan</label>
                        <input type="text" name="jenis_kendaraan" placeholder="Jenis Kendaraan" required>
                        <label><i class="fa-solid fa-money-bill" style="margin-right: 5px; color: var(--text-muted);"></i> Tarif Per Jam (Rp)</label>
                        <input type="number" name="tarif_per_jam" placeholder="Tarif Per Jam" required>
                        <button type="submit" name="tambah" class="btn-custom"><i class="fa-solid fa-plus"></i> Simpan Tarif</button>
                    </form>
                </div>

                <div class="table-container" style="flex: 2;">
                    <table>
                        <thead><tr><th>ID</th><th>Kategori Kendaraan</th><th>Harga Per Jam</th><th style="text-align: right;">Aksi</th></tr></thead>
                        <tbody>
                            <?php foreach($tarifs as $t): ?>
                            <tr>
                                <td style="color: var(--text-muted); font-family: monospace;">#<?= $t['id_tarif'] ?></td>
                                <td style="font-weight: 600;"><?= $t['jenis_kendaraan'] ?></td>
                                <td><span class="badge badge-green" style="font-size: 13px;">Rp <?= number_format($t['tarif_per_jam']) ?></span></td>
                                <td style="text-align: right;">
                                    <a href="?hapus=<?= $t['id_tarif'] ?>" class="btn-custom btn-danger" style="padding: 6px 14px; font-size: 12px; width: auto;" onclick="return confirm('Yakin hapus tarif <?= $t['jenis_kendaraan'] ?>?')"><i class="fa-solid fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
