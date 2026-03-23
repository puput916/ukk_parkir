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
                <a href="tarif_manage.php" ><span class="icon-box"><i class="fa-solid fa-tags"></i></span> <span>Kelola Tarif</span></a>
                <a href="area_manage.php" class="active"><span class="icon-box"><i class="fa-solid fa-map-location-dot"></i></span> <span>Kelola Area</span></a>
                <a href="log_aktivitas.php" ><span class="icon-box"><i class="fa-solid fa-clock-rotate-left"></i></span> <span>Log Aktivitas</span></a>
            </ul>
            <div class="sidebar-user">
                <div class="sidebar-user-info"><div class="avatar"><i class="fa-solid fa-user"></i></div><div class="user-detail"><span><?= $_SESSION['user']['nama_lengkap'] ?></span><small><?= $_SESSION['user']['role'] ?></small></div></div>
                <a href="../logout.php" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header"><h1><i class="fa-solid fa-map-location-dot"></i>Pemetaan Area Parkir</h1></div>

            <div style="display: flex; gap: 28px; align-items: flex-start;">
                <div class="form-card" style="flex: 1; max-width: 380px;">
                    <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px; font-weight: 700;"><i class="fa-solid fa-plus-circle" style="margin-right: 8px; color: var(--pink-400);"></i>Tambah Lokasi Baru</h3>
                    <form method="POST">
                        <label><i class="fa-solid fa-location-dot" style="margin-right: 5px; color: var(--text-muted);"></i> Nama Area</label>
                        <input type="text" name="nama_area" placeholder="Nama Area" required>
                        <label><i class="fa-solid fa-hashtag" style="margin-right: 5px; color: var(--text-muted);"></i> Kapasitas</label>
                        <input type="number" name="kapasitas" placeholder="Kapasitas" required>
                        <button type="submit" name="tambah" class="btn-custom"><i class="fa-solid fa-plus"></i> Simpan Area</button>
                    </form>
                </div>

                <div class="table-container" style="flex: 2;">
                    <table>
                        <thead><tr><th>Nama Area</th><th>Kapasitas Maks</th><th>Terisi</th><th style="text-align: right;">Aksi</th></tr></thead>
                        <tbody>
                            <?php foreach($areas as $a): ?>
                            <tr>
                                <td style="font-weight: 600;"><?= $a['nama_area'] ?></td>
                                <td><?= $a['kapasitas'] ?> Kendaraan</td>
                                <td><span class="badge badge-pink"><?= $a['terisi'] ?> Unit</span></td>
                                <td style="text-align: right;">
                                    <a href="?hapus=<?= $a['id_area'] ?>" class="btn-custom btn-danger" style="padding: 6px 14px; font-size: 12px; width: auto;" onclick="return confirm('Hapus area permanen?')"><i class="fa-solid fa-trash"></i> Hapus</a>
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
