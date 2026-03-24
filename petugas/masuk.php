<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'petugas') header("Location: ../index.php");

if (isset($_POST['simpan'])) {
    $plat = $_POST['plat_nomor'];
    $area = $_POST['id_area'];
    $tarif = $_POST['id_tarif'];
    $user_id = $_SESSION['user']['id_user'];

    mysqli_query($conn, "INSERT INTO tb_kendaraan (plat_nomor, id_user) VALUES ('$plat', '$user_id')");
    $id_knd = mysqli_insert_id($conn);

    mysqli_query($conn, "INSERT INTO tb_transaksi (id_kendaraan, id_tarif, id_area, id_user, status) 
                         VALUES ('$id_knd', '$tarif', '$area', '$user_id', 'masuk')");
    
    mysqli_query($conn, "UPDATE tb_area_parkir SET terisi = terisi + 1 WHERE id_area = $area");

    header("Location: transaksi.php");
    exit();
}

$areas = query("SELECT * FROM tb_area_parkir WHERE kapasitas > terisi");
$tarifs = query("SELECT * FROM tb_tarif");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check-In Parkir</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-logo"><img src="../assets/logo_web.png" alt="Logo"></div>
            <div class="sidebar-label">Menu</div>
                        <ul class="sidebar-menu">
                <a href="transaksi.php" ><span class="icon-box"><i class="fa-solid fa-car-side"></i></span> <span>Parkir Aktif</span></a>
                <a href="masuk.php" class="active"><span class="icon-box"><i class="fa-solid fa-right-to-bracket"></i></span> <span>Check-In</span></a>
                <a href="keluar.php" ><span class="icon-box"><i class="fa-solid fa-right-from-bracket"></i></span> <span>Check-Out</span></a>
            </ul>
            <div class="sidebar-user">
                <div class="sidebar-user-info"><div class="avatar"><i class="fa-solid fa-user"></i></div><div class="user-detail"><span><?= $_SESSION['user']['nama_lengkap'] ?></span><small><?= $_SESSION['user']['role'] ?></small></div></div>
                <a href="../logout.php" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header"><h1><i class="fa-solid fa-id-card-clip"></i>Entry Parkir Kendaraan</h1></div>
            <div style="display: flex; justify-content: center;">
                <div class="form-card" style="width: 100%; max-width: 480px;">
                    <form method="POST">
                        <label><i class="fa-solid fa-car-side" style="margin-right: 5px; color: var(--text-muted);"></i> Plat Nomor</label>
                        <input type="text" name="plat_nomor" placeholder="Contoh: B 1234 ABC" required style="font-family: monospace; font-size: 16px; font-weight: bold; text-transform: uppercase;">
                        
                        <label><i class="fa-solid fa-tag" style="margin-right: 5px; color: var(--text-muted);"></i> Jenis Kendaraan</label>
                        <select name="id_tarif" required>
                            <?php foreach($tarifs as $t): ?>
                                <option value="<?= $t['id_tarif'] ?>"><?= $t['jenis_kendaraan'] ?> - Rp <?= number_format($t['tarif_per_jam']) ?>/jam</option>
                            <?php endforeach; ?>
                        </select>

                        <label><i class="fa-solid fa-layer-group" style="margin-right: 5px; color: var(--text-muted);"></i> Area Parkir</label>
                        <select name="id_area" required>
                            <?php foreach($areas as $a): ?>
                                <option value="<?= $a['id_area'] ?>"><?= $a['nama_area'] ?> (Tersedia: <?= $a['kapasitas'] - $a['terisi'] ?>)</option>
                            <?php endforeach; ?>
                        </select>

                        <button type="submit" name="simpan" class="btn-custom" style="margin-top: 10px; padding: 14px; font-size: 16px;"><i class="fa-solid fa-check-circle"></i> Konfirmasi & Simpan Tiket</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>