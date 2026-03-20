<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'petugas') header("Location: ../index.php");
include '../config/helper.php';

if (isset($_POST['proses_keluar'])) {
    $id = $_POST['id_parkir'];
    $now = date('Y-m-d H:i:s');

    $tr = query("SELECT t.*, r.tarif_per_jam FROM tb_transaksi t 
                 JOIN tb_tarif r ON t.id_tarif = r.id_tarif WHERE id_parkir = $id")[0];

    $kalkulasi = hitungBiaya($tr['waktu_masuk'], $now, $tr['tarif_per_jam']);

    mysqli_query($conn, "UPDATE tb_transaksi SET 
        waktu_keluar = '$now', 
        durasi_jam = '{$kalkulasi['durasi']}', 
        biaya_total = '{$kalkulasi['total']}', 
        status = 'keluar' 
        WHERE id_parkir = $id");

    mysqli_query($conn, "UPDATE tb_area_parkir SET terisi = terisi - 1 WHERE id_area = {$tr['id_area']}");

    header("Location: cetak_struk.php?id=$id");
    exit();
}

$aktif = query("SELECT t.id_parkir, k.plat_nomor, t.waktu_masuk, a.nama_area FROM tb_transaksi t 
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                JOIN tb_area_parkir a ON t.id_area = a.id_area 
                WHERE t.status = 'masuk' ORDER BY t.waktu_masuk ASC");

$selected_id = isset($_GET['id']) ? $_GET['id'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check-Out Parkir</title>
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
            <a href="masuk.php">Check-In Kendaraan</a>
            <a href="keluar.php" class="active">Check-Out Kendaraan</a>
        </div>
        <div class="nav-user">
            <span style="font-size: 14px; font-weight: 600;"><i class="fa-regular fa-circle-user" style="margin-right: 6px;"></i><?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="../logout.php" class="nav-logout"><i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 4px;"></i> Logout</a>
        </div>
    </nav>
    <div class="container">
        <div style="display: flex; justify-content: center;">
            <div class="form-card" style="width: 100%; max-width: 500px;">
                <h2 style="margin-top: 0; margin-bottom: 24px; text-align: center;"><i class="fa-solid fa-money-bill-wave" style="color: #10b981; margin-right: 8px;"></i> Check-Out & Pembayaran</h2>
                
                <?php if(empty($aktif)): ?>
                    <div style="text-align: center; color: var(--text-muted); padding: 20px;">
                        <i class="fa-solid fa-car-side fa-2x" style="margin-bottom: 10px; opacity: 0.5;"></i><br>
                        Tidak ada kendaraan parkir yang bisa di-check-out saat ini.
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <label><i class="fa-solid fa-magnifying-glass" style="margin-right: 5px; color: var(--text-muted);"></i> Pilih Kendaraan (Plat Nomor)</label>
                        <select name="id_parkir" required style="font-family: monospace; font-size: 15px;">
                            <option value="">-- Pilih Kendaraan --</option>
                            <?php foreach($aktif as $a): ?>
                                <?php $sel = ($a['id_parkir'] == $selected_id) ? 'selected' : ''; ?>
                                <option value="<?= $a['id_parkir'] ?>" <?= $sel ?>>
                                    <?= $a['plat_nomor'] ?> - <?= $a['nama_area'] ?> (Masuk: <?= date('d/m H:i', strtotime($a['waktu_masuk'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <br><br>
                        <div style="background: #eff6ff; padding: 15px; border-radius: 8px; border-left: 4px solid #3b82f6; margin-bottom: 20px;">
                            <p style="margin: 0; font-size: 13.5px; color: #1e3a8a;"><i class="fa-solid fa-circle-info" style="margin-right: 5px;"></i> Klik tombol di bawah untuk mengkalkulasi durasi dan total biaya parkir. Struk otomatis akan dicetak setelahnya.</p>
                        </div>
                        
                        <button type="submit" name="proses_keluar" class="btn-custom" style="padding: 14px; font-size: 16px; border-radius: 12px; background: #10b981;"><i class="fa-solid fa-check-double" style="margin-right: 5px;"></i> Hitung & Selesaikan Pembayaran</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>