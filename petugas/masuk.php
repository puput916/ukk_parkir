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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <h3>PETUGAS PANEL</h3>
        <a href="transaksi.php">Parkir Aktif</a>
        <a href="masuk.php">Check-In</a>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="main-content">
        <h2>Check-In Kendaraan</h2>
        <form method="POST" style="background:white; padding:20px; border-radius:10px; max-width:500px;">
            <label>Plat Nomor</label>
            <input type="text" name="plat_nomor" placeholder="Contoh: B 1234 ABC" required>
            
            <label>Jenis Kendaraan</label>
            <select name="id_tarif" required>
                <?php foreach($tarifs as $t): ?>
                    <option value="<?= $t['id_tarif'] ?>"><?= $t['jenis_kendaraan'] ?> - Rp <?= number_format($t['tarif_per_jam']) ?>/jam</option>
                <?php endforeach; ?>
            </select>

            <label>Area Parkir</label>
            <select name="id_area" required>
                <?php foreach($areas as $a): ?>
                    <option value="<?= $a['id_area'] ?>"><?= $a['nama_area'] ?> (Tersedia: <?= $a['kapasitas'] - $a['terisi'] ?>)</option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="simpan" class="btn-custom" style="margin-top: 15px;">Simpan Masuk</button>
        </form>
    </div>
</body>
</html>