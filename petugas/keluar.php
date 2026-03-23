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
    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-logo"><img src="../assets/logo_web.png" alt="Logo"></div>
            <div class="sidebar-label">Menu</div>
                        <ul class="sidebar-menu">
                <a href="transaksi.php" ><span class="icon-box"><i class="fa-solid fa-car-side"></i></span> <span>Parkir Aktif</span></a>
                <a href="masuk.php" ><span class="icon-box"><i class="fa-solid fa-right-to-bracket"></i></span> <span>Check-In</span></a>
                <a href="keluar.php" class="active"><span class="icon-box"><i class="fa-solid fa-right-from-bracket"></i></span> <span>Check-Out</span></a>
            </ul>
            <div class="sidebar-user">
                <div class="sidebar-user-info"><div class="avatar"><i class="fa-solid fa-user"></i></div><div class="user-detail"><span><?= $_SESSION['user']['nama_lengkap'] ?></span><small><?= $_SESSION['user']['role'] ?></small></div></div>
                <a href="../logout.php" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header"><h1><i class="fa-solid fa-money-bill-wave"></i>Check-Out & Pembayaran</h1></div>
            <div style="display: flex; justify-content: center;">
                <div class="form-card" style="width: 100%; max-width: 480px;">
                    <?php if(empty($aktif)): ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-car-side"></i>
                            <p>Tidak ada kendaraan parkir yang bisa di-check-out saat ini.</p>
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

                            <div class="info-box" style="margin-top: 8px;">
                                <p><i class="fa-solid fa-circle-info"></i> Klik tombol di bawah untuk mengkalkulasi durasi dan total biaya parkir. Struk otomatis akan dicetak setelahnya.</p>
                            </div>
                            
                            <button type="submit" name="proses_keluar" class="btn-custom btn-success" style="padding: 14px; font-size: 16px;"><i class="fa-solid fa-check-double"></i> Hitung & Selesaikan Pembayaran</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>