<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'petugas') header("Location: ../index.php");

$aktif = query("SELECT t.*, k.plat_nomor, a.nama_area FROM tb_transaksi t 
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                JOIN tb_area_parkir a ON t.id_area = a.id_area 
                WHERE t.status = 'masuk'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Petugas - Transaksi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="top-nav">
        <div class="nav-brand">
            🅿️ PARKIR-PRO <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">| Petugas Loket</span>
        </div>
        <div class="nav-links">
            <a href="transaksi.php" class="active">Parkir Aktif</a>
            <a href="masuk.php">Check-In Kendaraan</a>
        </div>
        <div class="nav-user">
            <span style="font-size: 14px; font-weight: 600;"><?= $_SESSION['user']['nama_lengkap'] ?></span>
            <a href="../logout.php" class="nav-logout">Logout</a>
        </div>
    </nav>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2>Daftar Kendaraan Sedang Parkir</h2>
            <a href="masuk.php" class="btn-custom" style="width: auto; padding: 10px 20px;">+ Check-In Baru</a>
        </div>
        <div class="table-container">
            <tr>
                <th>Plat Nomor</th><th>Area</th><th>Waktu Masuk</th><th>Aksi</th>
            </tr>
            <?php foreach($aktif as $row): ?>
            <tr>
                <td style="font-weight: 600;"><?= $row['plat_nomor'] ?></td>
                <td><?= $row['nama_area'] ?></td>
                <td style="color: var(--text-muted);"><?= $row['waktu_masuk'] ?></td>
                <td style="text-align: right;"><a href="keluar.php?id=<?= $row['id_parkir'] ?>" class="btn-custom btn-danger" style="padding: 6px 16px; font-size: 13px; width: auto;" onclick="return confirm('Proses Check-Out kendaraan ini?')">Check-Out</a></td>
            </tr>
            <?php endforeach; ?>
            <?php if(count($aktif) == 0): ?>
            <tr>
                <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted);">Tidak ada kendaraan yang sedang parkir saat ini.</td>
            </tr>
            <?php endif; ?>
        </table>
        </div>
    </div>
</body>
</html>