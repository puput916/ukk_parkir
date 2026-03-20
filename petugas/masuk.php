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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="top-nav">
        <div class="nav-brand">
            <i class="fa-solid fa-square-parking"></i> PARKIR-PRO <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">| Petugas Loket</span>
        </div>
        <div class="nav-links">
            <a href="transaksi.php">Parkir Aktif</a>
            <a href="masuk.php" class="active">Check-In Kendaraan</a>
            <a href="keluar.php">Check-Out Kendaraan</a>
        </div>
        <div class="nav-user">
            <span style="font-size: 14px; font-weight: 600;"><i class="fa-regular fa-circle-user" style="margin-right: 6px;"></i><?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="../logout.php" class="nav-logout"><i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 4px;"></i> Logout</a>
        </div>
    </nav>
    <div class="container">
        <div style="display: flex; justify-content: center;">
            <div class="form-card" style="width: 100%; max-width: 500px;">
                <h2 style="margin-top: 0; margin-bottom: 24px; text-align: center;"><i class="fa-solid fa-id-card-clip" style="color: var(--primary); margin-right: 8px;"></i> Entry Parkir Kendaraan</h2>
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

                    <button type="submit" name="simpan" class="btn-custom" style="margin-top: 24px; padding: 14px; font-size: 16px; border-radius: 12px;"><i class="fa-solid fa-check-circle" style="margin-right: 5px;"></i> Konfirmasi & Simpan Tiket</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>